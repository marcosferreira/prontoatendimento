<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-bar-chart"></i> Relatório de Exames</h1>
                <p class="subtitle">Análise estatística de exames solicitados e realizados</p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="m-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('atendimento-exames') ?>">Exames</a></li>
                    <li class="breadcrumb-item active">Relatório</li>
                </ol>
            </nav>

            <!-- Filtros -->
            <div class="card m-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-funnel"></i> Filtros do Relatório</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="<?= base_url('atendimento-exames/relatorio') ?>" class="row g-3">
                        <div class="col-md-3">
                            <label for="periodo" class="form-label">Período</label>
                            <select class="form-select" id="periodo" name="periodo">
                                <option value="hoje" <?= $periodo === 'hoje' ? 'selected' : '' ?>>Hoje</option>
                                <option value="semana" <?= $periodo === 'semana' ? 'selected' : '' ?>>Esta Semana</option>
                                <option value="mes" <?= $periodo === 'mes' ? 'selected' : '' ?>>Este Mês</option>
                                <option value="ano" <?= $periodo === 'ano' ? 'selected' : '' ?>>Este Ano</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="tipo" class="form-label">Tipo de Exame</label>
                            <select class="form-select" id="tipo" name="tipo">
                                <option value="">Todos os Tipos</option>
                                <?php foreach ($tiposExame as $tipoOption): ?>
                                    <option value="<?= $tipoOption ?>" <?= $tipo === $tipoOption ? 'selected' : '' ?>>
                                        <?= ucfirst($tipoOption) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Gerar Relatório
                                </button>
                                <a href="<?= base_url('atendimento-exames/relatorio') ?>" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Limpar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Estatísticas Gerais -->
            <div class="row m-4">
                <div class="col-12">
                    <h3>Período: 
                        <?php
                        switch($periodo) {
                            case 'hoje': echo 'Hoje (' . date('d/m/Y') . ')'; break;
                            case 'semana': echo 'Esta Semana'; break;
                            case 'mes': echo 'Este Mês (' . date('m/Y') . ')'; break;
                            case 'ano': echo 'Este Ano (' . date('Y') . ')'; break;
                        }
                        
                        if ($tipo) {
                            echo ' - Tipo: ' . ucfirst($tipo);
                        }
                        ?>
                    </h3>
                </div>
            </div>

            <!-- Estatísticas por Tipo -->
            <?php if (!empty($estatisticasTipo)): ?>
                <div class="card m-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-pie-chart"></i> Estatísticas por Tipo de Exame</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($estatisticasTipo as $stat): ?>
                                <div class="col-lg-3 col-md-6 mb-3">
                                    <div class="stat-item text-center">
                                        <div class="stat-number text-primary"><?= $stat['total'] ?></div>
                                        <div class="stat-label"><?= ucfirst($stat['tipo']) ?></div>
                                        <div class="small text-muted">
                                            <?= $stat['realizados'] ?> realizados 
                                            (<?= $stat['total'] > 0 ? round(($stat['realizados'] / $stat['total']) * 100, 1) : 0 ?>%)
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <canvas id="chartTipos" width="400" height="200"></canvas>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Exames Mais Solicitados -->
            <div class="card m-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-trophy"></i> Exames Mais Solicitados</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="printReport()">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($examesMaisSolicitados)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Nenhum exame encontrado para o período selecionado</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="reportTable">
                                <thead>
                                    <tr>
                                        <th>Posição</th>
                                        <th>Exame</th>
                                        <th>Tipo</th>
                                        <th>Código</th>
                                        <th>Total Solicitações</th>
                                        <th>Realizados</th>
                                        <th>Cancelados</th>
                                        <th>Taxa Realização</th>
                                        <th>Participação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalGeral = array_sum(array_column($examesMaisSolicitados, 'total_solicitacoes'));
                                    foreach ($examesMaisSolicitados as $index => $item): 
                                        $percentual = $totalGeral > 0 ? ($item['total_solicitacoes'] / $totalGeral) * 100 : 0;
                                        $taxaRealizacao = $item['total_solicitacoes'] > 0 ? ($item['total_realizados'] / $item['total_solicitacoes']) * 100 : 0;
                                    ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary"><?= $index + 1 ?>º</span>
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?= esc($item['nome']) ?></div>
                                            </td>
                                            <td>
                                                <span class="badge 
                                                    <?php 
                                                    switch($item['tipo']) {
                                                        case 'laboratorial': echo 'bg-primary'; break;
                                                        case 'imagem': echo 'bg-info'; break;
                                                        case 'funcional': echo 'bg-warning'; break;
                                                        default: echo 'bg-secondary';
                                                    }
                                                    ?>">
                                                    <?= ucfirst($item['tipo']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($item['codigo']): ?>
                                                    <span class="badge bg-secondary"><?= esc($item['codigo']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary"><?= $item['total_solicitacoes'] ?></span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success"><?= $item['total_realizados'] ?></span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-danger"><?= $item['total_cancelados'] ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 16px;">
                                                        <div class="progress-bar bg-success" 
                                                             style="width: <?= $taxaRealizacao ?>%"
                                                             title="<?= number_format($taxaRealizacao, 1) ?>%">
                                                        </div>
                                                    </div>
                                                    <span class="small"><?= number_format($taxaRealizacao, 1) ?>%</span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 16px;">
                                                        <div class="progress-bar" 
                                                             style="width: <?= $percentual ?>%"
                                                             title="<?= number_format($percentual, 1) ?>%">
                                                        </div>
                                                    </div>
                                                    <span class="small"><?= number_format($percentual, 1) ?>%</span>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="table-dark">
                                        <th colspan="4">TOTAL GERAL</th>
                                        <th><?= $totalGeral ?></th>
                                        <th><?= array_sum(array_column($examesMaisSolicitados, 'total_realizados')) ?></th>
                                        <th><?= array_sum(array_column($examesMaisSolicitados, 'total_cancelados')) ?></th>
                                        <th>
                                            <?php 
                                            $totalRealizados = array_sum(array_column($examesMaisSolicitados, 'total_realizados'));
                                            echo $totalGeral > 0 ? number_format(($totalRealizados / $totalGeral) * 100, 1) : 0;
                                            ?>%
                                        </th>
                                        <th>100%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Gráfico de Exames -->
                        <div class="mt-4">
                            <h6><i class="bi bi-bar-chart"></i> Top 10 Exames Mais Solicitados</h6>
                            <canvas id="chartExames" width="400" height="200"></canvas>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions m-4">
                <a href="<?= base_url('atendimento-exames') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar à Lista
                </a>
                <button class="btn btn-success" onclick="exportToExcel()">
                    <i class="bi bi-file-earmark-excel"></i> Exportar Excel
                </button>
            </div>
        </div>
    </main>
</div>

<!-- Scripts para Gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico por tipos
    <?php if (!empty($estatisticasTipo)): ?>
        const tiposData = <?= json_encode($estatisticasTipo) ?>;
        const tiposLabels = tiposData.map(item => item.tipo.charAt(0).toUpperCase() + item.tipo.slice(1));
        const tiposValues = tiposData.map(item => parseInt(item.total));
        
        const ctxTipos = document.getElementById('chartTipos').getContext('2d');
        new Chart(ctxTipos, {
            type: 'doughnut',
            data: {
                labels: tiposLabels,
                datasets: [{
                    data: tiposValues,
                    backgroundColor: ['#007bff', '#17a2b8', '#ffc107', '#6c757d'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        document.getElementById('chartTipos').style.maxHeight = '250px';
    <?php endif; ?>

    // Gráfico de exames mais solicitados
    <?php if (!empty($examesMaisSolicitados)): ?>
        const examesData = <?= json_encode(array_slice($examesMaisSolicitados, 0, 10)) ?>;
        const examesLabels = examesData.map(item => item.nome.length > 20 ? item.nome.substring(0, 20) + '...' : item.nome);
        const examesTotalValues = examesData.map(item => parseInt(item.total_solicitacoes));
        const examesRealizadosValues = examesData.map(item => parseInt(item.total_realizados));
        
        const ctxExames = document.getElementById('chartExames').getContext('2d');
        new Chart(ctxExames, {
            type: 'bar',
            data: {
                labels: examesLabels,
                datasets: [
                    {
                        label: 'Solicitados',
                        data: examesTotalValues,
                        backgroundColor: '#007bff',
                        borderColor: '#0056b3',
                        borderWidth: 1
                    },
                    {
                        label: 'Realizados',
                        data: examesRealizadosValues,
                        backgroundColor: '#28a745',
                        borderColor: '#1e7e34',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        document.getElementById('chartExames').style.maxHeight = '400px';
    <?php endif; ?>
});

function printReport() {
    const printWindow = window.open('', '_blank');
    const content = `
        <html>
        <head>
            <title>Relatório de Exames</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; }
                .header { text-align: center; margin-bottom: 30px; }
                .no-print { display: none; }
                @media print {
                    .no-print { display: none; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Relatório de Exames</h1>
                <p>Período: ${getPeriodoText()}</p>
                <p>Gerado em: ${new Date().toLocaleString('pt-BR')}</p>
            </div>
            ${document.getElementById('reportTable').outerHTML}
        </body>
        </html>
    `;
    
    printWindow.document.write(content);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

function exportToExcel() {
    alert('Funcionalidade de exportação em desenvolvimento');
}

function getPeriodoText() {
    const periodo = '<?= $periodo ?>';
    const tipo = '<?= $tipo ?>';
    let text = '';
    
    switch(periodo) {
        case 'hoje': text = 'Hoje (<?= date('d/m/Y') ?>)'; break;
        case 'semana': text = 'Esta Semana'; break;
        case 'mes': text = 'Este Mês (<?= date('m/Y') ?>)'; break;
        case 'ano': text = 'Este Ano (<?= date('Y') ?>)'; break;
        default: text = 'Período não especificado';
    }
    
    if (tipo) {
        text += ' - Tipo: ' + tipo.charAt(0).toUpperCase() + tipo.slice(1);
    }
    
    return text;
}
</script>

<?= $this->endSection() ?>
