<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_login();

// Recupera e limpa a mensagem de sucesso
$mensagem_sucesso = $_SESSION["mensagem_sucesso"] ?? "";
unset($_SESSION["mensagem_sucesso"]);

$nome_sessao = $_SESSION["user"] ?? ""; 
$opcao_selecionada = $_REQUEST['opicao'] ?? '';

// Busca informações do usuário
$stmt = $pdo->prepare("SELECT usuario_nome, isAdmin, ID FROM first_data.usuarios WHERE usuario_nome = ?");
$stmt->execute([$nome_sessao]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $id_usuario = $user["ID"];
    $nome = $user["usuario_nome"]; 
    $func = $user["isAdmin"] ? "Administrador" : "Funcionario";
} else {
    $id_usuario = null;
    $nome = "Usuário não encontrado";
    $func = "Desconhecido";
}

$dd = ($func === "Funcionario") ? "display: none;" : "";

// Mapeamento das peças
$stmt_pecas = $pdo->query("SELECT id_pecas, nome_tipo, grupo_peca FROM first_data.pecas"); 
$pecas_banco = $stmt_pecas->fetchAll(PDO::FETCH_ASSOC);
$mapeamento_pecas = [];
foreach ($pecas_banco as $item) {
    $mapeamento_pecas[$item['nome_tipo']] = [
        'id'    => $item['id_pecas'],
        'grupo' => $item['grupo_peca'] ?? ''
    ];
}

$id_peca = $mapeamento_pecas[$opcao_selecionada]['id'] ?? null;
$grupo   = $mapeamento_pecas[$opcao_selecionada]['grupo'] ?? '';
$erro    = "";

// Processamento do Formulário (POST)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $total     = filter_input(INPUT_POST, 'total', FILTER_VALIDATE_INT);
    $aprovadas = filter_input(INPUT_POST, 'aprovadas', FILTER_VALIDATE_INT);
    $lote      = filter_input(INPUT_POST, 'lote', FILTER_SANITIZE_SPECIAL_CHARS) ?? "";

    if (empty($opcao_selecionada) || !isset($mapeamento_pecas[$opcao_selecionada])) {
        $erro = "Escolha um tipo de peça válido!";
    } elseif ($total === false || $aprovadas === false || $total < 0 || $aprovadas < 0) {
        $erro = "Informe valores numéricos válidos e positivos!";
    } elseif ($aprovadas > $total) {
        $erro = "O número de peças aprovadas não pode ser maior que o total!";
    }

    if (empty($erro)) {
        $rejeitadas = $total - $aprovadas;

        $stmt = $pdo->prepare("INSERT INTO pecas (quantidade_pecas, pecas_aprovadas, pecas_reprovadas, lote, id_pecas, grupo_peca, nome_tipo, usuario, funcao, id_usuario) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$total, $aprovadas, $rejeitadas, $lote, $id_peca, $grupo, $opcao_selecionada, $nome, $func, $id_usuario])) {
            $_SESSION["mensagem_sucesso"] = "Inspeção concluída com sucesso!";
            header("Location: inspecao.php?opicao=" . urlencode($opcao_selecionada));
            exit();
        } else {
            $erro = "Erro ao gravar a inspeção no banco de dados.";
        }
    }
}

// Busca as instruções de verificação
$inspecoes = [];
if ($id_peca) {
    $stmt = $pdo->prepare("SELECT id_peca, instrucao FROM first_data.instrucao WHERE id_peca = ?");
    $stmt->execute([$id_peca]);
    $inspecoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Opções do Select
$stmt3 = $pdo->query("SELECT DISTINCT nome_tipo FROM first_data.pecas");
$nome_peca = $stmt3->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panthers Cars - Inspeção</title>
    <link rel="stylesheet" href="../../FRONT-END/CSS/TELAS-ADMIN/inspecao.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="main">
        <!-- SIDEBAR -->
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
            </div>
            
            <div class="sidebar-section" style="<?= htmlspecialchars($dd) ?>">
                <div class="sidebar-section-title">Administração</div>
                <a href="./editar_inspe.php" class="nav-item"><i class="fas fa-edit"></i> Editar Inspeção</a>
                <a href="./funcionarios.php" class="nav-item"><i class="fas fa-users"></i> Funcionários</a>
            </div>
            
            <div class="sidebar-section">
                <div class="sidebar-section-title">Registros Inspeção</div>
                <a href="historico_inspecao.php" class="nav-item"><i class="fas fa-clock"></i> Histórico Peças</a>
                <a href="./inventario.php" class="nav-item"><i class="fas fa-boxes"></i> Inventário</a>
            </div>

            <div class="sidebar-footer">
                <div class="avatar"><?= htmlspecialchars(strtoupper($nome[0] ?? 'U')) ?></div>
                <div class="user-info">
                    <p><?= htmlspecialchars($nome) ?></p>
                    <span><?= htmlspecialchars($func) ?></span>
                </div>
            </div>
        </aside>

        <!-- TOPBAR -->
        <header class="topbar">
            <h2 id="topbar-title">Inspeção</h2>
            <a class="topbar-btn ghost" href="../../BACK-END/AUTH/logout.php">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </header>

        <div class="main-content">
            <?php if (!empty($mensagem_sucesso)): ?>
                <div style="background-color: #d4edda; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #c3e6cb; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($mensagem_sucesso) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($erro)): ?>
                <div style="background-color: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c6cb; font-weight: 500;">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <!-- FILTRO DE ITEM -->
            <form action="inspecao.php" method="GET">
                <div class="filter-bar">
                    <label for="opicao">Item:</label>
                    <select name="opicao" id="opicao" onchange="this.form.submit()">
                        <option value="" disabled <?= ($opcao_selecionada === '') ? 'selected' : '' ?>>
                            Selecione uma opção
                        </option>
                        <?php foreach ($nome_peca as $nome_pecas): ?>
                            <option value="<?= htmlspecialchars($nome_pecas["nome_tipo"]) ?>" <?= ($opcao_selecionada === $nome_pecas["nome_tipo"]) ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst(str_replace('_', ' ', $nome_pecas["nome_tipo"]))) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn-reload">
                        <i class="fas fa-rotate-right"></i> Recarregar
                    </button>

                    <a href="./scanner.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Voltar ao Scanner
                    </a>
                </div>
            </form>

            <!-- CHECKLIST -->
            <div class="checklist-card">
                <div class="checklist-header">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Itens de Verificação</h3>
                </div>
                <div class="checklist-body">
                    <?php if (!empty($inspecoes)): ?>
                        <?php $contador = 1; foreach ($inspecoes as $inspecao): ?>
                            <div class="checklist-item">
                                <div class="item-number"><?= $contador++; ?></div>
                                <span class="item-text"><?= htmlspecialchars($inspecao["instrucao"]); ?></span>
                                <div class="item-check"></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="padding: 15px; color: #666;">Selecione um item para carregar as instruções de inspeção.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- FORMULÁRIO DE ENVIO -->
            <form method="POST">
                <input type="hidden" name="opicao" value="<?= htmlspecialchars($opcao_selecionada) ?>">
                
                <div class="stats-row">
                    <div class="stat-card total">
                        <div class="stat-label">Total</div>
                        <input class="stat-input" type="number" min="0" placeholder="0" name="total" id="total" required>
                    </div>
                    <div class="stat-card aprovadas">
                        <div class="stat-label">Aprovadas</div>
                        <input class="stat-input" type="number" min="0" placeholder="0" name="aprovadas" id="aprovadas" required>
                    </div>
                    <div class="stat-card lote">
                        <div class="stat-label">Lote</div>
                        <input class="stat-input" type="text" placeholder="—" maxlength="10" name="lote">
                    </div>
                    <div class="stat-card reprovadas">
                        <div class="stat-label">Reprovadas</div>
                        <span class="stat-input" id="reprovadas">0</span>
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

<script>
const totalInput = document.getElementById("total");
const aprovadasInput = document.getElementById("aprovadas");
const reprovadasElement = document.getElementById("reprovadas");

function calcularReprovadas() {
    const total = Math.max(0, parseInt(totalInput.value) || 0);
    const aprovadas = Math.max(0, parseInt(aprovadasInput.value) || 0);
    
    const resultado = total - aprovadas;
    reprovadasElement.innerText = resultado < 0 ? 0 : resultado;
}

totalInput.addEventListener("input", calcularReprovadas);
aprovadasInput.addEventListener("input", calcularReprovadas);
</script>
</body>
</html>