<?php
require_once __DIR__ . "/init.php";

// Só entra quem fez login
if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit;
}

$erro = "";
$mensagem = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["titulo"])) {
    $titulo = trim($_POST["titulo"] ?? "");
    $descricao = trim($_POST["descricao"] ?? "");
    $categoria = trim($_POST["categoria"] ?? "");
    $prioridade = trim($_POST["prioridade"] ?? "media");
    $nomeAmbiente = trim($_POST["ambiente"] ?? "");
    $bloco = trim($_POST["bloco"] ?? "");

    if ($titulo === "" || $descricao === "" || $nomeAmbiente === "") {
        $erro = "Preencha título, ambiente e descrição.";
    } else {
        try {
            // Procura o ambiente pelo nome para pegar o id_ambiente (chave estrangeira)
            $ambienteRegistro = read($pdo, "ambiente", "nome = ?", [$nomeAmbiente]);

            if ($ambienteRegistro) {
                $idAmbiente = $ambienteRegistro["id"];
            } else {
                // Se o ambiente ainda não existir no banco, cria e pega o ID gerado
                create($pdo, "ambiente", [
                    "nome" => $nomeAmbiente,
                    "bloco" => $bloco
                ]);
                $idAmbiente = (int) $pdo->lastInsertId();
            }

            // Insere o chamado usando a função create do crud.php
            create($pdo, "chamado", [
                "titulo" => $titulo,
                "descricao" => $descricao,
                "categoria" => $categoria,
                "data_chamado" => date("Y-m-d"),
                "prioridade" => $prioridade,
                "status_chamado" => "aguardando",
                "id_usuario" => $_SESSION["id_usuario"],
                "id_cliente" => $_SESSION["id_cliente"] ?? 1,
                "id_ambiente" => $idAmbiente
            ]);

            header("Location: painel.php");
            exit;
        } catch (PDOException $e) {
            $erro = "Não foi possível cadastrar o chamado. Verifique os dados e o banco.";
        }
    }
}

$nomeUsuario = $_SESSION["nome"] ?? "Usuário";
$nivelUsuario = $_SESSION["nivel"] ?? "";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Abrir Chamado</title>
  <link rel="stylesheet" href="css/abrir_chamado.css">
</head>
<body>
  <div class="layout">
    <div class="menu">
      <h2>SchoolHelpDesk</h2>
      <a href="painel.php">Início</a>
      <a href="abrir_chamado.php" class="ativo">
        Abrir chamado
      </a>
      <a href="chamado.php">
        Meus chamados
      </a>
      <a href="historico.php">
        Histórico
      </a>
      <h4>CONTA</h4>
      <a href="meu_perfil.php">Meu perfil</a>
      <a href="logout.php">Sair</a>
    </div>
    <div class="conteudo">
      <div class="topo">
        <p>Início / Abrir chamado</p>
      <div class="usuario">
        <span>
          <?php echo htmlspecialchars(mb_strtoupper(mb_substr($nomeUsuario, 0, 2, "UTF-8"), "UTF-8"), ENT_QUOTES, "UTF-8"); ?>
        </span>
        <div>
          <strong>
            <?php echo htmlspecialchars($nomeUsuario, ENT_QUOTES, "UTF-8"); ?>
          </strong>
          <small>
            <?php echo htmlspecialchars($nivelUsuario, ENT_QUOTES, "UTF-8"); ?>
          </small>
        </div>
      </div>
      </div>
      <div class="pagina">
        <h1>Abrir novo chamado</h1>
        <p class="subtitulo">
          Descreva o problema para agilizar o atendimento
        </p>

        <?php if ($erro !== ""): ?>
          <p style="color: #b91c1c; margin-bottom: 1rem;">
            <?php echo htmlspecialchars($erro, ENT_QUOTES, "UTF-8"); ?>
          </p>
        <?php endif; ?>

        <div class="formulario">
          <form action="abrir_chamado.php" method="POST">
            <label>Tipo de solicitação</label>
            <div class="tipos">
              <label class="tipo">
                <input type="radio"
                  name="tipo"
                  value="Manutenção"
                  checked>
                <div>
                  <strong>Manutenção</strong>
                  <small>
                    Elétrica, hidráulica, mobiliário
                  </small>
                </div>
              </label>
              <label class="tipo">
                <input type="radio"
                  name="tipo"
                  value="Suporte técnico">
                <div>
                  <strong>Suporte técnico</strong>
                  <small>
                    Computadores, rede, sistemas
                  </small>
                </div>
              </label>
            </div>
            <label for="titulo">Título</label>
            <input type="text"
              id="titulo"
              name="titulo"
              placeholder="Ex: Ar-condicionado com vazamento"
              required>
            <div class="linha">
              <div>
                <label for="categoria">Categoria</label>
                <select name="categoria"
                    id="categoria"
                    required>
                  <option value="Hardware">Hardware</option>
                  <option value="Software">Software</option>
                  <option value="Rede">Rede</option>
                  <option value="Climatização">
                    Climatização
                  </option>
                  <option value="Infraestrutura">
                    Infraestrutura
                  </option>
                </select>
              </div>
            </div>
            <div class="linha">
              <div class="linha">
              <div>
                <label for="bloco">Bloco</label>
                <input type="text"
                  id="bloco"
                  name="bloco"
                  placeholder="Digite o bloco"
                  required>
              </div>
              <div>
                <label for="ambiente">Ambiente</label>
                <input type="text"
                  id="ambiente"
                  name="ambiente"
                  placeholder="Digite o ambiente"
                  required>
              </div>
            </div>
            </div>
            <label for="descricao">
              Descrição do problema
            </label>
            <textarea id="descricao"
                name="descricao"
                placeholder="Descreva o problema..."
                required></textarea>
            <label>Prioridade sugerida</label>
            <div class="prioridades">
              <label>
                <input type="radio"
                  name="prioridade"
                  value="baixa">
                Baixa
              </label>
              <label>
                <input type="radio"
                  name="prioridade"
                  value="media"
                  checked>
                Média
              </label>
              <label class="selecionada">
                <input type="radio"
                  name="prioridade"
                  value="alta">
                Alta
              </label>
              <label>
                <input type="radio"
                  name="prioridade"
                  value="urgente">
                Urgente
              </label>
            </div>
            <div class="botoes">
              <a href="painel.php"
              class="cancelar">
                Cancelar
              </a>
              <button type="submit">
                Enviar chamado
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</body>
</html>