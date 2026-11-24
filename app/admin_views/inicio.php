<?php 
require_once __DIR__ . '/../admin.php';
?>

<link rel="stylesheet" href="css/admin.css">
<section class="titulo">
    <div>
        <h1 class="saudacao">Olá, <?= h($usuarioLogado['nome']) ?></h1>
        <p class="subtitulo">Veja como estão os chamados da escola</p>
    </div>
    <a href="admin.php?aba=chamados" class="botao">Gerenciar chamados</a>
</section>

<section class="cards">
    <?php foreach ($cards as [$numero, $classe, $rotulo]): ?>
        <div class="card">
            <strong class="numero"><?= $numero ?></strong>
            <span class="<?= $classe ?>"><?= $rotulo ?></span>
        </div>
    <?php endforeach; ?>
</section>

<section class="duas-colunas">
    <div class="tabela-box">
        <h2 class="tabela-titulo">Resumo</h2>
        <p class="resumo-linha"><span>Total de chamados</span><strong><?= $total ?></strong></p>
        <p class="resumo-linha"><span>Chamados abertos</span><strong><?= $abertos ?></strong></p>
        <p class="resumo-linha"><span>Funcionários cadastrados</span><strong><?= $qtdFuncionarios ?></strong></p>
    </div>

    <div class="tabela-box">
        <h2 class="tabela-titulo">Locais com mais chamados</h2>
        <table>
            <thead><tr class="cabecalho"><th>Local</th><th>Chamados</th></tr></thead>
            <tbody>
                <?php foreach ($porLocal as $local): ?>
                    <tr><td><?= h($local['local']) ?></td><td><?= (int) $local['qtd'] ?></td></tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
