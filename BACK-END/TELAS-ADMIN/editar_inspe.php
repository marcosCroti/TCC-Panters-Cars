<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_login();

// Feedback do sistema
$mensagem_sucesso = $_SESSION["mensagem_sucesso"] ?? "";
$erro = $_SESSION["mensagem_erro"] ?? "";
unset($_SESSION["mensagem_sucesso"], $_SESSION["mensagem_erro"]);

// Dados do usuário logado
$nome_sessao = $_SESSION["user"] ?? ""; 
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

// Opção de peça selecionada (GET ou POST)
$opcao_selecionada = $_REQUEST['opicao'] ?? '';

// Mapeamento de Peças -> ID
$mapeamento_ids = [
    'pistao'      => 1,
    'pastilha'    => 2,
    'bateria'     => 5,
    'amortecedor' => 3,
    'para_choque' => 4
];

$id_peca = $mapeamento_ids[$opcao_selecionada] ?? null;

// ==========================================
// PROCESSAMENTO DAS AÇÕES (POST)
// ==========================================
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $acao = $_POST["acao"] ?? "";

    // 1. ADICIONAR NOVA INSTRUÇÃO
    if ($acao === "adicionar") {
        $nova_instrucao = trim($_POST["nova_instrucao"] ?? "");

        if (!$id_peca) {
            $_SESSION["mensagem_erro"] = "Selecione um item/peça válido antes de adicionar!";
        } elseif (empty($nova_instrucao)) {
            $_SESSION["mensagem_erro"] = "A instrução não pode ser vazia!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO first_data.instrucao (id_peca, instrucao) VALUES (?, ?)");
            if ($stmt->execute([$id_peca, $nova_instrucao])) {
                $_SESSION["mensagem_sucesso"] = "Instrução adicionada com sucesso!";
            } else {
                $_SESSION["mensagem_erro"] = "Erro ao adicionar instrução.";
            }
        }
    }

    // 2. EDITAR INSTRUÇÃO EXISTENTE
    elseif ($acao === "editar") {
        $id_instrucao = $_POST["id_instrucao"] ?? null;
        $texto_editado = trim($_POST["instrucao_editada"] ?? "");

        if (!empty($id_instrucao) && !empty($texto_editado)) {
            // Nota: ajuste a chave primária 'id' da tabela 'instrucao' caso o nome da coluna seja diferente no seu banco (ex: id_instrucao)
            $stmt = $pdo->prepare("UPDATE first_data.instrucao SET instrucao = ? WHERE id = ?");
            if ($stmt->execute([$texto_editado, $id_instrucao])) {
                $_SESSION["mensagem_sucesso"] = "Instrução atualizada com sucesso!";
            } else {
                $_SESSION["mensagem_erro"] = "Erro ao atualizar a instrução.";
            }
        } else {
            $_SESSION["mensagem_erro"] = "Preencha todos os campos para editar.";
        }
    }

    // Redireciona para evitar reenvio do formulário no F5
    header("Location: editar_inspe.php?opicao=" . urlencode($opcao_selecionada));
    exit();
}

// ==========================================
// PROCESSAMENTO DA EXCLUSÃO (GET)
// ==========================================
if (isset($_GET["action"]) && $_GET["action"] === "deletar") {
    $id_deletar = $_GET["id"] ?? null;

    if ($id_deletar) {
        $stmt = $pdo->prepare("DELETE FROM first_data.instrucao WHERE id = ?");
        if ($stmt->execute([$id_deletar])) {
            $_SESSION["mensagem_sucesso"] = "Instrução removida com sucesso!";
        } else {
            $_SESSION["mensagem_erro"] = "Erro ao remover instrução.";
        }
    }

    header("Location: editar_inspe.php?opicao=" . urlencode($opcao_selecionada));
    exit();
}

// ==========================================
// BUSCAR INSTRUÇÕES
// ==========================================
$inspecoes = [];
if ($id_peca) {
    // Se a chave primária da tabela 'instrucao' tiver outro nome, altere 'id' na query abaixo
    $stmt = $pdo->prepare("SELECT id, id_peca, instrucao FROM first_data.instrucao WHERE id_peca = ? ORDER BY id ASC");
    $stmt->execute([$id_peca]);
    $inspecoes = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panthers Cars - Editar Inspeção</title>
    <link rel="stylesheet" href="../../FRONT-END/CSS/TELAS-ADMIN/editar_inspecao.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Estilos auxiliares para os Modais de Edição e Mensagens */
        .alert-success { background-color: #d4edda; color: #155724; padding: 12px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #c3e6cb; }
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 6px; margin-bottom: 15px; border: 1px solid #f5c6cb; }
        .btn-action { background: none; border: none; cursor: pointer; font-size: 1.1rem; margin: 0 4px; }
        .modal-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-card { background: #fff; padding: 20px; border-radius: 8px; width: 400px; max-width: 90%; }
        .modal-card h3 { margin-top: 0; margin-bottom: 15px; }
        .modal-card input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .modal-buttons { display: flex; justify-content: flex-end; gap: 10px; }
        .btn-cancel { background: #6c757d; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
        .btn-save { background: #28a745; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>

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
            <a href="./inspecao.php" class="nav-item"><i class="fas fa-clipboard"></i> Inspeção</a>
            <a href="./editar_inspe.php" class="nav-item active"><i class="fas fa-edit"></i> Editar Inspeção</a>
            <a href="./inventario.php" class="nav-item"><i class="fas fa-boxes"></i> Inventário</a>
        </div>

        <div class="sidebar-section">
            <div class="sidebar-section-title">Administração</div>
            <a href="./funcionarios.php" class="nav-item"><i class="fas fa-users"></i> Funcionários</a>
        </div>

        <div class="sidebar-footer">
            <div class="avatar"><?= strtoupper($nome[0] ?? 'U') ?></div>
            <div class="user-info">
                <p><?= htmlspecialchars($nome) ?></p>
                <span><?= htmlspecialchars($func) ?></span>
            </div>
        </div>
    </aside>

    <div class="main">
        <!-- TOPBAR -->
        <header class="topbar">
            <h2 id="topbar-title">Editar Inspeção</h2>
            <div class="topbar-actions">
                <a class="topbar-btn ghost" href="../../BACK-END/AUTH/logout.php">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <div class="main-content">
            
            <!-- EXIBIÇÃO DE ALERTAS -->
            <?php if (!empty($mensagem_sucesso)): ?>
                <div class="alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($mensagem_sucesso) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($erro)): ?>
                <div class="alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <!-- FILTRO DE ITENS -->
            <form method="GET" action="editar_inspe.php">
                <div class="filter-bar">
                    <label>Item:</label>
                    <select name="opicao">
                        <option value="" disabled <?= ($opcao_selecionada === '') ? 'selected' : '' ?>>Selecione</option>
                        <option value="pistao" <?= ($opcao_selecionada === 'pistao') ? 'selected' : '' ?>>Pistão</option>
                        <option value="pastilha" <?= ($opcao_selecionada === 'pastilha') ? 'selected' : '' ?>>Pastilha</option>
                        <option value="bateria" <?= ($opcao_selecionada === 'bateria') ? 'selected' : '' ?>>Bateria</option>
                        <option value="amortecedor" <?= ($opcao_selecionada === 'amortecedor') ? 'selected' : '' ?>>Amortecedor</option>
                        <option value="para_choque" <?= ($opcao_selecionada === 'para_choque') ? 'selected' : '' ?>>Para Choque</option>
                    </select>
                    
                    <button type="submit" class="btn-reload">
                        <i class="fas fa-rotate-right"></i> Recarregar
                    </button>

                    <a href="./scanner.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Voltar ao Scanner
                    </a>
                </div>
            </form>

            <!-- ADICIONAR NOVA INSTRUÇÃO -->
            <form method="POST" action="editar_inspe.php">
                <input type="hidden" name="acao" value="adicionar">
                <input type="hidden" name="opicao" value="<?= htmlspecialchars($opcao_selecionada) ?>">
                
                <div class="search-box">
                    <i class="fas fa-clipboard-list"></i>
                    <input type="text" name="nova_instrucao" placeholder="Digite a nova instrução..." required>
                    <button class="btn-add" type="submit" title="Adicionar instrução">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </form>

            <!-- LISTA DE INSTRUÇÕES DA PEÇA -->
            <div class="checklist-card">
                <div class="checklist-header">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>Itens de Verificação</h3>
                </div>
                <div class="checklist-body">
                    <?php if (empty($opcao_selecionada)): ?>
                        <p style="padding: 15px;">Selecione um item acima para exibir ou cadastrar instruções.</p>
                    <?php elseif (empty($inspecoes)): ?>
                        <p style="padding: 15px;">Nenhuma instrução cadastrada para este item.</p>
                    <?php else: ?>
                        <?php $contador = 1; foreach ($inspecoes as $inspecao): ?>
                            <div class="checklist-item">
                                <div class="item-number"><?= $contador++; ?></div>
                                <span class="item-text"><?= htmlspecialchars($inspecao["instrucao"]); ?></span>
                                <div class="actions-cell">
                                    <!-- Botão Editar -->
                                    <button class="btn-action" title="Editar" onclick="abrirModalEdicao('<?= $inspecao['id'] ?>', '<?= htmlspecialchars(addslashes($inspecao['instrucao'])) ?>')">
                                        ✏️
                                    </button>
                                    <!-- Botão Excluir -->
                                    <a class="btn-action" title="Excluir" href="editar_inspe.php?action=deletar&id=<?= $inspecao['id'] ?>&opicao=<?= urlencode($opcao_selecionada) ?>" onclick="return confirm('Deseja realmente excluir esta instrução?');">
                                        🗑️
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL DE EDIÇÃO -->
    <div class="modal-overlay" id="modalEdicao">
        <div class="modal-card">
            <h3>Editar Instrução</h3>
            <form method="POST" action="editar_inspe.php">
                <input type="hidden" name="acao" value="editar">
                <input type="hidden" name="opicao" value="<?= htmlspecialchars($opcao_selecionada) ?>">
                <input type="hidden" name="id_instrucao" id="edit_id">

                <input type="text" name="instrucao_editada" id="edit_texto" required>

                <div class="modal-buttons">
                    <button type="button" class="btn-cancel" onclick="fecharModalEdicao()">Cancelar</button>
                    <button type="submit" class="btn-save">Salvar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function abrirModalEdicao(id, texto) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_texto').value = texto;
            document.getElementById('modalEdicao').style.display = 'flex';
        }

        function fecharModalEdicao() {
            document.getElementById('modalEdicao').style.display = 'none';
        }
    </script>
</body>
</html>
