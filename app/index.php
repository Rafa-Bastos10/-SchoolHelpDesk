
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SchoolHelpDesk | Início</title>
<link rel="stylesheet" href="css/index.css">
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

<div class="banner">
    <img src="imagens/imagemfundo.jpg" alt="SchoolHelpDesk">
</div>

<!-- SERVIÇOS -->
<section class="servicos">

    <div class="card">
        <div class="icone"> <img src="imagens/engrenagens.png" alt=""></div>
        <h3>Manutenção predial</h3>
        <p>
            Solicite reparos e melhorias
            na estrutura da escola.
        </p>
    </div>

    <div class="card">
        <div class="icone"><img src="imagens/laptop_4501940.png" alt=""></div>
        <h3>Suporte técnico</h3>
        <p>
            Problemas com equipamentos
            e recursos tecnológicos.
        </p>
    </div>

    <div class="card">
        <div class="icone"><img src="imagens/ficha.png" alt=""></div>
        <h3>Outros serviços</h3>
        <p>
            Registre solicitações
            de diferentes áreas da escola.
        </p>
    </div>

    <div class="card">
        <div class="icone"><img src="imagens/relogiopng.webp" alt=""></div>
        <h3>Acompanhamento</h3>
        <p>
            Veja o status das suas
            solicitações em tempo real.
        </p>
    </div>

</section>

<!-- SOBRE O SISTEMA -->
<section class="sobre">

    <div class="sobre-texto">

        <div class="subtitulo">
            POR QUE USAR O SCHOOLHELPDESK?
        </div>

        <h2>
            Mais organização,<br>
            mais eficiência!
        </h2>

        <p>
            O SchoolHelpDesk centraliza todas as solicitações
            em um só lugar, facilitando a comunicação entre
            professores, funcionários e a equipe
            de manutenção.
        </p>

        <div class="beneficios">
            <span>Registro rápido e simples</span>
            <span>Acompanhamento em tempo real</span>
            <span>Histórico de solicitações</span>
            <span>Comunicação direta com a equipe</span>
        </div>

    </div>

    <div class="sala">
        <img src="imagens/saladeaula.png" alt="Sala de aula">
    </div>

</section>

<!-- RODAPÉ -->

<footer id="contatos">

    <div class="footer-conteudo">

        <!-- LOGO -->
        <div class="footer-logo">
            <img src="imagens/logo_schoolhelpdesk.png" alt="SchoolHelpDesk">
        </div>

        <!-- MENSAGEM DE AJUDA -->
        <div class="footer-ajuda">
            <strong>✉ Precisa de ajuda?</strong>
            <p>Fale com a nossa equipe!</p>
        </div>

        <!-- SETA -->
        <div class="footer-seta">
            →
        </div>

        <!-- CONTATO DA GERENTE -->
        <div class="footer-gerente">
            <strong>Gerente</strong>
            <p>Rafaela Bastos</p>
            <a href="mailto:rafaela.bastos@schoolhelpdesk.com">
                rafaela.bastos@schoolhelpdesk.com
            </a>
        </div>

        <!-- CONTATO DOS TÉCNICOS -->
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

    <!-- DIREITOS AUTORAIS -->
    <div class="direitos">
        © 2026 SchoolHelpDesk. Todos os direitos reservados.
    </div>

</footer>
<script>
document.querySelectorAll('a[href="#contatos"]').forEach(link => {

    link.addEventListener('click', function() {

        const footer = document.getElementById('contatos');

        footer.classList.remove('destaque-footer');

        void footer.offsetWidth;

        footer.classList.add('destaque-footer');

        setTimeout(() => {
            footer.classList.remove('destaque-footer');
        }, 2000);

    });

});
</script>

</body>
</html>
