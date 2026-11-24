<?php
require_once __DIR__ . '/init.php';

// Valores do banco e textos exibidos na tela.
const NOMES_STATUS = [
    'aguardando' => 'Aguardando',
    'em andamento' => 'Em andamento',
    'concluido' => 'Concluído',
    'cancelado' => 'Cancelado'
];

const CLASSE_STATUS = [
    'aguardando' => 'aberto',
    'em andamento' => 'andamento',
    'concluido' => 'concluido',
    'cancelado' => 'cancelado'
];

const CARGOS = [
    'tecnico' => 'Técnico',
    'administrador' => 'Administrador'
];

// Funções pequenas usadas na página.
function h($texto): string {
    return htmlspecialchars((string) $texto, ENT_QUOTES, 'UTF-8');
}

function num($id): string {
    return '#' . str_pad((string) $id, 4, '0', STR_PAD_LEFT);
}

function dataBr($data): string {
    return date('d/m/Y', strtotime((string) $data));
}

function opcoes(array $lista, string $selecionado = ''): string {
    $html = '';
    foreach ($lista as $valor => $rotulo) {
        $marcado = (string) $valor === $selecionado ? ' selected' : '';
        $html .= '<option value="' . h($valor) . '"' . $marcado . '>' . h($rotulo) . '</option>';
    }
    return $html;
}

function avisar(string $tipo, string $texto): void {
    $_SESSION['msg'] = [$tipo, $texto];
}

// Só técnico pode acessar esta página.
if (empty($_SESSION['id_usuario']) || ($_SESSION['nivel'] ?? '') !== 'tecnico') {
    header('Location: login.php');
    exit;
}

$idUsuario = (int) $_SESSION['id_usuario'];

// Sair da conta.
if (isset($_GET['sair'])) {
    unset($_SESSION['adm_usuario'], $_SESSION['id_usuario'], $_SESSION['nome'],
          $_SESSION['email'], $_SESSION['nivel'], $_SESSION['id_cliente']);
    header('Location: login.php');
    exit;
}

// Ações enviadas pelos formulários.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    try {
        if ($acao === 'alterar_status') {
            $id = (int) ($_POST['id'] ?? 0);
            $status = $_POST['status'] ?? '';

            if (isset(NOMES_STATUS[$status]) && read($pdo, 'chamado', 'id = ?', [$id])) {
                update($pdo, 'chamado', ['status_chamado' => $status], 'id = ?', [$id]);
                avisar('ok', 'Chamado ' . num($id) . ' atualizado para ' . NOMES_STATUS[$status] . '.');
            }

            header('Location: tecnico.php?aba=chamados');
            exit;
        }

        if ($acao === 'alterar_senha') {
            $atual = $_POST['senha_atual'] ?? '';
            $nova = $_POST['senha_nova'] ?? '';
            $confirma = $_POST['senha_confirma'] ?? '';

            $usuario = read($pdo, 'usuario', 'id = ?', [$idUsuario]);

            if (!$usuario || !password_verify($atual, $usuario['senha'])) {
                avisar('erro', 'A senha atual está incorreta.');
            } elseif (strlen($nova) < 6) {
                avisar('erro', 'A nova senha deve ter pelo menos 6 caracteres.');
            } elseif ($nova !== $confirma) {
                avisar('erro', 'A confirmação não é igual à nova senha.');
            } else {
                update($pdo, 'usuario', ['senha' => password_hash($nova, PASSWORD_DEFAULT)], 'id = ?', [$idUsuario]);
                avisar('ok', 'Senha alterada com sucesso.');
            }

            header('Location: tecnico.php?aba=perfil');
            exit;
        }
    } catch (PDOException $e) {
        avisar('erro', 'Erro ao acessar o banco de dados. Tente novamente.');
        header('Location: tecnico.php');
        exit;
    }
}

// Define qual aba será exibida.
$aba = $_GET['aba'] ?? 'inicio';
if (!in_array($aba, ['inicio', 'chamados', 'historico', 'perfil'], true)) {
    $aba = 'inicio';
}

// Mensagem temporária.
$msg = $_SESSION['msg'] ?? null;
unset($_SESSION['msg']);

// Busca e filtro.
$busca = trim($_GET['busca'] ?? '');
$filtro = $_GET['status'] ?? '';
if (!isset(NOMES_STATUS[$filtro])) {
    $filtro = '';
}

// Carrega os dados necessários para a aba selecionada.
if ($aba === 'inicio') {
    $cont = array_fill_keys(array_keys(NOMES_STATUS), 0);
    $linhas = $pdo->query('SELECT status_chamado, COUNT(*) AS qtd FROM chamado GROUP BY status_chamado');

    foreach ($linhas as $linha) {
        $cont[$linha['status_chamado']] = (int) $linha['qtd'];
    }

    $total = array_sum($cont);
    $abertos = $cont['aguardando'] + $cont['em andamento'];

    $porLocal = $pdo->query(
        "SELECT COALESCE(a.nome, 'Não informado') AS local, COUNT(*) AS qtd
         FROM chamado c
         LEFT JOIN ambiente a ON a.id = c.id_ambiente
         GROUP BY a.id, a.nome
         ORDER BY qtd DESC
         LIMIT 5"
    )->fetchAll();

    $cards = [
        [$abertos, 'rotulo-aberto', 'Abertos'],
        [$cont['em andamento'], 'rotulo-andamento', 'Em andamento'],
        [$cont['concluido'], 'rotulo-concluido', 'Concluídos'],
        [$cont['cancelado'], 'rotulo-cancelado', 'Cancelados']
    ];
}

if ($aba === 'chamados') {
    $where = [];
    $params = [];

    if ($filtro !== '') {
        $where[] = 'c.status_chamado = ?';
        $params[] = $filtro;
    }

    if ($busca !== '') {
        $like = '%' . addcslashes($busca, '%_\\') . '%';
        $where[] = '(CAST(c.id AS CHAR) LIKE ? OR c.titulo LIKE ? OR a.nome LIKE ? OR cl.nome LIKE ?)';
        array_push($params, $like, $like, $like, $like);
    }

    // Técnico enxerga os chamados de todas as pessoas.
    $sql = "SELECT c.id, c.titulo, c.descricao,
                   c.data_chamado AS data, c.status_chamado AS status,
                   COALESCE(a.nome, 'Não informado') AS local,
                   COALESCE(cl.nome, 'Não informado') AS solicitante
            FROM chamado c
            LEFT JOIN ambiente a ON a.id = c.id_ambiente
            LEFT JOIN cliente cl ON cl.id = c.id_cliente";

    if ($where) {
        $sql .= ' WHERE ' . implode(' AND ', $where);
    }

    $sql .= ' ORDER BY c.data_chamado DESC, c.id DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $lista = $stmt->fetchAll();
}

// Histórico de atendimento: só chamados concluídos.
if ($aba === 'historico') {
    $stmt = $pdo->prepare(
        "SELECT id, descricao, data_chamado AS data, status_chamado AS status
         FROM chamado
         WHERE status_chamado = 'concluido'
         ORDER BY data_chamado DESC, id DESC"
    );
    $stmt->execute();
    $historico = $stmt->fetchAll();
}

if ($aba === 'perfil') {
    $perfil = read($pdo, 'usuario', 'id = ?', [$idUsuario]);

    if (!$perfil) {
        header('Location: tecnico.php?sair=1');
        exit;
    }
}

require __DIR__ . '/tecnico_views/layout.php';