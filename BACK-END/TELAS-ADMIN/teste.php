
<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_login();

// Pega o nome guardado na sessão pelo login.php
$nome_sessao = $_SESSION["user"] ?? ""; 


$stmt = $pdo->prepare("SELECT usuario_nome, isAdmin FROM first_data.usuarios WHERE usuario_nome = ?");
$stmt->execute([$nome_sessao]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if ($user) {
    // CORREÇÃO: Pega o valor real trazido do banco de dados
    $nome = $user["usuario_nome"]; 
    if($user["isAdmin"]){
        $func = "Administrador";
    }else{
        $func = "Funcionario";
    }
} else {
    $nome = "Usuário não encontrado";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoScanPro - Scanner</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../FRONT-END/CSS/TELAS-ADMIN/scanner.css">
</head>
<body>

 <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-logo">
        <div class="logo-icon">🚗</div>
        <span class="logo-text">Panthers<span>Cars</span></span>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Principal</div>
        <a
          href="./index.php"
          class="nav-item"
          onclick="setActive(this, 'Dashboard')"
        >
          <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a
          href="./scanner.php"
          class="nav-item"
          onclick="setActive(this, 'Scanner')"
        >
          <i class="fas fa-qrcode"></i> Scanner
        </a>
        <a
          href="./inventario.php"
          class="nav-item"
          onclick="setActive(this, 'Inventário')"
        >
          <i class="fas fa-boxes"></i> Inventário
        </a>
        <a
          href="./inspecao.php"
          class="nav-item"
          onclick="setActive(this, 'Inspeção')"
        >
          <i class="fas fa-clipboard"></i> Inspeção
        </a>
        <a
          href="./inspe_editar.php"
          class="nav-item"
          onclick="setActive(this, 'Editar Inspeção')"
        >
          <i class="fas fa-edit"></i> Editar Inspeção
        </a>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Administração</div>
        <a href="funcionario.php" class="nav-item" onclick="setActive(this, 'Funcionários')">
            
          <i class="fas fa-users"></i> Funcionários
        </a>

      </div>

    <div class="sidebar-footer">
        <div class="avatar"><?= strtoupper($nome[0]) ?></div>
        <div class="user-info">
            <p><?= $nome ?></p>
            <span><?= $func ?></span>
        </div>
    </div>
    </aside>

<!-- ===== MAIN ===== -->
<div class="main">

    <!-- TOPBAR -->
    <header class="topbar">
        <button class="topbar-menu-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h2 id="topbar-title">Scanner de Peças</h2>
        <div class="topbar-actions">
            <button class="topbar-btn red" onclick="showToast('🔴 Status: Online')">
                <i class="fas fa-circle" style="font-size:10px"></i>
            </button>
            <button class="topbar-btn ghost" onclick="showToast('🔔 3 alertas pendentes')" style="position:relative">
                <i class="fas fa-bell"></i>
                <span class="notif-dot"></span>
            </button>
            <a href="./../auth/logout.php" class="topbar-btn ghost" style="text-decoration: none;"><i class="fas fa-sign-out-alt" ></i></a>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="content">

        <!-- Page Header -->

        <div class="scanner-wrapper">

            <!-- Scanner Box -->
            <div class="scanner-box" id="scannerBox">
                <div id="webcam-container"></div>
                <div class="scan-line" id="scanLine"></div>
                <div class="scan-corners" id="scanCorners"></div>
                <div class="scan-corners-2" id="scanCorners2"></div>

                <div class="qr-icon-wrap"  id="qrIdle">
                    <div class="qr-grid" id="qrGrid">
                        <div class="qr-cell" id="qc1"></div>
                        <div class="qr-cell" id="qc2"></div>
                        <div class="qr-cell" id="qc3"></div>
                        <div class="qr-cell" id="qc4"></div>
                    </div>
                    <div class="scanner-label" id="scannerLabel">Pronto para escanear</div>
                    <div class="scanner-sublabel" id="scannerSub">Clique em "Ligar Camêra" abaixo</div>
                </div>

                <!-- Result Card (oculto por padrão) -->
                <div class="result-card" id="resultCard">
                    <h3 id="resultName">—</h3>
                    <div class="result-row">
                        <span class="rl">Código</span>
                        <span class="rv" id="rCode">—</span>
                    </div>
                    <div class="result-row">
                        <span class="rl">Categoria</span>
                        <span class="rv" id="rCat">—</span>
                    </div>
                    <div class="result-row">
                        <span class="rl">Localização</span>
                        <span class="rv" id="rLoc">—</span>
                    </div>
                    <div class="result-row">
                        <span class="rl">Quantidade</span>
                        <span class="rv" id="rQty">—</span>
                    </div>
                    <div class="result-row">
                        <span class="rl">Status</span>
                        <span class="rv" id="rStatus">—</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Buttons -->
            <div class="bottom-actions">
            <button class="btn-scan" id="btn-scan">
                    <i class="fas fa-qrcode"></i>
                    Escanear Peça
                </button>
                <button class="btn-alert" id="btn-cancel">
                    <i class="fas fa-clipboard"></i>
                    Inspecionar
                </button>
                <button class="btn-cancel" onclick="openAlertModal()">
                    <i class="fas fa-exclamation-triangle"></i>
                    Cancelar
                </button>
            </div>

            <p id="resultado-vencedor"></p>
            <p id="probabilidade"></p>


        </div>
    </div>
</div>

<!-- ===== MODAL ALERTA ===== -->
<div class="modal-overlay" id="alertModal" onclick="closeModalOutside(event)">
    <div class="modal">
        <div class="modal-header">
            <h3>⚠️ Registrar Alerta</h3>
            <button class="modal-close" onclick="closeAlertModal()">✕</button>
        </div>
        <p style="font-size:13px;color:#6b7280;margin-bottom:12px;">
            Descreva o problema identificado na peça:
        </p>
        <textarea id="alertText" placeholder="Ex: Peça danificada, código ilegível, estoque incorreto..."></textarea>
        <div class="modal-actions">
            <button class="modal-btn secondary" onclick="closeAlertModal()">Cancelar</button>
            <button class="modal-btn primary" onclick="submitAlert()">Registrar Alerta</button>
        </div>
    </div>
</div>

<!-- ===== TOAST ===== -->
<div class="toast" id="toast"></div>

 <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    <script src="../../Front-End/JAVASCRIPT/teste.js"></script>
     <script src="../../Front-End/JAVASCRIPT//fatores.js"></script>
</body>
</html>