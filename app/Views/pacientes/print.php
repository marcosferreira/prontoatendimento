<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha do Paciente - <?= esc($paciente['nome']) ?></title>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 5px;
        }
        
        .subtitle {
            color: #666;
            margin-bottom: 10px;
        }
        
        .patient-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .patient-name {
            font-size: 18px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 10px;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .info-item {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            min-width: 120px;
            color: #555;
        }
        
        .info-value {
            flex: 1;
        }
        
        .section {
            margin-bottom: 25px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a8a;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 15px;
        }
        
        .timeline-item {
            border-left: 3px solid #ddd;
            padding-left: 15px;
            margin-bottom: 15px;
            position: relative;
        }
        
        .timeline-item.vermelho { border-left-color: #dc3545; }
        .timeline-item.amarelo { border-left-color: #ffc107; }
        .timeline-item.verde { border-left-color: #28a745; }
        .timeline-item.azul { border-left-color: #007bff; }
        
        .timeline-date {
            font-weight: bold;
            color: #1e3a8a;
        }
        
        .timeline-doctor {
            color: #666;
            margin-bottom: 5px;
        }
        
        .timeline-diagnosis {
            margin-bottom: 5px;
        }
        
        .risk-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        
        .risk-vermelho { background-color: #dc3545; }
        .risk-amarelo { background-color: #ffc107; color: #333; }
        .risk-verde { background-color: #28a745; }
        .risk-azul { background-color: #007bff; }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #999;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .print-info {
            margin-top: 20px;
            font-size: 10px;
            color: #666;
        }
        
        .no-break {
            page-break-inside: avoid;
        }
        
        @media print {
            body { margin: 0; }
            .header { margin-bottom: 15px; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #1e3a8a; color: white; border: none; border-radius: 5px; cursor: pointer;">
            Imprimir
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            Fechar
        </button>
    </div>

    <div class="header">
        <div class="logo">SisPAM</div>
        <div class="subtitle">Sistema de Pronto Atendimento Municipal</div>
        <div style="font-size: 16px; font-weight: bold;">FICHA DO PACIENTE</div>
    </div>

    <div class="patient-info no-break">
        <div class="patient-name"><?= esc($paciente['nome']) ?></div>
        
        <div class="info-grid">
            <div>
                <div class="info-item">
                    <span class="info-label">CPF:</span>
                    <span class="info-value"><?= esc($paciente['cpf']) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Cartão SUS:</span>
                    <span class="info-value"><?= esc($paciente['sus'] ?? 'Não informado') ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Data de Nascimento:</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Idade:</span>
                    <span class="info-value"><?= esc($paciente['idade']) ?> anos</span>
                </div>
            </div>
            <div>
                <div class="info-item">
                    <span class="info-label">Endereço Completo:</span>
                    <span class="info-value">
                        <?php if (!empty($paciente['nome_logradouro'])): ?>
                            <?= esc($paciente['tipo_logradouro'] ?? '') ?> <?= esc($paciente['nome_logradouro']) ?>
                            <?php if (!empty($paciente['numero'])): ?>
                                , <?= esc($paciente['numero']) ?>
                            <?php endif; ?>
                            <?php if (!empty($paciente['complemento'])): ?>
                                - <?= esc($paciente['complemento']) ?>
                            <?php endif; ?>
                            <?php if (!empty($paciente['nome_bairro'])): ?>
                                - <?= esc($paciente['nome_bairro']) ?>
                            <?php endif; ?>
                            <?php if (!empty($paciente['cidade'])): ?>
                                - <?= esc($paciente['cidade']) ?>
                            <?php endif; ?>
                            <?php if (!empty($paciente['cep'])): ?>
                                - CEP: <?= esc($paciente['cep']) ?>
                            <?php endif; ?>
                        <?php else: ?>
                            Não informado
                        <?php endif; ?>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Cadastrado em:</span>
                    <span class="info-value"><?= date('d/m/Y', strtotime($paciente['created_at'])) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">ID Paciente:</span>
                    <span class="info-value"><?= esc($paciente['id_paciente']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($atendimentos) && !empty($atendimentos)): ?>
        <div class="section">
            <div class="section-title">HISTÓRICO DE ATENDIMENTOS</div>
            
            <?php foreach ($atendimentos as $atendimento): ?>
                <div class="timeline-item no-break <?= strtolower($atendimento['classificacao_risco']) ?>">
                    <div class="timeline-date">
                        <?= date('d/m/Y \à\s H:i', strtotime($atendimento['data_atendimento'])) ?>
                        <span class="risk-badge risk-<?= strtolower($atendimento['classificacao_risco']) ?>">
                            <?= esc($atendimento['classificacao_risco']) ?>
                        </span>
                    </div>
                    
                    <div class="timeline-doctor">
                        <strong>Médico:</strong> Dr. <?= esc($atendimento['nome_medico']) ?>
                        <?php if (!empty($atendimento['especialidade'])): ?>
                            - <?= esc($atendimento['especialidade']) ?>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($atendimento['consulta_enfermagem'])): ?>
                        <div style="margin-bottom: 5px;">
                            <strong>Triagem de Enfermagem:</strong><br>
                            <?= esc($atendimento['consulta_enfermagem']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($atendimento['hgt_glicemia']) || !empty($atendimento['pressao_arterial'])): ?>
                        <div style="margin-bottom: 5px;">
                            <strong>Sinais Vitais:</strong>
                            <?php if (!empty($atendimento['pressao_arterial'])): ?>
                                PA: <?= esc($atendimento['pressao_arterial']) ?>
                            <?php endif; ?>
                            <?php if (!empty($atendimento['hgt_glicemia'])): ?>
                                <?= !empty($atendimento['pressao_arterial']) ? ' | ' : '' ?>
                                Glicemia: <?= esc($atendimento['hgt_glicemia']) ?> mg/dL
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($atendimento['hipotese_diagnostico'])): ?>
                        <div class="timeline-diagnosis">
                            <strong>Diagnóstico:</strong><br>
                            <?= esc($atendimento['hipotese_diagnostico']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($atendimento['observacao'])): ?>
                        <div style="margin-bottom: 5px;">
                            <strong>Observações:</strong><br>
                            <?= esc($atendimento['observacao']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($atendimento['encaminhamento'])): ?>
                        <div>
                            <strong>Encaminhamento:</strong> <?= esc($atendimento['encaminhamento']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (isset($exames) && !empty($exames)): ?>
        <div class="section">
            <div class="section-title">EXAMES REALIZADOS</div>
            
            <?php foreach ($exames as $exame): ?>
                <div class="no-break" style="margin-bottom: 10px; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                        <strong><?= esc($exame['nome_exame']) ?></strong>
                        <span style="font-size: 10px; color: #666;">
                            <?= date('d/m/Y', strtotime($exame['data_solicitacao'])) ?>
                        </span>
                    </div>
                    
                    <div style="font-size: 11px; color: #666; margin-bottom: 3px;">
                        Tipo: <?= esc($exame['tipo']) ?> | Status: <?= esc($exame['status']) ?>
                    </div>
                    
                    <?php if (!empty($exame['resultado'])): ?>
                        <div>
                            <strong>Resultado:</strong> <?= esc($exame['resultado']) ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="footer">
        <div class="print-info">
            Ficha impressa em <?= date('d/m/Y \à\s H:i') ?> | 
            Sistema de Pronto Atendimento Municipal - SisPAM
        </div>
    </div>

    <script>
        // Auto-print quando abrindo em nova janela
        window.onload = function() {
            if (window.location.search.includes('autoprint=1')) {
                setTimeout(() => {
                    window.print();
                }, 1000);
            }
        };
    </script>
</body>
</html>
