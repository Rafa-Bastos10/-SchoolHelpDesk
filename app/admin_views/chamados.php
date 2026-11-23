<section class="titulo">
    <div>
        <h1 class="saudacao">Todos os chamados</h1>
        <p class="subtitulo">Veja a descrição, o local e altere o status dos chamados</p>
    </div>
</section>

<form method="GET" action="admin.php" class="filtros">
    <input type="hidden" name="aba" value="chamados">
    <input type="text" name="busca" placeholder="Buscar por número, título, local ou solicitante" value="<?= h($busca) ?>">
    <select name="status">
        <option value="">Todos os status</option>
        <?= opcoes(NOMES_STATUS, $filtro) ?>
    </select>
    <button type="submit" class="botao">Filtrar</button>
</form>

<section class="tabela-box">
    <table>
        <thead>
            <tr class="cabecalho">
                <th>Nº</th><th>Título</th><th>Descrição</th><th>Local</th>
                <th>Solicitante</th><th>Data</th><th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista as $c): ?>
                <tr>
                    <td><?= num($c['id']) ?></td>
                    <td><?= h($c['titulo']) ?></td>
                    <td class="descricao"><?= h($c['descricao']) ?></td>
                    <td><?= h($c['local']) ?></td>
                    <td><?= h($c['solicitante']) ?></td>
                    <td><?= dataBr($c['data']) ?></td>
                    <td>
                        <form method="POST" action="admin.php" class="form-status">
                            <input type="hidden" name="acao" value="alterar_status">
                            <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                            <select name="status" class="status-select <?= h(CLASSE_STATUS[$c['status']] ?? 'aberto') ?>">
                                <?= opcoes(NOMES_STATUS, $c['status']) ?>
                            </select>
                            <button type="submit" class="botao-pequeno">Salvar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (!$lista): ?>
                <tr><td colspan="7">Nenhum chamado encontrado.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>
