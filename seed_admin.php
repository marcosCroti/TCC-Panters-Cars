
<?php
require_once __DIR__ . "/config/db.php";

// Dados do Admin
$username = "admin";
$senha = "panther123";
$cpf = "00000000000"; // Precisa de um valor inicial
$email = "admin@email.com";
$telefone = "000000000";

// 1. Corrigido: Verificar se o usuário existe pelo campo 'usuario_nome' (que é o que você tem na tabela)
$stmt = $pdo->prepare("SELECT cpf FROM usuarios WHERE usuario_nome = ?");
$stmt->execute([$username]); 
$existe = $stmt->fetch(); 

if($existe){
    echo "Usuário admin já existe. Você pode apagar este arquivo.";
    exit;
}

// 2. Gerar o hash da senha
$hash = password_hash($senha, PASSWORD_DEFAULT);

// 3. Corrigido: Inserir todos os campos obrigatórios (NOT NULL)
try {
    $stmt = $pdo->prepare("INSERT INTO usuarios (cpf, usuario_nome, email, password_hash, isAdmin, telefone) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$cpf, $username, $email, $hash, "1", $telefone]);

    echo "Admin criado com sucesso!<br>";
    echo "Login: $username<br>";
    echo "Senha: $senha<br>";
    echo "<strong>Dica:</strong> apague o arquivo seed_admin.php após usar.<br>";
} catch (PDOException $e) {
    echo "Erro ao criar admin: " . $e->getMessage();
}
?>