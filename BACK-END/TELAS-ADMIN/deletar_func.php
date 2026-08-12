<?php

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

$id = $_GET["id"];
$stmt = $pdo->prepare("DELETE FROM usuarios WHERE ID = ?");
$stmt->execute([$id]);

header("Location: funcionario.php");
