<?php
require_once __DIR__ . "/init.php";

if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit;
}

if (($_SESSION["nivel"] ?? "") !== "administrador") {
    header("Location: painel.php");
    exit;
}

$usuarioLogado = read($pdo, "usuario", "id = ?", [$_SESSION["id_usuario"]]);
$nomeUsuario   = $usuarioLogado["nome"] ?? ($_SESSION["nome"] ?? "Administrador");
$_SESSION["nome"] = $nomeUsuario;

$pagina = $_GET["pagina"] ?? "inicio";
$paginasPermitidas = ["inicio", "chamados", "funcionarios", "perfil"];
if (!in_array($pagina, $paginasPermitidas, true)) {
    $pagina = "inicio";
}

$mensagem = "";
$erro     = "";

// Carrega listas usadas nas telas
$ambientes = readAll($pdo, "ambiente");
$mapaAmbientes = [];
foreach ($ambientes as $amb) {
    $mapaAmbientes[$amb["id"]] = $amb["nome"];
}

$usuarios = readAll($pdo, "usuario");
$mapaUsuarios = [];
$funcionarios = [];
foreach ($usuarios as $u) {
    $mapaUsuarios[$u["id"]] = $u["nome"];
    if (in_array($u["nivel"], ["tecnico", "administrador"], true)) {
        $funcionarios[] = $u;
    }
}

$clientes = readAll($pdo, "cliente");
$mapaClientes = [];
foreach ($clientes as $cli) {
    $mapaClientes[$cli["id"]] = $cli["nome"];
}

// Ações via POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $acao = $_POST["acao"] ?? "";

    if ($acao === "cadastrar_funcionario") {
        $nome  = trim($_POST["nome"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $senha = $_POST["senha"] ?? "";
        $nivel = $_POST["nivel"] ?? "tecnico";

        if (!in_array($nivel, ["tecnico", "administrador"], true)) {
            $nivel = "tecnico";
        }

        if ($nome === "" || $email === "" || $senha === "") {
            $erro = "Preencha todos os campos do funcionário.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = "Digite um e-mail válido.";
        } elseif (strlen($senha) < 6) {
            $erro = "A senha deve ter pelo menos 6 caracteres.";
        } elseif (read($pdo, "usuario", "email = ?", [$email])) {
            $erro = "Já existe um usuário com esse e-mail.";
        } else {
            create($pdo, "usuario", [
                "nome"  => $nome,
                "email" => $email,
                "senha" => password_hash($senha, PASSWORD_DEFAULT),
                "nivel" => $nivel
            ]);
            header("Location: admin.php?pagina=funcionarios&ok=1");
            exit;
        }
        $pagina = "funcionarios";
    }

    if ($acao === "excluir_funcionario") {
        $idFuncionario = (int) ($_POST["id_funcionario"] ?? 0);

        if ($idFuncionario === (int) $_SESSION["id_usuario"]) {
            $erro = "Você não pode excluir sua própria conta.";
        } elseif ($idFuncionario > 0) {
            try {
                delete($pdo, "usuario", "id = ?", [$idFuncionario]);
                header("Location: admin.php?pagina=funcionarios&excluido=1");
                exit;
            } catch (PDOException $e) {
                $erro = "Não é possível excluir este funcionário pois ele possui chamados vinculados.";
            }
        }
        $pagina = "funcionarios";
    }

    if ($acao === "atualizar_chamado") {
        $idChamado  = (int) ($_POST["id_chamado"] ?? 0);
        $novoStatus = $_POST["status_chamado"] ?? "";
        $idTecnico  = ($_POST["id_usuario"] ?? "") !== "" ? (int) $_POST["id_usuario"] : null;

        if ($idChamado > 0) {
            $dadosAtualizar = ["id_usuario" => $idTecnico];
            if ($novoStatus !== "") {
                $dadosAtualizar["status_chamado"] = $novoStatus;
            }
            update($pdo, "chamado", $dadosAtualizar, "id = ?", [$idChamado]);
            header("Location: admin.php?pagina=" . urlencode($pagina) . "&atualizado=1");
            exit;
        }
    }
}

if (isset($_GET["ok"])) {
    $mensagem = "Funcionário cadastrado com sucesso!";
}
if (isset($_GET["excluido"])) {
    $mensagem = "Cadastro excluído com sucesso!";
}
if (isset($_GET["atualizado"])) {
    $mensagem = "Chamado atualizado com sucesso!";
}

$chamados = readAll($pdo, "chamado");
usort($chamados, function ($a, $b) {
    return strtotime($b["data_chamado"]) <=> strtotime($a["data_chamado"]);
});

$abertos = 0;
$andamento = 0;
$concluidos = 0;
$cancelados = 0;

foreach ($chamados as &$chamado) {
    $chamado["nome_ambiente"] = $mapaAmbientes[$chamado["id_ambiente"]] ?? "Não informado";
    $chamado["nome_tecnico"]  = $mapaUsuarios[$chamado["id_usuario"]] ?? "Não atribuído";
    $chamado["nome_cliente"]  = $mapaClientes[$chamado["id_cliente"]] ?? "Não informado";

    if ($chamado["status_chamado"] === "aguardando") {
        $abertos++;
    } elseif ($chamado["status_chamado"] === "em andamento") {
        $abertos++;
        $andamento++;
    } elseif ($chamado["status_chamado"] === "concluido") {
        $concluidos++;
    } elseif ($chamado["status_chamado"] === "cancelado") {
        $cancelados++;
    }
}
unset($chamado);

require __DIR__ . "/admin_views/layout.php";