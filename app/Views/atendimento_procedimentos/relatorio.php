<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-bar-chart"></i> Relatório de Procedimentos</h1>
                <p class="subtitle">Análise estatística de procedimentos realizados</p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="m-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/atendimento-procedimentos">Procedimentos</a></li>
                    <li class="breadcrumb-item active">Relatório</li>
                </ol>
            </nav>

            <!-- Filtros -->
            <div class="card m-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-funnel"></i> Filtros do Relatório</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="/atendimento-procedimentos/relatorio" class="row g-3">
                        <div class="col-md-4">
                            <label for="periodo" class="form-label">Período</label>
                            <select class="form-select" id="periodo" name="periodo">
                                <option value="hoje" <?= $periodo === 'hoje' ? 'selected' : '' ?>>Hoje</option>
                                <option value="semana" <?= $periodo === 'semana' ? 'selected' : '' ?>>Esta Semana</option>
                                <option value="mes" <?= $periodo === 'mes' ? 'selected' : '' ?>>Este Mês</option>
                                <option value="ano" <?= $periodo === 'ano' ? 'selected' : '' ?>>Este Ano</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Gerar Relatório
                                </button>
                                <a href="/atendimento-procedimentos/relatorio" class="btn btn-outline-secondary">
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
                        ?>
                    </h3>
                </div>
            </div>

            <!-- Procedimentos Mais Utilizados -->
            <div class="card m-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><i class="bi bi-trophy"></i> Procedimentos Mais Realizados</h5>
                    <button class="btn btn-sm btn-outline-primary" onclick="printReport()">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($procedimentosMaisUtilizados)): ?>
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2">Nenhum procedimento encontrado para o período selecionado</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="reportTable">
                                <thead>
                                    <tr>
                                        <th>Posição</th>
                                        <th>Procedimento</th>
                                        <th>Código</th>
                                        <th>Total de Realizações</th>
                                        <th>Quantidade Total</th>
                                        <th>Participação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $totalGeral = array_sum(array_column($procedimentosMaisUtilizados, 'total_realizacoes'));
                                    foreach ($procedimentosMaisUtilizados as $index => $item): 
                                        $percentual = $totalGeral > 0 ? ($item['total_realizacoes'] / $totalGeral) * 100 : 0;
                                    ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-primary"><?= $index + 1 ?>º</span>
                                            </td>
                                            <td>
                                                <div class="fw-bold"><?= esc($item['nome']) ?></div>
                                            </td>
                                            <td>
                                                <?php if ($item['codigo']): ?>
                                                    <span class="badge bg-secondary"><?= esc($item['codigo']) ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary"><?= $item['total_realizacoes'] ?></span>
                                            </td>
                                            <td>
                                                <span class="fw-bold"><?= $item['quantidade_total'] ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="progress flex-grow-1 me-2" style="height: 20px;">
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
                                        <th colspan="3">TOTAL GERAL</th>
                                        <th><?= $totalGeral ?></th>
                                        <th><?= array_sum(array_column($procedimentosMaisUtilizados, 'quantidade_total')) ?></th>
                                        <th>100%</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- Gráfico -->
                        <div class="mt-4">
                            <h6><i class="bi bi-pie-chart"></i> Distribuição dos Procedimentos</h6>
                            <canvas id="chartProcedimentos" width="400" height="200"></canvas>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions m-4">
                <a href="/atendimento-procedimentos" class="btn btn-secondary">
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
    <?php if (!empty($procedimentosMaisUtilizados)): ?>
        // Dados para o gráfico
        const dados = <?= json_encode($procedimentosMaisUtilizados) ?>;
        const labels = dados.slice(0, 10).map(item => item.nome.length > 20 ? item.nome.substring(0, 20) + '...' : item.nome);
        const values = dados.slice(0, 10).map(item => parseInt(item.total_realizacoes));
        
        // Cores para o gráfico
        const colors = [
            '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
            '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384'
        ];

        // Configuração do gráfico
        const ctx = document.getElementById('chartProcedimentos').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: values,
                    backgroundColor: colors,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Ajustar altura do canvas
        document.getElementById('chartProcedimentos').style.height = '300px';
    <?php endif; ?>
});

function printReport() {
    const printWindow = window.open('', '_blank');
    const content = `
        <html>
        <head>
            <title>Relatório de Procedimentos</title>
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
                <h1>Relatório de Procedimentos</h1>
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
    // Implementar exportação para Excel
    alert('Funcionalidade de exportação em desenvolvimento');
}

function getPeriodoText() {
    const periodo = '<?= $periodo ?>';
    switch(periodo) {
        case 'hoje': return 'Hoje (<?= date('d/m/Y') ?>)';
        case 'semana': return 'Esta Semana';
        case 'mes': return 'Este Mês (<?= date('m/Y') ?>)';
        case 'ano': return 'Este Ano (<?= date('Y') ?>)';
        default: return 'Período não especificado';
    }
}
</script>

<?= $this->endSection() ?>
