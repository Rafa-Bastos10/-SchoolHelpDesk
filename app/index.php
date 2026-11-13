<?php
require_once __DIR__ . "/init.php";

// Para onde o usuário vai depois de entrar
$painel = "painel.php";

// Se já estiver logado, vai direto para o painel
if (isset($_SESSION["id_usuario"])) {
    header("Location: " . $painel);
    exit;
}

$erro = "";
$mensagem = "";
$email = "";

// Mensagem exibida quando o usuário acabou de se cadastrar
if (($_GET["cadastro"] ?? "") === "ok") {
    $mensagem = "Cadastro realizado com sucesso! Faça login para continuar.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";

    if ($email === "" || $senha === "") {
        $erro = "Preencha o e-mail e a senha.";
    } else {
        try {
            // Busca o usuário pelo e-mail
            $usuario = read($pdo, "usuario", "email = ?", [$email]);
            $senhaCorreta = false;

            if ($usuario) {
                if (password_verify($senha, $usuario["senha"])) {
                    // Senha salva com password_hash (cadastro pelo sistema)
                    $senhaCorreta = true;
                } elseif (hash_equals($usuario["senha"], $senha)) {
                    // Senha antiga salva em texto puro (ex.: usuários do seed.sql).
                    // Aceita o login e já converte para hash.
                    $senhaCorreta = true;
                    update($pdo, "usuario", ["senha" => password_hash($senha, PASSWORD_DEFAULT)], "id = ?", [$usuario["id"]]);
                }
            }

            if ($senhaCorreta) {
                session_regenerate_id(true);

                $_SESSION["id_usuario"] = $usuario["id"];
                $_SESSION["nome"] = $usuario["nome"];
                $_SESSION["email"] = $usuario["email"];
                $_SESSION["nivel"] = $usuario["nivel"];

                header("Location: " . $painel);
                exit;
            }

            $erro = "E-mail ou senha inválidos.";
        } catch (PDOException $e) {
            $erro = "Não foi possível fazer o login. Confira se o banco de dados está funcionando.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SchoolHelpDesk</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

    <div class="pagina">

        <div class="lado-esquerdo">
            <div class="logo">
                <img src="imagem/logo_schoolhelpdesk.png" alt="SchoolHelpDesk - Juntos por uma escola que funciona melhor!">
            </div>

            <div class="texto-lateral">
                <p class="subtitulo">SUPORTE ESCOLAR</p>
                <h1>Problema na escola? Abra um chamado em poucos cliques.</h1>
                <p>Manutenção predial e suporte técnico em um só lugar, com acompanhamento em tempo real.</p>

                <div class="vantagens">
                    <div class="item-vantagem">
                        <span>✓</span>
                        <p>Manutenção de salas, laboratórios e áreas comuns</p>
                    </div>

                    <div class="item-vantagem">
                        <span>✓</span>
                        <p>Suporte para computadores, projetores e rede</p>
                    </div>

                    <div class="item-vantagem">
                        <span>✓</span>
                        <p>Status, histórico e avaliação do atendimento</p>
                    </div>
                </div>
            </div>

            <p class="rodape-lateral">SchoolHelpDesk · Organização e suporte</p>
        </div>

        <div class="lado-direito">
            <div class="formulario-caixa">

                <div class="titulo-formulario">
                    <p class="subtitulo">ACESSO AO SISTEMA</p>
                    <h2>Entrar</h2>
                    <p>Use sua conta institucional para acessar o sistema.</p>
                </div>

                <?php if ($erro != "") { ?>
                    <div class="aviso erro" role="alert">
                        <?php echo htmlspecialchars($erro); ?>
                    </div>
                <?php } ?>

                <?php if ($mensagem != "") { ?>
                    <div class="aviso sucesso">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php } ?>

                <form action="index.php" method="POST">

                    <div class="campo">
                        <label for="email">E-mail institucional:</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($email); ?>"
                            required
                        >
                    </div>

                    <div class="campo">
                        <label for="senha">Senha:</label>
                        <div class="campo-senha">
                            <input
                                type="password"
                                id="senha"
                                name="senha"
                                required
                            >
                            <button type="button" class="botao-olho" data-alvo="senha" aria-label="Mostrar senha" title="Mostrar senha">
                                <svg class="olho-aberto" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="olho-fechado" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="botao-entrar">Entrar</button>

                </form>

                <div class="link-cadastro">
                    <p>Primeiro acesso?</p>
                    <a href="cadastro.php">Criar conta</a>
                </div>

                <p class="aviso-final">Aluno, professor, funcionário ou administrador: o sistema direciona você para a área correta.</p>

            </div>
        </div>

    </div>

    <script>
        // Mostra ou esconde a senha ao clicar no olhinho
        document.querySelectorAll(".botao-olho").forEach(function (botao) {
            botao.addEventListener("click", function () {
                var campo = document.getElementById(botao.dataset.alvo);
                var mostrar = campo.type === "password";

                campo.type = mostrar ? "text" : "password";
                botao.classList.toggle("ativo", mostrar);

                var texto = mostrar ? "Esconder senha" : "Mostrar senha";
                botao.setAttribute("aria-label", texto);
                botao.setAttribute("title", texto);
            });
        });
    </script>

</body>
</html>