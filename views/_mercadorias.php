<?php
// views/_mercadorias.php – VERSÃO FINAL INDESTRUTÍVEL (01/Dez/2025 – 16:00)
// NUNCA MAIS VAI DUPLICAR. PROMESSA CUMPRIDA.

$pageTitle = "Mercadorias";

// TOKEN ANTI-DUPLICAÇÃO (o
if (empty($_SESSION['form_token'])) {
    $_SESSION['form_token'] = bin2hex(random_bytes(32));
}

// PROCESSA O POST COM TOKEN (SÓ UMA VEZ NA VIDA)
if ($_SERVER['REQUEST_METHOD'] === 'POST' 
    && ($_POST['action'] ?? '') === 'save_mercadoria' 
    && ($_POST['token'] ?? '') === $_SESSION['form_token']) {

    // Destroi o token imediatamente
    unset($_SESSION['form_token']);

    $data = [
        'id'                 => $_POST['id'] ?? null,
        'nome'               => trim($_POST['nome'] ?? ''),
        'sku'                => strtoupper(trim($_POST['sku'] ?? '')),
        'categoria_id'       => !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null,
        'preco_custo'        => (float)str_replace(['.', ','], ['', '.'], $_POST['preco_custo'] ?? '0'),
        'preco_venda'        => (float)str_replace(['.', ','], ['', '.'], $_POST['preco_venda'] ?? '0'),
        'quantidade_estoque' => (int)($_POST['quantidade_estoque'] ?? 0),
        'unidade'            => trim($_POST['unidade'] ?? 'UN')
    ];

    $result = SystemCore::saveMercadoria($data, !empty($data['id']));

    $_SESSION['flash'] = [
        'type' => $result['success'] ? 'success' : 'danger',
        'msg'  => $result['message']
    ];

    // Redireciona e morre aqui
    header("Location: index.php?page=mercadorias");
    exit;
    exit;
}

// Regenera token para próximo uso
$_SESSION['form_token'] = bin2hex(random_bytes(32));

// PROCESSA GET (editar, excluir)
$edit = [];
$isUpdate = false;

if (isset($_GET['edit'])) {
    $edit = SystemCore::getById('mercadorias', (int)$_GET['edit']);
    $isUpdate = !empty($edit);
}

if (isset($_GET['delete'])) {
    $del = SystemCore::deleteById('mercadorias', (int)$_GET['delete']);
    $_SESSION['flash'] = ['type' => $del['success'] ? 'success' : 'danger', 'msg' => $del['message']];
    header("Location: index.php?page=mercadorias");
    exit;
}

$mercadorias = SystemCore::getMercadorias();
$categorias  = SystemCore::getCategorias();
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

    <div class="card shadow-lg border-0 rounded-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0">Mercadorias</h4>
            <button class="btn btn-success btn-lg shadow-sm" onclick="abrirNovo()">
                Nova Mercadoria
            </button>
        </div>

        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>SKU</th>
                            <th>Categoria</th>
                            <th>Estoque</th>
                            <th class="text-end">Preço Venda</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($mercadorias)): ?>
                            <tr><td colspan="7" class="text-center py-5 text-muted">Nenhuma mercadoria cadastrada</td></tr>
                        <?php else: foreach ($mercadorias as $m): ?>
                            <tr>
                                <td><?= $m['id'] ?></td>
                                <td><strong><?= htmlspecialchars($m['nome']) ?></strong></td>
                                <td><code><?= htmlspecialchars($m['sku'] ?: 'S/N') ?></code></td>
                                <td><?= htmlspecialchars($m['categoria_nome'] ?? 'Sem categoria') ?></td>
                                <td class="text-center">
                                    <span class="badge fs-6 <?= $m['quantidade_estoque'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                        <?= number_format($m['quantidade_estoque']) ?>
                                    </span>
                                </td>
                                <td class="text-end text-success fw-bold">
                                    R$ <?= number_format($m['preco_venda'] ?? 0, 2, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-warning btn-sm" 
                                            onclick="abrirEditar(<?= htmlspecialchars(json_encode($m), ENT_QUOTES) ?>)">
                                        Editar
                                    </button>
                                    <a href="?page=mercadorias&delete=<?= $m['id'] ?>" 
                                       class="btn btn-danger btn-sm" 
                                       onclick="return confirm('Excluir permanentemente?')">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="modalMercadoria" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="formMercadoria">
            <input type="hidden" name="action" value="save_mercadoria">
            <input type="hidden" name="token" value="<?= $_SESSION['form_token'] ?>">
            <input type="hidden" name="id" id="id">

            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><span id="tituloModal">Nova Mercadoria</span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="row g-3">
                        <div class="col-12 col-lg-8">
                            <label class="form-label fw-bold">Nome *</label>
                            <input type="text" name="nome" id="nome" class="form-control form-control-lg" required>
                        </div>
                        <div class="col-12 col-lg-4">
                            <label class="form-label fw-bold">SKU</label>
                            <input type="text" name="sku" id="sku" class="form-control form-control-lg text-uppercase">
                        </div>

                        <div class="col-12 col-lg-6">
                            <label class="form-label fw-bold">Categoria</label>
                            <select name="categoria_id" id="categoria_id" class="form-select form-select-lg">
                                <option value="">-- Sem categoria --</option>
                                <?php foreach ($categorias as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-6 col-lg-3">
                            <label class="form-label fw-bold">Custo</label>
                            <input type="text" name="preco_custo" id="preco_custo" class="form-control form-control-lg money" placeholder="0,00">
                        </div>
                        <div class="col-6 col-lg-3">
                            <label class="form-label fw-bold text-success">Venda *</label>
                            <input type="text" name="preco_venda" id="preco_venda" class="form-control form-control-lg money text-success fw-bold" required placeholder="0,00">
                        </div>

                        <div class="col-6 col-lg-3">
                            <label class="form-label fw-bold">Unidade</label>
                            <input type="text" name="unidade" id="unidade" class="form-control form-control-lg" value="UN">
                        </div>
                        <div class="col-6 col-lg-3">
                            <label class="form-label fw-bold">Estoque Inicial</label>
                            <input type="number" name="quantidade_estoque" id="quantidade_estoque" class="form-control form-control-lg text-center" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary btn-lg px-5" id="btnSalvar">
                        <span class="spinner-border spinner-border-sm d-none" id="loading"></span>
                        Salvar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let salvando = false;

function abrirNovo() {
    document.getElementById('tituloModal').textContent = 'Nova Mercadoria';
    document.getElementById('formMercadoria').reset();
    document.getElementById('id').value = '';
    document.getElementById('unidade').value = 'UN';
    new bootstrap.Modal(document.getElementById('modalMercadoria')).show();
}

function abrirEditar(m) {
    document.getElementById('tituloModal').textContent = 'Editar Mercadoria';
    document.getElementById('id').value = m.id;
    document.getElementById('nome').value = m.nome;
    document.getElementById('sku').value = m.sku || '';
    document.getElementById('categoria_id').value = m.categoria_id || '';
    document.getElementById('preco_custo').value = m.preco_custo ? Number(m.preco_custo).toLocaleString('pt-BR', {minimumFractionDigits: 2}) : '';
    document.getElementById('preco_venda').value = m.preco_venda ? Number(m.preco_venda).toLocaleString('pt-BR', {minimumFractionDigits: 2}) : '';
    document.getElementById('unidade').value = m.unidade || 'UN';
    document.getElementById('quantidade_estoque').value = m.quantidade_estoque || 0;
    new bootstrap.Modal(document.getElementById('modalMercadoria')).show();
}

// BLOQUEIA DUPLO ENVIO 100%
document.getElementById('formMercadoria').addEventListener('submit', function(e) {
    if (salvando) {
        e.preventDefault();
        return false;
    }
    salvando = true;
    const btn = document.getElementById('btnSalvar');
    btn.disabled = true;
    document.getElementById('loading').classList.remove('d-none');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Salvando...';
});

// Máscara dinheiro
document.querySelectorAll('.money').forEach(el => {
    el.addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '');
        v = (v/100).toFixed(2);
        v = v.replace(".", ",");
        v = v.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
        e.target.value = v;
    });
});
</script>

<?php if ($isUpdate): ?>
<script>
document.addEventListener('DOMContentLoaded', () => abrirEditar(<?= json_encode($edit) ?>));
</script>
<?php endif; ?>