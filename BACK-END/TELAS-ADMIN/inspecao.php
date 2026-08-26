<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_login();
$nome_sessao = $_SESSION["user"] ?? ""; 
$opcao_selecionada = $_GET['opicao'] ?? '';


$tabelas = [
    'pistao' => 'pistao_parametro',
    'pastilha' => 'pastilha_parametro',
    'amortecedor' => 'amortecedor_parametro',
    'para_choque' => 'para_choque_parametro',
    'bateria' => 'bateria_parametro'
];

$lista = [
    "Teste1",
    "Teste2",
    "Teste3",
    "Teste4",
];

if($opcao_selecionada === ""){
    $tabela_permetida = "";
}else{
    $tabela_permetida = $tabelas[$opcao_selecionada];
}
/*if(!array_key_exists($opcao_selecionada, $tabelas)){
    die("Categoria Invalida ou não selecionada");
}else if($opcao_selecionada === ""){
    echo "Pinhamonhangaba";
};*/


$stmt = $pdo->prepare("SELECT * FROM $tabela_permetida");

$user = $stmt->fetchAll(PDO::FETCH_ASSOC);






if($_SERVER["REQUEST_METHOD"] === "POST"){
    $opcoes = $_GET["opicao"];

    switch ($opcoes){
        case "pistao":
            $cor = "1";
            $modelo = "1";
            break;
        case "pastilha":
            $cor = "2";
            $modelo = "2";
            break;
        
        case "bateria":
            $cor = "3";
            $modelo = "3";
            break;
        case "amortecedor":
            $cor = "4";
            $modelo = "4";
            break;
        case "para_choque":
            $cor = "5";
            $modelo = "5";
            break;
            }
}       




?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Inspeção</title>
    <link rel="stylesheet" href="../../FRONT-END/CSS/TELAS-LOGAR/Style_Admin/Inspecao.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

</head>
<body>

    <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">
            <img src="../../logo_panthers.jpg" alt="logo" id="logo">
        </div>
        <span class="logo-text">Panthers<span>Cars</span></span>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">Principal</div>
        <a href="./index.php" class="nav-item" onclick="setActive(this, 'Dashboard')">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="./scanner.php" class="nav-item" onclick="setActive(this, 'Scanner')">
            <i class="fas fa-qrcode"></i> Scanner
        </a>
        <a href="./Inventario.php" class="nav-item active" onclick="setActive(this, 'Inventário')">
            <i class="fas fa-boxes"></i> Inventário
        </a>

         <a href="./inspecao.php" class="nav-item" onclick="setActive(this, 'Controle de Qualidade')">
            <i class="fas fa-chart-line"></i> Inspeção
        </a>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">Administração</div>
        <a href="./Funcionarios.html" class="nav-item" onclick="setActive(this,'Funcionários')">
            <i class="fas fa-users"></i> Funcionários
        </a>
        <a href="./Alerta_Admin.html" class="nav-item" onclick="setActive(this, 'Alertas')">
            <i class="fas fa-bell"></i> Alertas
            <span class="badge">0</span>
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="avatar">G</div>
        <div class="user-info">
            <p>Gerente Admin</p>
            <span>Administrador</span>
        </div>
    </div>
</aside>

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="navbar-left">
            <i class="fas fa-bars"></i>
            <span class="navbar-title">Editar Inspeção</span>
        </div>
        <div class="navbar-right">
            <button class="nav-icon-btn">
                <i class="fas fa-bell"></i>
                <span class="badge"></span>
            </button>
            <button class="nav-icon-btn">
                <i class="fas fa-right-from-bracket"></i>
            </button>
            <div class="avatar">A</div>
        </div>
    </nav>

    <!-- MAIN -->
    <div class="main-content">

        <!-- BACK BUTTON -->
        <div class="back-btn-wrapper">
            <a href="./scanner.php" class="btn-back">
                <i class="fas fa-arrow-left"></i>
                Voltar ao Scanner
            </a>
        </div>

<!--         FILTER BAR
         <form action="inspecao.php" method="GET">
        <div class="filter-bar">
            <label>Item:</label>
            
            <select name="opicao">
                <option value="pista" selected="pistao">Pistão</option>
                <option value="pastilha selected="pastilha">Pastilha</option>
                <option value="bateria">Bateria</option>
                <option value="suspensao"  >Suspensão</option>
                <option value="chassi">Chassi</option>
            </select>
            <button type="submit" class="btn-reload">
                <i class="fas fa-rotate-right"></i>
                Recarregar
            </button>
        </div>
           </form> -->

           <form action="inspecao.php" method="GET">
  <div class="filter-bar">
    <label>Item:</label>

    <select name="opicao">
      <option value="" disabled selected <?= ($opcao_selecionada === '') ? 'selected' : '' ?>>Selecione uma opção</option>
      <option value="pistao" <?= ($opcao_selecionada === 'pistao') ? 'selected' : '' ?>>Pistão</option>
      <option value="pastilha" <?= ($opcao_selecionada === 'pastilha') ? 'selected' : '' ?>>Pastilha</option>
      <option value="bateria" <?= ($opcao_selecionada === 'bateria') ? 'selected' : '' ?>>Bateria</option>
      <option value="amortecedor" <?= ($opcao_selecionada === 'amortecedor') ? 'selected' : '' ?>>Amortecedor</option>
      <option value="para_choque" <?= ($opcao_selecionada === 'para_choque') ? 'selected' : '' ?>>Para Choque</option>
    </select>

    <button type="submit" class="btn-reload">
      <i class="fas fa-rotate-right"></i>
      Recarregar
    </button>
  </div>
</form>

        <!-- CHECKLIST -->
        <div class="checklist-card">
            <div class="checklist-header">
                <i class="fas fa-clipboard-list"></i>
                <h3>Itens de Verificação</h3>
            </div>
            <div class="checklist-body">


            <?php foreach ($lista as $list){
                
            }
            
            
            
            
            ?>
        
        <!--
                <div class="checklist-item">
                    <div class="item-number">1</div>
                    <span class="item-text">Verificar se a cor do mouse é preta.</span>
                    <div class="item-check"></div>
                </div>

                <div class="checklist-item">
                    <div class="item-number">2</div>
                    <span class="item-text">Verificar se o mouse é USB ou Bluetooth.</span>
                    <div class="item-check"></div>
                </div>

                <div class="checklist-item">
                    <div class="item-number">3</div>
                    <span class="item-text">Verificar se o peso do mouse está entre 85-90g.</span>
                    <div class="item-check"></div>
                </div>

                <div class="checklist-item">
                    <div class="item-number">4</div>
                    <span class="item-text">Verificar se o acabamento do plástico é fosco.</span>
                    <div class="item-check"></div>
                </div>

                <div class="checklist-item">
                    <div class="item-number">5</div>
                    <span class="item-text">Verificar se o clique esquerdo faz som.</span>
                    <div class="item-check"></div>
                </div>

                <div class="checklist-item">
                    <div class="item-number">6</div>
                    <span class="item-text">Verificar se o botão do scroll clica.</span>
                    <div class="item-check"></div>
                </div>

                <div class="checklist-item">
                    <div class="item-number">7</div>
                    <span class="item-text">Verificar se o compartimento da pilha fecha bem.</span>
                    <div class="item-check"></div>
                </div>

            </div>

        -->
        </div>

        <!-- STATS CARDS -->
        <div class="stats-row">
            <div class="stat-card total">
                <div class="stat-label">Total</div>
                <input class="stat-input" type="number" placeholder="0">
            </div>
            <div class="stat-card aprovadas">
                <div class="stat-label">Aprovadas</div>
                <input class="stat-input" type="number" placeholder="0">
            </div>
            <div class="stat-card lote">
                <div class="stat-label">Lote</div>
                <input class="stat-input" type="text" placeholder="—">
            </div>
            <div class="stat-card reprovadas">
                <div class="stat-label">Reprovadas</div>
                <input class="stat-input" type="number" placeholder="0">
            </div>
        </div>

        <!-- ACTION -->
        <div class="action-bar">
            <button class="btn-fim">
                <i class="fas fa-check-circle" style="margin-right:8px;"></i>
                Finalizar Inspeção
            </button>
        </div>

    </div>

</body>
</html>