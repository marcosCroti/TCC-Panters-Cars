<?php
require_once __DIR__ . "/../CONFIG/db.php";
require_once __DIR__ . "/../CONFIG/auth.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cpf = preg_replace('/[^0-9]/', '', $_POST["cpf"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = trim($_POST["password"] ?? "");
    $confirmarSenha = trim($_POST["confirmar"] ?? "");
    $nome = trim($_POST["nome"] ?? "");
    $telefone = preg_replace('/[^0-9]/', '', $_POST["telefone"] ?? "");
    $inputAdmin = $_POST["perfil"] ?? "employee";

    // 1. Define o valor de admin separado da validação de erros
    $isAdminValue = ($inputAdmin === "admin") ? 1 : 0;

    // 2. Cadeia de Validação (Ordem correta)
    if (empty($cpf) || empty($email) || empty($senha) || empty($nome)) {
        $erro = "Preencha todos os campos obrigatórios!";
    } 
    else if (strlen($cpf) != 11) {
        $erro = "CPF inválido! Digite os 11 números.";
    } 
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido!";
    } 
    else if (strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres!";
    } 
    else if ($senha !== $confirmarSenha) {
        $erro = "As senhas não conferem!";
    } 
    else {
        // 3. Se passou em tudo, verifica banco de dados
        try {
            $stmt_check = $pdo->prepare("SELECT CPF FROM first_data.usuarios WHERE CPF = ? OR email = ?");
            $stmt_check->execute([$cpf, $email]);

            if ($stmt_check->rowCount() > 0) {
                $erro = "CPF ou E-mail já cadastrado!";
            } else {
                $hash = password_hash($senha, PASSWORD_DEFAULT);
                
                // Note que usei $isAdminValue aqui
                $stmt = $pdo->prepare("INSERT INTO first_data.usuarios (CPF, usuario_nome, email, password_hash, telefone, isAdmin) VALUES (?, ?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$cpf, $nome, $email, $hash, $telefone, $isAdminValue])) {
                    header("Location: ../AUTH/login.php");
                    exit;
                } else {
                    $erro = "Erro crítico ao salvar no banco.";
                }
            }
        } catch (PDOException $e) {
            $erro = "Erro no banco de dados: " . $e->getMessage();
        }
    }
}
?>

<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panthers Cars - Criar Conta</title>
    <link rel="stylesheet" href="../../FRONT-END/CSS/TELAS-LOGAR/Criar_Conta.css"/>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    />
  </head>
  <body>
    <div class="container">
      <div class="register-card">
        <!-- Logo -->
        <div class="logo">
          <div class="logo-icon">
            <i class="fas fa-user-plus"></i>
          </div>
        </div>

        <!-- Título -->
        <h1 class="title">Criar <span class="highlight">Conta</span></h1>
        <p class="subtitle">Preencha seus dados para acessar o sistema</p>

        <!-- Formulário -->
        <form id="registerForm" action="" method="POST">
          <input type="hidden" name="perfil" id="perfil" value="employee">
          <!-- Tipo de Perfil -->
          <div class="form-section">
            <label class="section-label">TIPO DE PERFIL</label>
            <div class="profile-options">
              <!-- Administrador -->
              <div class="profile-card" data-profile="admin" >
                <div class="profile-icon">
                  <i class="fas fa-user-shield"></i>
                </div>
                <h3>Administrador</h3>
                <p>Acesso total ao sistema e gestão de equipe</p>
                <div class="check-icon">
                  <i class="fas fa-check"></i>
                </div>
              </div>

              <!-- Funcionário -->
              <div class="profile-card active" data-profile="employee">
                <div class="profile-icon">
                  <i class="fas fa-user-tag"></i>
                </div>
                <h3>Funcionário</h3>
                <p>Acesso ao scanner e operação de peças</p>
                <div class="check-icon">
                  <i class="fas fa-check"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Nome Completo -->
          <div class="form-section">
            <label class="section-label">NOME COMPLETO</label>
            <div class="input-wrapper">
              <i class="fas fa-user input-icon"></i>
              <input
                type="text"
                id="nome"
                placeholder="Seu nome completo"
                required
                name="nome"
              />
            </div>
          </div>

          <!-- CPF e Telefone -->
          <div class="form-row">
            <div class="form-section">
              <label class="section-label">CPF</label>
              <div class="input-wrapper">
                <i class="fas fa-id-card input-icon"></i>
                <input
                  type="text"
                  id="cpf"
                  name="cpf"
                  placeholder="000.000.000.00"
                  maxlength="14"
                  required
                />
              </div>
            </div>

            <div class="form-section">
              <label class="section-label">TELEFONE</label>
              <div class="input-wrapper">
                <i class="fas fa-phone input-icon"></i>
                <input
                  type="text"
                  id="telefone"
                  placeholder="(00) 00000-0000"
                  maxlength="15"
                  required
                  name="telefone"
                />
              </div>
            </div>
          </div>

          <!-- E-mail -->
          <div class="form-section">
            <label class="section-label">E-MAIL</label>
            <div class="input-wrapper">
              <i class="fas fa-envelope input-icon"></i>
              <input
                type="email"
                id="email"
                placeholder="seu@email.com"
                required
                name="email"
                email="email"
              />
            </div>
          </div>

          <!-- Senha e Confirmar Senha -->
          <div class="form-row">
            <div class="form-section">
              <label class="section-label">SENHA</label>
              <div class="input-wrapper">
                <i class="fas fa-lock input-icon"></i>
                <input
                  type="password"
                  id="senha"
                  placeholder="Mínimo 8 caracteres"
                  required
                  name="password"                  
                />
                <button
                  type="button"
                  class="password-toggle"
                  data-target="senha"
                >
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>

            <div class="form-section">
              <label class="section-label">CONFIRMAR SENHA</label>
              <div class="input-wrapper">
                <i class="fas fa-lock input-icon"></i>
                <input
                  type="password"
                  name="confirmar"
                  id="confirmarSenha"
                  placeholder="Repita a senha"
                  required
                />
                <button
                  type="button"
                  class="password-toggle"
                  data-target="confirmarSenha"
                >
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Força da Senha -->
          <div class="password-strength">
            <div class="strength-bar">
              <div class="strength-fill"></div>
            </div>
            <small class="strength-text"
              >Força da senha: <span id="strengthText">Fraca</span></small
            >
          </div>

          <!-- Botão de Cadastro -->
          <button type="submit" class="btn-register">
            <i class="fas fa-user-plus"></i>
            Criar Conta
          </button>
          <br>

          <?php if ($erro): ?>
           <p><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>


          <!-- Link para Login -->
          <div class="login-link">
            Já tem conta? <a href="../AUTH/login.php">Fazer login</a>
          </div>
        </form>

        </div>
      </div>
    </div>

    <script src="../../FRONT-END/JAVASCRIPT/script.js"></script>
  </body>
</html>
