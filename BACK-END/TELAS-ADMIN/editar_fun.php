
<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
$erro = "";
$id = $_GET["id"];
$sql = "SELECT usuario_nome, CPF, telefone, email, setor_Funcionario, id FROM first_data.usuarios WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if($_SERVER["REQUEST_METHOD"] === "POST"){
    $nome = $_POST["nome"];
    $cpf = $_POST["cpf"];
    $telefone = $_POST["telefone"];
    $setor = $_POST["setor"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];
    if (empty($cpf) || empty($email) || empty($senha) || empty($nome)) {
        $erro = "Preencha todos os campos obrigatórios!";
    } 
    if (strlen($cpf) != 11 || !is_numeric($cpf) ){
        $erro = "CPF inválido!";
    }
    if(!is_numeric($telefone)){
        $erro = "telefone inválido!";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido!";
    } 
    if (strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres!";
    } 
    if(!$erro){

    $sql = "UPDATE usuarios SET usuario_nome = :nome, CPF = :cpf, telefone = :telefone, setor_Funcionario = :setor, email = :email WHERE id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":nome" => $nome,
        ":cpf" => $cpf,
        ":telefone" => $telefone,
        ":setor" => $setor,
        ":email" => $email,
        ":id" => $id
            ]);

    
    header("Location: ./funcionario.php");
    exit();
}  
} 


?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panthers Cars - Editar Conta</title>
    <link rel="stylesheet" href="../../FRONT-END/CSS/TELAS-LOGAR/criar_conta.css">
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
        <h1 class="title">Editar <span class="highlight">Conta</span></h1>
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
                value="<?=htmlspecialchars($user["usuario_nome"]);?>"
                
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
                  value="<?=htmlspecialchars($user["CPF"])?>"
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
                value="<?=htmlspecialchars($user["telefone"]) ?>"
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
                            placeholder="seu@email.com"
                            name="email"
                            value="<?=htmlspecialchars($user["email"])?>"
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
                                placeholder="Mínimo 8 caracteres"
                                name="senha"
                                required

                            >
                            <button type="button" class="password-toggle" data-target="senha">
                                <i class="fas fa-eye"></i>
                            </button>
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
                    <!-- <div class="form-section">
                        <label class="section-label">SETOR</label>
                        <div class="input-wrapper">
                    
                            <select name="" id="">
                              <option value="" disabled selected>Selecione</option>
                              <option value="">Montagem</option>
                              <option value="">Qualidade</option>
                              <option value="">Expedição</option>
                              <option value="">Inspeção</option>
                            </select>
                        </div>
                    </div> -->
                    <div class="form-section">
                        <label class="section-label">SETOR</label>
                        <div class="input-wrapper">
                            <i class="fas fa-sitemap input-icon"></i>


                  <select name="setor" id="setor">
                      <option value="" disabled>Selecione</option>
                      <!-- Compara o valor do banco para deixar a opção correta selecionada -->
                      <option value="montagem" <?= $user["setor_Funcionario"] === 'montagem' ? 'selected' : ''; ?>>Montagem</option>
                      <option value="qualidade" <?= $user["setor_Funcionario"] === 'qualidade' ? 'selected' : ''; ?>>Qualidade</option>
                      <option value="expedicao" <?= $user["setor_Funcionario"] === 'expedicao' ? 'selected' : ''; ?>>Expedição</option>
                      <option value="inspecao" <?= $user["setor_Funcionario"] === 'inspecao' ? 'selected' : ''; ?>>Inspeção</option>
                  </select>
                        </div>
                    </div>

                    <?php if($erro): ?>

                        <p style="color = 'red'"><?= htmlspecialchars($erro); ?></p>
                    <?php endif; ?>
                </div>

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
                    Salvar
                </button>

                <a class="link-func" href="./funcionario.php">
                  Voltar
                </a>

                <!-- Link para Login -->
                <!-- <div class="login-link">
                    Já tem conta? <a href="../auth/login.php">Fazer login</a>
                </div> -->
              </div>
            </div>

 

        </div>
      </div>
    </div>

    <script src="../Front-end/SCRIPT/script.js"></script>
  </body>
</html>
