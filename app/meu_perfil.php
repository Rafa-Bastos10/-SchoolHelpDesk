<?php

session_start();

require_once "../Página_principal/conexao.php";
require_once "../Página_principal/crud.php";

// Busca os dados da conta que está conectada.
if (!isset($_SESSION["id_usuario"])) {
    header("Location: ../login/login.php");
    exit;
}

$idUsuario = $_SESSION["id_usuario"];
$usuario = read($pdo, "usuario", "id = ?", [$idUsuario]);

if (!$usuario) {
    session_destroy();
    header("Location: ../login/login.php");
    exit;
}

// Atualiza o nome da sessão com o nome salvo no banco.
$_SESSION["nome"] = $usuario["nome"];

$nomeNivel = ($usuario["nivel"] === "administrador") ? "Administrador" : "Técnico";

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu perfil - SchoolHelpDesk</title>
    <link rel="stylesheet" href="meu_perfil.css">
</head>
<body>

    <aside class="menu">
        <div class="logo">
            <img src="../logo_schoolhelpdesk.png" alt="SchoolHelpDesk">
        </div>

        <nav>
            <a href="../Página_principal/painel.php">Início</a>
            <a href="../Página_principal/abrir_chamado.php">Abrir chamado</a>
            <a href="../Página_principal/chamados.php">Meus chamados</a>
            <a href="../Página_principal/historico.php">Histórico</a>

            <p class="grupo">CONTA</p>
            <a href="meu_perfil.php" class="ativo">Meu perfil</a>
        </nav>

        <a href="../login/logout.php" class="sair">Sair</a>
    </aside>

    <main class="conteudo">

        <header class="topo">
            <span class="caminho">CONTA / MEU PERFIL</span>
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
                <?= htmlspecialchars(mb_strtoupper(mb_substr($usuario["nome"], 0, 1, "UTF-8"), "UTF-8"), ENT_QUOTES, "UTF-8") ?>
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

        <a href="../Página_principal/painel.php" class="botao-voltar">Voltar ao painel</a>

    </main>

</body>
</html>
