<?php
session_start();

require_once "conexao.php";
require_once "crud.php";

$erro = "";
$mensagem = "";
$nome = "";
$email = "";


// ?? Ele serv para ver se existe a variavel e ver se não é uma variável vazia (null)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST["nome"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $confirmarSenha = $_POST["confirmar_senha"] ?? "";

    if ($nome == "" || $email == "" || $senha == "" || $confirmarSenha == "") {
        $erro = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Digite um e-mail válido.";
    } elseif (strlen($senha) < 6) {
        $erro = "A senha precisa ter pelo menos 6 caracteres.";
    } elseif ($senha != $confirmarSenha) {
        $erro = "A confirmação da senha está diferente.";
    } else {
        try {
            $usuario = read($pdo, "usuario", "email = ?", [$email]);

            if ($usuario) {
                $erro = "Esse e-mail já está cadastrado.";
            } else {
                create($pdo, "usuario", [
                    "nome" => $nome,
                    "email" => $email,
                    "senha" => password_hash($senha, PASSWORD_DEFAULT)
                ]);

                // Após cadastrar, volta para a tela de login.
                header("Location: ../login/login.php?cadastro=ok");
                exit;
            }
        } catch (PDOException $e) {
            $erro = "Não foi possível fazer o cadastro. Confira se o banco de dados está funcionando.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - SchoolHelpDesk</title>
    <link rel="stylesheet" href="cadastro.css">
</head>
<body>

    <div class="pagina">

        <div class="lado-esquerdo">
            <div class="logo">
                <img src="../logo_schoolhelpdesk.png" alt="SchoolHelpDesk - Juntos por uma escola que funciona melhor!">
            </div>

            <div class="texto-lateral">
                <p class="subtitulo">SUPORTE ESCOLAR</p>
                <h1>Mais praticidade para cuidar da sua escola.</h1>
                <p>Faça seu cadastro para registrar solicitações e acompanhar os atendimentos em um só lugar.</p>

                <div class="vantagens">
                    <div class="item-vantagem">
                        <span>✓</span>
                        <p>Registre seus chamados</p>
                    </div>

                    <div class="item-vantagem">
                        <span>✓</span>
                        <p>Acompanhe cada atendimento</p>
                    </div>

                    <div class="item-vantagem">
                        <span>✓</span>
                        <p>Consulte suas solicitações</p>
                    </div>
                </div>
            </div>

            <p class="rodape-lateral">SchoolHelpDesk · Organização e suporte</p>
        </div>

        <div class="lado-direito">
            <div class="formulario-caixa">

                <div class="titulo-formulario">
                    <p class="subtitulo">PRIMEIRO ACESSO</p>
                    <h2>Criar conta</h2>
                    <p>Preencha as informações abaixo para se cadastrar.</p>
                </div>

                <?php if ($erro != "") { ?>
                    <div class="aviso erro">
                        <?php echo htmlspecialchars($erro); ?>
                    </div>
                <?php } ?>

                <?php if ($mensagem != "") { ?>
                    <div class="aviso sucesso">
                        <?php echo htmlspecialchars($mensagem); ?>
                    </div>
                <?php } ?>

                <form method="POST">

                    <div class="campo">
                        <label for="nome">Nome completo</label>
                        <input
                            type="text" id="nome"
                            name="nome"
                            placeholder="Digite seu nome"
                            value="<?php echo htmlspecialchars($nome); ?>"
                            maxlength="100" required
                        >
                    </div>

                    <div class="campo">
                        <label for="email">E-mail</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Digite seu e-mail"
                            value="<?php echo htmlspecialchars($email); ?>"
                            maxlength="100"
                            required
                        >
                    </div>

                    <div class="campo">
                        <label for="senha">Senha</label>
                        <div class="campo-senha">
                            <input
                                type="password"
                                id="senha"
                                name="senha"
                                placeholder="Crie uma senha"
                                minlength="6"
                                required
                            >
                            <button type="button" class="botao-olho" data-alvo="senha" aria-label="Mostrar senha" title="Mostrar senha">
                                <svg class="olho-aberto" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="olho-fechado" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                        <small>A senha deve ter pelo menos 6 caracteres.</small>
                    </div>

                    <div class="campo">
                        <label for="confirmar_senha">Confirmação de senha</label>
                        <div class="campo-senha">
                            <input
                                type="password"
                                id="confirmar_senha"
                                name="confirmar_senha"
                                placeholder="Digite a senha novamente"
                                minlength="6"
                                required
                            >
                            <button type="button" class="botao-olho" data-alvo="confirmar_senha" aria-label="Mostrar senha" title="Mostrar senha">
                                <svg class="olho-aberto" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="olho-fechado" viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="botao-cadastrar">Cadastrar</button>


                </form>

                <div class="voltar-login">
                    <p>Já tem uma conta?</p>
                    <a href="../login/login.php">Voltar para a tela de login</a>
                </div>

                <p class="aviso-final">Seus dados serão usados somente para acessar o sistema e acompanhar seus chamados.</p>

            </div>
        </div>

    </div>

    <script>

        // Mostra ou esconde a senha ao clicar no olhinho
        document.querySelectorAll(".botao-olho").forEach(function (botao) {
            botao.addEventListener("click", function () {
                var campo = document.getElementById(botao.dataset.alvo);w
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
