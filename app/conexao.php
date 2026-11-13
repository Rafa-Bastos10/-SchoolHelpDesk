<?php

$host = "localhost";
$banco = "SchoolHelpDesk";
$porta = "3307";
$usuario = "root";
$senha = "Senai@123";

$pdo = new PDO(
    "mysql:host=$host;port=$porta;dbname=$banco;charset=utf8mb4",
    $usuario,
    $senha
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

?>