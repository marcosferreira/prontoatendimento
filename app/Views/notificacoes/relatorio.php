<?= $this->extend('layout/base') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/notificacoes.css') ?>">
<style>
    @media print {
        .no-print { display: none !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
        .main-content { padding: 0 !important; }
        .app-container .sidebar, .app-container .topbar { display: none !important; }
        .main-content { margin-left: 0 !important; }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="app-container">
    <?php echo $this->include('components/sidebar'); ?>
    <?php echo $this->include('components/topbar'); ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header no-print">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('notificacoes') ?>">
                                <i class="bi bi-bell"></i> Notificações
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Relatório</li>
                    </ol>
                </nav>
                <h1>
                    <i class="bi bi-file-earmark-text"></i> Relatório de Notificações BI
                </h1>
                <p class="subtitle">Período: <?= date('d/m/Y', strtotime($data_inicio)) ?> a <?= date('d/m/Y', strtotime($data_fim)) ?></p>
                
                <div class="header-actions">
                    <button type="button" class="btn btn-outline-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
                    <a href="<?= base_url('notificacoes/relatorio?periodo=' . $periodo . '&formato=pdf') ?>" 
                       class="btn btn-outline-danger">
                        <i class="bi bi-file-pdf"></i> Exportar PDF
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#modalFiltros">
                        <i class="bi bi-funnel"></i> Filtros
                    </button>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Cabeçalho do Relatório -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4>Sistema de Pronto Atendimento Municipal</h4>
                                <h5 class="text-muted">Relatório de Notificações BI</h5>
                                <p class="mb-1">
                                    <strong>Período:</strong> 
                                    <?= date('d/m/Y', strtotime($data_inicio)) ?> a <?= date('d/m/Y', strtotime($data_fim)) ?>
                                    (<?= $periodo ?> dias)
                                </p>
                                <p class="mb-0">
                                    <strong>Gerado em:</strong> <?= date('d/m/Y H:i:s') ?>
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" class="img-fluid" style="max-height: 80px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumo Executivo -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bi bi-graph-up"></i> Resumo Executivo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="text-center p-3 border rounded">
                                    <h3 class="text-primary"><?= count($notificacoes) ?></h3>
                                    <small class="text-muted">Total de Notificações</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="text-center p-3 border rounded">
                                    <h3 class="text-danger"><?= $estatisticas['total_criticas'] ?></h3>
                                    <small class="text-muted">Críticas</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="text-center p-3 border rounded">
                                    <h3 class="text-success"><?= $estatisticas['total_resolvidas'] ?></h3>
                                    <small class="text-muted">Resolvidas</small>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="text-center p-3 border rounded">
                                    <h3 class="text-warning"><?= $estatisticas['tempo_medio_resolucao'] ?>h</h3>
                                    <small class="text-muted">Tempo Médio Resolução</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Distribuição por Severidade -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bi bi-pie-chart"></i> Distribuição por Severidade
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Severidade</th>
                                        <th class="text-end">Quantidade</th>
                                        <th class="text-end">Percentual</th>
                                        <th class="text-end">Resolvidas</th>
                                        <th class="text-end">Taxa Resolução</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (['critica', 'alta', 'media', 'baixa'] as $severidade): ?>
                                        <?php 
                                        $total = $estatisticas['por_severidade'][$severidade]['total'] ?? 0;
                                        $resolvidas = $estatisticas['por_severidade'][$severidade]['resolvidas'] ?? 0;
                                        $percentual = count($notificacoes) > 0 ? round(($total / count($notificacoes)) * 100, 1) : 0;
                                        $taxa_resolucao = $total > 0 ? round(($resolvidas / $total) * 100, 1) : 0;
                                        ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $severidade === 'critica' ? 'danger' : 
                                                         ($severidade === 'alta' ? 'warning' : 
                                                          ($severidade === 'media' ? 'info' : 'success')); 
                                                ?>">
                                                    <?= ucfirst($severidade) ?>
                                                </span>
                                            </td>
                                            <td class="text-end"><?= $total ?></td>
                                            <td class="text-end"><?= $percentual ?>%</td>
                                            <td class="text-end"><?= $resolvidas ?></td>
                                            <td class="text-end"><?= $taxa_resolucao ?>%</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Distribuição por Tipo -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bi bi-bar-chart"></i> Distribuição por Tipo
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th class="text-end">Quantidade</th>
                                        <th class="text-end">Percentual</th>
                                        <th class="text-end">Tempo Médio Resolução</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $tipos_nomes = [
                                        'paciente_recorrente' => 'Paciente Recorrente',
                                        'surto_sintomas' => 'Surto de Sintomas',
                                        'alta_demanda' => 'Alta Demanda',
                                        'medicamento_critico' => 'Medicamento Crítico',
                                        'equipamento_falha' => 'Falha de Equipamento',
                                        'estatistica_anomala' => 'Estatística Anômala'
                                    ];
                                    
                                    foreach ($estatisticas['por_tipo'] as $tipo => $dados): 
                                        $percentual = count($notificacoes) > 0 ? round(($dados['total'] / count($notificacoes)) * 100, 1) : 0;
                                    ?>
                                        <tr>
                                            <td><?= $tipos_nomes[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo)) ?></td>
                                            <td class="text-end"><?= $dados['total'] ?></td>
                                            <td class="text-end"><?= $percentual ?>%</td>
                                            <td class="text-end"><?= $dados['tempo_medio'] ?>h</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Lista Detalhada -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bi bi-list-ul"></i> Lista Detalhada de Notificações
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($notificacoes)): ?>
                            <div class="text-center py-4">
                                <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">Nenhuma notificação encontrada</h5>
                                <p class="text-muted">Não há notificações para o período selecionado.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Data/Hora</th>
                                            <th>Tipo</th>
                                            <th>Título</th>
                                            <th>Severidade</th>
                                            <th>Status</th>
                                            <th>Módulo</th>
                                            <th>Tempo Resolução</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($notificacoes as $notificacao): ?>
                                            <tr>
                                                <td>
                                                    <small><?= date('d/m/Y H:i', strtotime($notificacao['acionada_em'])) ?></small>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?php 
                                                        $tipo = $notificacao['tipo'];
                                                        include APPPATH . 'Views/notificacoes/partials/icon.php'; 
                                                        ?>
                                                        <?= $tipos_nomes[$notificacao['tipo']] ?? ucfirst(str_replace('_', ' ', $notificacao['tipo'])) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small><?= esc($notificacao['titulo']) ?></small>
                                                    <?php if (!empty($notificacao['parametros'])): ?>
                                                        <br><small class="text-muted">
                                                            <?php
                                                            $parametros = $notificacao['parametros'];
                                                            if ($notificacao['tipo'] === 'paciente_recorrente') {
                                                                echo "Paciente: " . esc($parametros['paciente_nome'] ?? 'N/A');
                                                            } elseif ($notificacao['tipo'] === 'surto_sintomas') {
                                                                echo "Local: " . esc($parametros['bairro_nome'] ?? 'N/A') . " - Casos: " . ($parametros['total_casos'] ?? 0);
                                                            } elseif ($notificacao['tipo'] === 'alta_demanda') {
                                                                $data = $parametros['data'] ?? $parametros['data_simulada'] ?? null;
                                                                $dataFormatada = $data ? date('d/m/Y', strtotime($data)) : 'N/A';
                                                                echo "Data: " . $dataFormatada . " - " . ($parametros['atendimentos'] ?? $parametros['atendimentos_simulados'] ?? 0) . " atendimentos";
                                                            }
                                                            ?>
                                                        </small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        $severidade = $notificacao['severidade'];
                                                        include APPPATH . 'Views/notificacoes/partials/badge_color.php'; 
                                                    ?>">
                                                        <?= ucfirst($notificacao['severidade']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $notificacao['status'] === 'ativa' ? 'warning' : ($notificacao['status'] === 'resolvida' ? 'success' : 'secondary') ?>">
                                                        <?= ucfirst($notificacao['status']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small><?= esc($notificacao['modulo']) ?></small>
                                                </td>
                                                <td>
                                                    <small>
                                                        <?php if ($notificacao['resolvida_em']): ?>
                                                            <?php
                                                            $inicio = new DateTime($notificacao['acionada_em']);
                                                            $fim = new DateTime($notificacao['resolvida_em']);
                                                            $diff = $fim->diff($inicio);
                                                            echo $diff->days > 0 ? $diff->days . 'd ' : '';
                                                            echo $diff->h . 'h ' . $diff->i . 'm';
                                                            ?>
                                                        <?php else: ?>
                                                            -
                                                        <?php endif; ?>
                                                    </small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Observações e Recomendações -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="bi bi-clipboard-check"></i> Observações e Recomendações
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Principais Observações:</h6>
                                <ul>
                                    <?php if ($estatisticas['total_criticas'] > 0): ?>
                                        <li>Foram identificadas <?= $estatisticas['total_criticas'] ?> notificações críticas no período</li>
                                    <?php endif; ?>
                                    
                                    <?php if ($estatisticas['tempo_medio_resolucao'] > 24): ?>
                                        <li class="text-warning">Tempo médio de resolução está acima de 24 horas (<?= $estatisticas['tempo_medio_resolucao'] ?>h)</li>
                                    <?php else: ?>
                                        <li class="text-success">Tempo médio de resolução dentro do padrão (<?= $estatisticas['tempo_medio_resolucao'] ?>h)</li>
                                    <?php endif; ?>
                                    
                                    <?php 
                                    $tipo_mais_frequente = '';
                                    $maior_quantidade = 0;
                                    foreach ($estatisticas['por_tipo'] as $tipo => $dados) {
                                        if ($dados['total'] > $maior_quantidade) {
                                            $maior_quantidade = $dados['total'];
                                            $tipo_mais_frequente = $tipo;
                                        }
                                    }
                                    if ($tipo_mais_frequente): 
                                    ?>
                                        <li>Tipo mais frequente: <?= $tipos_nomes[$tipo_mais_frequente] ?? $tipo_mais_frequente ?> (<?= $maior_quantidade ?> ocorrências)</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>Recomendações:</h6>
                                <ul>
                                    <?php if ($estatisticas['tempo_medio_resolucao'] > 24): ?>
                                        <li>Revisar processo de resolução de notificações para reduzir tempo médio</li>
                                    <?php endif; ?>
                                    
                                    <?php if ($estatisticas['total_criticas'] > 5): ?>
                                        <li>Implementar ações preventivas para reduzir notificações críticas</li>
                                    <?php endif; ?>
                                    
                                    <li>Manter monitoramento contínuo dos indicadores</li>
                                    <li>Treinar equipe para identificação precoce de situações críticas</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php echo $this->include('components/footer'); ?>
        </div>
    </main>
</div>

<!-- Modal de Filtros -->
<div class="modal fade" id="modalFiltros" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET" action="<?= base_url('notificacoes/relatorio') ?>">
                <div class="modal-header">
                    <h5 class="modal-title">Filtros do Relatório</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="periodo" class="form-label">Período (dias)</label>
                        <select class="form-select" name="periodo" id="periodo">
                            <option value="7" <?= $periodo == 7 ? 'selected' : '' ?>>Últimos 7 dias</option>
                            <option value="15" <?= $periodo == 15 ? 'selected' : '' ?>>Últimos 15 dias</option>
                            <option value="30" <?= $periodo == 30 ? 'selected' : '' ?>>Últimos 30 dias</option>
                            <option value="60" <?= $periodo == 60 ? 'selected' : '' ?>>Últimos 60 dias</option>
                            <option value="90" <?= $periodo == 90 ? 'selected' : '' ?>>Últimos 90 dias</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="formato" class="form-label">Formato</label>
                        <select class="form-select" name="formato" id="formato">
                            <option value="html">HTML (Visualização)</option>
                            <option value="pdf">PDF (Download)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Gerar Relatório</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Melhorar apresentação para impressão
    window.addEventListener('beforeprint', function() {
        document.body.classList.add('printing');
    });

    window.addEventListener('afterprint', function() {
        document.body.classList.remove('printing');
    });
</script>
<?= $this->endSection() ?>
