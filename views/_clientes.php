<?php
/**
 * VIEW: _clientes.php
 * Módulo de Clientes (CRM & VENDAS)
 */

// Lógica de carregamento de dados
$clientes = SystemCore::getClientes();

// Título da página
$page_title = 'Clientes';
$module_name = 'clientes';
$form_action = 'index.php?page=' . $module_name . ($id ? '&action=edit&id=' . $id : '');

// Dados para edição
$edit_data = $edit_data ?? [
    'id' => '', 'tipo' => 'PF', 'nome_razao' => '', 'documento' => '', 
    'email' => '', 'telefone' => '', 'endereco' => '', 'cidade_estado' => '', 
    'potencial' => 'Médio'
];

$tipo_options = ['PF' => 'Pessoa Física', 'PJ' => 'Pessoa Jurídica'];
$potencial_options = ['Alto', 'Médio', 'Baixo'];

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
            <h6 class="m-0 font-weight-bold text-primary"><?php echo $id ? 'Editar' : 'Cadastrar Novo'; ?> Cliente</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="<?php echo $form_action; ?>">
                <?php if ($id): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="tipo" class="form-label">Tipo</label>
                        <select class="form-control" id="tipo" name="tipo" required>
                            <?php foreach ($tipo_options as $key => $value): ?>
                                <option value="<?php echo $key; ?>" <?php echo ($edit_data['tipo'] == $key) ? 'selected' : ''; ?>>
                                    <?php echo $value; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5 mb-3">
                        <label for="nome_razao" class="form-label">Nome / Razão Social</label>
                        <input type="text" class="form-control" id="nome_razao" name="nome_razao" value="<?php echo htmlspecialchars($edit_data['nome_razao']); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="documento" class="form-label">CPF / CNPJ</label>
                        <input type="text" class="form-control" id="documento" name="documento" value="<?php echo htmlspecialchars($edit_data['documento']); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($edit_data['email']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" value="<?php echo htmlspecialchars($edit_data['telefone']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="potencial" class="form-label">Potencial</label>
                        <select class="form-control" id="potencial" name="potencial" required>
                            <?php foreach ($potencial_options as $potencial): ?>
                                <option value="<?php echo $potencial; ?>" <?php echo ($edit_data['potencial'] == $potencial) ? 'selected' : ''; ?>>
                                    <?php echo $potencial; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="endereco" class="form-label">Endereço</label>
                        <input type="text" class="form-control" id="endereco" name="endereco" value="<?php echo htmlspecialchars($edit_data['endereco']); ?>">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="cidade_estado" class="form-label">Cidade/Estado</label>
                        <input type="text" class="form-control" id="cidade_estado" name="cidade_estado" value="<?php echo htmlspecialchars($edit_data['cidade_estado']); ?>">
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
            <h6 class="m-0 font-weight-bold text-primary">Lista de Clientes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome/Razão Social</th>
                            <th>Tipo</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Potencial</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $item): ?>
                            <tr>
                                <td><?php echo $item['id']; ?></td>
                                <td><?php echo htmlspecialchars($item['nome_razao']); ?></td>
                                <td><?php echo $item['tipo']; ?></td>
                                <td><?php echo htmlspecialchars($item['email']); ?></td>
                                <td><?php echo htmlspecialchars($item['telefone']); ?></td>
                                <td>
                                    <span class="badge bg-<?php 
                                        if ($item['potencial'] === 'Alto') echo 'success';
                                        else if ($item['potencial'] === 'Baixo') echo 'danger';
                                        else echo 'warning';
                                    ?>">
                                        <?php echo $item['potencial']; ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?page=<?php echo $module_name; ?>&action=edit&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-info">Editar</a>
                                    <a href="index.php?page=<?php echo $module_name; ?>&action=delete&id=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este cliente?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
