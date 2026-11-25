<section class="titulo">
    <div>
        <h1 class="saudacao">Olá, <?= htmlspecialchars($nomeUsuario) ?></h1>
        <p class="subtitulo">Resumo geral dos chamados da escola</p>
    </div>
    <a href="admin.php?pagina=chamados" class="botao">Gerenciar chamados</a>
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
        <span class="rotulo-concluido">Concluídos</span>
    </div>
    <div class="card">
        <strong class="numero"><?= $cancelados ?></strong>
        <span class="rotulo-cancelado">Cancelados</span>
    </div>
</section>

<section class="cartao-tabela">
    <div class="cabecalho-cartao">
        <h2>Chamados recentes</h2>
        <a href="admin.php?pagina=chamados" class="link-ver">Ver todos</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>Nº</th>
                <th>TÍTULO</th>
                <th>SOLICITANTE</th>
                <th>AMBIENTE</th>
                <th>TÉCNICO</th>
                <th>STATUS</th>
                <th>DATA</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($chamados) > 0): ?>
                <?php foreach (array_slice($chamados, 0, 8) as $chamado): ?>
                    <?php
                        $status = $chamado['status_chamado'];
                        $classeStatus = 'aberto';
                        if ($status === 'em andamento') $classeStatus = 'andamento';
                        elseif ($status === 'concluido') $classeStatus = 'concluido';
                        elseif ($status === 'cancelado') $classeStatus = 'cancelado';
                    ?>
                    <tr>
                        <td class="codigo">#<?= str_pad((string) $chamado['id'], 4, '0', STR_PAD_LEFT) ?></td>
                        <td class="col-titulo"><?= htmlspecialchars($chamado['titulo']) ?></td>
                        <td><?= htmlspecialchars($chamado['nome_cliente']) ?></td>
                        <td><?= htmlspecialchars($chamado['nome_ambiente']) ?></td>
                        <td><?= htmlspecialchars($chamado['nome_tecnico']) ?></td>
                        <td><span class="status <?= $classeStatus ?>"><?= htmlspecialchars(ucfirst($status)) ?></span></td>
                        <td><?= date('d/m/Y', strtotime($chamado['data_chamado'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="vazio">Nenhum chamado registrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>