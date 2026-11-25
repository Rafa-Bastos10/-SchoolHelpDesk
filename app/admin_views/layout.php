<?php
if (!isset($pdo)) {
    header("Location: ../admin.php");
    exit;
}

$pagina      = $pagina ?? "inicio";
$nomeUsuario = $nomeUsuario ?? ($_SESSION["nome"] ?? "Administrador");
$mensagem    = $mensagem ?? "";
$erro        = $erro ?? "";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SchoolHelpDesk</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

    <aside class="menu">
        <div class="logo">
            <img src="imagens/logo_schoolhelpdesk.png" alt="SchoolHelpDesk">
        </div>

        <nav>
            <a href="admin.php?pagina=inicio" class="<?= $pagina === 'inicio' ? 'ativo' : '' ?>">Início</a>
            <a href="admin.php?pagina=chamados" class="<?= $pagina === 'chamados' ? 'ativo' : '' ?>">Todos os chamados</a>
            <a href="admin.php?pagina=funcionarios" class="<?= $pagina === 'funcionarios' ? 'ativo' : '' ?>">Funcionários</a>

            <p class="grupo">CONTA</p>
            <a href="admin.php?pagina=perfil" class="<?= $pagina === 'perfil' ? 'ativo' : '' ?>">Meu perfil</a>
        </nav>

        <a href="logout.php" class="sair">Sair</a>
    </aside>

    <main class="conteudo">
        <header class="topo">
            <span class="caminho">Administração / <strong><?= $pagina === 'perfil' ? 'Meu perfil' : ucfirst($pagina) ?></strong></span>
            <span id="nomeUsuario"><?= htmlspecialchars($nomeUsuario) ?></span>
        </header>

        <?php if (!empty($mensagem)): ?>
            <div class="aviso sucesso"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <?php if (!empty($erro)): ?>
            <div class="aviso erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <?php if ($pagina === 'perfil'): ?>
            <?php
            $nomePerfil  = $usuarioLogado['nome'] ?? $nomeUsuario;
            $emailPerfil = $usuarioLogado['email'] ?? '';
            $idPerfil    = $usuarioLogado['id'] ?? ($_SESSION['id_usuario'] ?? 0);
            $inicial     = strtoupper(substr(trim($nomePerfil), 0, 1));
            ?>

            <section class="titulo">
                <div>
                    <h1 class="saudacao">Meu perfil</h1>
                    <p class="subtitulo">Visualize as informações da sua conta.</p>
                </div>
            </section>

            <section class="perfil-principal">
                <div class="avatar"><?= htmlspecialchars($inicial) ?></div>

                <div class="identificacao">
                    <h2><?= htmlspecialchars($nomePerfil) ?></h2>
                    <p><?= htmlspecialchars($emailPerfil) ?></p>
                    <span class="nivel">Administrador</span>
                </div>
            </section>

            <section class="informacoes">
                <h2>Informações pessoais</h2>

                <div class="grade-informacoes">
                    <div class="item">
                        <span class="rotulo">Nome completo</span>
                        <strong><?= htmlspecialchars($nomePerfil) ?></strong>
                    </div>

                    <div class="item">
                        <span class="rotulo">E-mail</span>
                        <strong><?= htmlspecialchars($emailPerfil) ?></strong>
                    </div>

                    <div class="item">
                        <span class="rotulo">Número da conta</span>
                        <strong>#<?= str_pad((string) $idPerfil, 4, '0', STR_PAD_LEFT) ?></strong>
                    </div>

                    <div class="item">
                        <span class="rotulo">Tipo de usuário</span>
                        <strong>Administrador</strong>
                    </div>
                </div>
            </section>

            <a href="admin.php?pagina=inicio" class="botao-voltar">Voltar ao início</a>
        <?php else: ?>
            <?php require __DIR__ . "/" . $pagina . ".php"; ?>
        <?php endif; ?>
    </main>

</body>
</html>