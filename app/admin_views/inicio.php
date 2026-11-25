<?php
// Agrupa a quantidade de chamados por ambiente/local
$chamadosPorLocal = [];
foreach ($chamados as$c) {
    $local =$c['nome_ambiente'] ?? 'Não informado';
    if (!isset($chamadosPorLocal[$local])) {
        $chamadosPorLocal[$local] = 0;
    }
    $chamadosPorLocal[$local]++;
}
arsort($chamadosPorLocal);

$totalChamados     = count($chamados);$totalFuncionarios = count($funcionarios);$semTecnico        = 0;
foreach ($chamados as$c) {
    if (empty($c['id_usuario'])) {$semTecnico++;
    }
}
?>

<section class="titulo">
    <div>
        <h1 class="saudacao">Olá, <?= htmlspecialchars($nomeUsuario) ?></h1>
        <p class="subtitulo">Resumo geral dos chamados e ambientes da escola</p>
    </div>
    <a href="admin.php?pagina=chamados" class="botao">Gerenciar chamados</a>
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

<section class="grade-inicio">
    <!-- Bloco esquerdo: Chamados por local -->
    <div class="cartao-painel">
        <div class="cabecalho-painel">
            <div>
                <h2>Chamados por local</h2>
                <p>Distribuição das solicitações por ambiente</p>
            </div>
            <span class="badge-total"><?= count($chamadosPorLocal) ?> locais</span>
        </div>

        <?php if (!empty($chamadosPorLocal)): ?>
            <div class="lista-locais">
                <?php foreach ($chamadosPorLocal as $local =>$qtd): 
                    $porcentagem =$totalChamados > 0 ? round(($qtd / $totalChamados) * 100) : 0;
                ?>
                    <div class="item-local">
                        <div class="info-local">
                            <span class="nome-local"><?= htmlspecialchars($local) ?></span>
                            <span class="qtd-local"><?= $qtd ?></span>
                        </div>
                        <div class="barra-fundo">
                            <div class="barra-preenchida" style="width: <?= $porcentagem ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="vazio">Nenhum chamado registrado ainda.</p>
        <?php endif; ?>
    </div>

    <!-- Bloco direito: Resumo -->
    <div class="cartao-painel">
        <div class="cabecalho-painel">
            <div>
                <h2>Resumo</h2>
                <p>Indicadores gerais do sistema</p>
            </div>
        </div>

        <div class="lista-resumo">
            <div class="item-resumo destaque-azul">
                <span>Total de chamados</span>
                <strong><?= $totalChamados ?></strong>
            </div>

            <div class="item-resumo">
                <span>Aguardando atribuição</span>
                <strong><?= $semTecnico ?></strong>
            </div>

            <div class="item-resumo">
                <span>Equipe cadastrada</span>
                <strong><?= $totalFuncionarios ?></strong>
            </div>

            <div class="item-resumo">
                <span>Taxa de conclusão</span>
                <strong><?= $totalChamados > 0 ? round(($concluidos / $totalChamados) * 100) : 0 ?>%</strong>
            </div>
        </div>

        <div class="rodape-resumo">
            <a href="admin.php?pagina=chamados" class="link-ver">Ver todos os chamados &rarr;</a>
        </div>
    </div>
</section>