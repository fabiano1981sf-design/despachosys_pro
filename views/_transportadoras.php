<?php
/**
 * VIEW: _transportadoras.php
 * Módulo de Transportadoras (WMS & LOGÍSTICA)
 */

// Lógica de carregamento de dados
$transportadoras = SystemCore::getTransportadoras();

// Título da página
$page_title = 'Transportadoras';
$module_name = 'transportadoras';
$form_action = 'index.php?page=' . $module_name . ($id ? '&action=edit&id=' . $id : '');

// Dados para edição
$edit_data = $edit_data ?? ['id' => '', 'nome' => '', 'telefone' => ''];

?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?php echo $page_title; ?></h1>

    <?php if (isset($msg)): ?>
        <div class="alert alert-<?php echo $msg['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $msg['text']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Formulário de Cadastro/Edição -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $id ? 'Editar' : 'Cadastrar Nova'; ?> Transportadora</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo $form_action; ?>">
                <?php if ($id): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nome" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($edit_data['nome']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($edit_data['telefone']); ?>">
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary"><?php echo $id ? 'Salvar Alterações' : 'Cadastrar'; ?></button>
                <?php if ($id): ?>
                    <a href="index.php?page=<?php echo $module_name; ?>" class="btn btn-secondary">Cancelar Edição</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Tabela de Registros -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Transportadoras</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transportadoras as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo htmlspecialchars($item['nome']); ?></td>
                                <td><?php echo htmlspecialchars($item['telefone']); ?></td>
                                <td>
                                    <a href="index.php?page=<?php echo $module_name; ?>&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">Editar</a>
                                    <a href="index.php?page=<?php echo $module_name; ?>&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta transportadora?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
