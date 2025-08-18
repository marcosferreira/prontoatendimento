<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-bar-chart"></i> Relatório de Atendimentos</h1>
                <p class="subtitle">Estatísticas e análises dos atendimentos médicos</p>
            </div>

            <div class="content-wrapper">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="my-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('atendimentos') ?>">Atendimentos</a></li>
                        <li class="breadcrumb-item active">Relatórios</li>
                    </ol>
                </nav>

                <!-- Filters -->
                <div class="card my-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-funnel"></i> Filtros do Relatório
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="filtrosRelatorio" method="GET">
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="data_inicio" class="form-label">Data Início</label>
                                    <input type="date" class="form-control" id="data_inicio" name="data_inicio"
                                        value="<?= $filtros['data_inicio'] ?? '' ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="data_fim" class="form-label">Data Fim</label>
                                    <input type="date" class="form-control" id="data_fim" name="data_fim"
                                        value="<?= $filtros['data_fim'] ?? '' ?>">
                                </div>
                                <div class="col-md-3">
                                    <label for="medico" class="form-label">Médico</label>
                                    <select class="form-select" id="medico" name="medico">
                                        <option value="">Todos os médicos</option>
                                        <?php if (isset($medicos)): ?>
                                            <?php foreach ($medicos as $medico): ?>
                                                <option value="<?= $medico['id_medico'] ?>"
                                                    <?= ($filtros['medico'] ?? '') == $medico['id_medico'] ? 'selected' : '' ?>>
                                                    <?= esc($medico['nome']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="classificacao" class="form-label">Classificação</label>
                                    <select class="form-select" id="classificacao" name="classificacao">
                                        <option value="">Todas as classificações</option>
                                        <option value="Vermelho" <?= ($filtros['classificacao'] ?? '') == 'Vermelho' ? 'selected' : '' ?>>Vermelho</option>
                                        <option value="Laranja" <?= ($filtros['classificacao'] ?? '') == 'Laranja' ? 'selected' : '' ?>>Laranja</option>
                                        <option value="Amarelo" <?= ($filtros['classificacao'] ?? '') == 'Amarelo' ? 'selected' : '' ?>>Amarelo</option>
                                        <option value="Verde" <?= ($filtros['classificacao'] ?? '') == 'Verde' ? 'selected' : '' ?>>Verde</option>
                                        <option value="Azul" <?= ($filtros['classificacao'] ?? '') == 'Azul' ? 'selected' : '' ?>>Azul</option>
                                        <option value="Sem classificação" <?= ($filtros['classificacao'] ?? '') == 'Sem classificação' ? 'selected' : '' ?>>Sem classificação</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-search"></i> Aplicar Filtros
                                    </button>
                                    <a href="<?= base_url('atendimentos/relatorio') ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-clockwise"></i> Limpar
                                    </a>
                                    <button type="button" class="btn btn-success" onclick="exportarRelatorio()">
                                        <i class="bi bi-file-excel"></i> Exportar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row my-4">
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $estatisticas['total_atendimentos'] ?? 0 ?></h3>
                                <p>Total Atendimentos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-info">
                                <i class="bi bi-file-earmark-text"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $estatisticas['diagnosticos_informados'] ?? 0 ?></h3>
                                <p>Diagnósticos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $estatisticas['atendimentos_concluidos'] ?? 0 ?></h3>
                                <p>Concluídos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-warning">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $estatisticas['em_andamento'] ?? 0 ?></h3>
                                <p>Em Andamento</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-danger">
                                <i class="bi bi-exclamation-triangle"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $estatisticas['casos_urgentes'] ?? 0 ?></h3>
                                <p>Casos Urgentes</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-dark">
                                <i class="bi bi-heartbreak"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $estatisticas['obitos'] ?? 0 ?></h3>
                                <p>Óbitos</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row my-4">
                    <!-- Classificação de Risco -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-pie-chart"></i> Atendimentos por Classificação de Risco
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartClassificacao" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Atendimentos por Mês -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-graph-up"></i> Atendimentos por Mês
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartMensal" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Médicos com mais atendimentos -->
                <div class="row my-4">
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-person-badge"></i> Top 10 Médicos com Mais Atendimentos
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartMedicos" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Encaminhamentos -->
                    <div class="col-lg-6 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-arrow-right-circle"></i> Tipos de Encaminhamento
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartEncaminhamentos" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Diagnósticos -->
                <div class="row my-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-clipboard-data"></i> Top 10 Diagnósticos Mais Comuns
                                </h5>
                            </div>
                            <div class="card-body">
                                <canvas id="chartDiagnosticos" width="400" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabela de Dados -->
                <div class="card my-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-table"></i> Dados Detalhados por Período
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tabelaRelatorio">
                                <thead>
                                    <tr>
                                        <th>Período</th>
                                        <th>Vermelho</th>
                                        <th>Laranja</th>
                                        <th>Amarelo</th>
                                        <th>Verde</th>
                                        <th>Azul</th>
                                        <th>Sem Classificação</th>
                                        <th>Total</th>
                                        <th>Taxa Urgência (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($dadosTabela) && !empty($dadosTabela)): ?>
                                        <?php foreach ($dadosTabela as $linha): ?>
                                            <tr>
                                                <td><?= $linha['periodo'] ?></td>
                                                <td class="text-danger fw-bold"><?= $linha['vermelho'] ?></td>
                                                <td style="color: orange;" class="fw-bold"><?= $linha['laranja'] ?></td>
                                                <td class="text-warning fw-bold"><?= $linha['amarelo'] ?></td>
                                                <td class="text-success fw-bold"><?= $linha['verde'] ?></td>
                                                <td class="text-info fw-bold"><?= $linha['azul'] ?></td>
                                                <td class="text-muted fw-bold"><?= $linha['sem_classificacao'] ?? 0 ?></td>
                                                <td><strong><?= $linha['total'] ?></strong></td>
                                                <td>
                                                    <span class="badge bg-<?= $linha['taxa_urgencia'] > 50 ? 'danger' : ($linha['taxa_urgencia'] > 20 ? 'warning' : 'success') ?>">
                                                        <?= number_format($linha['taxa_urgencia'], 1) ?>%
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="8" class="text-center text-muted">
                                                Nenhum dado encontrado para o período selecionado
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo $this->include('components/footer') ?>
        </div>
    </main>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dados dos gráficos vindos do PHP
        const classificacaoData = <?= json_encode($graficos['classificacao'] ?? []) ?>;
        const mensalData = <?= json_encode($graficos['mensal'] ?? []) ?>;
        const medicosData = <?= json_encode($graficos['medicos'] ?? []) ?>;
        const encaminhamentosData = <?= json_encode($graficos['encaminhamentos'] ?? []) ?>;
        const diagnosticosData = <?= json_encode($graficos['diagnosticos'] ?? []) ?>;

        console.log('Dados dos gráficos:', {
            classificacao: classificacaoData,
            mensal: mensalData,
            medicos: medicosData,
            encaminhamentos: encaminhamentosData,
            diagnosticos: diagnosticosData
        });

        const chartColors = {
            red: '#dc3545',
            orange: '#fd7e14',
            yellow: '#ffc107',
            green: '#28a745',
            blue: '#0d6efd',
            teal: '#20c997',
            cyan: '#0dcaf0',
            gray: '#6c757d'
        };

        // Gráfico de Classificação de Risco (Doughnut)
        if (document.getElementById('chartClassificacao')) {
            const ctxClassificacao = document.getElementById('chartClassificacao').getContext('2d');
            document.getElementById('chartClassificacao').style.maxHeight = '300px';
            new Chart(ctxClassificacao, {
                type: 'doughnut',
                data: {
                    labels: classificacaoData.length > 0 ? classificacaoData.map(item => item.classificacao) : ['Nenhum dado'],
                    datasets: [{
                        data: classificacaoData.length > 0 ? classificacaoData.map(item => item.total) : [1],
                        backgroundColor: classificacaoData.map(item => {
                            switch(item.classificacao) {
                                case 'Vermelho': return chartColors.red;
                                case 'Laranja': return chartColors.orange;
                                case 'Amarelo': return chartColors.yellow;
                                case 'Verde': return chartColors.green;
                                case 'Azul': return chartColors.blue;
                                case 'Sem classificação': return chartColors.gray;
                                default: return chartColors.gray;
                            }
                        })
                    }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });
        }

        // Gráfico Mensal (Line)
        if (document.getElementById('chartMensal')) {
            const ctxMensal = document.getElementById('chartMensal').getContext('2d');
            document.getElementById('chartMensal').style.maxHeight = '300px';
            new Chart(ctxMensal, {
                type: 'line',
                data: {
                    labels: mensalData.length > 0 ? mensalData.map(item => item.mes) : ['Nenhum dado'],
                    datasets: [{
                        label: 'Atendimentos',
                        data: mensalData.length > 0 ? mensalData.map(item => item.total) : [0],
                        borderColor: chartColors.blue,
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });
        }

        // Gráfico de Médicos (Bar)
        if (document.getElementById('chartMedicos')) {
            const ctxMedicos = document.getElementById('chartMedicos').getContext('2d');
            document.getElementById('chartMedicos').style.maxHeight = '300px';
            new Chart(ctxMedicos, {
                type: 'bar',
                data: {
                    labels: medicosData.length > 0 ? medicosData.map(item => item.medico) : ['Nenhum dado'],
                    datasets: [{
                        label: 'Atendimentos',
                        data: medicosData.length > 0 ? medicosData.map(item => item.total) : [0],
                        backgroundColor: chartColors.green
                    }]
                },
                options: { responsive: true, indexAxis: 'y', scales: { x: { beginAtZero: true } } }
            });
        }

        // Gráfico de Encaminhamentos (Pie)
        if (document.getElementById('chartEncaminhamentos')) {
            const ctxEncaminhamentos = document.getElementById('chartEncaminhamentos').getContext('2d');
            document.getElementById('chartEncaminhamentos').style.maxHeight = '300px';
            new Chart(ctxEncaminhamentos, {
                type: 'pie',
                data: {
                    labels: encaminhamentosData.length > 0 ? encaminhamentosData.map(item => item.encaminhamento || 'Não definido') : ['Nenhum dado'],
                    datasets: [{
                        data: encaminhamentosData.length > 0 ? encaminhamentosData.map(item => item.total) : [1],
                        backgroundColor: [chartColors.blue, chartColors.green, chartColors.yellow, chartColors.red, chartColors.teal, chartColors.cyan]
                    }]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
            });
        }

        // Gráfico de Diagnósticos (Horizontal Bar)
        if (document.getElementById('chartDiagnosticos')) {
            const ctxDiagnosticos = document.getElementById('chartDiagnosticos').getContext('2d');
            document.getElementById('chartDiagnosticos').style.maxHeight = '400px';
            new Chart(ctxDiagnosticos, {
                type: 'bar',
                data: {
                    labels: diagnosticosData.length > 0 ? diagnosticosData.map(item => item.diagnostico) : ['Nenhum dado'],
                    datasets: [{
                        label: 'Total de Casos',
                        data: diagnosticosData.length > 0 ? diagnosticosData.map(item => item.total) : [0],
                        backgroundColor: chartColors.cyan,
                        borderColor: chartColors.blue,
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    scales: { x: { beginAtZero: true } },
                    plugins: { legend: { display: false } }
                }
            });
        }

        // Função para exportar relatório
        window.exportarRelatorio = function() {
            const params = new URLSearchParams(new FormData(document.getElementById('filtrosRelatorio')));
            params.append('export', '1');
            window.location.href = `<?= base_url('atendimentos/export') ?>?${params.toString()}`;
        };

        // Auto-submit form when filters change
        const filterElements = document.querySelectorAll('#filtrosRelatorio select, #filtrosRelatorio input[type="date"]');
        filterElements.forEach(element => {
            element.addEventListener('change', function() {
                document.getElementById('filtrosRelatorio').submit();
            });
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 1rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        border-left: 5px solid var(--bs-primary);
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
        margin-right: 1rem;
    }

    .stat-content h3 {
        margin: 0;
        font-size: 1.75rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .stat-content p {
        margin: 0;
        color: #7f8c8d;
        font-size: 0.8rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .card-title {
        font-weight: 600;
        color: #343a40;
    }
</style>
<?= $this->endSection() ?>