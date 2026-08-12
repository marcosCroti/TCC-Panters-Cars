<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nome = trim($_POST["nome"] ?? "");
    $senha = $_POST["senha"] ?? "";
    $cpf = preg_replace('/[^0-9]/', '', $_POST["cpf"] ?? "");

    if ($nome === "" || $senha === "" || $cpf === "") {
        $erro = "Preencha todos os campos";
    } else {

        // busca usuário pelo CPF e nome
        $stmt = $pdo->prepare("
            SELECT usuario_nome, password_hash, CPF 
            FROM first_data.usuarios 
            WHERE usuario_nome = ? AND CPF = ?
        ");

        $stmt->execute([$nome, $cpf]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $erro = "Usuário não encontrado";
        } else {

            // verifica senha (HASH)
            if (!password_verify($senha, $user["password_hash"])) {
                $erro = "Senha incorreta";
            } else {

                // login OK
                $_SESSION["user"] = $user["usuario_nome"];

                header("Location: ../users/index.php");
                exit;
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoScanPro - Login</title>
    <link rel="stylesheet" href="../Front-End/CSS/Style_Users/Login_User.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="login-card">
            <!-- Logo -->
            <div class="logo-center">
                    <img src="../Front-End/logo_panthers.jpg" alt="logo" class="logo">
            </div>

            <!-- Título -->
            <h1 class="title">Panthers<span class="highlight">Cars</span></h1>
            <p class="subtitle">Sistema de Gestão de Peças Automotivas</p>

            <!-- Formulário -->
            <div class="form-section">
                <h2 class="form-title">Acesso ao Sistema</h2>
                <p class="form-subtitle">Entre com suas credenciais de acesso</p>

                <form method="POST">
                    <!-- campo nome -->
                    <div class="input-group">
                        <label for="nome">
                            <i class="fas fa-id-badge"></i> NOME COMPLETO
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-id-badge input-icon"></i>
                            <input type="text" id="nome" placeholder="Nome Completo" name="nome">
                        </div>
                    </div>

                    <!-- Campo CPF -->
                    <div class="input-group">
                        <label for="cpf">
                            <i class="fas fa-id-card"></i> CPF
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-id-card input-icon"></i>
                            <input 
                                type="text" 
                                id="cpf" 
                                placeholder="000.000.000-00"
                                maxlength="14"
                                name="cpf"
                            >
                        </div>
                    </div>

                    <!-- Campo Senha -->
                    <div class="input-group">
                        <label for="senha">
                            <i class="fas fa-lock"></i> SENHA
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input 
                                type="password" 
                                id="senha" 
                                placeholder="••••••••"
                                name="senha"
                            >
                        </div>
                    </div>

                    <!-- Botão de Login -->
                    <button type="submit" class="btn-login">
                        <i class="fas fa-arrow-right-to-bracket"></i>
                        Entrar no Sistema
                    </button>
                    <?php if ($erro): ?>
                    <p><?= htmlspecialchars($erro) ?></p>
                    <?php endif; ?> 

                    <!-- Divisor -->
                    <div class="divider">
                        <span>ou</span>
                    </div>

                    <!-- Links -->
                    <div class="links">
                        <a href="#" class="link-primary">Esqueceu a senha?</a>
                        <p class="link-secondary">
                            Sem cadastro? <a href="../users/create.php">Criar conta</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>