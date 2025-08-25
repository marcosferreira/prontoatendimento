<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18"><?= $title ?></h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Início</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('pacientes') ?>">Pacientes</a></li>
                        <li class="breadcrumb-item active">Relatórios</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
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
                                <i class="mdi mdi-filter"></i> Filtrar
                            </button>
                            <button type="button" class="btn btn-success" onclick="exportarRelatorios()">
                                <i class="mdi mdi-file-excel"></i> Exportar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Relatório por Faixa Etária -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-account-group text-primary"></i>
                        Pacientes por Faixa Etária
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($relatorio_idade)): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
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
                            <i class="mdi mdi-information-outline display-4"></i>
                            <p class="mt-2">Nenhum paciente encontrado para o período selecionado.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Relatório de Enfermagem -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-medical-bag text-success"></i>
                        Atendimentos de Enfermagem
                    </h4>
                </div>
                <div class="card-body">
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
                                <table class="table table-striped table-sm">
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
                            <i class="mdi mdi-information-outline display-4"></i>
                            <p class="mt-2">Nenhum atendimento de enfermagem encontrado para o período selecionado.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Relatório de Médicos -->
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">
                        <i class="mdi mdi-doctor text-info"></i>
                        Atendimentos Médicos
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (!empty($relatorio_medico)): ?>
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-primary"><?= number_format($relatorio_medico['pacientes_medico']) ?></h3>
                                        <p class="mb-0">Pacientes atendidos</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-success"><?= number_format($relatorio_medico['medicos_ativos']) ?></h3>
                                        <p class="mb-0">Médicos ativos</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <h3 class="text-info"><?= round($relatorio_medico['idade_media_medico'], 1) ?> anos</h3>
                                        <p class="mb-0">Idade média dos pacientes</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Ranking de Médicos -->
                            <div class="col-lg-8">
                                <?php if (!empty($relatorio_medico['ranking_medicos'])): ?>
                                    <h6>Ranking de Médicos por Atendimentos</h6>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
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
                                                            <?php if ($index < 3): ?>
                                                                <span class="badge bg-<?= $index == 0 ? 'warning' : ($index == 1 ? 'secondary' : 'dark') ?>">
                                                                    <?= $index + 1 ?>º
                                                                </span>
                                                            <?php else: ?>
                                                                <?= $index + 1 ?>º
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= $medico['nome_medico'] ?></td>
                                                        <td><?= $medico['crm'] ?></td>
                                                        <td><?= $medico['especialidade'] ?? 'Não informado' ?></td>
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
                                        <table class="table table-striped table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Tipo</th>
                                                    <th class="text-end">Pacientes</th>
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
                            <i class="mdi mdi-information-outline display-4"></i>
                            <p class="mt-2">Nenhum atendimento médico encontrado para o período selecionado.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
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

<?= $this->endSection() ?>
