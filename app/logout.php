<?php
session_start();

// Apaga todos os dados da sessão e volta para o login
$_SESSION = [];
session_destroy();

header("Location: index.php");
exit;