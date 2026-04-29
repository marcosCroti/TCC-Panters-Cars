<?php
 require_once __DIR__ . "/../config/db.php";


if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? ""; 

$stmt = $pdo->prepare("SELECT id, username, password_hash from first_data.users where username = ?");
$stmt->execute(["$username"]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user["password_hash"])) {

$_SESSION["user_id"] = $user["id"];
$_SESSION["username"] = $user["username"];

header("Location: /first_data/users/index.php");
exit;
} else {
    $erro = "Usuário ou senha inválidos";
}

}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>


    <?php if ($erro): ?>
        <p><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="post">
        <p>
    <label>Usuário<br>
        <input type="text" name="username" required>
    </label>
    </p>
    <p>
        <label>Senha<br>
            <input type="password" name="password" required>
        </label>
    </p>

        <button type="submit">Entrar</button>
    </form>
</body>
</html>