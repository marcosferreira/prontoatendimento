<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório de Notificações BI - PDF</title>
    <style>
        @page {
            margin: 2cm;
            @top-center {
                content: "Relatório de Notificações BI";
            }
            @bottom-center {
                content: "Página " counter(page) " de " counter(pages);
            }
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #0066cc;
            padding-bottom: 20px;
        }

        .header h1 {
            color: #0066cc;
            margin: 0;
            font-size: 24px;
        }

        .header h2 {
            color: #666;
            margin: 5px 0;
            font-size: 18px;
        }

        .header .info {
            margin-top: 10px;
            font-size: 11px;
            color: #666;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .section-title {
            background-color: #f8f9fa;
            padding: 8px 12px;
            margin: 0 0 15px 0;
            border-left: 4px solid #0066cc;
            font-weight: bold;
            font-size: 14px;
        }

        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .stats-row {
            display: table-row;
        }

        .stats-cell {
            display: table-cell;
            width: 25%;
            text-align: center;
            padding: 15px;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }

        .stats-number {
            font-size: 24px;
            font-weight: bold;
            color: #0066cc;
            display: block;
        }

        .stats-label {
            font-size: 11px;
            color: #666;
            margin-top: 5px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .table th,
        .table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }

        .table td {
            font-size: 10px;
        }

        .table .text-end {
            text-align: right;
        }

        .table .text-center {
            text-align: center;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            font-size: 9px;
            font-weight: bold;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 3px;
        }

        .badge.bg-danger { background-color: #dc3545; }
        .badge.bg-warning { background-color: #ffc107; color: #000; }
        .badge.bg-info { background-color: #0dcaf0; color: #000; }
        .badge.bg-success { background-color: #198754; }
        .badge.bg-secondary { background-color: #6c757d; }

        .observations {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }

        .observations h4 {
            margin-top: 0;
            color: #0066cc;
        }

        .observations ul {
            margin: 10px 0;
            padding-left: 20px;
        }

        .observations li {
            margin-bottom: 5px;
        }

        .row {
            display: table;
            width: 100%;
        }

        .col {
            display: table-cell;
            vertical-align: top;
            padding-right: 15px;
        }

        .col:last-child {
            padding-right: 0;
        }

        .page-break {
            page-break-before: always;
        }

        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-style: italic;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 50px;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <!-- Cabeçalho -->
    <div class="header">
        <h1>Sistema de Pronto Atendimento Municipal</h1>
        <h2>Relatório de Notificações BI</h2>
        <div class="info">
            <strong>Período:</strong> <?= date('d/m/Y', strtotime($data_inicio)) ?> a <?= date('d/m/Y', strtotime($data_fim)) ?> (<?= $periodo ?> dias)<br>
            <strong>Gerado em:</strong> <?= date('d/m/Y H:i:s') ?>
        </div>
    </div>

    <!-- Resumo Executivo -->
    <div class="section">
        <div class="section-title">Resumo Executivo</div>
        
        <div class="stats-grid">
            <div class="stats-row">
                <div class="stats-cell">
                    <span class="stats-number"><?= count($notificacoes) ?></span>
                    <div class="stats-label">Total de Notificações</div>
                </div>
                <div class="stats-cell">
                    <span class="stats-number"><?= $estatisticas['total_criticas'] ?></span>
                    <div class="stats-label">Críticas</div>
                </div>
                <div class="stats-cell">
                    <span class="stats-number"><?= $estatisticas['total_resolvidas'] ?></span>
                    <div class="stats-label">Resolvidas</div>
                </div>
                <div class="stats-cell">
                    <span class="stats-number"><?= $estatisticas['tempo_medio_resolucao'] ?>h</span>
                    <div class="stats-label">Tempo Médio Resolução</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Distribuição por Severidade -->
    <div class="section">
        <div class="section-title">Distribuição por Severidade</div>
        
        <table class="table">
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
                            <span class="badge bg-<?= $severidade === 'critica' ? 'danger' : ($severidade === 'alta' ? 'warning' : ($severidade === 'media' ? 'info' : 'success')) ?>">
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

    <!-- Distribuição por Tipo -->
    <div class="section">
        <div class="section-title">Distribuição por Tipo</div>
        
        <table class="table">
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

    <!-- Lista Detalhada -->
    <div class="section page-break">
        <div class="section-title">Lista Detalhada de Notificações</div>
        
        <?php if (empty($notificacoes)): ?>
            <div class="no-data">
                Nenhuma notificação encontrada para o período selecionado.
            </div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Data/Hora</th>
                        <th>Tipo</th>
                        <th>Título</th>
                        <th>Severidade</th>
                        <th>Status</th>
                        <th>Tempo Resolução</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($notificacoes as $notificacao): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($notificacao['acionada_em'])) ?></td>
                            <td><?= $tipos_nomes[$notificacao['tipo']] ?? ucfirst(str_replace('_', ' ', $notificacao['tipo'])) ?></td>
                            <td>
                                <?= esc($notificacao['titulo']) ?>
                                <?php if (!empty($notificacao['parametros'])): ?>
                                    <br><small style="color: #666;">
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
                                <span class="badge bg-<?= $notificacao['severidade'] === 'critica' ? 'danger' : ($notificacao['severidade'] === 'alta' ? 'warning' : ($notificacao['severidade'] === 'media' ? 'info' : 'success')) ?>">
                                    <?= ucfirst($notificacao['severidade']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $notificacao['status'] === 'ativa' ? 'warning' : ($notificacao['status'] === 'resolvida' ? 'success' : 'secondary') ?>">
                                    <?= ucfirst($notificacao['status']) ?>
                                </span>
                            </td>
                            <td>
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
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Observações e Recomendações -->
    <div class="section page-break">
        <div class="section-title">Observações e Recomendações</div>
        
        <div class="observations">
            <div class="row">
                <div class="col">
                    <h4>Principais Observações:</h4>
                    <ul>
                        <?php if ($estatisticas['total_criticas'] > 0): ?>
                            <li>Foram identificadas <?= $estatisticas['total_criticas'] ?> notificações críticas no período</li>
                        <?php endif; ?>
                        
                        <?php if ($estatisticas['tempo_medio_resolucao'] > 24): ?>
                            <li style="color: #dc3545;">Tempo médio de resolução está acima de 24 horas (<?= $estatisticas['tempo_medio_resolucao'] ?>h)</li>
                        <?php else: ?>
                            <li style="color: #198754;">Tempo médio de resolução dentro do padrão (<?= $estatisticas['tempo_medio_resolucao'] ?>h)</li>
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
                <div class="col">
                    <h4>Recomendações:</h4>
                    <ul>
                        <?php if ($estatisticas['tempo_medio_resolucao'] > 24): ?>
                            <li>Revisar processo de resolução de notificações para reduzir tempo médio</li>
                        <?php endif; ?>
                        
                        <?php if ($estatisticas['total_criticas'] > 5): ?>
                            <li>Implementar ações preventivas para reduzir notificações críticas</li>
                        <?php endif; ?>
                        
                        <li>Manter monitoramento contínuo dos indicadores</li>
                        <li>Treinar equipe para identificação precoce de situações críticas</li>
                        <li>Realizar análise quinzenal dos padrões identificados</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Rodapé -->
    <div class="footer">
        Sistema de Pronto Atendimento Municipal - Relatório de Notificações BI - <?= date('Y') ?>
    </div>
</body>
</html>
