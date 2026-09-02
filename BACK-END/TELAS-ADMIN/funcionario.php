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


<!doctype html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Panthers Cars - Funcionários</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
    />
    <link rel="stylesheet" href="../../FRONT-END/CSS/TELAS-ADMIN/funcionarios.css" />
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
      href="./editar_inspecao.php"
      class="nav-item"
      onclick="setActive(this, 'Editar Inspeção')"
      >
      <i class="fas fa-edit"></i> Editar Inspeção
    </a>
    <a
      href="./inventario.php"
      class="nav-item"
      onclick="setActive(this, 'Inventário')"
    >
      <i class="fas fa-boxes"></i> Inventário
    </a>
  </div>

      <div class="sidebar-section">
        <div class="sidebar-section-title">Administração</div>
        <a
          href="./funcionario.php" 
          class="nav-item active" onclick="setActive(this, 'Funcionários')"
          
        >
          <i class="fas fa-users"></i> Funcionários
        </a>

        <!-- <a href="#" class="nav-item" onclick="setActive(this, 'Alertas')">
          <i class="fas fa-bell"></i> Alertas
          <span class="badge">3</span>
        </a> -->
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
        <h2 id="topbar-title">Cadastro de Funcionários</h2>
        <div class="topbar-actions">
          <!-- <button
            class="topbar-btn ghost"
            onclick="showToast('🔔 3 alertas pendentes')"
            style="position: relative"
          >
            <i class="fas fa-bell"></i>
            <span class="notif-dot"></span>
          </button> -->
          <button class="topbar-btn ghost" onclick="showToast('🚪 Saindo...')">
            <i class="fas fa-sign-out-alt"></i>
          </button>
        </div>
      </header>
      <!-- CONTENT -->
      <div class="content">
        <div class="content-header">
          <div class="content-header-left"></div>
          <!-- BOTÃO QUE ABRE O MODAL -->
          <!-- <button class="btn-add" onclick="abrirModal()">👤+ Adicionar</button> -->
          <!-- <button class="btn-add">👤+ Adicionar</button> -->
          <div >
            <a class="btn-add" href="../LOGIN/create.php">👤+ Adicionar
            </a>
        </div>
        
        </div>

        <div class="table-card">
          <div class="search-bar">
            <span class="search-icon">🔍</span>
            <input type="text" placeholder="Buscar por nome, CPF ou setor..." />
            <!-- <button class="refresh-btn">🔄</button> -->
          </div>

          <table>
            <thead>
              <tr>
                <th>Funcionário</th>
                <th>CPF</th>
                <th>Telefone</th>
                <th>E-mail</th>
                <th>Setor</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
                
                <?php foreach($users as $user) { ?>
                <tr>
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
                    <td><?php echo " colocar telefone" ?></td>
                    <td><?php echo $user["email"]; ?></td>
                    <td><?php echo "colocar setor" ?></td>
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

            
                    </tbody>
                </table>
            </div>
        </div>
    </div>


                  <!-- <div class="employee-cell">
                    <div class="employee-avatar">J</div>
                    <div>
                      <div class="employee-name">João Silva</div>
                      <div class="employee-id">ID: 1</div>
                    </div>
                  </div>
                </td>
                <td>111.111.111-11</td>
                <td>(00) 00000-0000</td>
                <td><span class="setor-badge">joao@empresa.com</span></td>
                <td>Montagem</td>
                <!-<td>
                  <span class="status-ativo"
                    ><span class="status-dot-ativo"></span> Ativo</span
                  >
                </td> -->
                <!-- <td>
                  <div class="actions-cell">
                    <button class="btn-edit">✏️</button>
                    <button class="btn-delete">🗑️</button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="employee-cell">
                    <div class="employee-avatar">M</div>
                    <div>
                      <div class="employee-name">Maria Santos</div>
                      <div class="employee-id">ID: 2</div>
                    </div>
                  </div>
                </td>
                <td>222.222.222-22</td>
                <td>(00) 00000-0000</td>
                <td><span class="setor-badge">maria@empresa.com</span></td>
                <td>Qualidade</td> -->
                <!-- <td>
                  <span class="status-ativo"
                    ><span class="status-dot-ativo"></span> Ativo</span
                  >
                </td> -->
                <!-- <td>
                  <div class="actions-cell">
                    <button class="btn-edit">✏️</button>
                    <button class="btn-delete">🗑️</button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="employee-cell">
                    <div class="employee-avatar">C</div>
                    <div>
                      <div class="employee-name">Carlos Oliveira</div>
                      <div class="employee-id">ID: 3</div>
                    </div>
                  </div>
                </td>
                <td>333.333.333-33</td>
                <td>(00) 00000-0000</td>
                <td><span class="setor-badge">carlos@empresa.com</span></td>
                <td>Expedição</td> -->
                <!-- <td>
                  <span class="status-ativo"
                    ><span class="status-dot-ativo"></span> Ativo</span
                  >
                </td> -->
                <!-- <td>
                  <div class="actions-cell">
                    <button class="btn-edit">✏️</button>
                    <button class="btn-delete">🗑️</button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="employee-cell">
                    <div class="employee-avatar">A</div>
                    <div>
                      <div class="employee-name">Ana Costa</div>
                      <div class="employee-id">ID: 4</div>
                    </div>
                  </div>
                </td>
                <td>444.444.444-44</td>
                <td>(00) 00000-0000</td>
                <td><span class="setor-badge">ana@empresa.com</span></td>
                <td>Inspeção</td> -->
                <!-- <td>
                  <span class="status-inativo"
                    ><span class="status-dot-inativo"></span> Inativo</span
                  >
                </td> -->
                <!-- <td>
                  <div class="actions-cell">
                    <button class="btn-edit">✏️</button>
                    <button class="btn-delete">🗑️</button>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="employee-cell">
                    <div class="employee-avatar">P</div>
                    <div>
                      <div class="employee-name">Pedro Lima</div>
                      <div class="employee-id">ID: 5</div>
                    </div>
                  </div>
                </td>
                <td>555.555.555-55</td>
                <td>(00) 00000-0000</td>
                <td><span class="setor-badge">pedro@empresa.com</span></td>
                <td>Montagem</td> -->
                <!-- <td>
                  <span class="status-ativo"
                    ><span class="status-dot-ativo"></span> Ativo</span
                  >
                </td> -->
                <!-- <td>
                  <div class="actions-cell">
                    <button class="btn-edit">✏️</button>
                    <button class="btn-delete">🗑️</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>  -->

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
  </body>
</html>
