<?php
/**
 * VIEW: _oportunidades.php
 * Módulo de Oportunidades (CRM & VENDAS)
 */

// Lógica de carregamento de dados
$oportunidades = SystemCore::getOportunidades();
$clientes = SystemCore::getClientes();
$responsaveis = SystemCore::getUsers(); // Usuários como responsáveis

// Título da página
$page_title = 'Oportunidades';
$module_name = 'oportunidades';
$form_action = 'index.php?page=' . $module_name . ($id ? '&action=edit&id=' . $id : '');

// Dados para edição
$edit_data = $edit_data ?? [
    'id' => '', 'titulo' => '', 'cliente_id' => '', 'responsavel_id' => '', 
    'valor_estimado' => '', 'data_fechamento_prevista' => '', 'status' => 'Prospecção'
];

$status_options = ['Prospecção', 'Qualificação', 'Proposta', 'Fechamento', 'Ganho', 'Perdido'];

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
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $id ? 'Editar' : 'Cadastrar Nova'; ?> Oportunidade</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo $form_action; ?>">
                <?php if ($id): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="titulo" class="form-label">Título da Oportunidade</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($edit_data['titulo']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cliente_id" class="form-label">Cliente</label>
                        <select class="form-control" id="cliente_id" name="cliente_id" required>
                            <option value="">Selecione o Cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?php echo $cliente['id']; ?>" <?php echo ($edit_data['cliente_id'] == $cliente['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cliente['nome_razao']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="responsavel_id" class="form-label">Responsável</label>
                        <select class="form-control" id="responsavel_id" name="responsavel_id" required>
                            <option value="">Selecione o Responsável</option>
                            <?php foreach ($responsaveis as $resp): ?>
                                <option value="<?php echo $resp['id']; ?>" <?php echo ($edit_data['responsavel_id'] == $resp['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($resp['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="valor_estimado" class="form-label">Valor Estimado (R$)</label>
                        <input type="number" step="0.01" class="form-control" id="valor_estimado" name="valor_estimado" value="<?php echo htmlspecialchars($edit_data['valor_estimado']); ?>">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="data_fechamento_prevista" class="form-label">Fechamento Previsto</label>
                        <input type="date" class="form-control" id="data_fechamento_prevista" name="data_fechamento_prevista" value="<?php echo htmlspecialchars($edit_data['data_fechamento_prevista']); ?>">
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
            <h6 class="m-0 font-weight-bold text-primary">Lista de Oportunidades</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Cliente</th>
                            <th>Valor Estimado</th>
                            <th>Fechamento Previsto</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($oportunidades as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo htmlspecialchars($item['titulo']); ?></td>
                                <td><?php echo htmlspecialchars($item['cliente_nome']); ?></td>
                                <td>R$ <?php echo number_format($item['valor_estimado'], 2, ',', '.'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($item['data_fechamento_prevista'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        if ($item['status'] === 'Ganho') echo 'success';
                                        else if ($item['status'] === 'Perdido') echo 'danger';
                                        else if ($item['status'] === 'Proposta') echo 'info';
                                        else echo 'warning';
                                    ?>">
                                        <?php echo $item['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?page=<?php echo $module_name; ?>&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">Editar</a>
                                    <a href="index.php?page=<?php echo $module_name; ?>&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir esta oportunidade?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
