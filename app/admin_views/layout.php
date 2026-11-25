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

        <?php if ($mensagem !== ''): ?>
            <div class="aviso sucesso"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <?php if ($erro !== ''): ?>
            <div class="aviso erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <?php if ($pagina === 'perfil'): ?>
            <?php $inicial = strtoupper(substr(trim($usuarioLogado['nome']), 0, 1)); ?>

            <section class="titulo">
                <div>
                    <h1 class="saudacao">Meu perfil</h1>
                    <p class="subtitulo">Visualize as informações da sua conta.</p>
                </div>
            </section>

            <section class="perfil-principal">
                <div class="avatar"><?= htmlspecialchars($inicial) ?></div>

                <div class="identificacao">
                    <h2><?= htmlspecialchars($usuarioLogado['nome']) ?></h2>
                    <p><?= htmlspecialchars($usuarioLogado['email']) ?></p>
                    <span class="nivel">Administrador</span>
                </div>
            </section>

            <section class="informacoes">
                <h2>Informações pessoais</h2>

                <div class="grade-informacoes">
                    <div class="item">
                        <span class="rotulo">Nome completo</span>
                        <strong><?= htmlspecialchars($usuarioLogado['nome']) ?></strong>
                    </div>

                    <div class="item">
                        <span class="rotulo">E-mail</span>
                        <strong><?= htmlspecialchars($usuarioLogado['email']) ?></strong>
                    </div>

                    <div class="item">
                        <span class="rotulo">Número da conta</span>
                        <strong>#<?= str_pad((string) $usuarioLogado['id'], 4, '0', STR_PAD_LEFT) ?></strong>
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