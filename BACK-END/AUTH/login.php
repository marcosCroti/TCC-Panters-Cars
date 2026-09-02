<?php
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
require_once __DIR__ . "/../CONFIG/db.php";
require_once __DIR__ . "/../CONFIG/auth.php";


    $erro = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {

        // $nome = trim($_POST["nome"] ?? "");
        $senha = $_POST["senha"] ?? "";
        $cpf = preg_replace('/[^0-9]/', '', $_POST["cpf"] ?? "");
        $id = null;
        if ($senha === "" || $cpf === "") {
            $erro = "Preencha todos os campos";
        } else {

            // busca usuário pelo CPF e nome
            $stmt = $pdo->prepare("
                SELECT password_hash, CPF, id 
                FROM first_data.usuarios 
                WHERE CPF = ?
            ");

            $stmt->execute([$cpf]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $erro = "Usuário não encontrado";
            } else {

                // verifica senha (HASH)
                if (!password_verify($senha, $user["password_hash"])) {
                    $erro = "Senha incorreta";
                } else {

                    // login OK
                    // $_SESSION["user"] = $user["usuario_nome"];
                    $_SESSION["user_id"] = $user["id"];

                    header("Location: ../TELAS-ADMIN/index.php");
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
    <title>Panthers Cars - Login</title>
    <link rel="stylesheet" href="../../FRONT-END/CSS/TELAS-LOGAR/login_admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="login-card">
            <!-- Logo -->
            <div class="logo" id="logo-icon">
                    <img src="../../FRONT-END/LOGIN/IMG/LOGO.png" alt="logo" id="logo"> 
            </div>
            <!-- Título -->
            <h1 class="title">Panthers<span class="highlight">Cars</span></h1>
            <p class="subtitle">Sistema de Gestão de Peças Automotivas</p>

            <!-- Formulário -->
            <div class="form-section">
                <!-- <h2 class="form-title">Acesso ao Sistema</h2>
                <p class="form-subtitle">Entre com suas credenciais de acesso</p> -->

                <form method="POST">
                    <!-- campo nome -->
                    <!-- <div class="input-group">
                        <label for="nome">
                            <i class="fas fa-id-badge"></i> NOME COMPLETO
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-id-badge input-icon"></i>
                            <input type="text" id="nome" placeholder="Nome Completo">
                        </div>
                    </div> -->

                    <!-- Campo CPF -->
                    <div class="input-group">
                        <label for="cpf">
                            <i class="fas fa-id-card"></i> CPF
                        </label>
                        <div class="input-wrapper">
                            <i class="fas fa-id-card input-icon"></i>
                            <input 
                                type="text" 
                                name='cpf'
                                id="cpf" 
                                placeholder="000.000.000-00"
                                maxlength="14"
                                
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
                                name='senha'
                                placeholder="••••••••"
                            >
                        </div>
                    </div>

                    <!-- Botão de Login -->
                    <button type="submit" class="btn-login">
                        <i class="fas fa-arrow-right-to-bracket"></i>
                        Entrar no Sistema
                    </button>

                    <?php if ($erro):?>

                        <p><?= htmlspecialchars($erro)?></p>
                        <?php endif; ?>

                    <!-- Divisor -->
                    <!-- <div class="divider">
                        <span>ou</span>
                    </div> -->

                    <br>

                    <!-- Links -->
                    <!-- <div class="links">
                        <a href="../step1.html" class="link-primary">Esqueceu a senha?</a>
                        <p class="link-secondary">
                            Sem cadastro? <a href="index.html">Criar conta</a>
                        </p>
                    </div> -->

                    <!-- Credenciais de Demonstração -->
                    <!-- <div class="demo-credentials">
                        <h3>Credenciais de Demonstração</h3>
                        <p>Gerente: <span>000.000.000-00</span> / <span>admin123</span></p>
                        <p>Funcionário: <span>111.111.111-11</span> / <span>func123</span></p>
                    </div> -->
                </form>
            </div>
        </div>
    </div>
</body>
</html>