<?php
require_once __DIR__ . "/../config/db.php";
$nome_tipo = "";

$stmt = $pdo->query("SELECT * FROM first_data.pecas");
$pecas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panthers Cars - Inventário</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Front-End/CSS/inventario.css">
</head>
<body>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">🚗</div>
        <span class="logo-text">AutoScan<span>Pro</span></span>
    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">Principal</div>
        <a href="./Dashboard.html" class="nav-item" onclick="setActive(this, 'Dashboard')">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="./scanner.php" class="nav-item" onclick="setActive(this, 'Scanner')">
            <i class="fas fa-qrcode"></i> Scanner
        </a>
        <a href="./inventario.php" class="nav-item active" onclick="setActive(this, 'Inventário')">
            <i class="fas fa-boxes"></i> Inventário
        </a>

    </div>

    <div class="sidebar-section">
        <div class="sidebar-section-title">Administração</div>
        <a href="#" class="nav-item" onclick="setActive(this, 'Funcionários')">
            <i class="fas fa-users"></i> Funcionários
        </a>
        <a href="#" class="nav-item" onclick="setActive(this, 'Alertas')">
            <i class="fas fa-bell"></i> Alertas
            <span class="badge">3</span>
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

<!-- MAIN -->
<div class="main">
    <!-- TOPBAR -->
    <header class="topbar">
        <button class="topbar-menu-btn" onclick="toggleSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <h2 id="topbar-title">Inventário</h2>
        <div class="topbar-actions">
            <button class="topbar-btn red" onclick="showToast('🔴 Status: Online')">
                <i class="fas fa-circle" style="font-size:10px"></i>
            </button>
            <button class="topbar-btn ghost" onclick="showToast('🔔 3 alertas pendentes')" style="position:relative">
                <i class="fas fa-bell"></i>
                <span class="notif-dot"></span>
            </button>
            <button class="topbar-btn ghost" onclick="showToast('🚪 Saindo...')">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </div>
    </header>

    <!-- CONTENT -->
    <div class="content">
        <div class="page-header">
            <div>
                <h1>Inventário de Peças</h1>
                <p>Catálogo completo do estoque</p>
            </div>
            <div class="view-toggle">
                <button class="view-btn active" id="gridBtn" onclick="setView('grid')" title="Grade">
                    <i class="fas fa-th"></i>
                </button>
                <button class="view-btn" id="listBtn" onclick="setView('list')" title="Lista">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>

        <!-- TOOLBAR -->
        <div class="toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Buscar peças..." id="searchInput" oninput="filterCards()">
            </div>
            <button class="classify-btn" onclick="toggleSort()">
                <i class="fas fa-sliders-h"></i> Classificar
            </button>
        </div>

        <!-- SORT DROPDOWN -->
        <div class="sort-dropdown" id="sortDropdown">
            <div class="sort-option" onclick="sortCards('nome')">Nome A–Z</div>
            <div class="sort-option" onclick="sortCards('qtd-asc')">Qtd: Menor → Maior</div>
            <div class="sort-option" onclick="sortCards('qtd-desc')">Qtd: Maior → Menor</div>
            <div class="sort-option" onclick="sortCards('status')">Por Status</div>
        </div>

        <!-- FILTER TAGS -->
        <div class="filter-tags">
            <button class="tag-btn active" onclick="filtrar('todos')">Todos</button>
            <button class="tag-btn"onclick="filtrar('motor')" >Motor</button>
            <button class="tag-btn" onclick="filtrar('transmissao')">Transmissão</button>
            <button class="tag-btn" onclick="filtrar('suspensao')">Suspensão</button>
            <button class="tag-btn" onclick="filtrar('freios')">Freios</button>
            <button class="tag-btn" onclick="filtrar('eletrico')">Elétrica</button>
            <button class="tag-btn" onclick="filtrar('direcao')">Direção</button>
            <button class="tag-btn" onclick="filtrar('escapamento')">Escapamento</button>
        </div>

        <!-- CARDS GRID -->
        <div class="cards-grid" id="cardsContainer">
        <?php foreach($pecas as $peca) { ?>
        <div class="card"> 
            <img class="card-banner grad-red" src="<?php echo $peca["ima_peca"]; ?>" alt="<?php echo $peca["nome_tipo"]; ?>"> 
            <div class="card-body">
            <h1 class="card-title"><?php echo $peca["nome_tipo"]; ?></h1> 
            
     
            <div class="card-meta"> 
                <i class="fas fa-industry"></i>
                <p class="card-meta"><?php echo $peca["grupo_peca"]; ?></p> 
                <span class="dot"></span>
                <i class="fas fa-map-marker-alt"></i> 
                <p class="card-meta"><?php echo $peca["local_peca"]; ?></p> 
            </div> 

            <div class="card-footer">
                <span>Qtd: <span><?php echo $peca["quantidade_pecas"]; ?> </span>   </span>

                <button class="info-btn" onclick="openModal('Motor V8 5.0L','MOT-001','Motor','A1-01',15,'bom')">
                            <i class="fas fa-info"></i>
                        </button>
            </div>
            </div>
        </div>
        <?php } ?>
          
<!-- MODAL -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModalOutside(event)">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTitle">Detalhes da Peça</h3>
            <button class="modal-close" onclick="closeModal()">✕</button>
        </div>
        <div class="modal-row">
            <span class="label">Código</span>
            <span class="value" id="mCode">—</span>
        </div>
        <div class="modal-row">
            <span class="label">Categoria</span>
            <span class="value" id="mCat">—</span>
        </div>
        <div class="modal-row">
            <span class="label">Localização</span>
            <span class="value" id="mLoc">—</span>
        </div>
        <div class="modal-row">
            <span class="label">Quantidade</span>
            <span class="value" id="mQty">—</span>
        </div>
        <div class="modal-row">
            <span class="label">Status</span>
            <span class="value" id="mStatus">—</span>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>

function filtrar(tipo){

    
}


/*
function filtrar(tipo) {
     // 1. Usamos querySelectorAll para pegar TODOS os cards da tela
     // Substitua '.card-item' pela classe real da DIV principal do seu card
     const cards = document.querySelectorAll(".card-meta");
     const corpo = document.querySelectorAll(".card");

     console.log(tipo);
     
     // 2. Corrigido para forEach com 'E' maiúsculo
     corpo.forEach((card) => {
        const tituloElemento = card.querySelector(".card-title");
        
        const textoTitulo = tituloElemento ? tituloElemento.textContent.trim().toLowerCase() : "";

        if(tipo === "motor" || textoTitulo === tipo){
            
            card.style.display = "block";
            
        }else{
            card.style.display = "none";
        }

     });
}
*/
</script>
</body>
</html>