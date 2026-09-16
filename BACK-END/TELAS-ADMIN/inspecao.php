<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_login();

// Recupera a mensagem da sessão (se houver) e limpa para não repetir nas próximas atualizações
$mensagem_sucesso = $_SESSION["mensagem_sucesso"] ?? "";
unset($_SESSION["mensagem_sucesso"]);

$nome_sessao = $_SESSION["user"] ?? ""; 
// Unifica o recebimento da opção tanto por GET quanto por POST
$opcao_selecionada = $_REQUEST['opicao'] ?? '';

$stmt = $pdo->prepare("SELECT usuario_nome, isAdmin FROM first_data.usuarios WHERE usuario_nome = ?");
$stmt->execute([$nome_sessao]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $nome = $user["usuario_nome"]; 
    $func = $user["isAdmin"] ? "Administrador" : "Funcionario";
} else {
    $nome = "Usuário não encontrado";
    $func = "Desconhecido";
}

$inspecoes = [];
$mapeamento_ids = [
    'pistao'      => 1,
    'pastilha'    => 2,
    'bateria'     => 5,
    'amortecedor' => 3,
    'para_choque' => 4
];

$id_peca = $mapeamento_ids[$opcao_selecionada] ?? null;

// Lógica de Atualização (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $total = $_POST["total"] ?? "";
    $aprovadas = $_POST["aprovadas"] ?? "";
    $lote = $_POST["lote"] ?? "";
    $rejeitadas = (int)$total - (int)$aprovadas;
    $grupo = "";

    if ($opcao_selecionada === "pistao") {
        $grupo = "Motor e Transmissão";
    } else if ($opcao_selecionada === "pastilha") {
        $grupo = "Freios";
    } else if ($opcao_selecionada === "bateria") {
        $grupo = "Elétrica";
    } else if ($opcao_selecionada === "para_choque") {
        $grupo = "Carroceria/Acabamento";
    } else if ($opcao_selecionada === "amortecedor") {
        $grupo = "Suspensão e Direção";
    }

    $stmt = $pdo->prepare("INSERT INTO pecas (quantidade_pecas, pecas_aprovadas, pecas_reprovadas, lote, id_pecas, grupo_peca, nome_tipo) VALUES(?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$total, $aprovadas, $rejeitadas, $lote, $id_peca, $grupo, $opcao_selecionada])) {
        // Armazena a mensagem na sessão para persistir no redirecionamento
        $_SESSION["mensagem_sucesso"] = "Inspeção concluída";
        
        // Padrão PRG: Redireciona via GET mantendo a opção selecionada na URL
        header("Location: inspecao.php?opicao=" . urlencode($opcao_selecionada));
        exit();
    }
}

// Busca as instruções de verificação da peça
if ($id_peca) {
    $stmt = $pdo->prepare("SELECT id_peca, instrucao FROM first_data.instrucao WHERE id_peca = ?");
    $stmt->execute([$id_peca]);
    $inspecoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Inspeção</title>
    <link rel="stylesheet" href="../../FRONT-END/CSS/TELAS-ADMIN/inspecao.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="main">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <div class="logo" id="logo-icon">
                    <img src="../../FRONT-END/LOGIN/IMG/LOGO.png" alt="logo" id="logo">
                </div>
                <span class="logo-text">Panthers<span>Cars</span></span>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-section-title">Principal</div>
                <a href="./index.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="./scanner.php" class="nav-item"><i class="fas fa-qrcode"></i> Scanner</a>
                <a href="./inspecao.php" class="nav-item active"><i class="fas fa-clipboard"></i> Inspeção</a>
                <a href="./editar_inspecao.php" class="nav-item"><i class="fas fa-edit"></i> Editar-Inspeção</a>
                <a href="./inventario.php" class="nav-item"><i class="fas fa-boxes"></i> Inventário</a>
            </div>

            <div class="sidebar-section">
                <div class="sidebar-section-title">Administração</div>
                <a href="./funcionario.php" class="nav-item"><i class="fas fa-users"></i> Funcionários</a>
            </div>

            <div class="sidebar-footer">
                <div class="avatar"><?= strtoupper($nome[0] ?? 'U') ?></div>
                <div class="user-info">
                    <p><?= htmlspecialchars($nome) ?></p>
                    <span><?= htmlspecialchars($func) ?></span>
                </div>
            </div>
        </aside>

        <header class="topbar">
            <button class="topbar-menu-btn" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h2 id="topbar-title">Inspeção</h2>
            <div class="topbar-actions">
                <a class="topbar-btn ghost" href="../Inicializaçao.html">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </header>

        <div class="main-content">
            <?php if (!empty($mensagem_sucesso)): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($mensagem_sucesso) ?>
                </div>
            <?php endif; ?>

            <div class="back-btn-wrapper">
                <a href="./scanner.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar ao Scanner
                </a>
            </div>

            <!-- FORMULÁRIO GET: Seleção/Filtro -->
            <form action="inspecao.php" method="GET">
                <div class="filter-bar">
                    <label>Item:</label>
                    <select name="opicao">
                        <option value="" disabled <?= ($opcao_selecionada === '') ? 'selected' : '' ?>>Selecione uma opção</option>
                        <option value="pistao" <?= ($opcao_selecionada === 'pistao') ? 'selected' : '' ?>>Pistão</option>
                        <option value="pastilha" <?= ($opcao_selecionada === 'pastilha') ? 'selected' : '' ?>>Pastilha</option>
                        <option value="bateria" <?= ($opcao_selecionada === 'bateria') ? 'selected' : '' ?>>Bateria</option>
                        <option value="amortecedor" <?= ($opcao_selecionada === 'amortecedor') ? 'selected' : '' ?>>Amortecedor</option>
                        <option value="para_choque" <?= ($opcao_selecionada === 'para_choque') ? 'selected' : '' ?>>Para Choque</option>
                    </select>

                    <button type="submit" class="btn-reload">
                        <i class="fas fa-rotate-right"></i> Recarregar
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
                    <?php 
                    $contador = 1; 
                    foreach ($inspecoes as $inspecao): 
                    ?>
                        <div class="checklist-item">
                            <div class="item-number"><?= $contador++; ?></div>
                            <span class="item-text"><?= htmlspecialchars($inspecao["instrucao"]); ?></span>
                            <div class="item-check"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- FORMULÁRIO POST: Envio do Relatório -->
            <form method="POST">
                <input type="hidden" name="opicao" value="<?= htmlspecialchars($opcao_selecionada) ?>">
                
                <div class="stats-row">
                    <div class="stat-card total">
                        <div class="stat-label">Total</div>
                        <input class="stat-input" type="number" placeholder="0" name="total" required id="total">
                    </div>
                    <div class="stat-card aprovadas">
                        <div class="stat-label">Aprovadas</div>
                        <input class="stat-input" type="number" placeholder="0" name="aprovadas" id="aprovadas">
                    </div>
                    <div class="stat-card lote">
                        <div class="stat-label">Lote</div>
                        <input class="stat-input" type="text" placeholder="—" name="lote">
                    </div>
                    <div class="stat-card reprovadas">
                        <div class="stat-label">Reprovadas</div>
                        <label class="stat-input" type="number" placeholder="0" name="reprovadas" id="reprovadas">0</label>
                    </div>
                </div>

                <div class="action-bar">
                    <button class="btn-fim" type="submit">
                        <i class="fas fa-check-circle" style="margin-right:8px;"></i>
                        Finalizar Inspeção
                    </button>
                </div>
            </form> 
        </div>
    </div>
</body>
<script>
let totalInput = document.getElementById("total");
let aprovadasInput = document.getElementById("aprovadas");
let reprovadasElement = document.getElementById("reprovadas");

// Função responsável por calcular e atualizar a tela
function calcularReprovadas() {
    let total = Number(totalInput.value) || 0;
    let aprovadas = Number(aprovadasInput.value) || 0;
    
    let resultado = total - aprovadas;
    
    // Atualiza o texto na tela
    reprovadasElement.innerText = resultado;
}

// Adiciona os eventos para executar o cálculo automaticamente ao alterar os campos
totalInput.addEventListener("input", calcularReprovadas);
aprovadasInput.addEventListener("input", calcularReprovadas);
</script>
</html>