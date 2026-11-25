<?php
$filtroStatus = $_GET['status'] ?? '';
$termoBusca   = trim($_GET['busca'] ?? '');

$chamadosFiltrados = array_filter($chamados, function ($c) use ($filtroStatus, $termoBusca) {
    if ($filtroStatus !== '' && $c['status_chamado'] !== $filtroStatus) {
        return false;
    }
    if ($termoBusca !== '') {
        $codigo = '#' . str_pad((string) $c['id'], 4, '0', STR_PAD_LEFT);
        if (stripos($codigo, $termoBusca) === false && stripos($c['titulo'], $termoBusca) === false) {
            return false;
        }
    }
    return true;
});
?>

<section class="titulo">
    <div>
        <h1 class="saudacao">Todos os chamados</h1>
        <p class="subtitulo"><?= count($chamadosFiltrados) ?> chamados encontrados</p>
    </div>
</section>

<section class="cartao-tabela">
    <div class="abas">
        <a href="admin.php?pagina=chamados" class="<?= $filtroStatus === '' ? 'ativa' : '' ?>">Todos</a>
        <a href="admin.php?pagina=chamados&status=aguardando" class="<?= $filtroStatus === 'aguardando' ? 'ativa' : '' ?>">Aguardando</a>
        <a href="admin.php?pagina=chamados&status=em andamento" class="<?= $filtroStatus === 'em andamento' ? 'ativa' : '' ?>">Em andamento</a>
        <a href="admin.php?pagina=chamados&status=concluido" class="<?= $filtroStatus === 'concluido' ? 'ativa' : '' ?>">Concluídos</a>
        <a href="admin.php?pagina=chamados&status=cancelado" class="<?= $filtroStatus === 'cancelado' ? 'ativa' : '' ?>">Cancelados</a>
    </div>

    <form method="GET" action="admin.php" class="filtros">
        <input type="hidden" name="pagina" value="chamados">
        <?php if ($filtroStatus !== ''): ?>
            <input type="hidden" name="status" value="<?= htmlspecialchars($filtroStatus) ?>">
        <?php endif; ?>
        <input type="text" name="busca" class="campo-busca" placeholder="Buscar por número (#0001) ou título" value="<?= htmlspecialchars($termoBusca) ?>">
        <button type="submit" class="botao">Buscar</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Nº</th>
                <th>TÍTULO</th>
                <th>SOLICITANTE</th>
                <th>AMBIENTE</th>
                <th>DATA</th>
                <th>TÉCNICO RESPONSÁVEL</th>
                <th>STATUS</th>
                <th>SALVAR</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($chamadosFiltrados) > 0): ?>
                <?php foreach ($chamadosFiltrados as $chamado): ?>
                    <?php
                        $classeStatus = 'aberto';
                        if ($chamado['status_chamado'] === 'em andamento') $classeStatus = 'andamento';
                        elseif ($chamado['status_chamado'] === 'concluido') $classeStatus = 'concluido';
                        elseif ($chamado['status_chamado'] === 'cancelado') $classeStatus = 'cancelado';
                    ?>
                    <tr>
                        <form method="POST" action="admin.php?pagina=chamados">
                            <input type="hidden" name="acao" value="atualizar_chamado">
                            <input type="hidden" name="id_chamado" value="<?= $chamado['id'] ?>">

                            <td class="codigo">#<?= str_pad((string) $chamado['id'], 4, '0', STR_PAD_LEFT) ?></td>
                            <td class="col-titulo"><?= htmlspecialchars($chamado['titulo']) ?></td>
                            <td><?= htmlspecialchars($chamado['nome_cliente']) ?></td>
                            <td><?= htmlspecialchars($chamado['nome_ambiente']) ?></td>
                            <td><?= date('d/m/Y', strtotime($chamado['data_chamado'])) ?></td>

                            <td>
                                <select name="id_usuario" class="select-tabela">
                                    <option value="">Não atribuído</option>
                                    <?php foreach ($funcionarios as $func): ?>
                                        <option value="<?= $func['id'] ?>" <?= (int) $chamado['id_usuario'] === (int) $func['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($func['nome']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>

                            <td>
                                <select name="status_chamado" class="select-tabela select-status <?= $classeStatus ?>" onchange="atualizarCorStatus(this)">
                                    <option value="aguardando" <?= $chamado['status_chamado'] === 'aguardando' ? 'selected' : '' ?>>Aguardando</option>
                                    <option value="em andamento" <?= $chamado['status_chamado'] === 'em andamento' ? 'selected' : '' ?>>Em andamento</option>
                                    <option value="concluido" <?= $chamado['status_chamado'] === 'concluido' ? 'selected' : '' ?>>Concluído</option>
                                    <option value="cancelado" <?= $chamado['status_chamado'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                            </td>

                            <td>
                                <button type="submit" class="botao-salvar">Salvar</button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="vazio">Nenhum chamado encontrado.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</section>

<script>
    function atualizarCorStatus(select) {
        select.classList.remove("aberto", "andamento", "concluido", "cancelado");

        if (select.value === "em andamento") {
            select.classList.add("andamento");
        } else if (select.value === "concluido") {
            select.classList.add("concluido");
        } else if (select.value === "cancelado") {
            select.classList.add("cancelado");
        } else {
            select.classList.add("aberto");
        }
    }
</script>