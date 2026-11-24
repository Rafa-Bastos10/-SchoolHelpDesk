<?php
// Layout do painel do técnico. Variáveis vindas do tecnico.php:
// $aba, $msg, $busca, $filtro, $cards, $total, $porLocal, $lista, $perfil

require_once __DIR__ . "/../tecnico.php";

$nomeTecnico = $_SESSION['nome'] ?? 'Técnico';
$menu = [
    'inicio'   => 'Início',
    'chamados' => 'Chamados',
    'historico' => 'Histórico de atendimento',
    'perfil'   => 'Perfil'
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Painel do Técnico - SchoolHelpDesk</title>
    <link rel="stylesheet" href="css/tecnico.css">
</head>
<body>

<aside class="menu-lateral">
    <div class="logo">SchoolHelpDesk</div>
    <p class="saudacao">Olá, <?= h($nomeTecnico) ?></p>

    <nav>
        <?php foreach ($menu as $chave => $rotulo): ?>
            <a href="tecnico.php?aba=<?= h($chave) ?>" class="<?= $aba === $chave ? 'ativo' : '' ?>">
                <?= h($rotulo) ?>
            </a>
        <?php endforeach; ?>
        <a href="tecnico.php?sair=1">Sair</a>
    </nav>
</aside>

<main class="conteudo">

    <?php if ($msg): ?>
        <div class="alerta <?= h($msg[0]) ?>"><?= h($msg[1]) ?></div>
    <?php endif; ?>

    <?php if ($aba === 'inicio'): ?>

        <h1>Início</h1>

        <div class="cards">
            <?php foreach ($cards as [$qtd, $classe, $rotulo]): ?>
                <div class="card">
                    <strong><?= (int) $qtd ?></strong>
                    <span class="<?= h($classe) ?>"><?= h($rotulo) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="duas-colunas">
            <section class="caixa">
                <h2>Chamados por local</h2>
                <?php if (!$porLocal): ?>
                    <p>Nenhum chamado registrado.</p>
                <?php endif; ?>
                <?php foreach ($porLocal as $linha): ?>
                    <div class="resumo-linha">
                        <span><?= h($linha['local']) ?></span>
                        <strong><?= (int) $linha['qtd'] ?></strong>
                    </div>
                <?php endforeach; ?>
            </section>

            <section class="caixa">
                <h2>Resumo</h2>
                <div class="resumo-linha">
                    <span>Total de chamados</span>
                    <strong><?= (int) $total ?></strong>
                </div>
            </section>
        </div>

    <?php elseif ($aba === 'chamados'): ?>

        <h1>Chamados</h1>

        <form method="get" class="filtros">
            <input type="hidden" name="aba" value="chamados">
            <input type="text" name="busca" placeholder="Buscar por número, título, local ou solicitante"
                   value="<?= h($busca) ?>">
            <select name="status">
                <option value="">Todos os status</option>
                <?= opcoes(NOMES_STATUS, $filtro) ?>
            </select>
            <button type="submit" class="botao">Filtrar</button>
        </form>

        <div class="tabela-box">
            <table>
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th>Local</th>
                        <th>Solicitante</th>
                        <th>Data</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$lista): ?>
                    <tr><td colspan="7">Nenhum chamado encontrado.</td></tr>
                <?php endif; ?>

                <?php foreach ($lista as $c): ?>
                    <tr>
                        <td><?= h(num($c['id'])) ?></td>
                        <td><?= h($c['titulo']) ?></td>
                        <td class="descricao"><?= h($c['descricao']) ?></td>
                        <td><?= h($c['local']) ?></td>
                        <td><?= h($c['solicitante']) ?></td>
                        <td><?= h(dataBr($c['data'])) ?></td>
                        <td>
                            <form method="post" class="form-status">
                                <input type="hidden" name="acao" value="alterar_status">
                                <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                <select name="status" class="status-select">
                                    <?= opcoes(NOMES_STATUS, $c['status']) ?>
                                </select>
                                <button type="submit" class="botao-pequeno">Salvar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($aba === 'historico'): ?>

        <h1>Histórico de atendimento</h1>

        <div class="tabela-box">
            <table>
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descrição</th>
                        <th>Status</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$historico): ?>
                    <tr><td colspan="4">Nenhum atendimento concluído ainda.</td></tr>
                <?php endif; ?>

                <?php foreach ($historico as $c): ?>
                    <tr>
                        <td><?= h(num($c['id'])) ?></td>
                        <td class="descricao"><?= h($c['descricao']) ?></td>
                        <td><span class="selo selo-concluido"><?= h(NOMES_STATUS[$c['status']]) ?></span></td>
                        <td><?= h(dataBr($c['data'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($aba === 'perfil'): ?>

        <h1>Perfil</h1>

        <div class="duas-colunas">
            <section class="caixa">
                <h2>Meus dados</h2>
                <div class="resumo-linha">
                    <span>Nome</span>
                    <strong><?= h($perfil['nome']) ?></strong>
                </div>
                <div class="resumo-linha">
                    <span>E-mail</span>
                    <strong><?= h($perfil['email']) ?></strong>
                </div>
                <div class="resumo-linha">
                    <span>Cargo</span>
                    <strong><?= h(CARGOS[$perfil['nivel']] ?? $perfil['nivel']) ?></strong>
                </div>
            </section>

            <section class="caixa">
                <h2>Alterar senha</h2>
                <form method="post" class="formulario">
                    <input type="hidden" name="acao" value="alterar_senha">

                    <label for="senha_atual">Senha atual</label>
                    <input type="password" id="senha_atual" name="senha_atual" required>

                    <label for="senha_nova">Nova senha</label>
                    <input type="password" id="senha_nova" name="senha_nova" minlength="6" required>

                    <label for="senha_confirma">Confirmar nova senha</label>
                    <input type="password" id="senha_confirma" name="senha_confirma" minlength="6" required>

                    <button type="submit" class="botao">Salvar nova senha</button>
                </form>
            </section>
        </div>

    <?php endif; ?>

</main>

</body>
</html>