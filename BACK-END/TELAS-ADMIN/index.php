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
    <title>Panthers Cars - Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../CSS/TELAS-ADMIN/dashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-logo">
        <!-- <div class="logo-icon">🚗</div> -->
         <div class="logo" id="logo-icon">
                    <img src="../../LOGIN/IMG/LOGO.png" alt="logo" id="logo">

         </div>
        <span class="logo-text">Panthers<span>Cars</span></span>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Principal</div>
        <a
          href="./dashboard.html"
          class="nav-item active"
          onclick="setActive(this, 'Dashboard')"
        >
          <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        <a
          href="./scanner.html"
          class="nav-item"
          onclick="setActive(this, 'Scanner')"
        >
          <i class="fas fa-qrcode"></i> Scanner
        </a>
                <a
          href="./inspecao.html"
          class="nav-item"
          onclick="setActive(this, 'Inspeção')"
        >
          <i class="fas fa-clipboard"></i> Inspeção
        </a>
        <a
          href="./editar_inspecao.html"
          class="nav-item"
          onclick="setActive(this, 'Editar Inspeção')"
        >
          <i class="fas fa-edit"></i> Editar Inspeção
        </a>
        <a
          href="./inventario.html"
          class="nav-item"
          onclick="setActive(this, 'Inventário')"
        >
          <i class="fas fa-boxes"></i> Inventário
        </a>
      </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Administração</div>
        <a href="#" class="nav-item" onclick="setActive(this, 'Funcionários')">
          <i class="fas fa-users"></i> Funcionários
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

<!-- ===== MAIN ===== -->
<div class="main">
    <!-- TOPBAR -->
    <header class="topbar">
        <button class="topbar-menu-btn" onclick="toggleSidebar()">
          <i class="fas fa-bars"></i>
        </button>
        <h2 id="topbar-title">Dashboard Gerencial</h2>
        <div class="topbar-actions">
            <a class="topbar-btn ghost" href="../Inicializaçao.html">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </header>

<!-- CONTENT -->
    <div class="content">

        <!-- Page Header -->
        <div class="page-header">
            <div></div>
            <button class="refresh-btn" onclick="refreshData()">
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
                    <div class="stat-value" id="s1">847</div>
                    <div class="stat-sub green"><i class="fas fa-arrow-up"></i> 12% este mês</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-bg green"></div>
                <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <p>Aprovadas</p>
                    <div class="stat-value" id="s2">762</div>
                    <div class="stat-sub green"><i class="fas fa-check"></i> Taxa 90%</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-bg red"></div>
                <div class="stat-icon red"><i class="fas fa-times-circle"></i></div>
                <div class="stat-info">
                    <p>Reprovadas</p>
                    <div class="stat-value" id="s3">47</div>
                    <div class="stat-sub red"><i class="fas fa-exclamation"></i> Verificar</div>
                </div>
            </div>
            <div class="stat-card">
            <div class="stat-icon yellow"><i class="fas fa-cogs"></i></div> 
            <div class="stat-info"> 
                <p>Total Inspeções</p> 
                <div class="stat-value" id="s4">410</div> 
                <div class="stat-sub yellow"><i class="fas fa-arrow-up"></i> 50% este mês</div>
            </div> 
            </div>
        </div>

        <!-- Charts Row -->
        <div class="charts-row">

            <!-- Bar Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="fas fa-chart-bar"></i> Produção Hoje
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
                        <i class="fas fa-chart-pie"></i> Por Setor
                    </div>
                </div>
                <div class="chart-canvas-wrap" style="height:175px">
                    <canvas id="donutChart"></canvas>
                </div>
                <div class="donut-legend" id="donutLegend"></div>
            </div>
        </div>

        <!-- Alerts Section -->
        <div class="alerts-section">
            <div class="alerts-header">
                <div class="alerts-title">
                    <i class="fas fa-bell"></i> Histórico Inspeções
                </div>
                <button class="ver-todos-btn" onclick="openAlertsModal()">Ver todos</button>
            </div>

            <div class="alert-item" onclick="showToast('🔴 Peça ELE-005 fora do padrão')">
                <div class="alert-dot danger"><i class="fas fa-times-circle"></i></div>
                <div class="alert-content">
                    <h4>Peça ELE-005 fora do padrão – Módulo ECU</h4>
                    <div class="alert-meta">
                        <span><i class="fas fa-clock"></i> 5 min atrás</span>
                        <span><i class="fas fa-tag"></i> Elétrica</span>
                    </div>
                </div>
            </div>

            <div class="alert-item" onclick="showToast('⚠️ Estoque crítico ELE-005')">
                <div class="alert-dot warning"><i class="fas fa-exclamation-triangle"></i></div>
                <div class="alert-content">
                    <h4>Estoque crítico: ELE-005 com apenas 5 unidades</h4>
                    <div class="alert-meta">
                        <span><i class="fas fa-clock"></i> 15 min atrás</span>
                        <span><i class="fas fa-tag"></i> Elétrica</span>
                    </div>
                </div>
            </div>

            <div class="alert-item" onclick="showToast('✅ Scanner finalizado – 23 peças aprovadas')">
                <div class="alert-dot info"><i class="fas fa-info-circle"></i></div>
                <div class="alert-content">
                    <h4>Scanner finalizado – 23 peças aprovadas</h4>
                    <div class="alert-meta">
                        <span><i class="fas fa-clock"></i> 20 min atrás</span>
                        <span><i class="fas fa-tag"></i> Freios</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



<!-- ===== MODAL ALERTAS ===== -->
<div class="modal-overlay" id="alertsModal" onclick="closeModalOutside(event)">
    <div class="modal">
        <div class="modal-header">
            <h3>🔔 Todos os Alertas</h3>
            <button class="modal-close" onclick="closeAlertsModal()">✕</button>
        </div>
        <div class="modal-row">
            <span class="ml">🔴 ELE-005 fora do padrão</span>
            <span class="mv" style="color:#e53e3e">Crítico</span>
        </div>
        <div class="modal-row">
            <span class="ml">⚠️ Estoque baixo ELE-005</span>
            <span class="mv" style="color:#d69e2e">Atenção</span>
        </div>
        <div class="modal-row">
            <span class="ml">✅ Scanner finalizado</span>
            <span class="mv" style="color:#38a169">Info</span>
        </div>
        <div style="margin-top:14px;text-align:right">
            <button onclick="closeAlertsModal()" style="background:#e53e3e;color:#fff;border:none;border-radius:8px;padding:8px 18px;font-size:12px;font-weight:600;cursor:pointer;">
                Fechar
            </button>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"></div>

<script>
    // ===== GREETING =====


    // ===== BAR CHART =====
    const barCtx = document.getElementById('barChart').getContext('2d');
    const hours = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00'];
    const approved = [8, 15, 22, 18, 5, 20, 22, 12];
    const rejected = [1, 2, 1, 3, 2, 2, 1, 2];

    const barChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: hours,
            datasets: [
                {
                    label: 'Aprovadas',
                    data: approved,
                    backgroundColor: '#38a169',
                    borderRadius: 5,
                    barPercentage: 0.5,
                },
                {
                    label: 'Reprovadas',
                    data: rejected,
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
                y: { grid: { color: '#f3f4f6' }, ticks: { font: { size: 10 }, color: '#9ca3af', stepSize: 5 } }
            },
            animation: { duration: 1000, easing: 'easeOutBounce' }
        }
    });

    // ===== DONUT CHART =====
    const donutCtx = document.getElementById('donutChart').getContext('2d');
    const sectors = ['Motor','Transmissão','Suspensão','Freios','Elétrica','Direção'];
    const sectorColors = ['#e53e3e','#48bb78','#4299e1','#d69e2e','#9f7aea','#ed8936'];
    const sectorData = [22, 15, 18, 20, 10, 15];

    const donutChart = new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: sectors,
            datasets: [{
                data: sectorData,
                backgroundColor: sectorColors,
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
                        label: ctx => ` ${ctx.label}: ${ctx.parsed}%`
                    }
                }
            },
            animation: { animateRotate: true, duration: 1200 }
        }
    });

    // Build donut legend
    const legendEl = document.getElementById('donutLegend');
    sectors.forEach((s, i) => {
        const item = document.createElement('div');
        item.className = 'donut-legend-item';
        item.innerHTML = `<div class="donut-dot" style="background:${sectorColors[i]}"></div>${s}`;
        legendEl.appendChild(item);
    });

    // ===== REFRESH =====
    function refreshData() {
        const icon = document.getElementById('refreshIcon');
        icon.style.transition = 'transform 0.6s';
        icon.style.transform = 'rotate(360deg)';
        setTimeout(() => { icon.style.transition = ''; icon.style.transform = ''; }, 650);

        // Animate stat values
        animateCounter('s1', 847, randomInt(820, 900));
        animateCounter('s2', 762, randomInt(740, 800));
        animateCounter('s3', 47, randomInt(40, 60));
        animateCounter('s4', 4, randomInt(3, 6));

        // Update bar chart
        barChart.data.datasets[0].data = hours.map(() => randomInt(5, 25));
        barChart.data.datasets[1].data = hours.map(() => randomInt(1, 4));
        barChart.update();

        showToast('✅ Dados atualizados!');
    }

    function randomInt(min, max) {
        return Math.floor(Math.random() * (max - min + 1)) + min;
    }

    function animateCounter(id, from, to) {
        const el = document.getElementById(id);
        const diff = to - from;
        const steps = 30;
        let step = 0;
        const timer = setInterval(() => {
            step++;
            el.textContent = Math.round(from + (diff * step / steps));
            if (step >= steps) clearInterval(timer);
        }, 20);
    }

    // ===== SIDEBAR =====
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('open');
    }

    function setActive(el, name) {
        document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('topbar-title').textContent = name;
        showToast('📂 ' + name);
    }

    // ===== MODAL =====
    function openAlertsModal() {
        document.getElementById('alertsModal').classList.add('open');
    }

    function closeAlertsModal() {
        document.getElementById('alertsModal').classList.remove('open');
    }

    function closeModalOutside(e) {
        if (e.target === document.getElementById('alertsModal')) closeAlertsModal();
    }

    // ===== TOAST =====
    function showToast(msg) {
        const t = document.getElementById('toast');
        t.textContent = msg;
        t.classList.add('show');
        clearTimeout(t._t);
        t._t = setTimeout(() => t.classList.remove('show'), 2600);
    }

    // ===== KEYBOARD =====
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeAlertsModal();
        if (e.key === 'F5') { e.preventDefault(); refreshData(); }
    });
</script>
</body>
</html>