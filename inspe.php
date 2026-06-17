
<?php
$host = 'localhost';
$usuario = 'root'; 
$senha = '12345678';       
$banco = 'first_data';

$conexao = new mysqli($host, $usuario, $senha, $banco);


if ($conexao->connect_error) {
    die("Erro de conexão: " . $conexao->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id_pecas'];
    $nome = $_POST['nome_tipo'];
    $grupo = $_POST['grupo_peca'];
    $setor = $_POST['setor'];
    
    
    

    
    $stmt = $conexao->prepare("INSERT INTO inspecoes (pecas_aprovadas, pecas_rejeitadas, pecas_examinadas) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("Peças", $id, $nome, $grupo, $setor);

    if ($stmt->execute()) {
        $mensagem = "Inspeção salva com sucesso!";
    } else {
        $mensagem = "Erro ao salvar: " . $stmt->erro;
    }

    $stmt->close();
}
$conexao->close();
?>








