<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../config/auth.php";
require_login();



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

$nome_sessao = $_SESSION["user"] ?? ""; 
$stmt = $pdo->query("SELECT CPF, usuario_nome, email, isAdmin, ID FROM first_data.usuarios WHERE isAdmin is null or isAdmin = 0");

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // 1. CORREÇÃO: Evita o erro 'Undefined array key' usando o operador ??
    $busca = $_GET["busca"] ?? "";

    // Só executa o filtro se o usuário realmente digitou algo na busca
    if ($busca !== "") {
        // 2. CORREÇÃO: Uso de placeholders '?' em vez de variáveis diretas na string do SQL
        // IMPORTANTE: Adicionados parênteses ao redor do OR para não quebrar a lógica do isAdmin
        $sql = "SELECT CPF, usuario_nome, email, ID FROM first_data.usuarios WHERE isAdmin IS NULL AND (usuario_nome LIKE ? OR CPF LIKE ?)";
        $stmt = $pdo->prepare($sql);
        
        // 3. CORREÇÃO: Passando as duas variáveis correspondentes aos dois pontos de interrogação (?)
        $param = "%" . $busca . "%";
        $stmt->execute([$param, $param]);
        
        // 4. CORREÇÃO: Alterado de fetch() para fetchAll() para trazer uma lista de usuários, não apenas um
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phanters Cars - Funcionários</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../Front-end/CSS/Funcionarios.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
</head>
<body>

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
          href="./inspeção.php"
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
        <a href="funcionarios.php" class="nav-item">
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
        <h2 id="topbar-title">Gerenciamento de Funcionarios</h2>
        <div class="topbar-actions">
            <button class="topbar-btn ghost" onclick="showToast('🔔 3 alertas pendentes')" style="position:relative">
                <i class="fas fa-bell"></i>
                <span class="notif-dot"></span>
            </button>
                <a href="./../auth/logout.php" class="topbar-btn ghost" style="text-decoration: none;"><i class="fas fa-sign-out-alt" ></i></a>
        </div>
    </header>
        <!-- CONTENT -->
        <div class="content">
            <div class="content-header">
                <div class="content-header-left">
                    <h1>Cadastro de Funcionários</h1>
                    <p>Gerencie a equipe de operação</p>
                </div>
                <!-- BOTÃO QUE ABRE O MODAL -->
                <button class="btn-add" onclick="abrirModal()">
                    👤+ Adicionar
                </button>
            </div>

                <div class="table-card">
                    <!-- O formulário agora é a própria barra de pesquisa -->
                    <form method="GET" class="search-bar">
                        <span class="search-icon">🔍</span>
                        <input type="text" placeholder="Buscar por nome, CPF ou setor..." name="busca" />
                        <button type="submit" class="btn-add">🔄 Buscar</button>
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Funcionário</th>
                            <th>CPF</th>
                            <th>E-mail</th>
     <!--                       <th>Setor</th>
                            <th>Peças Hoje</th>
                            <th>Status</th> -->
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach($users as $user) { ?>
                            <td>
                                <div class="employee-cell">
                                    <div class="employee-avatar"><?php echo strtoupper($user["usuario_nome"][0]) ?></div>
                                    <div>
                                        <div class="employee-name"><?php echo $user["usuario_nome"];?></div>
                                        <div class="employee-id">ID: <?php echo $user["ID"]; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td><?php echo $user["CPF"]; ?></td>
                            <td><?php echo $user["email"]; ?></td>
                           <!-- <td><span class="setor-badge">Montagem</span></td>
                            <td><span class="pecas-count">47</span></td> -->
                            <td>
                                <div class="actions-cell">
                                <button class="btn-edit" onclick='abrireditar(<?= json_encode($user, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>✏️</button>    
                                <a href="./deletar_func.php?id=<?php echo $user["ID"]; ?>" onclick="return confirm('Deseja realmente Excluir?')" style="text-decoration: none;">🗑️</a>
                                </div>
                            </td>
                        </tr>
                            <?php } ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ========================
         MODAL NOVO FUNCIONÁRIO
    ======================== -->
    <div class="modal-overlay" id="modalOverlay" onclick="fecharModalFora(event)">
        <div class="modal" id="modalBox">
            <div class="modal-header">
                <div class="modal-header-left">
                    <span class="modal-icon">👤</span>
                    Novo Funcionário
                </div>
                <button class="modal-close" onclick="fecharModal()">✕</button>
            </div>
            <div class="modal-body">
                <form action="./criarfunc.php" method="POST">
                <div class="modal-row">
                    <div class="modal-field">
                        <label>Nome Completo</label>
                        <input type="text" placeholder="Nome completo" name="nome"/>


                    </div>
                    <div class="modal-field">
                        <label>CPF</label>
                        <input type="text" placeholder="000.000.000-00"  name="cpf" maxlength="11"/>
                    </div>
                </div>

                <div class="modal-row">
                    <div class="modal-field">
                        <label>E-mail</label>
                        <input type="email" placeholder="email@empresa.com" name="email"/>
                    </div>
                    <div class="modal-field">
                        <label>Senha</label>
                        <input type="password" placeholder="Senha de acesso" name="senha"/>
                    </div>
                </div>
            
            </div>

            <div class="modal-footer">
                <button class="btn-cancelar" type="button" onclick="fecharModal()">Cancelar</button>
                <button class="btn-cadastrar" type="submit">💾 Cadastrar</button>
            </div>
                </form>
        </div>
    </div>


        <div class="modal-overlay" id="modalEditar" onclick="fecharModalFora(event)">
        <div class="modal" id="modalBox">
            <div class="modal-header">
                <div class="modal-header-left">
                    <span class="modal-icon">👤</span>
                        Editar funcionario
                </div>
                <button class="modal-close" onclick="fechareditar()">✕</button>
            </div>
            <div class="modal-body">


                <form action="./editar_fun.php" method="POST">
                <input type="hidden" id="modal_edit_id" name="id_funcionario">
                <div class="modal-row">
                    <div class="modal-field">
                        <label>Nome Completo</label>
                         <input id="modal_edit_nome" type="text" placeholder="Nome Completo" name="nome" value="<?php echo htmlspecialchars($user["usuario_nome"] ?? ''); ?>" ><br><br>

                    </div>
                    <div class="modal-field">
                        <label>CPF</label>
                        <input  id="modal_edit_cpf" type="text" name="CPF"  placeholder="000.000.000-00" value="<?php echo htmlspecialchars($user["CPF"] ?? ''); ?>"><br><br>
                    </div>
                </div>

                <div class="modal-row">
                    <div class="modal-field">
                        <label>E-mail</label>
                <input id="modal_edit_email" type="email"  placeholder="email@empresa.com" name="email" value="<?php echo htmlspecialchars($user["email"] ?? ''); ?>"><br><br>
                    </div>

            </div>

            <div class="modal-footer">
                <button class="btn-cancelar" type="button" onclick="fechareditar()">Cancelar</button>
                <button class="btn-cadastrar" type="submit">💾 Editar</button>
            </div>


                </form>
        </div>
    </div>

    <script>
        function abrirModal() {
            document.getElementById('modalOverlay').classList.add('active');
        }
        function abrireditar(user){
            document.getElementById('modalEditar').classList.add('active');
            let idInput = document.getElementById("modal_edit_id");
            let nome = document.getElementById("modal_edit_nome");
            let email = document.getElementById("modal_edit_email");
            let cpf = document.getElementById("modal_edit_cpf");
            console.log(nome);
            console.log(user);
            console.log(cpf);
            idInput.value = user.ID;
            nome.value = user.usuario_nome;
            email.value = user.email;
            cpf.value = user.CPF;
        }
        function fechareditar(){
            document.getElementById('modalEditar').classList.remove('active');
        }

        function fecharModal() {
            document.getElementById('modalOverlay').classList.remove('active');
        }

        function fecharModalFora(event) {
            if (event.target === document.getElementById('modalOverlay')) {
                fecharModal();
            }
        }

        // Fechar com ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                fecharModal();
                fechareditar();
            }
        });
    </script>

</body>
</html>