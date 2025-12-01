<?php
/**
 * VIEW: _rastrear.php
 * Página de Rastreamento Público
 */

$rastreio_data = null;
$rastreio_msg = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rastrear_codigo'])) {
    $codigo = trim($_POST['codigo_rastreio']);
    if (!empty($codigo)) {
        $rastreio_data = SystemCore::getRastreioData($codigo);
        if (!$rastreio_data) {
            $rastreio_msg = ['type' => 'danger', 'text' => 'Código de rastreio não encontrado.'];
        }
    } else {
        $rastreio_msg = ['type' => 'warning', 'text' => 'Por favor, insira um código de rastreio.'];
    }
}

?>
<div class="row">
    <div class="col-lg-6 d-none d-lg-block bg-register-image"></div>
    <div class="col-lg-6">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Rastrear Despacho</h1>
            </div>
            
            <?php if (isset($rastreio_msg)): ?>
                <div class="alert alert-<?php echo $rastreio_msg['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $rastreio_msg['text']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form class="user" method="POST" action="index.php?page=rastrear">
                <input type="hidden" name="rastrear_codigo" value="1">
                <div class="form-group">
                    <input type="text" class="form-control form-control-user"
                        id="codigo_rastreio" placeholder="Insira o Código de Rastreio" name="codigo_rastreio" required>
                </div>
                <button type="submit" class="btn btn-primary btn-user btn-block">
                    Rastrear
                </button>
            </form>
            <hr>
            <div class="text-center">
                <a class="small" href="index.php?page=login">Acesso Restrito</a>
            </div>

            <?php if ($rastreio_data): ?>
                <hr>
                <h5 class="text-gray-900 mb-3">Detalhes do Despacho: <?php echo htmlspecialchars($rastreio_data['codigo_rastreio']); ?></h5>
                <p><strong>Status Atual:</strong> 
                    <span class="badge bg-<?php 
                        if ($rastreio_data['status'] === 'Entregue') echo 'success';
                        else if ($rastreio_data['status'] === 'Cancelado') echo 'danger';
                        else if ($rastreio_data['status'] === 'Em Trânsito') echo 'info';
                        else echo 'warning';
                    ?>">
                        <?php echo $rastreio_data['status']; ?>
                    </span>
                </p>
                <p><strong>Destino:</strong> <?php echo htmlspecialchars($rastreio_data['destino_nome']); ?></p>
                <p><strong>Transportadora:</strong> <?php echo htmlspecialchars($rastreio_data['transportadora_nome']); ?></p>
                <p><strong>Previsão de Entrega:</strong> <?php echo $rastreio_data['data_prevista_entrega'] ? date('d/m/Y', strtotime($rastreio_data['data_prevista_entrega'])) : 'N/A'; ?></p>

                <h6 class="text-gray-900 mt-4 mb-2">Histórico de Rastreio</h6>
                <ul class="timeline">
                    <?php foreach ($rastreio_data['historico'] as $hist): ?>
                        <li>
                            <p class="text-sm mb-0">
                                <span class="text-muted"><?php echo date('d/m/Y H:i', strtotime($hist['created_at'])); ?></span> - 
                                <strong><?php echo htmlspecialchars($hist['status']); ?></strong>
                            </p>
                            <p class="text-xs text-gray-600 mb-0"><?php echo htmlspecialchars($hist['descricao']); ?></p>
                            <?php if ($hist['localizacao']): ?>
                                <p class="text-xs text-gray-600 mb-0">Local: <?php echo htmlspecialchars($hist['localizacao']); ?></p>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
