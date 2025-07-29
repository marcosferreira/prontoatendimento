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
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-primary">
                                <i class="bi bi-clipboard-check"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $estatisticas['total_atendimentos'] ?? 0 ?></h3>
                                <p>Total de Atendimentos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
                        <div class="stat-card">
                            <div class="stat-icon bg-success">
                                <i class="bi bi-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <h3><?= $estatisticas['atendimentos_concluidos'] ?? 0 ?></h3>
                                <p>Atendimentos Concluídos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-4">
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
                    <div class="col-lg-3 col-md-6 mb-4">
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
                                    <i class="bi bi-person-badge"></i> Médicos com Mais Atendimentos
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

                <!-- Tabela de Dados -->
                <div class="card my-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-table"></i> Dados Detalhados
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
                                        <th>Total</th>
                                        <th>Taxa Urgência (%)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (isset($dadosTabela) && !empty($dadosTabela)): ?>
                                        <?php foreach ($dadosTabela as $linha): ?>
                                            <tr>
                                                <td><?= $linha['periodo'] ?></td>
                                                <td class="text-danger"><?= $linha['vermelho'] ?></td>
                                                <td style="color: orange;"><?= $linha['laranja'] ?></td>
                                                <td class="text-warning"><?= $linha['amarelo'] ?></td>
                                                <td class="text-success"><?= $linha['verde'] ?></td>
                                                <td class="text-info"><?= $linha['azul'] ?></td>
                                                <td><strong><?= $linha['total'] ?></strong></td>
                                                <td>
                                                    <span class="badge bg-<?= $linha['taxa_urgencia'] > 50 ? 'danger' : 'success' ?>">
                                                        <?= number_format($linha['taxa_urgencia'], 1) ?>%
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
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

        console.log('Dados dos gráficos:', {
            classificacao: classificacaoData,
            mensal: mensalData,
            medicos: medicosData,
            encaminhamentos: encaminhamentosData
        });

        // Gráfico de Classificação de Risco (Pie) - sempre criar, mesmo sem dados
        const ctxClassificacao = document.getElementById('chartClassificacao').getContext('2d');
        // Altura máxima do gráfico
        document.getElementById('chartClassificacao').style.maxHeight = '300px';
        
        new Chart(ctxClassificacao, {
            type: 'doughnut',
            data: {
                labels: classificacaoData.length > 0 ? classificacaoData.map(item => item.classificacao) : ['Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul'],
                datasets: [{
                    data: classificacaoData.length > 0 ? classificacaoData.map(item => item.total) : [0, 0, 0, 0],
                    backgroundColor: [
                        '#dc3545', // Vermelho
                        'orange', // Laranja
                        '#ffc107', // Amarelo
                        '#28a745', // Verde
                        '#17a2b8' // Azul
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Gráfico Mensal (Line) - sempre criar, mesmo sem dados
        const ctxMensal = document.getElementById('chartMensal').getContext('2d');
        // Altura máxima do gráfico
        document.getElementById('chartMensal').style.maxHeight = '300px';
        
        new Chart(ctxMensal, {
            type: 'line',
            data: {
                labels: mensalData.length > 0 ? mensalData.map(item => item.mes) : ['Jan', 'Fev', 'Mar'],
                datasets: [{
                    label: 'Atendimentos',
                    data: mensalData.length > 0 ? mensalData.map(item => item.total) : [0, 0, 0],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Médicos (Bar) - sempre criar, mesmo sem dados
        const ctxMedicos = document.getElementById('chartMedicos').getContext('2d');
        // Altura máxima do gráfico
        document.getElementById('chartMedicos').style.maxHeight = '300px';
        
        new Chart(ctxMedicos, {
            type: 'bar',
            data: {
                labels: medicosData.length > 0 ? medicosData.map(item => item.medico) : ['Médico 1', 'Médico 2'],
                datasets: [{
                    label: 'Atendimentos',
                    data: medicosData.length > 0 ? medicosData.map(item => item.total) : [0, 0],
                    backgroundColor: '#28a745'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Gráfico de Encaminhamentos (Pie) - sempre criar, mesmo sem dados
        const ctxEncaminhamentos = document.getElementById('chartEncaminhamentos').getContext('2d');
        // Altura máxima do gráfico
        document.getElementById('chartEncaminhamentos').style.maxHeight = '300px';
        
        new Chart(ctxEncaminhamentos, {
            type: 'pie',
            data: {
                labels: encaminhamentosData.length > 0 ? encaminhamentosData.map(item => item.encaminhamento || 'Em Atendimento') : ['Em Atendimento', 'Alta'],
                datasets: [{
                    data: encaminhamentosData.length > 0 ? encaminhamentosData.map(item => item.total) : [0, 0],
                    backgroundColor: [
                        '#007bff',
                        '#28a745',
                        '#ffc107',
                        '#dc3545',
                        '#6f42c1',
                        '#fd7e14'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Função para exportar relatório
        window.exportarRelatorio = function() {
            const params = new URLSearchParams();

            // Adicionar filtros aos parâmetros
            const formData = new FormData(document.getElementById('filtrosRelatorio'));
            for (let [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }

            params.append('export', '1');

            // Abrir URL de exportação
            window.location.href = `<?= base_url('atendimentos/export') ?>?${params.toString()}`;
        };

        // Auto-submit form when filters change
        const filterElements = document.querySelectorAll('#filtrosRelatorio select, #filtrosRelatorio input[type="date"]');
        filterElements.forEach(element => {
            element.addEventListener('change', function() {
                // Debounce para evitar muitas requisições
                clearTimeout(window.filterTimeout);
                window.filterTimeout = setTimeout(function() {
                    document.getElementById('filtrosRelatorio').submit();
                }, 500);
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
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
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
        font-size: 2rem;
        font-weight: bold;
        color: #2c3e50;
    }

    .stat-content p {
        margin: 0;
        color: #7f8c8d;
        font-size: 0.9rem;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }
</style>
<?= $this->endSection() ?>