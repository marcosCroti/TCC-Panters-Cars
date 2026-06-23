<?php
require_once __DIR__ . "/../config/db.php";

/*
    Inventário dinâmico com PDO
    Inventário dinâmico com filtros no SELECT do banco:
    - ?filtro=todos       => SELECT * FROM pecas
    - ?filtro=motor       => WHERE grupo_peca = "Motor e Transmissão"
    - ?filtro=transmissao => WHERE grupo_peca = "Motor e Transmissão"
    - ?filtro=suspensao   => WHERE grupo_peca = "Suspensão e Direção"

    O restante do visual foi mantido o mais próximo possível do arquivo original.
*/

$filtro = $_GET["filtro"] ?? "todos";

$grupos = [
    "motor"       => "Motor e Transmissão",
    "transmissao" => "Motor e Transmissão",
    "suspensao"   => "Suspensão e Direção",
    "freios"   => "Freios", 
    "eletrica"   => "Eletrica", 
    "carroca"   => "Carroceria/Acabamento", 
    "seguranca"   => "Componentes de Segurança", 
];

if ($filtro !== "todos" && isset($grupos[$filtro])) {
    $stmt = $pdo->prepare("SELECT * FROM first_data.pecas WHERE grupo_peca = ? ORDER BY id_pecas ASC");
    $stmt->execute([$grupos[$filtro]]);
} else {
    $filtro = "todos";
    $stmt = $pdo->query("SELECT * FROM first_data.pecas ORDER BY id_pecas ASC");
}

$pecas = $stmt->fetchAll(PDO::FETCH_ASSOC);

function h($valor) {
    return htmlspecialchars((string)$valor, ENT_QUOTES, "UTF-8");
}

function statusPeca($quantidade) {
    if ($quantidade <= 0) {
        return "critico";
    }

    if ($quantidade <= 10) {
        return "atencao";
    }

    return "bom";
}

function textoStatus($status) {
    if ($status === "critico") {
        return "crítico";
    }

    if ($status === "atencao") {
        return "atenção";
    }

    return "bom";
}

function categoriaCard($grupo) {
    if ($grupo === "Motor e Transmissão") {
        return "motor";
    }

    if ($grupo === "Freios") {
        return "freios";
    }

    if ($grupo === "Suspensão e Direção") {
        return "suspensao";
    }

    if ($grupo === "Elétrica") {
        return "eletrica";
    }

    if ($grupo === "Carroceria/Acabamento") {
        return "carroceria";
    }

    if ($grupo === "Componentes de Segurança") {
        return "seguranca";
    }

    if ($grupo === "Escapamento") {
        return "escapamento";
    }

    return "outros";
}

function gradienteCard($grupo) {
    if ($grupo === "Motor e Transmissão") {
        return "grad-red";
    }

    if ($grupo === "Freios") {
        return "grad-purple";
    }

    if ($grupo === "Suspensão e Direção") {
        return "grad-blue";
    }

    if ($grupo === "Elétrica") {
        return "grad-orange";
    }

    if ($grupo === "Carroceria/Acabamento") {
        return "grad-green";
    }

    if ($grupo === "Componentes de Segurança") {
        return "grad-teal";
    }

    return "grad-red";
}

function codigoPeca($id) {
    return "PEC-" . str_pad((string)$id, 3, "0", STR_PAD_LEFT);
}
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
        <a href="./index.php" class="nav-item" onclick="setActive(this, 'Dashboard')">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="./scanner.php" class="nav-item" onclick="setActive(this, 'Scanner')">
            <i class="fas fa-qrcode"></i> Scanner
        </a>
        <a href="./inventario.php?filtro=todos" class="nav-item active" onclick="setActive(this, 'Inventário')">
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
<a href="./../auth/logout.php" class="topbar-btn ghost" style="text-decoration: none;"><i class="fas fa-sign-out-alt"></i></a>
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
            <a href="?filtro=todos"><button class="tag-btn <?= $filtro === 'todos' ? 'active' : '' ?>" type="button">Todos</button></a>
            <a href="?filtro=motor"><button class="tag-btn <?= $filtro === 'motor' ? 'active' : '' ?>" type="button">Motor</button></a>
            <a href="?filtro=transmissao"><button class="tag-btn <?= $filtro === 'transmissao' ? 'active' : '' ?>" type="button">Transmissão</button></a>
            <a href="?filtro=suspensao"><button class="tag-btn <?= $filtro === 'suspensao' ? 'active' : '' ?>" type="button">Suspensão</button></a>
            <a href="?filtro=freios"><button class="tag-btn <?= $filtro === 'freios' ? 'active' : '' ?>" type="button">Freios</button></a>
            <a href="?filtro=eletrica"><button class="tag-btn <?= $filtro === 'eletrica' ? 'active' : '' ?>" type="button">Eletrica</button></a>
            <a href="?filtro=carroca"><button class="tag-btn <?= $filtro === 'carroca' ? 'active' : '' ?>" type="button">Carroceria</button></a>
            <a href="?filtro=seguranca"><button class="tag-btn <?= $filtro === 'seguranca' ? 'active' : '' ?>" type="button">Segurança</button></a>
        </div>

        <!-- CARDS GRID -->
        <div class="cards-grid" id="cardsContainer">

            <?php if (count($pecas) === 0) { ?>
                <div class="card" data-cat="vazio" data-name="Nenhuma peça encontrada" data-qty="0" data-status="critico">
                    <div class="card-banner grad-red">
                        <i class="fas fa-box-open bg-icon"></i>
                        <i class="fas fa-search bg-icon-2"></i>
                        <div class="status-badge">
                            <span class="status-dot critico"></span> vazio
                        </div>
                        <div class="code-badge">---</div>
                    </div>
                    <div class="card-body">
                        <div class="card-title">Nenhuma peça encontrada</div>
                        <div class="card-meta">
                            <i class="fas fa-industry"></i> Sem categoria
                            <span class="dot"></span>
                            <i class="fas fa-map-marker-alt"></i> Sem setor
                        </div>
                        <div class="card-footer">
                            <span class="qty-text">Qtd: <span>0</span></span>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php foreach ($pecas as $peca) { 
                $id = $peca["id_pecas"] ?? "";
                $nome = $peca["nome_tipo"] ?? "";
                $grupo = $peca["grupo_peca"] ?? "";
                $setor = $peca["setor"] ?? "";
                $quantidade = (int)($peca["quantidade_pecas"] ?? 0);
                $status = statusPeca($quantidade);
                $statusTexto = textoStatus($status);
                $cat = categoriaCard($grupo);
                $grad = gradienteCard($grupo);
                $codigo = codigoPeca($id);

                $modalArgs = [
                    $nome,
                    $codigo,
                    $grupo,
                    $setor,
                    $quantidade,
                    $status
                ];
            ?>
                <div class="card" data-cat="<?= h($cat) ?>" data-name="<?= h($nome) ?>" data-qty="<?= h($quantidade) ?>" data-status="<?= h($status) ?>">
                    <div class="card-banner <?= h($grad) ?>">
                        <i class="fas fa-cog bg-icon"></i>
                        <i class="fas fa-wrench bg-icon-2"></i>
                        <div class="status-badge">
                            <span class="status-dot <?= h($status) ?>"></span> <?= h($statusTexto) ?>
                        </div>
                        <div class="code-badge"><?= h($codigo) ?></div>
                    </div>
                    <div class="card-body">
                        <div class="card-title"><?= h($nome) ?></div>
                        <div class="card-meta">
                            <i class="fas fa-industry"></i> <?= h($grupo) ?>
                            <span class="dot"></span>
                            <i class="fas fa-map-marker-alt"></i> <?= h($setor) ?>
                        </div>
                        <div class="card-footer">
                            <span class="qty-text">Qtd: <span><?= h($quantidade) ?></span></span>
                            <button class="info-btn" onclick='openModal(...<?= json_encode($modalArgs, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>)'>
                                <i class="fas fa-info"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php } ?>

        </div>
    </div>
</div>

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
    // ====== SIDEBAR TOGGLE ======
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
    }

    // ====== ACTIVE NAV ======
    function setActive(el, name) {
        document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('topbar-title').textContent = name;
        showToast('📂 ' + name);
    }

    // ====== VIEW TOGGLE ======
    let currentView = 'grid';
    function setView(view) {
        currentView = view;
        const container = document.getElementById('cardsContainer');
        const gridBtn = document.getElementById('gridBtn');
        const listBtn = document.getElementById('listBtn');

        if (view === 'grid') {
            container.className = 'cards-grid';
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
            location.reload();
        } else {
            container.className = 'cards-list';
            gridBtn.classList.remove('active');
            listBtn.classList.add('active');
            renderListView();
        }
    }

    // ====== SEARCH & FILTER ======
    function filterCards() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.card, .list-item');
        cards.forEach(card => {
            const name = (card.dataset.name || '').toLowerCase();
            const cat = (card.dataset.cat || '').toLowerCase();
            const matchSearch = name.includes(query) || cat.includes(query);
            card.style.display = matchSearch ? '' : 'none';
        });
    }

    // ====== SORT ======
    function toggleSort() {
        document.getElementById('sortDropdown').classList.toggle('open');
    }

    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('sortDropdown');
        if (!e.target.closest('.classify-btn') && !e.target.closest('.sort-dropdown')) {
            dropdown.classList.remove('open');
        }
    });

    function sortCards(type) {
        const container = document.getElementById('cardsContainer');
        const cards = [...container.children];
        cards.sort((a, b) => {
            if (type === 'nome') return (a.dataset.name || '').localeCompare(b.dataset.name || '');
            if (type === 'qtd-asc') return parseInt(a.dataset.qty) - parseInt(b.dataset.qty);
            if (type === 'qtd-desc') return parseInt(b.dataset.qty) - parseInt(a.dataset.qty);
            if (type === 'status') return (a.dataset.status || '').localeCompare(b.dataset.status || '');
            return 0;
        });
        cards.forEach(c => container.appendChild(c));
        document.getElementById('sortDropdown').classList.remove('open');
        showToast('✅ Ordenado!');
    }

    // ====== LIST VIEW RENDER ======
    function renderListView() {
        const container = document.getElementById('cardsContainer');
        const cards = [...container.querySelectorAll('.card')];
        container.innerHTML = '';
        cards.forEach(card => {
            const name = card.dataset.name;
            const qty = card.dataset.qty;
            const cat = card.dataset.cat;
            const status = card.dataset.status;
            const grad = card.querySelector('.card-banner').className.split(' ').find(c => c.startsWith('grad-'));
            const item = document.createElement('div');
            item.className = 'list-item';
            item.dataset.cat = cat;
            item.dataset.name = name;
            item.dataset.qty = qty;
            item.dataset.status = status;
            item.innerHTML = `
                <div class="list-thumb ${grad}"><i class="fas fa-cog"></i></div>
                <div class="list-info"><h4>${name}</h4><p>${cat}</p></div>
                <div class="list-qty"><span>${qty}</span><p>unid.</p></div>
            `;
            container.appendChild(item);
        });

        filterCards();
    }

    // ====== MODAL ======
    function openModal(name, code, cat, loc, qty, status) {
        document.getElementById('modalTitle').textContent = name;
        document.getElementById('mCode').textContent = code;
        document.getElementById('mCat').textContent = cat;
        document.getElementById('mLoc').textContent = loc;
        document.getElementById('mQty').textContent = qty + ' unidades';
        const statusMap = { bom: '🟢 Bom', atencao: '🟡 Atenção', critico: '🔴 Crítico' };
        document.getElementById('mStatus').textContent = statusMap[status] || status;
        document.getElementById('modalOverlay').classList.add('open');
    }

    function closeModal() {
        document.getElementById('modalOverlay').classList.remove('open');
    }

    function closeModalOutside(e) {
        if (e.target === document.getElementById('modalOverlay')) closeModal();
    }

    // ====== TOAST ======
    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 2500);
    }

    // ====== KEYBOARD ESC ======
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });
</script>
</body>
</html>
