<?php 
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); } 
require_once __DIR__ . '/../config/db.php'; 
require_once __DIR__ . '/../config/auth.php'; 
require_login(); 

$nome_sessao = $_SESSION['user'] ?? ''; 

$stmt = $pdo->prepare("SELECT usuario_nome, isAdmin FROM first_data.usuarios WHERE usuario_nome = ?"); 
$stmt->execute([$nome_sessao]); 
$user = $stmt->fetch(PDO::FETCH_ASSOC); 

if ($user) { 
    $nome = $user['usuario_nome']; 
    $func = $user['isAdmin'] ? 'Administrador' : 'Funcionario'; 
} else { 
    $nome = 'Usuário não encontrado'; 
} 

if($func === "Funcionario"){
    $dd = "display: none;";
}else{
    $dd = "";
}

// Captura os parâmetros da URL
$busca = $_GET['busca'] ?? '';
$meses = $_GET['meses'] ?? ''; 
$paginaAtual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;

// Define quantos registros vão aparecer por página (ajuste se quiser mais ou menos)
$limitePorPagina = 50; 
$offset = ($paginaAtual - 1) * $limitePorPagina;

// Cláusula WHERE base (isolada para usarmos no COUNT e no SELECT)
$whereSql = "WHERE usuario IS NOT NULL AND usuario <> '' 
        AND funcao IS NOT NULL AND funcao <> '' 
        AND nome_tipo IS NOT NULL AND nome_tipo <> '' 
        AND quantidade_pecas IS NOT NULL AND quantidade_pecas > 0 
        AND pecas_aprovadas IS NOT NULL 
        AND lote IS NOT NULL AND lote > 0 
        AND data_insp IS NOT NULL 
        AND id_usuario != 0";

$params = [];

// 1. FILTRO DE BUSCA
if (!empty($busca)) {
    $whereSql .= " AND (usuario LIKE :busca OR nome_tipo LIKE :busca)";
    $params[':busca'] = '%' . $busca . '%'; // Adicionado % no início para buscar em qualquer lugar do nome
}

// 2. FILTRO DE DATA
if (in_array($meses, ['3', '6', '12'])) {
    $mesesInt = (int)$meses;
    $whereSql .= " AND data_insp >= DATE_SUB(NOW(), INTERVAL {$mesesInt} MONTH)";
}

// 3. CONTA TOTAL DE REGISTROS (Para a paginação)
$sqlCount = "SELECT COUNT(*) FROM first_data.pecas $whereSql";
$stmtCount = $pdo->prepare($sqlCount);
foreach ($params as $key => $value) {
    $stmtCount->bindValue($key, $value, PDO::PARAM_STR);
}
$stmtCount->execute();
$totalRegistros = $stmtCount->fetchColumn();
$totalPaginas = ceil($totalRegistros / $limitePorPagina);

// 4. CONSULTA PRINCIPAL COM LIMITE (Paginação)
$sql = "SELECT usuario, funcao, nome_tipo, quantidade_pecas, pecas_aprovadas, pecas_reprovadas, lote, data_insp, id_usuario 
        FROM first_data.pecas 
        $whereSql 
        ORDER BY data_insp DESC 
        LIMIT $limitePorPagina OFFSET $offset"; 

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->execute();
$historicos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panthers Cars - Histórico Inspeções</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="../../FRONT-END/CSS/TELAS-ADMIN/historico_inspecao.css" />
  </head>
  <body>
    <!-- ===== SIDEBAR ===== -->
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
        <a href="./inventario.php" class="nav-item"><i class="fas fa-boxes"></i> Inventário</a>
      </div>
  
      <div class="sidebar-section" style="<?= htmlspecialchars($dd) ?>">
        <div class="sidebar-section-title">Administração</div>
        <a href="./funcionarios.php" class="nav-item"><i class="fas fa-users"></i> Funcionários</a>
        <a href="./editar_inspe.php" class="nav-item"><i class="fas fa-edit"></i> Editar Inspeção</a>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Registros Inspeção</div>
        <a href="historico_inspecao.php" class="nav-item active"><i class="fas fa-clock"></i> Histórico Peças</a>
      </div>

      <div class="sidebar-footer">
        <div class="avatar"><?= strtoupper($nome[0] ?? 'U') ?></div>
        <div class="user-info">
            <p><?= htmlspecialchars($nome) ?></p>
            <span><?= htmlspecialchars($func) ?></span>
        </div>
      </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <div class="main">
      <!-- TOPBAR -->
      <header class="topbar">
        <h2 id="topbar-title">Histórico Inspeções</h2>
        <div class="topbar-actions">
            <a class="topbar-btn ghost" href="../AUTH/logout.php">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
      </header>

      <!-- AQUI É A CORREÇÃO PRINCIPAL NO HTML (div content) -->
      <div class="content">

        <!-- FILTROS -->
        <form method="GET">
          <div class="table-card">
            <div class="search-bar">
              <span class="search-icon">🔍</span>
              <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Buscar por nome ou peça..." />
            </div>
            
            <div class="filter-tags">
              <button class="tag-btn active" type="submit">Pesquisar</button>
              
              <a href="?busca=<?= urlencode($busca) ?>" class="tag-btn <?= empty($_GET['meses']) ? 'active' : '' ?>">Todos</a>
              <a href="?busca=<?= urlencode($busca) ?>&meses=3" class="tag-btn <?= ($_GET['meses'] ?? '') == '3' ? 'active' : '' ?>">3 meses</a>
              <a href="?busca=<?= urlencode($busca) ?>&meses=6" class="tag-btn <?= ($_GET['meses'] ?? '') == '6' ? 'active' : '' ?>">6 meses</a>
              <a href="?busca=<?= urlencode($busca) ?>&meses=12" class="tag-btn <?= ($_GET['meses'] ?? '') == '12' ? 'active' : '' ?>">12 meses</a>
              
              <!-- Mantém a página atual ao filtrar -->
              <input type="hidden" name="pagina" value="1"> 
            </div>
          </div>
        </form>

        <!-- TABELA -->
        <div class="table-func">
          <table>
            <thead>
              <tr>
                <th>Funcionário</th>
                <th>Função</th>
                <th>Peça</th>
                <th>Total</th>
                <th>Aprovadas</th>
                <th>Reprovadas</th>
                <th>Lote</th>
                <th>Data</th>
              </tr>
            </thead>
            <tbody>
            <?php if(empty($historicos)): ?>
              <tr>
                  <td colspan="8" style="text-align: center; padding: 20px;">Nenhum registro encontrado.</td>
              </tr>
            <?php else: ?>
              <?php foreach($historicos as $historico) { ?>
                <tr>
                  <td>
                    <div class="employee-cell">
                      <div class="employee-avatar"><?= strtoupper($historico["usuario"][0] ?? 'U') ?></div>
                      <div>
                        <div class="employee-name"><?=htmlspecialchars($historico["usuario"])?></div>
                        <div class="employee-id"><?=htmlspecialchars($historico["id_usuario"])?></div>
                      </div>
                    </div>
                  </td>
                  <td><?=htmlspecialchars($historico["funcao"])?></td>
                  <td><?=htmlspecialchars($historico["nome_tipo"])?></td>
                  <td><?=htmlspecialchars($historico["quantidade_pecas"])?></td>
                  <td><?=htmlspecialchars($historico["pecas_aprovadas"])?></td>
                  <td><?=htmlspecialchars($historico["pecas_reprovadas"])?></td>
                  <td><?=htmlspecialchars($historico["lote"])?></td>
                  <td><?= date('d/m/Y H:i', strtotime($historico["data_insp"])) ?></td>
                </tr>
              <?php } ?>
            <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- BOTÕES DE PAGINAÇÃO -->
        <?php if ($totalPaginas > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <a href="?busca=<?= urlencode($busca) ?>&meses=<?= urlencode($meses) ?>&pagina=<?= $i ?>" 
                   class="page-link <?= $i === $paginaAtual ? 'active' : '' ?>">
                   <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

      </div> <!-- FIM DA DIV CONTENT -->
    </div> <!-- FIM DA DIV MAIN -->
  </body>
</html>