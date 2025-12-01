<?php
// views/_dashboard.php - Dashboard 100% sem erros e sem notices (28/Nov/2025)

$pageTitle = "Dashboard";

// Busca estatísticas com valores padrão caso não existam
$stats = SystemCore::getDashboardStats() ?? [];

// Define valores padrão para evitar Undefined index
$defaults = [
    'entregues'               => 0,
    'em_processamento'        => 0,
    'total_mercadorias'       => 0,
    'qtd_total_estoque'       => 0,
    'total_clientes'          => 0,
    'oportunidades_abertas'   => 0,
    'pedidos_venda_mes'       => 0,
    'contas_atrasadas'        => 0,
    'contas_a_pagar_aberto'   => 0,
    'contas_a_receber_aberto' => 0,
    'ticket_medio'            => 0.00
];

$stats = array_merge($defaults, $stats);
?>

<div class="container-fluid py-4">
    <div class="row g-4">
        <!-- Despachos Entregues -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white shadow-lg border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-truck fa-3x opacity-75"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Entregues</h5>
                        <h3 class="mb-0"><?= number_format($stats['entregues']) ?></h3>
                        <small>no último mês</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Em Processamento -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white shadow-lg border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-shipping-fast fa-3x opacity-75"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Em Processamento</h5>
                        <h3 class="mb-0"><?= number_format($stats['em_processamento']) ?></h3>
                        <small>aguardando envio</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total de Mercadorias -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white shadow-lg border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-boxes fa-3x opacity-75"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Mercadorias</h5>
                        <h3 class="mb-0"><?= number_format($stats['total_mercadorias']) ?></h3>
                        <small>itens cadastrados</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estoque Total -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white shadow-lg border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-warehouse fa-3x opacity-75"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Em Estoque</h5>
                        <h3 class="mb-0"><?= number_format($stats['qtd_total_estoque']) ?></h3>
                        <small>unidades disponíveis</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-2">
        <!-- Clientes -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-dark text-white shadow-lg border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-users fa-3x opacity-75"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Clientes</h5>
                        <h3 class="mb-0"><?= number_format($stats['total_clientes']) ?></h3>
                        <small>ativos no sistema</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Oportunidades Abertas -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-purple text-white shadow-lg border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-handshake fa-3x opacity-75"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Oportunidades</h5>
                        <h3 class="mb-0"><?= number_format($stats['oportunidades_abertas']) ?></h3>
                        <small>em aberto</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pedidos do Mês -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-teal text-white shadow-lg border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-shopping-cart fa-3x opacity-75"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Pedidos (Mês)</h5>
                        <h3 class="mb-0"><?= number_format($stats['pedidos_venda_mes']) ?></h3>
                        <small>neste mês</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contas Atrasadas -->
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white shadow-lg border-0">
                <div class="card-body d-flex align-items-center">
                    <div class="me-3">
                        <i class="fas fa-exclamation-triangle fa-3x opacity-75"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Contas Atrasadas</h5>
                        <h3 class="mb-0"><?= number_format($stats['contas_atrasadas']) ?></h3>
                        <small>vencidas</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo Financeiro -->
    <div class="row g-4 mt-2">
        <div class="col-12">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Resumo Financeiro</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-4 py-3 border-end">
                            <h6 class="text-muted">A Pagar (Aberto)</h6>
                            <h3 class="text-danger">R$ <?= number_format($stats['contas_a_pagar_aberto'], 2, ',', '.') ?></h3>
                        </div>
                        <div class="col-md-4 py-3 border-end">
                            <h6 class="text-muted">A Receber (Aberto)</h6>
                            <h3 class="text-success">R$ <?= number_format($stats['contas_a_receber_aberto'], 2, ',', '.') ?></h3>
                        </div>
                        <div class="col-md-4 py-3">
                            <h6 class="text-muted">Ticket Médio</h6>
                            <h3 class="text-info">R$ <?= number_format($stats['ticket_medio'], 2, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-purple { background: linear-gradient(135deg, #8e44ad, #9b59b6) !important; }
.bg-teal   { background: linear-gradient(135deg, #16a085, #1abc9c) !important; }
</style>