<?php
require_once __DIR__ . "/../CONFIG/db.php";
require_once __DIR__ . "/../CONFIG/auth.php";

$erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $cpf = preg_replace('/[^0-9]/', '', $_POST["cpf"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $setor = $_POST["setor"] ?? "";
    $senha = trim($_POST["password"] ?? "");
    $nome = trim($_POST["nome"] ?? "");
    //$telefone = preg_replace('/[^0-9]/', '', $_POST["telefone"] ?? "");
    $telefone = ($_POST["telefone"] ?? "");
    // 1. Define o valor de admin separado da validação de erros

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
                $stmt = $pdo->prepare("INSERT INTO first_data.usuarios (CPF, usuario_nome, email, password_hash, telefone, setor_Funcionario) VALUES (?, ?, ?, ?, ?, ?)");
                
                if ($stmt->execute([$cpf, $nome, $email, $hash, $telefone, $setor])) {
                    header("Location: ../../BACK-END/TELAS-ADMIN/funcionario.php");
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
    <link rel="stylesheet" href="../../FRONT-END/CSS/TELAS-LOGAR/criar_conta.css" />
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
          <img src="../../FRONT-END/LOGIN/IMG/LOGO.png" alt="logo" id="logo">
          
        </div>
        
        <!-- Título -->
        <h1 class="title">Criar <span class="highlight">Conta</span></h1>
        <p class="subtitle">Preencha seus dados para acessar o sistema</p>

        <!-- Formulário -->
        <form id="registerForm" method="POST">
        
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
                  maxlength="11"
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
                  maxlength="11"
                  required
                  name="telefone"
                />
              </div>
            </div>
          </div>

            <!-- Formulário -->
            <!-- <form id="registerForm"> -->

                <!-- Nome Completo -->
                <!-- <div class="form-section">
                    <label class="section-label">NOME COMPLETO</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user input-icon"></i>
                        <input 
                            type="text" 
                            id="nome" 
                            placeholder="Seu nome completo"
                            nome="name"
                        >
                    </div>
                </div> -->

                <!-- CPF e Telefone -->
                <!-- <div class="form-row">
                    <div class="form-section">
                        <label class="section-label">CPF</label>
                        <div class="input-wrapper">
                            <i class="fas fa-id-card input-icon"></i>
                            <input 
                                type="text" 
                                id="cpf" 
                                placeholder="000.000.000-00"
                                maxlength="14"
                                required
                            >
                        </div>
                    </div> -->

                    <!-- <div class="form-section">
                        <label class="section-label">TELEFONE</label>
                        <div class="input-wrapper">
                            <i class="fas fa-phone input-icon"></i>
                            <input 
                                type="tel" 
                                id="telefone" 
                                placeholder="(00) 00000-0000"
                                maxlength="15"
                                required
                            >
                        </div>
                    </div>
                </div> -->

                
          
                <!-- E-mail -->
                <div class="form-section">
                    <label class="section-label">E-MAIL</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope input-icon"></i>
                        <input 
                            type="email" 
                            id="email"
                            name="email"
                            placeholder="seu@email.com"
                            required
                        >
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
                                name="password"
                                placeholder="Mínimo 8 caracteres"
                                required
                            >
                            <button type="button" class="password-toggle" data-target="senha">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-section">
                        <label class="section-label">SETOR</label>
                        <div class="input-wrapper">
                            <i class="fas fa-sitemap input-icon"></i>
                            <select name="setor" id="setor">
                              <option value="" disabled selected>Selecione</option>
                              <option value="montagem">Montagem</option>
                              <option value="qualidade">Qualidade</option>
                              <option value="expedicao">Expedição</option>
                              <option value="inspecao">Inspeção</option>
                            </select>
                        </div>
                    </div>
                </div>

                    <!-- <div class="form-section">
                        <label class="section-label">CONFIRMAR SENHA</label>
                        <div class="input-wrapper">
                            <i class="fas fa-lock input-icon"></i>
                            <input 
                                type="password" 
                                id="confirmarSenha" 
                                placeholder="Repita a senha"
                                required
                            >
                            <button type="button" class="password-toggle" data-target="confirmarSenha">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div> -->

                <!-- Força da Senha -->
                <!-- <div class="password-strength">
                    <div class="strength-bar">
                        <div class="strength-fill"></div>
                    </div>
                    <small class="strength-text">Força da senha: <span id="strengthText">Fraca</span></small>
                </div> -->

                <!-- Botão de Cadastro -->
                <button type="submit" class="btn-register">
                    <i class="fas fa-user-plus"></i>
                    Criar Conta
                </button>

                <!-- Link para Login -->
                <!-- <div class="login-link">
                    Já tem conta? <a href="../auth/login.php">Fazer login</a>
                </div> -->
                <?php if($erro): ?>
                  <p style="color: red  ;"><?= $erro ?></p>

                  <?php endif; ?>
              </div>
            </div>

 

        </div>
      </div>
    </div>

    <script src="../Front-end/SCRIPT/script.js"></script>
  </body>
</html>
