<?php
require_once __DIR__ . "/init.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit;
}

$idConta = $_SESSION["id_usuario"];
$nivelSessao = $_SESSION["nivel"] ?? "";

// Cliente fica na tabela cliente; administrador e técnico ficam em usuario
$tabela = ($nivelSessao === "cliente") ? "cliente" : "usuario";

$stmt = $pdo->prepare("SELECT * FROM $tabela WHERE id = ? LIMIT 1");
$stmt->execute([$idConta]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Na tabela cliente não existe a coluna nivel, então usa o da sessão
$nivel = $usuario["nivel"] ?? $nivelSessao;

$nomes = [
    "administrador" => "Administrador",
    "tecnico"       => "Técnico",
    "cliente"       => "Cliente",
];
$nomeNivel = $nomes[$nivel] ?? "Usuário";

$inicial = strtoupper(substr(trim($usuario["nome"]), 0, 1));
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu perfil - SchoolHelpDesk</title>
    <link rel="stylesheet" href="css/meu_perfil.css">
</head>
<body>

    <aside class="menu">
        <div class="logo">
            <img src="imagens/logo_schoolhelpdesk.png" alt="SchoolHelpDesk">
        </div>

        <nav>
            <a href="painel.php">Início</a>
            <a href="abrir_chamado.php">Abrir chamado</a>
            <a href="chamado.php">Meus chamados</a>
            

            <p class="grupo">CONTA</p>
            <a href="meu_perfil.php" class="ativo">Meu perfil</a>
        </nav>

        <a href="logout.php" class="sair">Sair</a>
    </aside>

    <main class="conteudo">

        <header class="topo">
            <span class="caminho">Conta / <strong>Meu Perfil</strong></span>
            <span class="nome-topo"><?= htmlspecialchars($usuario["nome"], ENT_QUOTES, "UTF-8") ?></span>
        </header>

        <section class="titulo">
            <div>
                <h1>Meu perfil</h1>
                <p class="subtitulo">Visualize as informações da sua conta.</p>
            </div>
        </section>

        <section class="perfil-principal">
            <div class="avatar">
                <?= htmlspecialchars($inicial, ENT_QUOTES, "UTF-8") ?>
            </div>

            <div class="identificacao">
                <h2><?= htmlspecialchars($usuario["nome"], ENT_QUOTES, "UTF-8") ?></h2>
                <p><?= htmlspecialchars($usuario["email"], ENT_QUOTES, "UTF-8") ?></p>
                <span class="nivel"><?= htmlspecialchars($nomeNivel, ENT_QUOTES, "UTF-8") ?></span>
            </div>
        </section>

        <section class="informacoes">
            <h2>Informações pessoais</h2>

            <div class="grade-informacoes">
                <div class="item">
                    <span class="rotulo">Nome completo</span>
                    <strong><?= htmlspecialchars($usuario["nome"], ENT_QUOTES, "UTF-8") ?></strong>
                </div>

                <div class="item">
                    <span class="rotulo">E-mail</span>
                    <strong><?= htmlspecialchars($usuario["email"], ENT_QUOTES, "UTF-8") ?></strong>
                </div>

                <div class="item">
                    <span class="rotulo">Número da conta</span>
                    <strong>#<?= str_pad((string) $usuario["id"], 4, "0", STR_PAD_LEFT) ?></strong>
                </div>

                <div class="item">
                    <span class="rotulo">Tipo de usuário</span>
                    <strong><?= htmlspecialchars($nomeNivel, ENT_QUOTES, "UTF-8") ?></strong>
                </div>
            </div>
        </section>

        <a href="painel.php" class="botao-voltar">Voltar ao painel</a>

    </main>

</body>
</html>