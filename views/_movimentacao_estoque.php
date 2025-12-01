<?php
// views/_movimentacao_estoque.php – 100% IGUAL AO MÓDULO MERCADORIAS (28/Nov/2025)

$pageTitle = "Movimentação de Estoque";

// Processa movimentação
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'movimentar') {
    $result = SystemCore::addEstoqueMovement($_POST);
    $tipo = $result['success'] ? 'success' : 'danger';
    $mensagem = "<div class='alert alert-$tipo alert-dismissible fade show mb-4'>
                    <i class='fas fa-" . ($result['success'] ? "check-circle" : "exclamation-triangle") . " me-2'></i>
                    <strong>" . ($result['success'] ? "Sucesso!" : "Erro!") . "</strong> {$result['message']}
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                  </div>";
}

$movimentos  = SystemCore::getEstoqueMovements();
$mercadorias = SystemCore::getMercadorias();
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white"> <!-- COR DO CABEÇALHO -->
                <h4 class="card-title mb-0">
                    <i class="fas fa-exchange-alt me-2"></i> Movimentação de Estoque
                </h4>
            </div>
            <div class="card-body">

                <?= $mensagem ?>

                <!-- FORMULÁRIO 100% IGUAL AO DO MERCADORIAS -->
                <form method="POST" class="row g-3 mb-4 align-items-end">
                    <input type="hidden" name="action" value="movimentar">

                    <div class="col-md-5">
                        <label class="form-label">Mercadoria</label>
                        <select name="mercadoria_id" class="form-select" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($mercadorias as $m): ?>
                                <option value="<?= $m['id'] ?>">
                                    [<?= htmlspecialchars($m['sku'] ?? '') ?>] <?= htmlspecialchars($m['nome']) ?>
                                    (Estoque: <?= number_format($m['quantidade_estoque'] ?? 0) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Tipo</label>
                        <select name="tipo" class="form-select" required>
                            <option value="entrada">Entrada</option>
                            <option value="saida">Saída</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Quantidade</label>
                        <input type="number" name="quantidade" class="form-control" min="1" required>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Motivo (opcional)</label>
                        <input type="text" name="motivo" class="form-control" placeholder="Ex: Compra">
                    </div>

                    <div class="col-md-1">
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check"></i>
                        </button>
                    </div>
                </form>

                <!-- TABELA 100% IGUAL À DO MERCADORIAS -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Mercadoria</th>
                                <th>SKU</th>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                <th>Motivo</th>
                                <th>Usuário</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($movimentos)): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-5">
                                        Nenhuma movimentação registrada
                                    </td>
                                </tr>
                            <?php else: foreach ($movimentos as $mov): ?>
                                <tr>
                                    <td class="text-nowrap"><?= date('d/m/Y H:i', strtotime($mov['created_at'] ?? 'now')) ?></td>
                                    <td><?= htmlspecialchars($mov['mercadoria_nome'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($mov['sku'] ?? '') ?></td>
                                    <td>
                                        <span class="badge bg-<?= ($mov['tipo'] ?? '') === 'entrada' ? 'success' : 'danger' ?>">
                                            <?= ($mov['tipo'] ?? '') === 'entrada' ? 'Entrada' : 'Saída' ?>
                                        </span>
                                    </td>
                                    <td class="text-end fw-bold">
                                        <?= ($mov['tipo'] ?? '') === 'entrada' ? '+' : '-' ?>
                                        <?= number_format($mov['quantidade'] ?? 0) ?>
                                    </td>
                                    <td><?= htmlspecialchars($mov['motivo'] ?? '') ?: '-' ?></td>
                                    <td><?= htmlspecialchars($mov['usuario_nome'] ?? 'Sistema') ?></td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>