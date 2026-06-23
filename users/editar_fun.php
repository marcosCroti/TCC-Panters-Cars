<?php
require_once __DIR__ ."/../config/db.php";

$erros = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Pegamos o ID que vem do campo oculto (hidden) do formulário
    $id = $_POST["id_funcionario"] ?? null; 
    $nome = $_POST["nome"] ?? '';
    $CPF = $_POST["CPF"] ?? '';
    $email = $_POST["email"] ?? '';

    if (!$id) {
        $erros = "ID do funcionário não foi informado.";
    }

    if (empty($erros)) {
    // Executa o UPDATE no banco
    $sql = "UPDATE first_data.usuarios SET usuario_nome = ?, CPF = ?, email = ? WHERE ID = ?";
    $stmt = $pdo->prepare($sql);
    
    // CORRIGIDO: Agora passamos os 4 valores na ordem exata da query!
    $stmt->execute([$nome, $CPF, $email, $id]); 

    // Redireciona de volta para a página inicial de funcionários
    header("Location: funcionarios.php");
    exit;
} else {
    echo $erros;
}
}
// 2. SE FOR UMA REQUISIÇÃO GET (Apenas para carregar a página/modal se necessário)
// Nota: Como você está usando Modal com JS, essa parte abaixo pode nem ser necessária 
// se você já renderiza a lista de usuários na página principal. Mas deixei aqui por segurança:
$id = $_GET["id"] ?? null;
$user = null;

if ($id) {
    $sql = "SELECT usuario_nome, CPF, email, ID FROM first_data.usuarios WHERE ID = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>  