<?php  
/** * VIEW: _plano_contas.php  
 * Módulo de Plano de Contas (FINANCEIRO)  
 */  
  
// Lógica de carregamento de dados  
$plano_contas = SystemCore::getPlanoContas();  
  
// Título da página  
$page_title = 'Plano de Contas';  
$module_name = 'plano_contas';  
$form_action = 'index.php?page=' . $module_name . ($id ? '&action=edit&id=' . $id : '');  
  
// Dados para edição  
$edit_data = $edit_data ?? ['id' => '', 'codigo' => '', 'descricao' => '', 'tipo' => 'RECEITA'];  
  
$tipo_options = ['RECEITA', 'DESPESA'];  
  
?>  
  
<div class="container-fluid">  
    <h1 class="h3 mb-4 text-gray-800"><?php echo $page_title; ?></h1>  
  
    <?php if (isset($msg)): ?>  
    <div class="alert alert-<?php echo $msg['type']; ?> alert-dismissible fade show" role="alert">  
        <?php echo $msg['text']; ?>  
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>  
    </div>  
    <?php endif; ?>  
  
    <div class="card shadow mb-4">  
        <div class="card-header py-3">  
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $id ? 'Editar' : 'Cadastrar Novo'; ?> Item do Plano de Contas</h6>  
        </div>  
        <div class="card-body">  
            <form method="POST" action="<?php echo $form_action; ?>">  
                <?php if ($id): ?>  
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">  
                <?php endif; ?>  
              
                <div class="row">  
                    <div class="col-md-3 mb-3">  
                        <label for="codigo" class="form-label">Código</label>  
                        <input type="text" class="form-control" id="codigo" name="codigo" value="<?php echo htmlspecialchars($edit_data['codigo']); ?>" required>  
                    </div>  
                    <div class="col-md-6 mb-3">  
                        <label for="descricao" class="form-label">Descrição</label>  
                        <input type="text" class="form-control" id="descricao" name="descricao" value="<?php echo htmlspecialchars($edit_data['descricao']); ?>" required>  
                    </div>  
                    <div class="col-md-3 mb-3">  
                        <label for="tipo" class="form-label">Tipo</label>  
                        <select class="form-control" id="tipo" name="tipo" required>  
                            <?php foreach ($tipo_options as $tipo): ?>  
                            <option value="<?php echo $tipo; ?>" <?php echo ($edit_data['tipo'] == $tipo) ? 'selected' : ''; ?>>  
                                <?php echo $tipo; ?>  
                            </option>  
                            <?php endforeach; ?>  
                        </select>  
                    </div>  
                </div>  
              
                <button type="submit" class="btn btn-primary"><?php echo $id ? 'Salvar Alterações' : 'Cadastrar'; ?></button>  
                <?php if ($id): ?>  
                <a href="index.php?page=<?php echo $module_name; ?>" class="btn btn-secondary">Cancelar Edição</a>  
                <?php endif; ?>  
            </form>  
        </div>  
    </div>  
  
    <div class="card shadow mb-4">  
        <div class="card-header py-3">  
            <h6 class="m-0 font-weight-bold text-primary">Lista de Contas</h6>  
        </div>  
        <div class="card-body">  
            <div class="table-responsive">  
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">  
                    <thead>  
                        <tr>  
                            <th>ID</th>  
                            <th>Código</th>  
                            <th>Descrição</th>  
                            <th>Tipo</th>  
                            <th>Ações</th>  
                        </tr>  
                    </thead>  
                    <tbody>  
                        <?php foreach ($plano_contas as $item): ?>  
                        <tr>  
                            <td><?php echo $item['id']; ?></td>  
                            <td><?php echo htmlspecialchars($item['codigo']); ?></td>  
                            <td><?php echo htmlspecialchars($item['descricao']); ?></td>  
                            <td>  
                                <?php   
                                $badge_class = ($item['tipo'] == 'RECEITA') ? 'bg-success' : 'bg-danger';  
                                echo "<span class=\"badge $badge_class\">" . $item['tipo'] . "</span>";  
                                ?>  
                            </td>  
                            <td>  
                                <a href="index.php?page=<?php echo $module_name; ?>&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">Editar</a>  
                                <a href="index.php?page=<?php echo $module_name; ?>&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta conta?');">Excluir</a>  
                            </td>  
                        </tr>  
                        <?php endforeach; ?>  
                    </tbody>  
                </table>  
            </div>  
        </div>  
    </div>  
</div>