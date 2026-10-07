<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_login();

// ----- 1. DADOS DO USUÁRIO -----
$nome_sessao =$_SESSION["user"] ?? ""; 
$stmt =$pdo->prepare("SELECT usuario_nome, isAdmin FROM first_data.usuarios WHERE usuario_nome = ?");
$stmt->execute([$nome_sessao]);
$user =$stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
    $nome =$user["usuario_nome"]; 
    $func =$user["isAdmin"] ? "Administrador" : "Funcionario";
    $dd = ($func === "Funcionario") ? "display: none;" : "";
} else {
    $nome = "Usuário não encontrado";
    $func = "";
    $dd = "display: none;";
}

// ----- 2. LÓGICA DOS FILTROS -----
$meses = $_GET['meses'] ?? '';$where = "WHERE 1=1";
$params = [];

if (in_array($meses, ['1', '3', '6', '12'])) {
    $mesesInt = (int)$meses;
    $where .= " AND data_insp >= DATE_SUB(NOW(), INTERVAL {$mesesInt} MONTH)";
}

// ----- 3. CONSULTAS PARA OS CARDS (KPIs) -----
$sqlCards = "SELECT 
                COALESCE(SUM(quantidade_pecas), 0) AS total_pecas,
                COALESCE(SUM(pecas_aprovadas), 0) AS aprovadas,
                COALESCE(SUM(pecas_reprovadas), 0) AS reprovadas,
                COUNT(id_usuario) AS total_inspecoes
             FROM first_data.pecas 
             $where";
$stmtCards =$pdo->prepare($sqlCards);$stmtCards->execute();
$cardsData =$stmtCards->fetch(PDO::FETCH_ASSOC);

$taxaAprovacao =$cardsData['total_pecas'] > 0 
    ? round(($cardsData['aprovadas'] /$cardsData['total_pecas']) * 100, 1) 
    : 0;

// ----- 4. CONSULTA GRÁFICO DE BARRAS (Produção Diária) -----
$sqlBar = "SELECT 
                DATE_FORMAT(MAX(data_insp), '%d/%m') as data, 
                SUM(pecas_aprovadas) as aprovadas, 
                SUM(pecas_reprovadas) as reprovadas 
           FROM first_data.pecas 
           $where 
           GROUP BY DATE(data_insp) 
           ORDER BY DATE(data_insp) ASC 
           LIMIT 10";
$stmtBar = $pdo->prepare($sqlBar);
$stmtBar->execute();
$barResult = $stmtBar->fetchAll(PDO::FETCH_ASSOC);

$barLabels = [];
$barAprovadas = [];$barReprovadas = [];
foreach ($barResult as$row) {
    $barLabels[] =$row['data'];
    $barAprovadas[] =$row['aprovadas'];
    $barReprovadas[] =$row['reprovadas'];
}

// ----- 5. CONSULTA GRÁFICO DONUT (Por Peça/Setor) -----
$sqlDonut = "SELECT 
                nome_tipo as setor, 
                SUM(quantidade_pecas) as total 
             FROM first_data.pecas 
             $where 
             GROUP BY nome_tipo 
             ORDER BY total DESC 
             LIMIT 6";
$stmtDonut =$pdo->prepare($sqlDonut);$stmtDonut->execute();
$donutResult =$stmtDonut->fetchAll(PDO::FETCH_ASSOC);

$donutLabels = [];$donutData = [];
foreach ($donutResult as$row) {
    $donutLabels[] =$row['setor'] ?: 'Não definido';
    $donutData[] =$row['total'];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panthers Cars - Dashboard Gerencial</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../FRONT-END/CSS/TELAS-ADMIN/dashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
    <style>
        /* Ajuste simples para os botões de filtro no painel superior */
        .filter-header { display: flex; gap: 10px; margin-bottom: 20px; }
        .tag-btn { padding: 6px 14px; border: 1px solid #d3d3d3; background: #fff; border-radius: 20px; cursor: pointer; text-decoration: none; color: #4a5568; font-size: 14px; transition: 0.2s;}
        .tag-btn.active { background: #e53e3e; color: white; }
        .tag-btn:hover { background: #e3e3e3; }
        .tag-btn.active:hover { background: #e53e3e; }
    </style>
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
        <a href="./index.php" class="nav-item active" onclick="setActive(this, 'Dashboard')">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a href="./scanner.php" class="nav-item" onclick="setActive(this, 'Scanner')">
            <i class="fas fa-qrcode"></i> Scanner
        </a>
        <a href="./inspecao.php" class="nav-item" onclick="setActive(this, 'Inspeção')">
            <i class="fas fa-clipboard"></i> Inspeção
        </a>
    </div>
    
    <div class="sidebar-section" style="<?= htmlspecialchars($dd) ?>">
        <div class="sidebar-section-title">Administração</div>
        <a href="./editar_inspe.php" class="nav-item" onclick="setActive(this, 'Editar Inspeção')">
            <i class="fas fa-edit"></i> Editar Inspeção
        </a>
        <a href="./funcionarios.php" class="nav-item" onclick="setActive(this, 'Funcionários')">
            <i class="fas fa-users"></i> Funcionários
        </a>
    </div>
    
    <div class="sidebar-section">
        <div class="sidebar-section-title">Registros Inspeção</div>
        <a href="historico_inspecao.php" class="nav-item" onclick="setActive(this, 'Histórico')">
            <i class="fas fa-clock"></i> Histórico Peças
        </a>

                <a href="./inventario.php" class="nav-item" onclick="setActive(this, 'Inventário')">
            <i class="fas fa-boxes"></i> Inventário
        </a>
    </div>

    <div class="sidebar-footer">
        <div class="avatar"><?= strtoupper($nome[0]) ?></div>
        <div class="user-info">
            <p><?= htmlspecialchars($nome) ?></p>
            <span><?= htmlspecialchars($func) ?></span>
        </div>
    </div>
</aside>

<!-- ===== MAIN ===== -->
<div class="main">
    <header class="topbar">
        <h2 id="topbar-title">Dashboard Gerencial</h2>
        <div class="topbar-actions">
            <a class="topbar-btn ghost" href="../../BACK-END/AUTH/logout.php">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </header>

    <div class="content">

        <!-- Page Header & Filters -->
        <div class="page-header" style="flex-direction: column; align-items: flex-start; gap: 15px;">
            <div class="filter-header">
                <!-- <span style="font-weight: bold; margin-top: 5px; color: #4a5568;">Filtrar por:</span> -->
                <a href="?meses=" class="tag-btn <?= empty($_GET['meses']) ? 'active' : '' ?>">Todo o período</a>
                <a href="?meses=1" class="tag-btn <?= ($_GET['meses'] ?? '') == '1' ? 'active' : '' ?>">1 mês</a>
                <a href="?meses=3" class="tag-btn <?= ($_GET['meses'] ?? '') == '3' ? 'active' : '' ?>">3 meses</a>
                <a href="?meses=6" class="tag-btn <?= ($_GET['meses'] ?? '') == '6' ? 'active' : '' ?>">6 meses</a>
                <a href="?meses=12" class="tag-btn <?= ($_GET['meses'] ?? '') == '12' ? 'active' : '' ?>">12 meses</a>
            </div>
            
            <button class="refresh-btn" onclick="window.location.reload();">
                <i class="fas fa-sync-alt" id="refreshIcon"></i> Atualizar
            </button>
        </div>

        <!-- Stats Row -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-bg blue"></div>
                <div class="stat-icon blue"><i class="fas fa-boxes"></i></div>
                <div class="stat-info">
                    <p>Total de Peças</p>
                    <div class="stat-value" id="s1"><?= $cardsData['total_pecas'] ?></div>
                    <div class="stat-sub blue">Itens Inspecionados</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-bg green"></div>
                <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <p>Aprovadas</p>
                    <div class="stat-value" id="s2"><?= $cardsData['aprovadas'] ?></div>
                    <div class="stat-sub green"><i class="fas fa-check"></i> Taxa <?= $taxaAprovacao ?>%</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-bg red"></div>
                <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
                <div class="stat-info">
                    <p>Reprovadas</p>
                    <div class="stat-value" id="s3"><?= $cardsData['reprovadas'] ?></div>
                    <div class="stat-sub red"><i class="fas fa-exclamation"></i> Requer atenção</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow"><i class="fas fa-cogs"></i></div> 
                <div class="stat-info"> 
                    <p>Total Lotes / Inspeções</p> 
                    <div class="stat-value" id="s4"><?= $cardsData['total_inspecoes'] ?></div> 
                    <div class="stat-sub yellow">Registros realizados</div>
                </div> 
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">
            <!-- Bar Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="fas fa-chart-bar"></i> Produção Recente
                    </div>
                    <div class="chart-legend">
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#38a169"></div> Aprovadas
                        </div>
                        <div class="legend-item">
                            <div class="legend-dot" style="background:#e53e3e"></div> Reprovadas
                        </div>
                    </div>
                </div>
                <div class="chart-canvas-wrap">
                    <canvas id="barChart"></canvas>
                </div>
            </div>

            <!-- Donut Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="fas fa-chart-pie"></i> Por Peça / Setor
                    </div>
                </div>
                <div class="chart-canvas-wrap" style="height:175px">
                    <canvas id="donutChart"></canvas>
                </div>
                <div class="donut-legend" id="donutLegend"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // Injetando dados do PHP no JavaScript via JSON
    const barLabels = <?= json_encode($barLabels) ?>;
    const barAprovadas = <?= json_encode($barAprovadas) ?>;
    const barReprovadas = <?= json_encode($barReprovadas) ?>;
    
    const donutLabels = <?= json_encode($donutLabels) ?>;
    const donutData = <?= json_encode($donutData) ?>;

    // ===== BAR CHART =====
    const barCtx = document.getElementById('barChart').getContext('2d');
    const barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: barLabels.length > 0 ? barLabels : ['Sem dados'],
            datasets: [
                {
                    label: 'Aprovadas',
                    data: barAprovadas.length > 0 ? barAprovadas : [0],
                    backgroundColor: '#38a169',
                    borderRadius: 5,
                    barPercentage: 0.5,
                },
                {
                    label: 'Reprovadas',
                    data: barReprovadas.length > 0 ? barReprovadas : [0],
                    backgroundColor: '#e53e3e',
                    borderRadius: 5,
                    barPercentage: 0.5,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#9ca3af' } },
                y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 }, color: '#9ca3af' } }
            }
        }
    });

    // ===== DONUT CHART =====
    const donutCtx = document.getElementById('donutChart').getContext('2d');
    const defaultColors = ['#4299e1','#e53e3e','#48bb78','#d69e2e','#9f7aea','#ed8936'];
    const sectorColors = donutData.map((_, i) => defaultColors[i % defaultColors.length]);

    const donutChart = new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: donutLabels.length > 0 ? donutLabels : ['Sem dados'],
            datasets: [{
                data: donutData.length > 0 ? donutData : [1],
                backgroundColor: donutData.length > 0 ? sectorColors : ['#e2e8f0'],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '58%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed} peças`
                    }
                }
            }
        }
    });

    // Construção da legenda do Donut Chart via JS
    const legendEl = document.getElementById('donutLegend');
    if (donutLabels.length > 0) {
        donutLabels.forEach((s, i) => {
            const item = document.createElement('div');
            item.className = 'donut-legend-item';
            item.innerHTML = `<div class="donut-dot" style="background:${sectorColors[i]}"></div>${s}`;
            legendEl.appendChild(item);
        });
    } else {
        legendEl.innerHTML = '<span style="font-size:12px; color:#a0aec0;">Nenhum dado encontrado no período.</span>';
    }
</script>
</body>
</html>