<?php
// Layout do painel do técnico. Variáveis vindas do tecnico.php:
// $aba, $msg, $busca, $filtro, $cards, $total, $cont, $meusChamados, $lista, $historico, $perfil

require_once __DIR__ . "/../tecnico.php";

$nomeTecnico = $_SESSION['nome'] ?? 'Técnico';

$menuPrincipal = [
    'inicio'    => 'Início',
    'chamados'  => 'Todos os chamados',
    'historico' => 'Histórico de atendimento'
];

$tituloAba = [
    'inicio'    => 'Início',
    'chamados'  => 'Chamados',
    'historico' => 'Histórico de atendimento',
    'perfil'    => 'Meu perfil'
];

$abasStatus = ['' => 'Todos'] + [
    'aguardando'   => 'Aguardando',
    'em andamento' => 'Em andamento',
    'concluido'    => 'Concluídos',
    'cancelado'    => 'Cancelados'
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
        <?php foreach ($menuPrincipal as $chave => $rotulo): ?>
            <a href="tecnico.php?aba=<?= h($chave) ?>"
               class="<?= $aba === $chave ? 'ativo' : '' ?>">
                <?= h($rotulo) ?>
            </a>
        <?php endforeach; ?>

        <span class="grupo">Conta</span>

        <a href="tecnico.php?aba=perfil"
           class="<?= $aba === 'perfil' ? 'ativo' : '' ?>">
            Meu perfil
        </a>
    </nav>

    <a href="tecnico.php?sair=1" class="sair">Sair</a>
</aside>

<div class="area">

    <header class="topo">
        <div class="trilha">Técnico / <strong><?= h($tituloAba[$aba]) ?></strong></div>
        <div class="topo-usuario"><?= h($nomeTecnico) ?></div>
    </header>

    <main class="conteudo">

        <?php if ($msg): ?>
            <div class="alerta <?= h($msg[0]) ?>"><?= h($msg[1]) ?></div>
        <?php endif; ?>

        <?php if ($aba === 'inicio'): ?>

            <div class="cabecalho">
                <div>
                    <h1>Olá, <?= h($nomeTecnico) ?></h1>
                    <p class="subtitulo">Resumo geral dos chamados e prioridades da escola</p>
                </div>
                <a href="tecnico.php?aba=chamados" class="botao">Ver chamados</a>
            </div>

            <div class="cards">
                <?php foreach ($cards as [$qtd, $classe, $rotulo]): ?>
                    <div class="card card-<?= h($classe) ?>">
                        <strong><?= (int) $qtd ?></strong>
                        <span><?= h($rotulo) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="duas-colunas">
                <section class="caixa">
                    <div class="caixa-topo">
                        <div>
                            <h2>Meus chamados</h2>
                            <p class="subtitulo">Chamados atribuídos a você em aberto</p>
                        </div>
                        <span class="etiqueta"><?= count($meusChamados) ?> em aberto</span>
                    </div>

                    <?php if (!$meusChamados): ?>
                        <p class="vazio">Nenhum chamado atribuído a você no momento.</p>
                    <?php endif; ?>

                    <?php foreach ($meusChamados as $c): ?>
                        <div class="item-chamado">
                            <span class="item-id"><?= h(num($c['id'])) ?></span>
                            <span class="item-titulo"><?= h($c['titulo']) ?></span>
                            <span class="prioridade prioridade-<?= h($c['prioridade']) ?>">
                                <?= h(NOMES_PRIORIDADE[$c['prioridade']] ?? $c['prioridade']) ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </section>

                <section class="caixa">
                    <div class="caixa-topo">
                        <div>
                            <h2>Resumo</h2>
                            <p class="subtitulo">Chamados por status</p>
                        </div>
                    </div>

                    <div class="resumo-linha destaque">
                        <span>Total de chamados</span>
                        <strong><?= (int) $total ?></strong>
                    </div>
                    <?php foreach (NOMES_STATUS as $valor => $rotulo): ?>
                        <div class="resumo-linha">
                            <span><?= h($rotulo) ?></span>
                            <strong><?= (int) $cont[$valor] ?></strong>
                        </div>
                    <?php endforeach; ?>

                    <a href="tecnico.php?aba=chamados" class="link-ver">Ver todos os chamados →</a>
                </section>
            </div>

        <?php elseif ($aba === 'chamados'): ?>

            <div class="cabecalho">
                <div>
                    <h1>Todos os chamados</h1>
                    <p class="subtitulo"><?= count($lista) ?> chamados encontrados</p>
                </div>
            </div>

            <section class="painel">
                <div class="abas">
                    <?php foreach ($abasStatus as $valor => $rotulo): ?>
                        <?php
                        $url = 'tecnico.php?aba=chamados';
                        if ($valor !== '') { $url .= '&status=' . urlencode($valor); }
                        if ($busca !== '') { $url .= '&busca=' . urlencode($busca); }
                        ?>
                        <a href="<?= h($url) ?>" class="<?= $filtro === $valor ? 'ativa' : '' ?>"><?= h($rotulo) ?></a>
                    <?php endforeach; ?>
                </div>

                <form method="get" class="filtros">
                    <input type="hidden" name="aba" value="chamados">
                    <input type="hidden" name="status" value="<?= h($filtro) ?>">
                    <input type="text" name="busca" placeholder="Buscar por número (#0001) ou título"
                           value="<?= h($busca) ?>">
                    <button type="submit" class="botao">Buscar</button>
                </form>

                <div class="tabela-box">
                    <table>
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Título</th>
                                <th>Solicitante</th>
                                <th>Ambiente</th>
                                <th>Data</th>
                                <th>Técnico responsável</th>
                                <th>Status</th>
                                <th>Salvar</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!$lista): ?>
                            <tr><td colspan="8">Nenhum chamado encontrado.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($lista as $c): ?>
                            <tr>
                                <td class="col-id"><?= h(num($c['id'])) ?></td>
                                <td class="col-titulo"><?= h($c['titulo']) ?></td>
                                <td><?= h($c['solicitante']) ?></td>
                                <td><?= h($c['local']) ?></td>
                                <td><?= h(dataBr($c['data'])) ?></td>
                                <td><?= h($c['tecnico']) ?></td>
                                <td>
                                    <select name="status" class="status-select"
                                            form="status-<?= (int) $c['id'] ?>">
                                        <?= opcoes(NOMES_STATUS, $c['status']) ?>
                                    </select>
                                </td>
                                <td>
                                    <form method="post" id="status-<?= (int) $c['id'] ?>">
                                        <input type="hidden" name="acao" value="alterar_status">
                                        <input type="hidden" name="id" value="<?= (int) $c['id'] ?>">
                                        <button type="submit" class="botao-pequeno">Salvar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        <?php elseif ($aba === 'historico'): ?>

            <div class="cabecalho">
                <div>
                    <h1>Histórico de atendimento</h1>
                    <p class="subtitulo"><?= count($historico) ?> atendimentos concluídos</p>
                </div>
            </div>

            <section class="painel">
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
                                <td class="col-id"><?= h(num($c['id'])) ?></td>
                                <td class="descricao"><?= h($c['descricao']) ?></td>
                                <td><span class="selo selo-concluido"><?= h(NOMES_STATUS[$c['status']]) ?></span></td>
                                <td><?= h(dataBr($c['data'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        <?php elseif ($aba === 'perfil'): ?>

            <?php
            $nomePerfil = (string) ($perfil['nome'] ?? '');
            $inicialPerfil = function_exists('mb_substr')
                ? mb_strtoupper(mb_substr(trim($nomePerfil), 0, 1))
                : strtoupper(substr(trim($nomePerfil), 0, 1));
            $cargoPerfil = CARGOS[$perfil['nivel'] ?? 'tecnico'] ?? 'Técnico';
            ?>

            <div class="cabecalho">
                <div>
                    <h1>Meu perfil</h1>
                    <p class="subtitulo">Visualize as informações da sua conta.</p>
                </div>
            </div>

            <section class="painel perfil-topo">
                <div class="avatar"><?= h($inicialPerfil) ?></div>
                <div>
                    <h2><?= h($nomePerfil) ?></h2>
                    <p class="perfil-email"><?= h($perfil['email'] ?? '') ?></p>
                    <span class="etiqueta"><?= h($cargoPerfil) ?></span>
                </div>
            </section>

            <section class="painel">
                <h2>Informações pessoais</h2>
                <div class="grade-info">
                    <div class="info">
                        <span>Nome completo</span>
                        <strong><?= h($nomePerfil) ?></strong>
                    </div>
                    <div class="info">
                        <span>E-mail</span>
                        <strong><?= h($perfil['email'] ?? '') ?></strong>
                    </div>
                    <div class="info">
                        <span>Número da conta</span>
                        <strong><?= h(num($perfil['id'] ?? 0)) ?></strong>
                    </div>
                    <div class="info">
                        <span>Tipo de usuário</span>
                        <strong><?= h($cargoPerfil) ?></strong>
                    </div>
                </div>
            </section>

            <a href="tecnico.php?aba=inicio" class="botao">Voltar ao início</a>

        <?php endif; ?>

    </main>
</div>

</body>
</html>