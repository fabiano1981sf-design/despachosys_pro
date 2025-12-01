<?php  
/** * VIEW: _contas_a_pagar.php  
 * Módulo de Contas a Pagar (FINANCEIRO)  
 */  
  
// Lógica de carregamento de dados  
$contas = SystemCore::getContasPagar();  
$plano_contas = SystemCore::getPlanoContas();  
  
// Título da página  
$page_title = 'Contas a Pagar';  
$module_name = 'contas_a_pagar';  
$form_action = 'index.php?page=' . $module_name . ($id ? '&action=edit&id=' . $id : '');  
  
// Dados para edição  
$edit_data = $edit_data ?? [  
 'id' => '', 'descricao' => '', 'valor' => '', 'data_vencimento' => '',   
'data_pagamento' => '', 'status' => 'Aberto', 'fornecedor_nome' => '',   
'plano_conta_id' => ''  
];  
  
$status_options = ['Aberto', 'Pago', 'Vencido'];  
  
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
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $id ? 'Editar' : 'Cadastrar Nova'; ?> Conta a Pagar</h6>  
        </div>  
        <div class="card-body">  
            <form method="POST" action="<?php echo $form_action; ?>">  
                <?php if ($id): ?>  
                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">  
                <?php endif; ?>  
              
                <div class="row">  
                    <div class="col-md-6 mb-3">  
                        <label for="descricao" class="form-label">Descrição</label>  
                        <input type="text" class="form-control" id="descricao" name="descricao" value="<?php echo htmlspecialchars($edit_data['descricao']); ?>" required>  
                    </div>  
                    <div class="col-md-3 mb-3">  
                        <label for="valor" class="form-label">Valor (R$)</label>  
                        <input type="number" step="0.01" class="form-control" id="valor" name="valor" value="<?php echo htmlspecialchars($edit_data['valor']); ?>" required>  
                    </div>  
                    <div class="col-md-3 mb-3">  
                        <label for="data_vencimento" class="form-label">Data de Vencimento</label>  
                        <input type="date" class="form-control" id="data_vencimento" name="data_vencimento" value="<?php echo htmlspecialchars($edit_data['data_vencimento']); ?>" required>  
                    </div>  
                </div>  
              
                <div class="row">  
                    <div class="col-md-4 mb-3">  
                        <label for="fornecedor_nome" class="form-label">Fornecedor</label>  
                        <input type="text" class="form-control" id="fornecedor_nome" name="fornecedor_nome" value="<?php echo htmlspecialchars($edit_data['fornecedor_nome']); ?>">  
                    </div>  
                    <div class="col-md-4 mb-3">  
                        <label for="plano_conta_id" class="form-label">Plano de Contas</label>  
                        <select class="form-control" id="plano_conta_id" name="plano_conta_id">  
                            <option value="">Selecione</option>  
                            <?php foreach ($plano_contas as $pc): ?>  
                            <option value="<?php echo $pc['id']; ?>" <?php echo ($edit_data['plano_conta_id'] == $pc['id']) ? 'selected' : ''; ?>>  
                                <?php echo htmlspecialchars($pc['codigo'] . ' - ' . $pc['descricao']); ?>  
                            </option>  
                            <?php endforeach; ?>  
                        </select>  
                    </div>  
                    <div class="col-md-2 mb-3">  
                        <label for="status" class="form-label">Status</label>  
                        <select class="form-control" id="status" name="status" required>  
                            <?php foreach ($status_options as $status): ?>  
                            <option value="<?php echo $status; ?>" <?php echo ($edit_data['status'] == $status) ? 'selected' : ''; ?>>  
                                <?php echo $status; ?>  
                            </option>  
                            <?php endforeach; ?>  
                        </select>  
                    </div>  
                    <div class="col-md-2 mb-3">  
                        <label for="data_pagamento" class="form-label">Data de Pagamento</label>  
                        <input type="date" class="form-control" id="data_pagamento" name="data_pagamento" value="<?php echo htmlspecialchars($edit_data['data_pagamento']); ?>">  
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
            <h6 class="m-0 font-weight-bold text-primary">Lista de Contas a Pagar</h6>  
        </div>  
        <div class="card-body">  
            <div class="table-responsive">  
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">  
                    <thead>  
                        <tr>  
                            <th>ID</th>  
                            <th>Vencimento</th>  
                            <th>Descrição</th>  
                            <th>Valor</th>  
                            <th>Fornecedor</th>  
                            <th>Status</th>  
                            <th>Ações</th>  
                        </tr>  
                    </thead>  
                    <tbody>  
                        <?php foreach ($contas as $conta): ?>  
                        <tr>  
                            <td><?php echo $conta['id']; ?></td>  
                            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($conta['data_vencimento']))); ?></td>  
                            <td><?php echo htmlspecialchars($conta['descricao']); ?></td>  
                            <td>R$ <?php echo number_format($conta['valor'], 2, ',', '.'); ?></td>  
                            <td><?php echo htmlspecialchars($conta['fornecedor_nome']); ?></td>  
                            <td>  
                                <?php   
                                $status = $conta['status'];  
                                $badge_class = 'bg-secondary';  
                                if ($status == 'Pago') $badge_class = 'bg-success';  
                                elseif ($status == 'Vencido') $badge_class = 'bg-danger';  
                                elseif ($status == 'Aberto') $badge_class = 'bg-warning text-dark';  
                                echo "<span class=\"badge $badge_class\">" . $status . "</span>";  
                                ?>  
                            </td>  
                            <td>  
                                <a href="index.php?page=<?php echo $module_name; ?>&action=edit&id=<?php echo $conta['id']; ?>" class="btn btn-sm btn-info">Editar</a>  
                                <?php if ($status != 'Pago'): ?>  
                                <a href="index.php?page=<?php echo $module_name; ?>&action=pay&id=<?php echo $conta['id']; ?>" class="btn btn-sm btn-success">Pagar</a>  
                                <?php endif; ?>  
                                <a href="index.php?page=<?php echo $module_name; ?>&action=delete&id=<?php echo $conta['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta conta?');">Excluir</a>  
                            </td>  
                        </tr>  
                        <?php endforeach; ?>  
                    </tbody>  
                </table>  
            </div>  
        </div>  
    </div>  
</div>