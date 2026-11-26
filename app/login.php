<?php
session_start();
require_once "banco/database.php";
require_once "banco/crud.php";

$pdo = database();

// Para onde cada tipo de usuário vai depois de entrar
function destinoPorNivel($nivel)
{
    if ($nivel === "administrador") {
        return "/admin.php";
    }

    if ($nivel === "tecnico") {
        return "/tecnico.php";
    }

    // cliente
    return "/painel.php";
}


$erro = "";
$mensagem = "";
$email = "";


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
            // Procura em usuario
            $tabela = "usuario";
            $conta = read($pdo, "usuario", "email = ?", [$email]);

            //Se não achou procura em cliente
            if (!$conta) {
                $tabela = "cliente";
                $conta = read($pdo, "cliente", "email = ?", [$email]);
            }

            $senhaCorreta = false;

            if ($conta) {
                if (password_verify($senha, $conta["senha"])) {
                    // salva a senha
                    $senhaCorreta = true;
                } elseif (hash_equals($conta["senha"], $senha)) {
                    
                    $senhaCorreta = true;

                    update($pdo, $tabela, ["senha" => password_hash($senha, PASSWORD_DEFAULT)], "id = ?", [$conta["id"]]);
                }
            }

            if ($senhaCorreta) {
                session_regenerate_id(true);

             
                $nivel = ($tabela === "cliente") ? "cliente" : $conta["nivel"];

                $_SESSION["id_usuario"] = $conta["id"];
                $_SESSION["nome"] = $conta["nome"];
                $_SESSION["email"] = $conta["email"];
                $_SESSION["nivel"] = $nivel;

                if ($nivel === "cliente") {
                    $_SESSION["id_cliente"] = $conta["id"];
                }

                header("Location: " . destinoPorNivel($nivel));
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
                <img src="imagens/logo_schoolhelpdesk.png" alt="SchoolHelpDesk - Juntos por uma escola que funciona melhor!">
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

                <form action="login.php" method="POST">

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
                    <a href="/cadastro.php">Criar conta</a>
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

    <script>
    var caixa = document.querySelector(".formulario-caixa");
    var campos = caixa.querySelectorAll("input");

    // Brilho suave que acompanha o mouse na caixa
    caixa.addEventListener("mousemove", function (e) {
        var rect = caixa.getBoundingClientRect();
        var x = e.clientX - rect.left;
        var y = e.clientY - rect.top;
        caixa.style.setProperty("--mouse-x", x + "px");
        caixa.style.setProperty("--mouse-y", y + "px");
    });

    // Acende a borda azul e destaca o card ao clicar em um campo
    campos.forEach(function (input) {
        input.addEventListener("focus", function () {
            caixa.classList.add("digitando");
        });
        input.addEventListener("blur", function () {
            caixa.classList.remove("digitando");
        });
    });
</script>

</body>
</html>
