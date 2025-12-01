<?php
$pageTitle = "Despachos";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_despacho') {
    $result = SystemCore::saveDespacho($_POST);
    $_SESSION['flash'] = [
        'type' => $result['success'] ? 'success' : 'danger',
        'msg' => $result['message']
    ];
    header("Location: index.php?page=despachos");
    exit;
}

$despachos = SystemCore::getDespachos();
$clientes = SystemCore::getClientes();
$mercadorias = SystemCore::getMercadorias();
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>

<div class="container-fluid py-4">
    <?php if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
            <strong><?= $flash['type'] === 'success' ? 'Sucesso!' : 'Erro!' ?></strong>
            <?= htmlspecialchars($flash['msg']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-lg border-0 rounded-4 mb-4">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0">Novo Despacho</h4>
            <button class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#modalDespacho">
                Lançar Despacho
            </button>
        </div>
    </div>

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white py-3">
            <h4 class="mb-0">Histórico de Despachos</h4>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Data</th>
                            <th>Cliente</th>
                            <th>Mercadoria</th>
                            <th>Qtd</th>
                            <th>Rastreio</th>
                            <th>Transportadora</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($despachos)): ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">Nenhum despacho realizado</td></tr>
                        <?php else: foreach ($despachos as $d): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($d['created_at'])) ?></td>
                                <td><strong><?= htmlspecialchars($d['cliente_nome'] ?? '—') ?></strong></td>
                                <td><?= htmlspecialchars($d['mercadoria_nome']) ?> <small class="text-muted">(<?= $d['sku'] ?>)</small></td>
                                <td><span class="badge bg-info fs-6"><?= $d['quantidade'] ?></span></td>
                                <td><code><?= htmlspecialchars($d['codigo_rastreio'] ?: '—') ?></code></td>
                                <td><?= htmlspecialchars($d['transportadora'] ?: '—') ?></td>
                                <td><span class="badge bg-success">Despachado</span></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DESPACHO -->
<div class="modal fade" id="modalDespacho" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="formDespacho">
            <input type="hidden" name="action" value="save_despacho">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">Lançar Despacho</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="row g-3">
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-bold">Cliente *</label>
                            <select name="cliente_id" class="form-select form-select-lg" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($clientes as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-bold">Mercadoria *</label>
                            <select name="mercadoria_id" class="form-select form-select-lg" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($mercadorias as $m): ?>
                                    <option value="<?= $m['id'] ?>">
                                        <?= htmlspecialchars($m['nome']) ?> (Estoque: <?= $m['quantidade_estoque'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6 col-lg-3">
                            <label class="form-label fw-bold">Quantidade *</label>
                            <input type="number" name="quantidade" class="form-control form-control-lg" min="1" required>
                        </div>
                        <div class="col-6 col-lg-4">
                            <label class="form-label fw-bold">Código de Rastreio</label>
                            <input type="text" name="codigo_rastreio" class="form-control form-control-lg text-uppercase">
                        </div>
                        <div class="col-12 col-lg-5">
                            <label class="form-label fw-bold">Transportadora</label>
                            <input type="text" name="transportadora" class="form-control form-control-lg">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Observação</label>
                            <textarea name="observacao" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success btn-lg px-5">Despachar</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('formDespacho').addEventListener('submit', function() {
    this.querySelector('button[type=submit]').disabled = true;
    this.querySelector('button[type=submit]').innerHTML = 'Despachando...';
});
</script>