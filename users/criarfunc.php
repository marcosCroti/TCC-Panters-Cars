<?php
    require_once __DIR__ . "/../config/db.php";

    $erro = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $cpf = preg_replace('/[^0-9]/', '', $_POST["cpf"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $senha = trim($_POST["senha"] ?? "");
    $nome = trim($_POST["nome"] ?? "");

    echo $cpf;
    echo $email;
    echo $senha;
    echo $nome;
    if (strlen($cpf) != 11) {
        $erro = "CPF inválido! Digite os 11 números.";
    } 
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido!";
    } 
    else if (strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres!";
    } 
    if($erro){
        echo $erro;
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
                $stmt = $pdo->prepare("INSERT INTO first_data.usuarios (CPF, usuario_nome, email, password_hash) VALUES (?, ?, ?, ?)");
                
               if ($stmt->execute([$cpf, $nome, $email, $hash])) {
                    header("Location: ./funcionarios.php");
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