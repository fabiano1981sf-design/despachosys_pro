<?php
/**
 * VIEW: _perfil.php
 * Módulo de Perfil do Usuário (ADMINISTRAÇÃO)
 */

// Título da página
$page_title = 'Meu Perfil';
$module_name = 'perfil';
$form_action = 'index.php?page=' . $module_name;

// Dados do usuário logado
$edit_data = $currentUser;

?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?php echo $page_title; ?></h1>

    <?php if (isset($msg)): ?>
        <div class="alert alert-<?php echo $msg['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $msg['text']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Formulário de Edição de Perfil -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informações Pessoais</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo $form_action; ?>" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($edit_data['nome']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($edit_data['email']); ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Nova Senha (Deixe em branco para não alterar)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="password_confirm" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="foto_perfil" class="form-label">Foto de Perfil</label>
                        <input type="file" class="form-control" id="foto_perfil" name="foto_perfil" accept="image/*">
                        <?php if ($edit_data['foto_perfil']): ?>
                            <small class="text-muted">Foto atual: <a href="<?php echo $edit_data['foto_perfil']; ?>" target="_blank">Visualizar</a></small>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nível de Acesso</label>
                        <p class="form-control-static"><strong><?php echo ucfirst($edit_data['role']); ?></strong></p>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </form>
        </div>
    </div>
</div>
