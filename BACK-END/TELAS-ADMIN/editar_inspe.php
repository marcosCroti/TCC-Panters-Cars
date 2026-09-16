<?php
require_once __DIR__ ."/../config/db.php";

$erros = "";


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
   $nome_sessao = $_SESSION["user"] ?? ""; 
    $opcao_selecionada = $_GET['opicao'] ?? '';
    

    if (empty($erros)) {
   
    $sql = "UPDATE first_data.instrucao SET user = ?, opicao = ?";
    $stmt = $pdo->prepare($sql);
    
   
    $stmt->execute([$nome_sessao, $opcao_selecionada]); 

    
    header("Location: inspecao.php");
    exit;
} else {
    echo $erros;
}
}

$id_peca = $mapeamento_ids[$opcao_selecionada] ?? null;

if ($id_peca) {
        $stmt = $pdo->prepare("SELECT id_peca, instrucao FROM first_data.instrucao WHERE id_peca = ?");
        $stmt->execute([$id_peca]);
        $inspecoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

?>  