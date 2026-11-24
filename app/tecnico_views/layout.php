<?php
// Layout do painel do técnico. Variáveis vindas do tecnico.php:
// $aba, $msg, $busca, $filtro, $filtroUrg, $cards, $abertos, $total,
// $porLocal, $lista, $historico, $perfil

require_once __DIR__ . "/../tecnico.php";

$nomeTecnico = $_SESSION['nome'] ?? 'Técnico';
$menu = [
    'inicio'    => 'Início',
    'chamados'  => 'Todos os chamados',
    'historico' => 'Histórico de atendimento'
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

<aside class="menu">
    <div class="logo">
        <img src="imagens/logo_schoolhelpdesk.png" alt="SchoolHelpDesk">
    </div>

    <nav>
        <?php foreach ($menu as $chave => $rotulo): ?>
            <a href="tecnico.php?aba=<?= h($chave) ?>" class="<?= $aba === $chave ? 'ativo' : '' ?>">
                <?= h($rotulo) ?>
            </a>
        <?php endforeach; ?>

        <p class="grupo">CONTA</p>
        <a href="tecnico.php?aba=perfil" class="<?= $aba === 'perfil' ? 'ativo' : '' ?>">Meu perfil</a>
    </nav>

    <a href="tecnico.php?sair=1" class="sair">Sair</a>
</aside>

<main class="conteudo">

    <header class="topo">
        <?php if ($aba === 'perfil'): ?>
            <span class="caminho">Conta / <strong>Meu Perfil</strong></span>
        <?php else: ?>
            <strong class="marca-topo">SchoolHelpdesk</strong>
        <?php endif; ?>
        <span id="nomeUsuario"><?= h($nomeTecnico) ?> (Técnico)</span>
    </header>

    <?php if ($msg): ?>
        <div class="alerta <?= h($msg[0]) ?>"><?= h($msg[1]) ?></div>
    <?php endif; ?>

    <?php if ($aba === 'inicio'): ?>

        <section class="titulo">
            <div>
                <h1 class="saudacao">Olá, <?= h($nomeTecnico) ?></h1>
                <p class="subtitulo">Veja como estão os chamados da escola</p>
            </div>
            <a href="tecnico.php?aba=chamados" class="botao">Gerenciar chamados</a>
        </section>

        <div class="cards">
            <?php foreach ($cards as [$qtd, $classe, $rotulo]): ?>
                <div class="card">
                    <strong><?= (int) $qtd ?></strong>
                    <span class="<?= h($classe) ?>"><?= h($rotulo) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="duas-colunas">
            <section class="secao">
                <h2>Resumo</h2>
                <div class="resumo-linha">
                    <span>Total de chamados</span>
                    <strong><?= (int) $total ?></strong>
                </div>
                <div class="resumo-linha">
                    <span>Chamados abertos</span>
                    <strong><?= (int) $abertos ?></strong>
                </div>
            </section>

            <section class="secao">
                <h2>Locais com mais chamados</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Local</th>
                            <th>Chamados</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!$porLocal): ?>
                        <tr><td colspan="2" class="vazio">Nenhum chamado registrado.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($porLocal as $linha): ?>
                        <tr>
                            <td><?= h($linha['local']) ?></td>
                            <td><?= (int) $linha['qtd'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>

    <?php elseif ($aba === 'chamados'): ?>

        <h1>Todos os chamados</h1>
        <p class="subtitulo">Veja a descrição, o local e altere o status dos chamados</p>

        <div class="tabela-box">
            <form method="get" class="filtros">
                <input type="hidden" name="aba" value="chamados">
                <input type="text" name="busca" value="<?= h($busca) ?>"
                       placeholder="Buscar por número, título, local ou solicitante">
                <select name="status">
                    <option value="">Todos os status</option>
                    <?= opcoes(NOMES_STATUS, $filtro) ?>
                </select>
                <select name="urgencia">
                    <option value="">Todas as urgências</option>
                    <?= opcoes(URGENCIAS, $filtroUrg) ?>
                </select>
                <button type="submit" class="botao">Filtrar</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Título</th>
                        <th>Descrição</th>
                        <th>Local</th>
                        <th>Solicitante</th>
                        <th>Data</th>
                        <th>Urgência</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!$lista): ?>
                    <tr><td colspan="8" class="vazio">Nenhum chamado encontrado.</td></tr>
                <?php endif; ?>

                <?php foreach ($lista as $c): ?>
                    <tr>
                        <td><?= h(num($c['id'])) ?></td>
                        <td><?= h($c['titulo']) ?></td>
                        <td class="descricao"><?= h($c['descricao']) ?></td>
                        <td><?= h($c['local']) ?></td>
                        <td><?= h($c['solicitante']) ?></td>
                        <td><?= h(dataBr($c['data'])) ?></td>
                        <td><?= urgencia($c['urgencia'] ?? '') ?></td>
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
        <p class="subtitulo">Chamados já concluídos</p>

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
                    <tr><td colspan="4" class="vazio">Nenhum atendimento concluído ainda.</td></tr>
                <?php endif; ?>

                <?php foreach ($historico as $c): ?>
                    <tr>
                        <td class="codigo"><?= h(num($c['id'])) ?></td>
                        <td class="descricao"><?= h($c['descricao']) ?></td>
                        <td><span class="status concluido"><?= h(NOMES_STATUS[$c['status']]) ?></span></td>
                        <td><?= h(dataBr($c['data'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($aba === 'perfil'): ?>

        <?php
            $nomePerfil  = $perfil['nome'];
            $cargoPerfil = CARGOS[$perfil['nivel']] ?? 'Técnico';
            $inicial     = mb_strtoupper(mb_substr(trim($nomePerfil), 0, 1, 'UTF-8'), 'UTF-8');
        ?>

        <section class="titulo">
            <div>
                <h1>Meu perfil</h1>
                <p class="subtitulo">Visualize as informações da sua conta.</p>
            </div>
        </section>

        <section class="perfil-principal">
            <div class="avatar"><?= h($inicial) ?></div>

            <div class="identificacao">
                <h2><?= h($nomePerfil) ?></h2>
                <p><?= h($perfil['email']) ?></p>
                <span class="nivel"><?= h($cargoPerfil) ?></span>
            </div>
        </section>

        <section class="informacoes">
            <h2>Informações pessoais</h2>

            <div class="grade-informacoes">
                <div class="item">
                    <span class="rotulo">Nome completo</span>
                    <strong><?= h($nomePerfil) ?></strong>
                </div>

                <div class="item">
                    <span class="rotulo">E-mail</span>
                    <strong><?= h($perfil['email']) ?></strong>
                </div>

                <div class="item">
                    <span class="rotulo">Número da conta</span>
                    <strong><?= h(num($perfil['id'])) ?></strong>
                </div>

                <div class="item">
                    <span class="rotulo">Tipo de usuário</span>
                    <strong><?= h($cargoPerfil) ?></strong>
                </div>
            </div>
        </section>

        <section class="informacoes">
            <h2>Alterar senha</h2>

            <form method="post" class="formulario formulario-senha">
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

        <a href="tecnico.php?aba=inicio" class="botao botao-voltar">Voltar ao painel</a>

    <?php endif; ?>

</main>

</body>
</html>