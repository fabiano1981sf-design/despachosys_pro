<?php
/**
 * VIEW: _pedidos_venda.php
 * Módulo de Pedidos de Venda (CRM & VENDAS)
 */

// Lógica de carregamento de dados
$pedidos = SystemCore::getPedidosVenda();
$clientes = SystemCore::getClientes();
$vendedores = SystemCore::getUsers(); // Usuários como vendedores

// Título da página
$page_title = 'Pedidos de Venda';
$module_name = 'pedidos_venda';
$form_action = 'index.php?page=' . $module_name . ($id ? '&action=edit&id=' . $id : '');

// Dados para edição
$edit_data = $edit_data ?? [
    'id' => '', 'numero_pedido' => '', 'data_pedido' => date('Y-m-d'), 
    'cliente_id' => '', 'vendedor_id' => '', 'valor_total' => '', 
    'status' => 'Pendente'
];

$status_options = ['Pendente', 'Confirmado', 'Em Separação', 'Faturado', 'Cancelado'];

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
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $id ? 'Editar' : 'Cadastrar Novo'; ?> Pedido de Venda</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo $form_action; ?>">
                <?php if ($id): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="numero_pedido" class="form-label">Número do Pedido</label>
                        <input type="text" class="form-control" id="numero_pedido" name="numero_pedido" value="<?php echo htmlspecialchars($edit_data['numero_pedido']); ?>" required>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="data_pedido" class="form-label">Data do Pedido</label>
                        <input type="date" class="form-control" id="data_pedido" name="data_pedido" value="<?php echo htmlspecialchars($edit_data['data_pedido']); ?>" required>
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
                        <label for="vendedor_id" class="form-label">Vendedor</label>
                        <select class="form-control" id="vendedor_id" name="vendedor_id" required>
                            <option value="">Selecione o Vendedor</option>
                            <?php foreach ($vendedores as $vend): ?>
                                <option value="<?php echo $vend['id']; ?>" <?php echo ($edit_data['vendedor_id'] == $vend['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($vend['nome']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="valor_total" class="form-label">Valor Total (R$)</label>
                        <input type="number" step="0.01" class="form-control" id="valor_total" name="valor_total" value="<?php echo htmlspecialchars($edit_data['valor_total']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
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
            <h6 class="m-0 font-weight-bold text-primary">Lista de Pedidos de Venda</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nº Pedido</th>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Vendedor</th>
                            <th>Valor Total</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo htmlspecialchars($item['numero_pedido']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($item['data_pedido'])); ?></td>
                                <td><?php echo htmlspecialchars($item['cliente_nome']); ?></td>
                                <td><?php echo htmlspecialchars($item['vendedor_nome']); ?></td>
                                <td>R$ <?php echo number_format($item['valor_total'], 2, ',', '.'); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        if ($item['status'] === 'Faturado') echo 'success';
                                        else if ($item['status'] === 'Cancelado') echo 'danger';
                                        else if ($item['status'] === 'Confirmado') echo 'info';
                                        else echo 'warning';
                                    ?>">
                                        <?php echo $item['status']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?page=<?php echo $module_name; ?>&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">Editar</a>
                                    <a href="index.php?page=<?php echo $module_name; ?>&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este pedido?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
