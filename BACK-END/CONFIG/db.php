<?php

$host = "localhost";
$db   = "first_data";
$user = "root";
$pass = "12345678";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);

    // Lança exceções em caso de erros de SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Retorna resultados como arrays associativos
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Força o uso de Prepared Statements nativos do MySQL
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}