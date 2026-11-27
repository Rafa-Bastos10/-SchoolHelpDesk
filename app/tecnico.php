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

const NOMES_PRIORIDADE = [
    'urgente' => 'Urgente',
    'alta' => 'Alta',
    'media' => 'Média',
    'baixa' => 'Baixa'
];

const CARGOS = [
    'tecnico' => 'Técnico',
    'administrador' => 'Administrador'
];

// Funções utilizadas na página.
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
    unset(
        $_SESSION['adm_usuario'],
        $_SESSION['id_usuario'],
        $_SESSION['nome'],
        $_SESSION['email'],
        $_SESSION['nivel'],
        $_SESSION['id_cliente']
    );

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
                update(
                    $pdo,
                    'usuario',
                    ['senha' => password_hash($nova, PASSWORD_DEFAULT)],
                    'id = ?',
                    [$idUsuario]
                );

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

    // Contagem por status.
    $cont = array_fill_keys(array_keys(NOMES_STATUS), 0);

    $linhas = $pdo->query(
        'SELECT status_chamado, COUNT(*) AS qtd FROM chamado GROUP BY status_chamado'
    );

    foreach ($linhas as $linha) {
        if (isset($cont[$linha['status_chamado']])) {
            $cont[$linha['status_chamado']] = (int) $linha['qtd'];
        }
    }

    $total = array_sum($cont);

    // Contagem por prioridade.
    $prio = array_fill_keys(array_keys(NOMES_PRIORIDADE), 0);

    $linhas = $pdo->query(
        'SELECT prioridade, COUNT(*) AS qtd FROM chamado GROUP BY prioridade'
    );

    foreach ($linhas as $linha) {
        if (isset($prio[$linha['prioridade']])) {
            $prio[$linha['prioridade']] = (int) $linha['qtd'];
        }
    }

    $cards = [];

    foreach (NOMES_PRIORIDADE as $valor => $rotulo) {
        $cards[] = [$prio[$valor], $valor, $rotulo];
    }

    // Chamados atribuídos a este técnico.
    $stmt = $pdo->prepare(
        "SELECT id, titulo, prioridade
         FROM chamado
         WHERE id_usuario = ?
         AND status_chamado IN ('aguardando', 'em andamento')
         ORDER BY FIELD(prioridade, 'urgente', 'alta', 'media', 'baixa'),
         data_chamado DESC, id DESC
         LIMIT 7"
    );

    $stmt->execute([$idUsuario]);
    $meusChamados = $stmt->fetchAll();
}

// Todos os chamados.
if ($aba === 'chamados') {

    $where = [];
    $params = [];

    if ($filtro !== '') {
        $where[] = 'c.status_chamado = ?';
        $params[] = $filtro;
    }

    if ($busca !== '') {
        $numero = ltrim(ltrim($busca, '#'), '0');

        $likeNum = '%' . addcslashes($numero !== '' ? $numero : $busca, '%\_\\\\') . '%';
        $like = '%' . addcslashes($busca, '%\_\\\\') . '%';

        $where[] = '(CAST(c.id AS CHAR) LIKE ? OR c.titulo LIKE ? OR a.nome LIKE ? OR cl.nome LIKE ?)';

        array_push($params, $likeNum, $like, $like, $like);
    }

    $sql = "SELECT c.id, c.titulo,
                   c.data_chamado AS data,
                   c.status_chamado AS status,
                   COALESCE(a.nome, 'Não informado') AS local,
                   COALESCE(cl.nome, 'Não informado') AS solicitante,
                   COALESCE(t.nome, 'Não atribuído') AS tecnico
            FROM chamado c
            LEFT JOIN ambiente a ON a.id = c.id_ambiente
            LEFT JOIN cliente cl ON cl.id = c.id_cliente
            LEFT JOIN usuario t ON t.id = c.id_usuario";

    // Adiciona os filtros, quando existirem.
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

// Perfil do técnico.
if ($aba === 'perfil') {

    $perfil = read($pdo, 'usuario', 'id = ?', [$idUsuario]);

    if (!$perfil) {
        header('Location: tecnico.php?sair=1');
        exit;
    }
}

// Carrega o layout.
require __DIR__ . '/tecnico_views/layout.php';
