<?php
require_once __DIR__ . "/init.php";

// Só entra quem fez login
if (!isset($_SESSION["id_usuario"])) {
    header("Location: index.php");
    exit;
}

// Nome do próprio arquivo atual (funciona seja chamado.php ou chamados.php)
$paginaAtual = basename($_SERVER["PHP_SELF"]);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_var($_POST["id"] ?? null, FILTER_VALIDATE_INT);

    if ($id) {
        if (isset($_POST["cancelar"])) {
            update($pdo, "chamado", ["status_chamado" => "cancelado"], "id = ?", [$id]);
        }

        if (isset($_POST["excluir"])) {
            delete($pdo, "chamado", "id = ?", [$id]);
        }
    }

    header("Location: " . $paginaAtual);
    exit;
}

$statusFiltro = trim($_GET["status"] ?? "");

if ($statusFiltro === "abertos") {
    $chamados = readAll($pdo, "chamado", "status_chamado IN ('aguardando', 'em andamento')");
} elseif ($statusFiltro !== "") {
    $chamados = readAll($pdo, "chamado", "status_chamado = ?", [$statusFiltro]);
} else {
    $chamados = readAll($pdo, "chamado");
}

$nomeUsuario = $_SESSION["nome"] ?? "Usuário";
$nivelUsuario = $_SESSION["nivel"] ?? "";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <title>Meus Chamados</title>
  <link rel="stylesheet" href="css/chamado.css">
</head>

<body>
  <div class="layout">
    <div class="menu">
      <h2>SchoolHelpDesk</h2>
      <a href="painel.php">Início</a>
      <a href="abrir_chamado.php">
        Abrir chamado
      </a>
      <a href="<?= htmlspecialchars($paginaAtual) ?>" class="ativo">
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
        <p>Início / Meus chamados</p>
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
        <div class="titulo-lista">
          <div>
            <h1>Meus chamados</h1>
            <p class="subtitulo">
              Consulte os chamados registrados por você
            </p>
          </div>
          <a href="abrir_chamado.php" class="novo">
            + Abrir novo chamado
          </a>
        </div>
        <div class="abas">
          <a href="<?= htmlspecialchars($paginaAtual) ?>" class="aba <?= $statusFiltro === '' ? 'ativa' : '' ?>">
            Todos
          </a>
          <a href="<?= htmlspecialchars($paginaAtual) ?>?status=abertos" class="aba <?= $statusFiltro === 'abertos' ? 'ativa' : '' ?>">
            Abertos
          </a>
          <a href="<?= htmlspecialchars($paginaAtual) ?>?status=em+andamento" class="aba <?= $statusFiltro === 'em andamento' ? 'ativa' : '' ?>">
            Em andamento
          </a>
          <a href="<?= htmlspecialchars($paginaAtual) ?>?status=aguardando" class="aba <?= $statusFiltro === 'aguardando' ? 'ativa' : '' ?>">
            Aguardando
          </a>
          <a href="<?= htmlspecialchars($paginaAtual) ?>?status=concluido" class="aba <?= $statusFiltro === 'concluido' ? 'ativa' : '' ?>">
            Concluídos
          </a>
        </div>
        <div class="filtros">
          <input type="text" placeholder="Buscar por número, título ou ambiente">
          <select>
            <option>Tipo: Todos</option>
            <option>Manutenção</option>
            <option>Suporte técnico</option>
          </select>
          <select>
            <option>Categoria</option>
            <option>Hardware</option>
            <option>Software</option>
            <option>Rede</option>
          </select>
          <select>
            <option>Últimos 90 dias</option>
            <option>Últimos 30 dias</option>
            <option>Últimos 7 dias</option>
          </select>
        </div>
        <div class="lista-chamados">

          <?php if (count($chamados) > 0) { ?>
            <?php foreach ($chamados as $chamado) { ?>
              <div class="chamado">
                <h3><?php echo htmlspecialchars($chamado["titulo"], ENT_QUOTES, "UTF-8"); ?></h3>
                <p>
                  Data: <?php echo htmlspecialchars($chamado["data_chamado"], ENT_QUOTES, "UTF-8"); ?>
                </p>
                <p>
                  Prioridade: <?php echo htmlspecialchars($chamado["prioridade"], ENT_QUOTES, "UTF-8"); ?>
                </p>
                <p>
                  Status: <?php echo htmlspecialchars($chamado["status_chamado"], ENT_QUOTES, "UTF-8"); ?>
                </p>
                <form method="post" style="display:inline"
                  onsubmit="return confirm('Deseja cancelar este chamado?')">
                  <input type="hidden" name="id" value="<?php echo (int)$chamado["id"]; ?>">
                  <button type="submit" name="cancelar">Cancelar</button>
                </form>

                <form method="post" style="display:inline"
                  onsubmit="return confirm('Deseja excluir este chamado? Essa ação não pode ser desfeita.')">
                  <input type="hidden" name="id" value="<?php echo (int)$chamado["id"]; ?>">
                  <button type="submit" name="excluir">Excluir</button>
                </form>
              </div>
              <hr>
            <?php } ?>
          <?php } else { ?>
            <p>Nenhum chamado encontrado.</p>
          <?php } ?>

        </div>
      </div>
    </div>
  </div>
</body>
</html>