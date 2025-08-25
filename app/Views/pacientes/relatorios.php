<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-graph-up-arrow"></i> Relatórios de Pacientes</h1>
                <p class="subtitle">Análise Estatística e Relatórios Médicos</p>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-left m-4">
                    <div class="search-container position-relative">
                        <span class="text-muted">Filtros ativos: 
                            <strong><?= $meses[$mes_selecionado] ?>/<?= $ano_selecionado ?></strong>
                        </span>
                    </div>
                </div>
                <div class="action-right m-4">
                    <button type="button" class="btn btn-success" onclick="exportarRelatorios()">
                        <i class="bi bi-file-excel"></i> Exportar Relatórios
                    </button>
                </div>
            </div>

            <!-- Content -->
            <div class="content-wrapper">
                <!-- Filtros -->
                <div class="section-card mb-4">
                    <div class="section-header mb-3">
                        <h3 class="section-title">
                            <i class="bi bi-funnel"></i>
                            Filtros do Relatório
                        </h3>
                    </div>
                    
                    <form method="GET" action="<?= current_url() ?>" class="row g-3">
                        <div class="col-md-4">
                            <label for="mes" class="form-label">Mês</label>
                            <select class="form-select" id="mes" name="mes">
                                <?php foreach ($meses as $num => $nome): ?>
                                    <option value="<?= $num ?>" <?= $mes_selecionado == $num ? 'selected' : '' ?>>
                                        <?= $nome ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="ano" class="form-label">Ano</label>
                            <select class="form-select" id="ano" name="ano">
                                <?php foreach ($anos_disponiveis as $ano_disponivel): ?>
                                    <option value="<?= $ano_disponivel ?>" <?= $ano_selecionado == $ano_disponivel ? 'selected' : '' ?>>
                                        <?= $ano_disponivel ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="bi bi-funnel"></i> Filtrar
                            </button>
                            <button type="button" class="btn btn-success" onclick="exportarRelatorios()">
                                <i class="bi bi-file-excel"></i> Exportar
                            </button>
                        </div>
                    </form>
                </div>

                
                <!-- Relatórios -->
                <div class="row">
                    <!-- Relatório por Faixa Etária -->
                    <div class="col-lg-6">
                        <div class="section-card">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="bi bi-people text-primary"></i>
                                    Pacientes por Faixa Etária
                                </h3>
                            </div>
                            <div class="section-body">
                                <?php if (!empty($relatorio_idade)): ?>
                                    <div class="table-responsive">
                                        <table class="table modern-table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Faixa Etária</th>
                                                    <th class="text-end">Total</th>
                                                    <th class="text-end">%</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $total_geral = array_sum(array_column($relatorio_idade, 'total_pacientes'));
                                                foreach ($relatorio_idade as $item): 
                                                    $percentual = $total_geral > 0 ? round(($item['total_pacientes'] / $total_geral) * 100, 1) : 0;
                                                ?>
                                                    <tr>
                                                        <td><?= $item['faixa_etaria'] ?></td>
                                                        <td class="text-end">
                                                            <span class="badge bg-primary"><?= number_format($item['total_pacientes']) ?></span>
                                                        </td>
                                                        <td class="text-end"><?= $percentual ?>%</td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr class="table-active">
                                                    <th>Total</th>
                                                    <th class="text-end"><?= number_format($total_geral) ?></th>
                                                    <th class="text-end">100%</th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>

                                    <!-- Gráfico de Pizza -->
                                    <div class="mt-3">
                                        <canvas id="graficoIdade" height="300"></canvas>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle" style="font-size: 3rem;"></i>
                                        <p class="mt-2">Nenhum paciente encontrado para o período selecionado.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Relatório de Enfermagem -->
                    <div class="col-lg-6">
                        <div class="section-card">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="bi bi-heart-pulse text-success"></i>
                                    Atendimentos de Enfermagem
                                </h3>
                            </div>
                            <div class="section-body">
                                <?php if (!empty($relatorio_enfermagem)): ?>
                                    <div class="row mb-3">
                                        <div class="col-6">
                                            <div class="d-flex justify-content-between">
                                                <span>Com consulta de enfermagem:</span>
                                                <strong class="text-success">
                                                    <?= number_format($relatorio_enfermagem['com_consulta_enfermagem']) ?>
                                                </strong>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="d-flex justify-content-between">
                                                <span>Sem consulta de enfermagem:</span>
                                                <strong class="text-warning">
                                                    <?= number_format($relatorio_enfermagem['sem_consulta_enfermagem']) ?>
                                                </strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between">
                                            <span>Idade média dos pacientes:</span>
                                            <strong class="text-primary">
                                                <?= round($relatorio_enfermagem['idade_media_enfermagem'], 1) ?> anos
                                            </strong>
                                        </div>
                                    </div>

                                    <?php if (!empty($relatorio_enfermagem['classificacoes'])): ?>
                                        <h6 class="mt-4">Classificação de Risco na Enfermagem</h6>
                                        <div class="table-responsive">
                                            <table class="table modern-table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Classificação</th>
                                                        <th class="text-end">Pacientes</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($relatorio_enfermagem['classificacoes'] as $classificacao): ?>
                                                        <tr>
                                                            <td>
                                                                <span class="badge bg-<?= getClassificacaoColor($classificacao['classificacao']) ?>">
                                                                    <?= $classificacao['classificacao'] ?>
                                                                </span>
                                                            </td>
                                                            <td class="text-end"><?= number_format($classificacao['total_pacientes']) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle" style="font-size: 3rem;"></i>
                                        <p class="mt-2">Nenhum atendimento de enfermagem encontrado para o período selecionado.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                
                <!-- Relatório de Médicos -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="section-card">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="bi bi-person-badge text-info"></i>
                                    Atendimentos Médicos
                                </h3>
                            </div>
                            <div class="section-body">
                                <?php if (!empty($relatorio_medico)): ?>
                                    <div class="row mb-4">
                                        <div class="col-md-4">
                                            <div class="stat-item">
                                                <div class="stat-number text-primary"><?= number_format($relatorio_medico['pacientes_medico']) ?></div>
                                                <div class="stat-label">Pacientes atendidos</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stat-item">
                                                <div class="stat-number text-success"><?= number_format($relatorio_medico['medicos_ativos']) ?></div>
                                                <div class="stat-label">Médicos ativos</div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stat-item">
                                                <div class="stat-number text-info"><?= round($relatorio_medico['idade_media_medico'], 1) ?> anos</div>
                                                <div class="stat-label">Idade média dos pacientes</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Ranking de Médicos -->
                                        <div class="col-lg-8">
                                            <?php if (!empty($relatorio_medico['ranking_medicos'])): ?>
                                                <h6>Ranking de Médicos por Atendimentos</h6>
                                                <div class="table-responsive">
                                                    <table class="table modern-table">
                                                        <thead>
                                                            <tr>
                                                                <th>Posição</th>
                                                                <th>Médico</th>
                                                                <th>CRM</th>
                                                                <th>Especialidade</th>
                                                                <th class="text-end">Pacientes</th>
                                                                <th class="text-end">Atendimentos</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($relatorio_medico['ranking_medicos'] as $index => $medico): ?>
                                                                <tr>
                                                                    <td>
                                                                        <?php if ($index === 0): ?>
                                                                            <span class="badge bg-warning"><i class="bi bi-trophy"></i> 1º</span>
                                                                        <?php elseif ($index === 1): ?>
                                                                            <span class="badge bg-secondary"><i class="bi bi-award"></i> 2º</span>
                                                                        <?php elseif ($index === 2): ?>
                                                                            <span class="badge bg-warning"><i class="bi bi-award"></i> 3º</span>
                                                                        <?php else: ?>
                                                                            <span class="badge bg-light text-dark"><?= $index + 1 ?>º</span>
                                                                        <?php endif; ?>
                                                                    </td>
                                                                    <td><?= esc($medico['nome_medico']) ?></td>
                                                                    <td><?= esc($medico['crm']) ?></td>
                                                                    <td><?= esc($medico['especialidade'] ?? 'Não informada') ?></td>
                                                                    <td class="text-end">
                                                                        <span class="badge bg-primary"><?= number_format($medico['total_pacientes']) ?></span>
                                                                    </td>
                                                                    <td class="text-end"><?= number_format($medico['total_atendimentos']) ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Encaminhamentos -->
                                        <div class="col-lg-4">
                                            <?php if (!empty($relatorio_medico['encaminhamentos'])): ?>
                                                <h6>Encaminhamentos</h6>
                                                <div class="table-responsive">
                                                    <table class="table modern-table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Tipo</th>
                                                                <th class="text-end">Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($relatorio_medico['encaminhamentos'] as $encaminhamento): ?>
                                                                <tr>
                                                                    <td>
                                                                        <span class="badge bg-<?= getEncaminhamentoColor($encaminhamento['encaminhamento']) ?>">
                                                                            <?= $encaminhamento['encaminhamento'] ?>
                                                                        </span>
                                                                    </td>
                                                                    <td class="text-end"><?= number_format($encaminhamento['total_pacientes']) ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-info-circle" style="font-size: 3rem;"></i>
                                        <p class="mt-2">Nenhum atendimento médico encontrado para o período selecionado.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?= $this->include('components/footer') ?>
        </div>
    </main>
</div>

<?php
function getClassificacaoColor($classificacao) {
    switch (strtolower($classificacao)) {
        case 'vermelho': return 'danger';
        case 'laranja': return 'warning';
        case 'amarelo': return 'warning';
        case 'verde': return 'success';
        case 'azul': return 'info';
        default: return 'secondary';
    }
}

function getEncaminhamentoColor($encaminhamento) {
    switch (strtolower($encaminhamento)) {
        case 'alta': return 'success';
        case 'internação': return 'warning';
        case 'transferência': return 'info';
        case 'especialista': return 'primary';
        case 'retorno': return 'secondary';
        case 'óbito': return 'danger';
        default: return 'light';
    }
}
?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Pizza - Faixa Etária
    <?php if (!empty($relatorio_idade)): ?>
    const ctxIdade = document.getElementById('graficoIdade');
    if (ctxIdade) {
        const dadosIdade = {
            labels: [
                <?php foreach ($relatorio_idade as $item): ?>
                    '<?= $item['faixa_etaria'] ?>',
                <?php endforeach; ?>
            ],
            datasets: [{
                data: [
                    <?php foreach ($relatorio_idade as $item): ?>
                        <?= $item['total_pacientes'] ?>,
                    <?php endforeach; ?>
                ],
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB', 
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF',
                    '#FF9F40',
                    '#FF6384'
                ],
                borderWidth: 2
            }]
        };

        new Chart(ctxIdade, {
            type: 'pie',
            data: dadosIdade,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
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
    }
    <?php endif; ?>
});

function exportarRelatorios() {
    const mes = document.getElementById('mes').value;
    const ano = document.getElementById('ano').value;
    
    // Criar um formulário temporário para enviar dados via POST
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= base_url('pacientes/exportar-relatorios') ?>';
    
    const inputMes = document.createElement('input');
    inputMes.type = 'hidden';
    inputMes.name = 'mes';
    inputMes.value = mes;
    
    const inputAno = document.createElement('input');
    inputAno.type = 'hidden';
    inputAno.name = 'ano';
    inputAno.value = ano;
    
    form.appendChild(inputMes);
    form.appendChild(inputAno);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>

<!-- Estilos específicos para relatórios -->
<style>
    /* Estilos para as estatísticas seguindo padrão do index.php */
    .stat-item {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        border: 1px solid #dee2e6;
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        height: 100%;
    }

    .stat-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Ajustes para os cards dos relatórios */
    .section-card {
        background: #fff;
        border-radius: 0.75rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        border: 1px solid #e9ecef;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }

    .section-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e9ecef;
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    }

    .section-body {
        padding: 1.5rem;
    }

    .section-title {
        margin-bottom: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
    }

    .section-title i {
        margin-right: 0.5rem;
        opacity: 0.8;
    }

    /* Ajustes nas tabelas */
    .modern-table {
        margin-bottom: 0;
    }

    .modern-table th {
        background-color: #f8f9fa;
        border-top: none;
        font-weight: 600;
        color: #495057;
        font-size: 0.875rem;
    }

    .modern-table td {
        vertical-align: middle;
        font-size: 0.875rem;
    }

    /* Badges customizados */
    .badge {
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* Action bar melhorada */
    .action-bar {
        padding: 1rem 0;
        margin-bottom: 1rem;
        border-bottom: 1px solid #e9ecef;
    }

    /* Formulário de filtros */
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-select, .form-control {
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-select:focus, .form-control:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    /* Responsividade melhorada */
    @media (max-width: 768px) {
        .stat-number {
            font-size: 2rem;
        }
        
        .section-header {
            padding: 1rem;
        }
        
        .section-body {
            padding: 1rem;
        }
        
        .action-bar .action-left,
        .action-bar .action-right {
            margin: 0.5rem 1rem !important;
        }
    }
</style>

<?= $this->endSection() ?>
