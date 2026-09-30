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

// Mantemos a sua regra de negócio estrita para remover os testes (!= 0)
$sql = "SELECT usuario, funcao, nome_tipo, quantidade_pecas, pecas_aprovadas, pecas_reprovadas, lote, data_insp, id_usuario 
        FROM first_data.pecas 
        WHERE usuario IS NOT NULL AND usuario <> '' 
        AND funcao IS NOT NULL AND funcao <> '' 
        AND nome_tipo IS NOT NULL AND nome_tipo <> '' 
        AND quantidade_pecas IS NOT NULL AND quantidade_pecas > 0 
        AND pecas_aprovadas IS NOT NULL 
        AND lote IS NOT NULL AND lote > 0 
        AND data_insp IS NOT NULL 
        AND id_usuario != 0"; 

$params = [];

if (!empty($busca)) {
    $sql .= " AND (usuario LIKE :busca OR nome_tipo LIKE :busca)";
    $params[':busca'] = $busca . '%'; // Removida a '%' do início
}

// 2. FILTRO DE DATA (Concatenado de forma segura após in_array)
if (in_array($meses, ['3', '6', '12'])) {
    $mesesInt = (int)$meses;
    $sql .= " AND data_insp >= DATE_SUB(NOW(), INTERVAL {$mesesInt} MONTH)";
}

$stmt = $pdo->prepare($sql);

// Bind genérico apenas para os parâmetros restantes (como a busca)
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
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="../../FRONT-END/CSS/TELAS-ADMIN/historico_inspecao.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
  </head>
  <body>
    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-logo">
        <!-- <div class="logo-icon">🚗</div> -->
         <div class="logo" id="logo-icon">
             <img src="../../FRONT-END/LOGIN/IMG/LOGO.png" alt="logo" id="logo">
         </div>
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
        href="./inspecao.php"
        class="nav-item"
        onclick="setActive(this, 'Inspeção')"
        >
        <i class="fas fa-clipboard"></i> Inspeção
      </a>
      <a
      href="./inventario.php"
      class="nav-item"
      onclick="setActive(this, 'Inventário')"
      >
      <i class="fas fa-boxes"></i> Inventário
    </a>
  </div>
  
  <div class="sidebar-section" style="<?= htmlspecialchars($dd) ?>">
    <div class="sidebar-section-title">Administração</div>
    <a
    href="./funcionarios.php" 
    class="nav-item" onclick="setActive(this, 'Funcionários')"
    
    >
    <i class="fas fa-users"></i> Funcionários
  </a>
  <a
  href="./editar_inspe.php"
  class="nav-item"
  onclick="setActive(this, 'Editar Inspeção')"
  >
  <i class="fas fa-edit"></i> Editar Inspeção
</a>

        <!-- <a href="#" class="nav-item" onclick="setActive(this, 'Alertas')">
          <i class="fas fa-bell"></i> Alertas
          <span class="badge">3</span>
        </a> -->
      </div>

            <div class="sidebar-section">
    <div class="sidebar-section-title">Registros Inspeção</div>
    <a href="historico_inspecao.php" class="nav-item active" onclick="setActive(this, 'Funcionários')">
        <i class="fas fa-clock"></i> Histórico Peças
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
        <!-- <button class="topbar-menu-btn" onclick="toggleSidebar()">
          <i class="fas fa-bars"></i>
        </button> -->
        <h2 id="topbar-title">Histórico Inspeções</h2>
        <div class="topbar-actions">
          <!-- <button
            class="topbar-btn ghost"
            onclick="showToast('🔔 3 alertas pendentes')"
            style="position: relative"
          >
            <i class="fas fa-bell"></i>
            <span class="notif-dot"></span>
          </button> -->
            <a class="topbar-btn ghost" href="../AUTH/logout.php">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
      </header>

        <form method="GET">
      <div class="table-card">

        <div class="search-bar">
          <span class="search-icon">🔍</span>
<input 
  type="text" 
  name="busca" 
  value="<?= htmlspecialchars($busca) ?>" 
  placeholder="Buscar por nome ou peça..." 
/>
</div>
        
      
      
      <!-- FILTER TAGS -->
      <div class="filter-tags">
        <button class="tag-btn active" type="submit">
          Pesquisar
          </button>
        <div class="filter-tags">
          <!-- Botão Todos (remove o filtro de meses) -->
          <a href="?busca=<?= urlencode($busca) ?>" class="tag-btn <?= empty($_GET['meses']) ? 'active' : '' ?>">Todos</a>
          
          <!-- Botões de meses -->
          <a href="?busca=<?= urlencode($busca) ?>&meses=3" class="tag-btn <?= ($_GET['meses'] ?? '') == '3' ? 'active' : '' ?>">3 meses</a>
          <a href="?busca=<?= urlencode($busca) ?>&meses=6" class="tag-btn <?= ($_GET['meses'] ?? '') == '6' ? 'active' : '' ?>">6 meses</a>
          <a href="?busca=<?= urlencode($busca) ?>&meses=12" class="tag-btn <?= ($_GET['meses'] ?? '') == '12' ? 'active' : '' ?>">12 meses</a>
      </div>
          
                  </form>

        </div>

    </div>
        
      

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
            
            <?php foreach($historicos as $historico) { ?>
              <tr>
                <td>
                  <div class="employee-cell">
                    <div class="employee-avatar"><?= strtoupper($historico["usuario"][0]) ?></div>
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
                <td><?=htmlspecialchars($historico["data_insp"])?></td>

</tr>
<?php } ?>            
             
        </div>


                              
        



                              



    <!-- ========================
         MODAL NOVO FUNCIONÁRIO
    ======================== -->
    <!-- <div
      class="modal-overlay"
      id="modalOverlay"
      onclick="fecharModalFora(event)"
    >
      <div class="modal" id="modalBox">
        <div class="modal-header">
          <div class="modal-header-left">
            <span class="modal-icon">👤</span>
            Novo Funcionário
          </div>
          <button class="modal-close" onclick="fecharModal()">✕</button>
        </div>

        <div class="modal-body">
          <div class="modal-row">
            <div class="modal-field">
              <label>Nome Completo</label>
              <input type="text" placeholder="Nome completo" />
            </div>
            <div class="modal-field">
              <label>CPF</label>
              <input type="text" placeholder="000.000.000-00" />
            </div>
          </div>

          <div class="modal-row">
            <div class="modal-field">
              <label>E-mail</label>
              <input type="email" placeholder="email@empresa.com" />
            </div>
            <div class="modal-field">
              <label>Senha</label>
              <input type="password" placeholder="Senha de acesso" />
            </div>
          </div>

          <div class="modal-row">
            <div class="modal-field">
              <label>Setor</label>
              <select>
                <option value="" disabled selected>Selecione o setor</option>
                <option value="montagem">Montagem</option>
                <option value="qualidade">Qualidade</option>
                <option value="expedicao">Expedição</option>
                <option value="inspecao">Inspeção</option>
              </select>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button class="btn-cancelar" onclick="fecharModal()">Cancelar</button>
          <button class="btn-cadastrar">💾 Cadastrar</button>
        </div>
      </div>
    </div>

    <script>
      function abrirModal() {
        document.getElementById("modalOverlay").classList.add("active");
      }

      function fecharModal() {
        document.getElementById("modalOverlay").classList.remove("active");
      }

      function fecharModalFora(event) {
        if (event.target === document.getElementById("modalOverlay")) {
          fecharModal();
        }
      }

      // Fechar com ESC
      document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
          fecharModal();
        }
      });
    </script> -->


<script>
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const main = document.querySelector(".main");

    sidebar.classList.toggle("closed");
    main.classList.toggle("expanded");
  }
</script>



  </body>
</html>

