<?php
    require_once __DIR__ . "/../config/db.php";
    require_once __DIR__ . "/../config/auth.php";
    

    //Fase 2 criavar erro variavel

    $erro = "";

    if($_SERVER["REQUEST_METHOD"] === "POST"){
        $cpf = preg_replace('/[^0-9]/', '', $_POST["cpf"] ?? "");
        $email = trim($_POST["email" ] ?? "");
        $senha = trim($_POST["password" ] ?? "");
        $confirmarSenha = trim($_POST["confirmar" ] ?? "");
        $nome = trim($_POST["nome"] ?? "");
        $telefone = preg_replace('/[^0-9]/', '', $_POST["telefone"] ?? "");

        if(strlen($cpf) != 11){
            $erro = "CPF INVALIDO!";
        }
        else if($senha != $confirmarSenha){
            $erro = "Senha diferentes";
        }

        else if(strlen($senha) < 6){
            $erro = "Limite de 6 caracteres de senha";
        }else if($cpf === "" || $email === "" || $senha === "" || $confirmarSenha === "" || $nome === ""){
            $erro = "Campos vazios";
        }else if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $erro = "Email invalido";
        }else{
            $stmt_check = $pdo->prepare("SELECT CPF FROM first_data.usuarios WHERE CPF = ? OR email = ?");
            $stmt_check->execute([$cpf, $email]);
        
            if ($stmt_check->rowCount() > 0) {
            $erro = "CPF ou E-mail já cadastrado no sistema!";
            }else{
                $hash = password_hash($senha, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("INSERT INTO first_data.usuarios(CPF, usuario_nome, email, password_hash, telefone) VALUES (?, ?, ?, ?, ?)");

            if ($stmt->execute([$cpf, $nome, $email, $hash, $telefone])) {
                header("Location: ../auth/login.php");
                exit;
            } else {
                $erro = "Erro ao salvar no banco de dados.";
            }
        }
        }

    }
    require_once __DIR__ . "/../Front-end/index.html";

?>
