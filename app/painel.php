<?php

session_start();

// Só entra no painel quem fez login.
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../login/login.php");
    exit;
}

// Conecta ao banco e carrega as funções do CRUD.
require_once "conexao.php";
require_once "crud.php";

// Se o login já tiver sido criado, o nome salvo na sessão será exibido.
$nomeUsuario = $_SESSION['nome'] ?? 'Usuário';

// Busca os chamados. Quando existir um cliente na sessão, mostra apenas os chamados dele.
if (isset($_SESSION['id_cliente'])) {
    $chamados = readAll($pdo, 'chamado', 'id_cliente = ?', [$_SESSION['id_cliente']]);
} else {
    $chamados = readAll($pdo, 'chamado');
}

// Organiza os chamados do mais recente para o mais antigo.
usort($chamados, function ($a, $b) {
    return strtotime($b['data_chamado']) <=> strtotime($a['data_chamado']);
});

// Inicia os contadores dos cards
$abertos = 0;
$andamento = 0;
$cancelado = 0;
$concluidos = 0;

// Conta a quantidade de chamados em cada situação
foreach ($chamados as $chamado) {
    if ($chamado['status_chamado'] === 'cancelado') {
        $cancelado++;
    } elseif ($chamado['status_chamado'] === 'em andamento') {
        $abertos++;
        $andamento++;
    } elseif ($chamado['status_chamado'] === 'aguardando') {
        $abertos++;
    } elseif ($chamado['status_chamado'] === 'concluido') {
        $concluidos++;
    }
}

// Filtra os chamados pelo número ou pelo título, caso o usuário pesquise.
$termoBusca = trim($_GET['busca'] ?? '');
if ($termoBusca !== '') {
    $chamados = array_filter($chamados, function ($chamado) use ($termoBusca) {
        return stripos((string) $chamado['id'], $termoBusca) !== false
            || stripos($chamado['titulo'], $termoBusca) !== false;
    });
}

// Busca o nome do ambiente de cada chamado.
foreach ($chamados as &$chamado) {
    $ambiente = read($pdo, 'ambiente', 'id = ?', [$chamado['id_ambiente']]);
    $chamado['nome_ambiente'] = $ambiente ? $ambiente['nome'] : 'Não informado';
}
unset($chamado);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchoolHelpDesk</title>
    <link rel="stylesheet" href="painel.css">
</head>
<body>

    <div class="menu">
        <div class="logo">
            <img src="../logo_schoolhelpdesk.png" alt="SchoolHelpDesk - Juntos por uma escola que funciona melhor!">
        </div>

        <nav>
            <a href="painel.php" class="ativo">Início</a>
            <a href="abrir_chamado.php">Abrir chamado</a>
            <a href="chamados.php">Meus chamados</a>
            <a href="historico.php">Histórico</a>

            <p class="grupo">CONTA</p>
            <a href="../Meu_perfil/meu_perfil.php">Meu perfil</a>
        </nav>

        <a href="../login/logout.php" class="sair">Sair</a>
    </div>

    <main class="conteudo">

        <header class="topo">
            <form method="GET" action="painel.php">
                <input type="text" name="busca" placeholder="Buscar chamado por número ou título" value="<?= htmlspecialchars($termoBusca) ?>">
            </form>
            <span id="nomeUsuario"><?= htmlspecialchars($nomeUsuario) ?></span>
        </header>

        <section class="titulo">
            <div>
                <h1 class="saudacao">Olá, <?= htmlspecialchars($nomeUsuario) ?></h1>
                <p class="subtitulo">Veja como estão seus chamados</p>
            </div>
            <a href="abrir_chamado.php" class="botao">Abrir novo chamado</a>
        </section>

        <section class="cards">
            <div class="card">
                <strong class="numero"><?= $abertos ?></strong>
                <span class="rotulo-aberto">Abertos</span>
            </div>
            <div class="card">
                <strong class="numero"><?= $andamento ?></strong>
                <span class="rotulo-andamento">Em andamento</span>
            </div>
            <div class="card">
                <strong class="numero"><?= $concluidos ?></strong>
                <span class="rotulo-concluido">Concluidos</span>
            </div>
            <div class="card">
                <strong class="numero"><?= $cancelado ?></strong>
                <span class="rotulo-cancelado">Cancelados</span>
            </div>
        </section>

        <section class="tabela-box">
            <h2 class="tabela-titulo">Chamados recentes</h2>

            <table>
                <thead>
                    <tr class="cabecalho">
                        <th>Nº</th>
                        <th>Título</th>
                        <th>Local</th>
                        <th>Status</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($chamados) > 0): ?>
                        <?php foreach (array_slice($chamados, 0, 10) as $chamado): ?>
                            <?php
                                $status = $chamado['status_chamado'];
                                $classeStatus = 'aberto';

                                if ($status === 'em andamento') {
                                    $classeStatus = 'andamento';
                                } elseif ($status === 'concluido') {
                                    $classeStatus = 'concluido';
                                } elseif ($status === 'cancelado') {
                                    $classeStatus = 'cancelado';
                                }

                                $nomeStatus = ucfirst($status);
                                $dataFormatada = date('d/m/Y', strtotime($chamado['data_chamado']));
                            ?>
                            <tr>
                                <td>#<?= str_pad((string) $chamado['id'], 4, '0', STR_PAD_LEFT) ?></td>
                                <td><?= htmlspecialchars($chamado['titulo']) ?></td>
                                <td><?= htmlspecialchars($chamado['nome_ambiente']) ?></td>
                                <td><span class="status <?= $classeStatus ?>"><?= htmlspecialchars($nomeStatus) ?></span></td>
                                <td><?= $dataFormatada ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">Nenhum chamado encontrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

    </main>

</body>
</html>
