<?php
/**
 * VIEW: _configuracoes.php
 * Página de Configurações do Sistema (ADMINISTRAÇÃO)
 * Permite a edição de configurações globais, como o acesso ao menu.
 */

if (!Auth::isLoggedIn() || $currentUser['role'] !== 'admin') {
    // Redirecionamento de segurança, embora o index.php já faça a checagem
    header('Location: index.php?page=dashboard');
    exit;
}

// Carrega as configurações atuais
$menu_access_roles_json = SystemCore::getConfigRaw('menu_access_roles');
$menu_access_roles = json_decode($menu_access_roles_json, true) ?? [];

// Lista de todos os módulos e roles
$all_modules = [
    'dashboard' => 'Dashboard',
    'mercadorias' => 'Mercadorias',
    'movimentacao_estoque' => 'Movimentação Estoque',
    'despachos' => 'Despachos',
    'transportadoras' => 'Transportadoras',
    'categorias' => 'Categorias',
    'clientes' => 'Clientes',
    'oportunidades' => 'Oportunidades',
    'pedidos_venda' => 'Pedidos de Venda',
    'plano_contas' => 'Plano de Contas',
    'contas_a_pagar' => 'Contas a Pagar',
    'contas_a_receber' => 'Contas a Receber',
    'usuarios' => 'Usuários',
    'configuracoes' => 'Configurações',
    'perfil' => 'Meu Perfil',
];

$all_roles = ['admin', 'despachante', 'visualizador'];

// Processamento do formulário de configurações
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_config'])) {
    $new_config = [];
    foreach ($all_modules as $module => $name) {
        $new_config[$module] = $_POST['roles'][$module] ?? [];
    }
    
    $result = SystemCore::saveConfig('menu_access_roles', json_encode($new_config));
    $msg = $result;
    
    // Recarrega a página para refletir as mudanças e evitar reenvio
    if ($result['success']) {
        header('Location: index.php?page=configuracoes&msg=success');
        exit;
    }
}

?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Configurações do Sistema</h1>

    <?php if (isset($msg)): ?>
        <div class="alert alert-<?php echo $msg['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $msg['text']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Controle de Acesso ao Menu por Perfil</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="index.php?page=configuracoes">
                <input type="hidden" name="save_config" value="1">
                
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Módulo</th>
                            <?php foreach ($all_roles as $role): ?>
                                <th class="text-center"><?php echo ucfirst($role); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_modules as $module => $name): ?>
                            <tr>
                                <td><?php echo $name; ?></td>
                                <?php foreach ($all_roles as $role): ?>
                                    <td class="text-center">
                                        <?php 
                                            $checked = isset($menu_access_roles[$module]) && in_array($role, $menu_access_roles[$module]);
                                            // O módulo de Configurações só pode ser acessado pelo Admin
                                            $disabled = ($module === 'configuracoes' && $role !== 'admin') || ($module === 'dashboard' || $module === 'perfil');
                                            if ($module === 'dashboard' || $module === 'perfil') $checked = true; // Sempre permitido
                                        ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" 
                                                name="roles[<?php echo $module; ?>][]" 
                                                value="<?php echo $role; ?>" 
                                                id="check-<?php echo $module; ?>-<?php echo $role; ?>"
                                                <?php echo $checked ? 'checked' : ''; ?>
                                                <?php echo $disabled ? 'disabled' : ''; ?>
                                            >
                                        </div>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <button type="submit" class="btn btn-primary">Salvar Configurações</button>
            </form>
        </div>
    </div>
</div>
