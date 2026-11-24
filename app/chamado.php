<?php
require_once __DIR__ . "/init.php";

$pdo = database();
if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST["excluir"])) {
    $stmt = $pdo->prepare("DELETE FROM chamado WHERE id = ?");
    $stmt->execute([$_POST["excluir"]]);
    header("Location: chamado.php");
    exit;
}

$status = $_GET["status"] ?? "";
$busca  = $_GET["busca"] ?? "";

$sql = "SELECT * FROM chamado WHERE titulo LIKE ?";
$params = ["%$busca%"];

if ($status != "") {
    $sql .= " AND status_chamado = ?";
    $params[] = $status;
}

$sql .= " ORDER BY data_chamado DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Meus chamados - SchoolHelpDesk</title>
    <link rel="stylesheet" href="css/chamado.css">
</head>
<body>

    <aside class="menu">
        <div class="logo">
            <img src="imagem/logo_schoolhelpdesk.png" alt="SchoolHelpDesk">
        </div>

        <nav>
            <a href="painel.php">Início</a>
            <a href="abrir_chamado.php">Abrir chamado</a>
            <a href="chamado.php" class="ativo">Meus chamados</a>
            <a href="historico.php">Histórico</a>

            <p class="grupo">CONTA</p>
            <a href="meu_perfil.php">Meu perfil</a>
        </nav>

        <a href="logout.php" class="sair">Sair</a>
    </aside>

    <main class="conteudo">
        <header class="topo">
            <span class="caminho">Início / <strong>Meus chamados</strong></span>
            <span class="nome-topo"><?= $_SESSION["nome"] ?? "" ?></span>
        </header>

        <section class="cabecalho-lista">
            <div>
                <h1>Meus chamados</h1>
                <p class="subtitulo"><?= count($chamados) ?> chamados registrados</p>
            </div>
            <a href="abrir_chamado.php" class="botao">+ Abrir novo chamado</a>
        </section>

        <section class="cartao-tabela">
            <div class="abas">
                <a href="chamado.php" class="<?= $status == '' ? 'ativa' : '' ?>">Todos</a>
                <a href="chamado.php?status=aberto" class="<?= $status == 'aberto' ? 'ativa' : '' ?>">Abertos</a>
                <a href="chamado.php?status=em_andamento" class="<?= $status == 'em_andamento' ? 'ativa' : '' ?>">Em andamento</a>
                <a href="chamado.php?status=aguardando" class="<?= $status == 'aguardando' ? 'ativa' : '' ?>">Aguardando</a>
                <a href="chamado.php?status=concluido" class="<?= $status == 'concluido' ? 'ativa' : '' ?>">Concluídos</a>
            </div>

            <form method="get" class="filtros">
                <?php if ($status != "") { ?>
                    <input type="hidden" name="status" value="<?= $status ?>">
                <?php } ?>
                <input type="text" name="busca" value="<?= $busca ?>" placeholder="Buscar por título" class="campo-busca">
                <button type="submit" class="botao">Buscar</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>TÍTULO</th>
                        <th>TIPO</th>
                        <th>AMBIENTE</th>
                        <th>PRIORIDADE</th>
                        <th>STATUS</th>
                        <th>ABERTURA</th>
                        <th>AÇÃO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($chamados as $c) { ?>
                        <tr>
                            <td class="codigo">#SHD-<?= $c["id"] ?></td>
                            <td class="col-titulo"><?= $c["titulo"] ?></td>
                            <td><?= $c["tipo"] ?></td>
                            <td><?= $c["ambiente"] ?></td>
                            <td>
                                <span class="tag prio-<?= $c["prioridade"] ?>">
                                    <?= $c["prioridade"] ?>
                                </span>
                            </td>
                            <td>
                                <span class="tag status-<?= $c["status_chamado"] ?>">
                                    <?= $c["status_chamado"] ?>
                                </span>
                            </td>
                            <td><?= date("d/m/Y", strtotime($c["data_chamado"])) ?></td>
                            <td>
                                <form method="post" onsubmit="return confirm('Deseja excluir este chamado?')">
                                    <button type="submit" class="excluir" name="excluir" value="<?= $c["id"] ?>">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>
    </main>

</body>
</html>