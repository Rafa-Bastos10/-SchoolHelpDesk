<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchoolHelpDesk - Administrador</title>
    <link rel="stylesheet" href="painel.css">
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <aside class="menu">
        <div class="logo">
            <img src="img/logo.png" alt="SchoolHelpDesk">
        </div>

        <nav>
            <a href="admin.php?aba=inicio" class="<?= $aba === 'inicio' ? 'ativo' : '' ?>">Início</a>
            <a href="admin.php?aba=chamados" class="<?= $aba === 'chamados' ? 'ativo' : '' ?>">Todos os chamados</a>
            <a href="admin.php?aba=funcionarios" class="<?= $aba === 'funcionarios' ? 'ativo' : '' ?>">Funcionários</a>
            <p class="grupo">CONTA</p>
        </nav>

        <a href="admin.php?sair=1" class="sair">Sair</a>
    </aside>

    <main class="conteudo">
        <header class="topo">
            <form method="GET" action="admin.php">
                <input type="hidden" name="aba" value="chamados">
                <input type="text" name="busca" placeholder="Buscar chamado por número ou título" value="<?= h($busca) ?>">
            </form>
            <span><?= h($usuarioLogado['nome']) ?> (<?= $ehRoot ? 'Root' : 'Administrador' ?>)</span>
        </header>

        <?php if ($msg): ?>
            <div class="alerta <?= h($msg[0]) ?>"><?= h($msg[1]) ?></div>
        <?php endif; ?>

        <?php
        if ($aba === 'inicio') {
            require __DIR__ . '/inicio.php';
        } elseif ($aba === 'chamados') {
            require __DIR__ . '/chamados.php';
        } else {
            require __DIR__ . '/funcionarios.php';
        }
        ?>
    </main>
</body>
</html>
