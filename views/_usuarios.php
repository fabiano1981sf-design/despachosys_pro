<?php
// views/_usuarios.php - MÓDULO DE USUÁRIOS (100% corrigido e profissional)

$pageTitle = "Usuários do Sistema";
$edit = [];
$isUpdate = false;

if (isset($_GET['edit'])) {
    $edit = SystemCore::getById('users', (int)$_GET['edit']);
    $isUpdate = !empty($edit);
}

// SALVAR / EDITAR USUÁRIO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'save_user') {
    $data = [
        'id'    => $_POST['id'] ?? null,
        'nome'  => trim($_POST['nome']),
        'email' => trim($_POST['email']),
        'role'  => $_POST['role'],
        'senha' => $_POST['senha'] ?? ''
    ];

    $result = SystemCore::saveUser($data, $isUpdate);

    $tipo = $result['success'] ? 'success' : 'danger';
    echo "<div class='alert alert-$tipo alert-dismissible fade show'>
            {$result['message']}
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
    
    if ($result['success']) {
        echo "<script>setTimeout(() => location.href='index.php?page=usuarios', 1500);</script>";
    }
}

// EXCLUIR USUÁRIO
if (isset($_GET['delete'])) {
    $del = SystemCore::deleteById('users', (int)$_GET['delete']);
    echo "<script>
        alert('{$del['message']}');
        window.location='index.php?page=usuarios';
    </script>";
    exit;
}

$usuarios = SystemCore::getUsers();
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Gerenciar Usuários</h4>
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#modalUsuario">
                        Novo Usuário
                    </button>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>Foto</th>
                                    <th>Nome</th>
                                    <th>E-mail</th>
                                    <th>Cargo</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($usuarios)): ?>
                                    <tr><td colspan="5" class="text-center py-4 text-muted">Nenhum usuário cadastrado</td></tr>
                                <?php else: foreach ($usuarios as $u): ?>
                                    <tr>
                                        <td>
                                            <img src="<?= htmlspecialchars($u['foto_perfil'] ?? 'assets/img/default-avatar.png') ?>"
                                                 class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                        </td>
                                        <td><strong><?= htmlspecialchars($u['nome']) ?></strong></td>
                                        <td><?= htmlspecialchars($u['email']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : 'info' ?>">
                                                <?= ucfirst($u['role']) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="?page=usuarios&edit=<?= $u['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                            <?php if ($u['id'] != ($_SESSION['user_id'] ?? 0)): ?>
                                                <a href="?page=usuarios&delete=<?= $u['id'] ?>" 
                                                   class="btn btn-danger btn-sm" 
                                                   onclick="return confirm('Excluir este usuário?')">Excluir</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CADASTRO / EDIÇÃO -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><?= $isUpdate ? 'Editar Usuário' : 'Novo Usuário' ?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="save_user">
                    <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nome Completo *</label>
                            <input type="text" name="nome" class="form-control" 
                                   value="<?= htmlspecialchars($edit['nome'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-mail *</label>
                            <input type="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($edit['email'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cargo / Perfil</label>
                            <select name="role" class="form-select">
                                <option value="user" <?= (isset($edit['role']) && $edit['role'] === 'user') ? 'selected' : '' ?>>Usuário</option>
                                <option value="admin" <?= (isset($edit['role']) && $edit['role'] === 'admin') ? 'selected' : '' ?>>Administrador</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">
                                <?= $isUpdate ? 'Nova Senha (deixe em branco para manter)' : 'Senha *' ?>
                            </label>
                            <input type="password" name="senha" class="form-control" 
                                   <?= $isUpdate ? '' : 'required' ?> minlength="6">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar Usuário</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($isUpdate): ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new bootstrap.Modal(document.getElementById('modalUsuario')).show();
    });
</script>
<?php endif; ?>