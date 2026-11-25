<?php
require_once __DIR__ . "/init.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit;
}

$nomeUsuario = $_SESSION["nome"] ?? "Usuário";

// Busca todos os chamados para contar nos cards de resumo
if (isset($_SESSION["id_cliente"])) {
    $todosChamados = readAll($pdo, "chamado", "id_cliente = ?", [$_SESSION["id_cliente"]]);
} else {
    $todosChamados = readAll($pdo, "chamado");
}

$abertos    = 0;
$andamento  = 0;
$concluidos = 0;
$cancelados = 0;

foreach ($todosChamados as $item) {
    if ($item["status_chamado"] === "aguardando") {
        $abertos++;
    } elseif ($item["status_chamado"] === "em andamento") {
        $abertos++;
        $andamento++;
    } elseif ($item["status_chamado"] === "concluido") {
        $concluidos++;
    } elseif ($item["status_chamado"] === "cancelado") {
        $cancelados++;
    }
}

// Filtros das abas e da barra de pesquisa
$status = $_GET["status"] ?? "";
$busca  = trim($_GET["busca"] ?? "");

$sql = "SELECT c.*, a.nome AS nome_ambiente
        FROM chamado c
        LEFT JOIN ambiente a ON c.id_ambiente = a.id
        WHERE (c.titulo LIKE ? OR CONCAT('#', LPAD(c.id, 4, '0')) LIKE ?)";
$params = ["%$busca%", "%$busca%"];

if (isset($_SESSION["id_cliente"])) {
    $sql .= " AND c.id_cliente = ?";
    $params[] = $_SESSION["id_cliente"];
}

if ($status !== "") {
    $sql .= " AND c.status_chamado = ?";
    $params[] = $status;
}

$sql .= " ORDER BY c.data_chamado DESC, c.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$chamados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel - SchoolHelpDesk</title>
    <link rel="stylesheet" href="css/painel.css">
</head>
<body>

    <aside class="menu">
        <div class="logo">
            <img src="imagens/logo_schoolhelpdesk.png" alt="SchoolHelpDesk">
        </div>

        <nav>
            <a href="painel.php" class="ativo">Início</a>
            <a href="abrir_chamado.php">Abrir chamado</a>
            <a href="chamado.php">Meus chamados</a>
            

            <p class="grupo">CONTA</p>
            <a href="meu_perfil.php">Meu perfil</a>
        </nav>

        <a href="logout.php" class="sair">Sair</a>
    </aside>

    <main class="conteudo">

        <header class="topo">
            <strong class="marca-topo">SchoolHelpdesk</strong>
            <span id="nomeUsuario"><?= htmlspecialchars($nomeUsuario) ?></span>
        </header>

        <section class="titulo">
            <div>
                <h1 class="saudacao">Olá, <?= htmlspecialchars($nomeUsuario) ?></h1>
                <p class="subtitulo">Veja como estão seus chamados</p>
            </div>
            <a href="abrir_chamado.php" class="botao">+ Abrir novo chamado</a>
        </section>

    <section class="cards">
        <div class="card card-borda-azul">
            <strong class="numero"><?= $abertos ?></strong>
            <span class="rotulo-aberto">Abertos</span>
        </div>

        <div class="card card-borda-amarela">
            <strong class="numero"><?= $andamento ?></strong>
            <span class="rotulo-andamento">Em andamento</span>
        </div>

        <div class="card card-borda-verde">
            <strong class="numero"><?= $concluidos ?></strong>
            <span class="rotulo-concluido">Concluídos</span>
        </div>

        <div class="card card-borda-vermelha">
            <strong class="numero"><?= $cancelados ?></strong>
            <span class="rotulo-cancelado">Cancelados</span>
        </div>
    </section>
        

        <section class="cartao-tabela">
            <div class="abas">
                <a href="painel.php" class="<?= $status === '' ? 'ativa' : '' ?>">Todos</a>
                <a href="painel.php?status=aguardando" class="<?= $status === 'aguardando' ? 'ativa' : '' ?>">Aguardando</a>
                <a href="painel.php?status=em andamento" class="<?= $status === 'em andamento' ? 'ativa' : '' ?>">Em andamento</a>
                <a href="painel.php?status=concluido" class="<?= $status === 'concluido' ? 'ativa' : '' ?>">Concluídos</a>
                <a href="painel.php?status=cancelado" class="<?= $status === 'cancelado' ? 'ativa' : '' ?>">Cancelados</a>
            </div>

            <form method="get" class="filtros">
                <?php if ($status !== "") { ?>
                    <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
                <?php } ?>
                <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Buscar chamado por número ou título" class="campo-busca">
                <button type="submit" class="botao">Buscar</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>TÍTULO</th>
                        <th>AMBIENTE</th>
                        <th>STATUS</th>
                        <th>ABERTURA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($chamados)) { ?>
                        <tr>
                            <td colspan="5" class="vazio">Nenhum chamado encontrado.</td>
                        </tr>
                    <?php } ?>

                    <?php foreach ($chamados as $c) {
                        $stat = strtolower($c["status_chamado"] ?? "aguardando");
                        $classeStatus = "aberto";

                        if ($stat === "em andamento") {
                            $classeStatus = "andamento";
                        } elseif ($stat === "concluido") {
                            $classeStatus = "concluido";
                        } elseif ($stat === "cancelado") {
                            $classeStatus = "cancelado";
                        }
                    ?>
                        <tr>
                            <td>#<?= str_pad((string) $c["id"], 4, "0", STR_PAD_LEFT) ?></td>
                            <td><?= htmlspecialchars($c["titulo"]) ?></td>
                            <td><?= htmlspecialchars($c["nome_ambiente"] ?? "Não informado") ?></td>
                            <td>
                                <span class="status <?= $classeStatus ?>">
                                    <?= htmlspecialchars(ucfirst($stat)) ?>
                                </span>
                            </td>
                            <td><?= !empty($c["data_chamado"]) ? date("d/m/Y", strtotime($c["data_chamado"])) : "" ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </section>

    </main>

    
</body>
</html>