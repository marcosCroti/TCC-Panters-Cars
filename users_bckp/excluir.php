<?php
require_once "db.php";

$funcionario = $_GET["nome_fun"];

$sql = "DELETE FROM funcionario WHERE nome_fun = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$funcionario]);

header("Location: funcionario.php");
exit;