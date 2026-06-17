<?php
require_once "db.php";

$erros = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $funcionario = $_POST["nome_fun"];
    $CPF = $_POST["CPF"];
    $email = $_POST["email"];
    $setor = $_POST["setor"];
    $quantiade = $_POST["quantidade_pecas_no_dia"];
    $status = $_POST["status_fun"];

  
if(empty($erros)) {
    print("tudo ok!");

    $sql = "INSERT INTO funcionario (nome_fun, CPF, email, setor,  quantidade_pecas_no_dia, status_fun) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$funcionario, $CPF, $email, $setor, $quantiade, $status]);

    header("Location: funcionario.php");
    exit;
} else {
    echo $erros;
}
}

?>

<h1>Cadastrar Funcionário</h1>

<form method="post">

Funcionário:<br>
<input type="text" name="nome_fun" value="<?php echo $fun["nome_fun"]; ?>" required><br><br>

CPF:<br>
<input type="number" name="CPF" value="<?php echo $fun["CPF"]; ?>" required><br><br>

E-mail:<br>
<input type="text" name="email" value="<?php echo $fun["email"]; ?>" required><br><br>

Setor:<br>
<input type="text" name="setor" value="<?php echo $fun["setor"]; ?>" required><br><br>

Quantidade de peças:<br>
<input type="text" name="quantidade_pecas_no_dia" value="<?php echo $fun["quantidade_pecas_no_dia"]; ?>" required><br><br>

Categoria:<br>
<input type="text" name="status_fun" value="<?php echo $fun["status_fun"]; ?>" required><br><br>

<button type="submit">Salvar</button>
</form>

<br>
<a href="funcionario.php">Voltar</a>
