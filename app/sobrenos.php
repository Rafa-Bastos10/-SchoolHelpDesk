<?php
require_once "init.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SchoolHelpDesk | Sobre nós</title>
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="css/sobrenos.css">
</head>

<body>

<!-- CABEÇALHO -->
<header>
    <div class="logo">
        <img src="imagens/logo_schoolhelpdesk.png" alt="logo">
    </div>

    <nav>
        <a href="index.php">Início</a>
        <a href="sobrenos.php">Sobre</a>
        <a href="#contatos">Contato</a>
    </nav>

    <a href="login.php" class="acessar">♙ Acessar</a>
</header>

<!-- BANNER PRINCIPAL -->
<section class="sobre-banner">
    <div class="banner-texto">
        <span>SOBRE O SCHOOLHELPDESK</span>
        <h1>Tecnologia e suporte para facilitar o dia a dia escolar.</h1>
        <p>
            Uma plataforma criada para ajudar alunos e funcionários
            a resolver problemas tecnológicos, eletrônicos e de
            infraestrutura de forma rápida, organizada e eficiente.
        </p>
    </div>
    <div class="banner-icone">💻</div>
</section>

<!-- QUEM SOMOS -->
<section class="quem-somos">
    <div class="imagem-sobre">
        <div class="imagem-conteudo">
            <span>💻</span>
            <h2>Conectando pessoas e soluções</h2>
            <p>Suporte tecnológico escolar</p>
        </div>
    </div>

    <div class="texto-sobre">
        <h2>Quem somos?</h2>
        <p>
            O SchoolHelpDesk é uma plataforma desenvolvida com o
            objetivo de facilitar a comunicação entre alunos,
            professores e funcionários da escola com a equipe
            responsável pelo suporte técnico.
        </p>
        <p>
            Nosso sistema permite registrar chamados relacionados
            a problemas eletrônicos, tecnológicos e outros
            imprevistos que possam atrapalhar as atividades
            escolares, tornando o atendimento mais rápido,
            organizado e transparente.
        </p>
        <p>
            Acreditamos que a tecnologia deve ser uma aliada
            da educação. Por isso, buscamos oferecer soluções
            que contribuam para um ambiente escolar mais
            eficiente e conectado.
        </p>
    </div>
</section>

<!-- MISSÃO, VISÃO E VALORES -->
<section class="objetivos">
    <div class="objetivo-card">
        <div class="objetivo-icone">🎯</div>
        <h3>Nossa missão</h3>
        <p>
            Facilitar a resolução de problemas escolares,
            oferecendo suporte acessível e eficiente
            para alunos e funcionários.
        </p>
    </div>

    <div class="objetivo-card">
        <div class="objetivo-icone">👁️</div>
        <h3>Nossa visão</h3>
        <p>
            Ser uma plataforma de referência no suporte
            escolar, contribuindo para uma escola
            cada vez mais tecnológica e organizada.
        </p>
    </div>

    <div class="objetivo-card">
        <div class="objetivo-icone">⭐</div>
        <h3>Nossos valores</h3>
        <p>
            Compromisso, responsabilidade, agilidade,
            organização, transparência e respeito
            com toda a comunidade escolar.
        </p>
    </div>
</section>

<!-- NOSSO OBJETIVO -->
<section class="nosso-objetivo">
    <h2>Por que criamos o SchoolHelpDesk?</h2>
    <p>
        Sabemos que problemas com computadores, equipamentos
        eletrônicos, internet e outros recursos tecnológicos
        podem prejudicar as atividades dentro da escola.
    </p>
    <p>
        Pensando nisso, desenvolvemos o SchoolHelpDesk para
        centralizar as solicitações de suporte em um único lugar.
        Assim, os usuários podem registrar seus problemas,
        acompanhar o andamento dos chamados e ter mais facilidade
        para encontrar uma solução.
    </p>
</section>

<!-- RODAPÉ (igual ao da página inicial) -->
<footer id="contatos">

    <div class="footer-conteudo">

        <div class="footer-logo">
            <img src="imagens/logo_schoolhelpdesk.png" alt="SchoolHelpDesk">
        </div>

        <div class="footer-ajuda">
            <strong>✉ Precisa de ajuda?</strong>
            <p>Fale com a nossa equipe!</p>
        </div>

        <div class="footer-seta">→</div>

        <div class="footer-gerente">
            <strong>Gerente</strong>
            <p>Rafaela Bastos</p>
            <a href="mailto:rafaela.bastos@schoolhelpdesk.com">
                rafaela.bastos@schoolhelpdesk.com
            </a>
        </div>

        <div class="footer-tecnicos">
            <strong>Técnicos</strong>

            <p>Fernando Ribeiro</p>
            <a href="mailto:fernando.ribeiro@schoolhelpdesk.com">
                fernando.ribeiro@schoolhelpdesk.com
            </a>

            <p>Pedro Henrique</p>
            <a href="mailto:pedro.henrique@schoolhelpdesk.com">
                pedro.henrique@schoolhelpdesk.com
            </a>

            <p>Davi Guedes</p>
            <a href="mailto:davi.guedes@schoolhelpdesk.com">
                davi.guedes@schoolhelpdesk.com
            </a>
        </div>

    </div>

    <div class="direitos">
        © 2026 SchoolHelpDesk. Todos os direitos reservados.
    </div>

</footer>

<script>
document.querySelectorAll('a[href="#contatos"]').forEach(link => {
    link.addEventListener('click', function () {
        const footer = document.getElementById('contatos');
        footer.classList.remove('destaque-footer');
        void footer.offsetWidth;
        footer.classList.add('destaque-footer');
        setTimeout(() => footer.classList.remove('destaque-footer'), 2000);
    });
});
</script>

</body>
</html>