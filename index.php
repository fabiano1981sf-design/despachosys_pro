<?php

// FORÇA NUNCA CACHEAR NADA
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");


/**
 * ARQUIVO index.php (COMPLETO E CORRIGIDO PARA PHP 7.x)
 * Controlador central que utiliza as Classes refatoradas no core.php
 * CORREÇÃO: Substituído o 'match' por 'switch' para compatibilidade com PHP < 8.0
 */
require_once 'core.php';

// Obtém a página atual e o ID para edição/exclusão
$page = $_GET['page'] ?? 'login';
$id = $_GET['id'] ?? null;
$action = $_GET['action'] ?? null;
$edit_data = null;
$msg = null;
$currentUser = Auth::isLoggedIn() ? SystemCore::getUser() : null; // Obtém dados do usuário logado

// --- REDIRECIONAMENTOS E AUTH ---
if (Auth::isLoggedIn() && $page === 'login') $page = 'dashboard';
if (!Auth::isLoggedIn() && $page !== 'login' && $page !== 'rastrear') {
    header('Location: index.php?page=login'); exit;
}
if ($page === 'logout') { Auth::logout(); header('Location: index.php?page=login'); exit; }

// Verifica permissão.
if(Auth::isLoggedIn() && !SystemCore::checkPermission($page)) {
    $msg = ['type'=>'warning', 'text'=>'Você não tem permissão para acessar esta área.'];
    $page = 'dashboard';
}

// --- PROCESSAMENTO DE FORMULÁRIOS (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Processamento de Login
    if ($page === 'login' && isset($_POST['login'])) {
        $result = Auth::login($_POST['email'], $_POST['password']);
        if ($result['success']) {
            header('Location: index.php?page=dashboard'); exit;
        } else {
            $msg = ['type' => 'danger', 'text' => $result['message']];
        }
    }

    // PROCESSAMENTO GERAL DE MÓDULOS (SAVE/UPDATE)
    switch ($page) {
        case 'usuarios':
            $result = SystemCore::saveUser($_POST, (bool)$id);
            $msg = $result;
            if ($result['success'] && !$id) {
                // Limpa o POST para evitar reenvio
                header('Location: index.php?page=usuarios'); exit; 
            }
            break;
        case 'perfil':
            // Lidar com upload de imagem, se necessário
            $fotoPath = null; // Lógica de upload aqui
            $result = SystemCore::updateProfile($currentUser['id'], $_POST['nome'], $_POST['email'], $_POST['password'] ?? null, $fotoPath);
            $msg = $result;
            break;
        case 'transportadoras':
            $result = SystemCore::saveTransportadora($_POST, (bool)$id);
            $msg = $result;
            break;
        case 'categorias':
            $result = SystemCore::saveCategoria($_POST, (bool)$id);
            $msg = $result;
            break;
        case 'mercadorias':
            $result = SystemCore::saveMercadoria($_POST, (bool)$id);
            $msg = $result;
            break;
        case 'despachos':
            $result = SystemCore::saveDespacho($_POST, (bool)$id);
            $msg = $result;
            break;
        case 'movimentacao_estoque':
            $result = SystemCore::addEstoqueMovement($_POST);
            $msg = $result;
            break;
            
        // NOVOS MÓDULOS CRM/FINANCEIRO
        case 'clientes':
            $result = SystemCore::saveCliente($_POST, (bool)$id);
            $msg = $result;
            break;
        case 'oportunidades':
            $result = SystemCore::saveOportunidade($_POST, (bool)$id);
            $msg = $result;
            break;
        case 'pedidos_venda':
            $result = SystemCore::savePedidoVenda($_POST, (bool)$id);
            $msg = $result;
            break;
        case 'plano_contas':
            $result = SystemCore::savePlanoContas($_POST, (bool)$id);
            $msg = $result;
            break;
        case 'contas_a_pagar':
            $result = SystemCore::saveContaPagar($_POST, (bool)$id);
            $msg = $result;
            break;
        case 'contas_a_receber':
            $result = SystemCore::saveContaReceber($_POST, (bool)$id);
            $msg = $result;
            break;
    }
    
    // Redirecionamento após salvar, se for uma criação
    if (isset($result) && $result['success'] && $page !== 'perfil' && !$id) {
        header('Location: index.php?page=' . $page); exit;
    }
}


// --- PROCESSAMENTO DE AÇÕES (GET) ---
if ($action === 'delete' && $id) {
    switch ($page) {
        case 'usuarios': $result = SystemCore::deleteById('users', $id); break;
        case 'transportadoras': $result = SystemCore::deleteById('transportadoras', $id); break;
        case 'categorias': $result = SystemCore::deleteById('categorias', $id); break;
        case 'mercadorias': $result = SystemCore::deleteById('mercadorias', $id); break;
        case 'despachos': $result = SystemCore::deleteById('despachos', $id); break;
        case 'clientes': $result = SystemCore::deleteById('clientes', $id); break;
        case 'oportunidades': $result = SystemCore::deleteById('oportunidades', $id); break;
        case 'pedidos_venda': $result = SystemCore::deleteById('pedidos_venda', $id); break;
        case 'plano_contas': $result = SystemCore::deleteById('plano_contas', $id); break;
        case 'contas_a_pagar': $result = SystemCore::deleteById('contas_a_pagar', $id); break;
        case 'contas_a_receber': $result = SystemCore::deleteById('contas_a_receber', $id); break;
        default: $result = ['success' => false, 'message' => 'Ação não permitida.'];
    }
    $msg = $result;
    // Redireciona para limpar o GET
    header('Location: index.php?page=' . $page . '&msg=' . ($result['success'] ? 'success' : 'danger')); exit;
}
if (isset($_GET['msg'])) {
    $msg = ['type' => $_GET['msg'] === 'success' ? 'success' : 'danger', 'text' => 'Operação concluída.'];
    if ($_GET['msg'] === 'danger') $msg['text'] = 'Operação falhou. Verifique se há dados dependentes.';
}


// --- CARREGAR DADOS PARA EDIÇÃO ---
if ($id && $action === 'edit') {
    // CORREÇÃO: Usando switch para compatibilidade com PHP 7.x
    $table = null;
    switch ($page) {
        case 'usuarios': $table = 'users'; break;
        case 'transportadoras': $table = 'transportadoras'; break;
        case 'categorias': $table = 'categorias'; break;
        case 'mercadorias': $table = 'mercadorias'; break;
        case 'despachos': $table = 'despachos'; break;
        case 'clientes': $table = 'clientes'; break;
        case 'oportunidades': $table = 'oportunidades'; break;
        case 'pedidos_venda': $table = 'pedidos_venda'; break;
        case 'plano_contas': $table = 'plano_contas'; break;
        case 'contas_a_pagar': $table = 'contas_a_pagar'; break;
        case 'contas_a_receber': $table = 'contas_a_receber'; break;
    }
    
    if ($table) {
        $edit_data = SystemCore::getById($table, $id);
        if (!$edit_data) {
            $msg = ['type' => 'danger', 'text' => 'Registro não encontrado.'];
            $id = null;
        }
    }
}


// --- INÍCIO DO HTML ---
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> | <?php echo ucfirst(str_replace('_', ' ', $page)); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="css/sb-admin-2.min.css" rel="stylesheet"> 
</head>

<?php if ($page === 'login' || $page === 'rastrear'): ?>
    <body class="bg-gradient-primary">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-10 col-lg-12 col-md-9">
                    <div class="card o-hidden border-0 shadow-lg my-5">
                        <div class="card-body p-0">
                            <?php if ($page === 'login'): ?>
                                <?php include 'views/_login.php'; ?>
                            <?php else: ?>
                                <?php include 'views/_rastrear.php'; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>

<?php else: ?>
    <body id="page-top">
        <div id="wrapper">
            
            <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
                <a class="sidebar-brand d-flex align-items-center justify-content-center" href="index.php?page=dashboard">
                    <div class="sidebar-brand-text mx-3"><?php echo SITE_NAME; ?></div>
                </a>
                <hr class="sidebar-divider my-0">
                
                <?php 
                    // Carrega a configuração de permissões para montar o menu dinamicamente
                    // Nota: 'menu_access_roles' é apenas um exemplo de chave de configuração.
                    $menu_items = SystemCore::getConfig('menu_access_roles') ?? [];
                    $current_role = $currentUser['role'] ?? 'guest';
                    
                    // Definindo a lista completa de módulos e seus ícones para exibição no menu
                    $all_modules = [
                        'dashboard' => 'fas fa-tachometer-alt',
                        // WMS/Logística
                        'mercadorias' => 'fas fa-boxes',
                        'movimentacao_estoque' => 'fas fa-exchange-alt',
                        'despachos' => 'fas fa-shipping-fast',
                        'transportadoras' => 'fas fa-truck',
                        'categorias' => 'fas fa-tags',
                        // CRM/Vendas
                        'clientes' => 'fas fa-handshake',
                        'oportunidades' => 'fas fa-bullhorn',
                        'pedidos_venda' => 'fas fa-shopping-cart',
                        // Financeiro
                        'plano_contas' => 'fas fa-clipboard-list',
                        'contas_a_pagar' => 'fas fa-money-bill-wave',
                        'contas_a_receber' => 'fas fa-hand-holding-usd',
                        // Admin
                        'usuarios' => 'fas fa-users',
                        'configuracoes' => 'fas fa-cogs',
                        'perfil' => 'fas fa-user',
                    ];
                ?>
                
                <?php foreach ($all_modules as $menu_page => $icon): ?>
                    <?php 
                        // Regra de permissão simplificada: Admin vê tudo. Outros perfis dependem da configuração ou são exibidos por padrão.
                        $is_allowed = ($current_role === 'admin' || (isset($menu_items[$menu_page]) && in_array($current_role, $menu_items[$menu_page])));
                        
                        // Garante que o dashboard e perfil são sempre visíveis
                        if ($menu_page === 'dashboard' || $menu_page === 'perfil') $is_allowed = true;
                    ?>
                    <?php if ($is_allowed): 
                        $display_name = ucfirst(str_replace('_', ' ', $menu_page));
                    ?>
                        <li class="nav-item <?php echo ($page === $menu_page) ? 'active' : ''; ?>">
                            <a class="nav-link" href="index.php?page=<?php echo $menu_page; ?>">
                                <i class="<?php echo $icon; ?>"></i>
                                <span><?php echo $display_name; ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
                
                <hr class="sidebar-divider d-none d-md-block">
                
                <div class="text-center d-none d-md-inline">
                    <button class="rounded-circle border-0" id="sidebarToggle"></button>
                </div>
            </ul>
            
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item dropdown no-arrow">
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?php echo $currentUser['nome']; ?></span>
                                    <img class="img-profile rounded-circle" src="<?php echo $currentUser['foto_perfil'] ?? 'img/undraw_profile.svg'; ?>">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="index.php?page=perfil">
                                        <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                                        Perfil
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="index.php?page=logout">
                                        <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                                        Sair
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </nav>
                    
                    <div class="container-fluid">
                        <?php 
                            // Inclui a view correspondente à página
                            $view_path = 'views/_' . $page . '.php';
                            if (file_exists($view_path)) {
                                include $view_path;
                            } else {
                                include 'views/_404.php';
                            }
                        ?>
                    </div>
                </div>
                
                <footer class="sticky-footer bg-white">
                    <div class="container my-auto">
                        <div class="copyright text-center my-auto">
                            <span>Copyright &copy; <?php echo SITE_NAME; ?> <?php echo date('Y'); ?></span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="js/sb-admin-2.min.js"></script>
    </body>
<?php endif; ?>
</html>
