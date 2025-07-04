<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Solicitação de Exame - <?= esc($atendimentoExame['nome_exame']) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            background: white;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #007bff;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header p {
            color: #666;
            font-size: 14px;
        }
        
        .info-section {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .info-header {
            background: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
            color: #007bff;
        }
        
        .info-content {
            padding: 15px;
        }
        
        .row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .col {
            flex: 1;
            padding-right: 15px;
        }
        
        .col:last-child {
            padding-right: 0;
        }
        
        .field {
            margin-bottom: 8px;
        }
        
        .field-label {
            font-weight: bold;
            color: #555;
        }
        
        .field-value {
            color: #333;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-solicitado { background: #fff3cd; color: #856404; }
        .status-realizado { background: #d4edda; color: #155724; }
        .status-cancelado { background: #f8d7da; color: #721c24; }
        
        .tipo-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .tipo-laboratorial { background: #007bff; color: white; }
        .tipo-imagem { background: #17a2b8; color: white; }
        .tipo-funcional { background: #ffc107; color: #212529; }
        .tipo-outros { background: #6c757d; color: white; }
        
        .observacoes-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-top: 10px;
            min-height: 60px;
        }
        
        .footer {
            margin-top: 40px;
            border-top: 1px solid #ddd;
            padding-top: 15px;
            font-size: 11px;
            color: #666;
        }
        
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        
        .signature-box {
            width: 45%;
            text-align: center;
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 50px;
        }
        
        @media print {
            body {
                padding: 10px;
            }
            
            .no-print {
                display: none !important;
            }
            
            .info-section {
                break-inside: avoid;
            }
        }
        
        .urgente {
            background: #dc3545 !important;
            color: white !important;
            font-weight: bold;
            padding: 10px;
            text-align: center;
            margin-bottom: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>SOLICITAÇÃO DE EXAME</h1>
        <p>Sistema de Pronto Atendimento Municipal</p>
        <p>Data de Emissão: <?= date('d/m/Y H:i') ?></p>
    </div>

    <!-- Botão de Impressão (não aparece na impressão) -->
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
            🖨️ Imprimir Solicitação
        </button>
        <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-left: 10px;">
            ❌ Fechar
        </button>
    </div>

    <!-- Informações do Paciente -->
    <div class="info-section">
        <div class="info-header">
            👤 DADOS DO PACIENTE
        </div>
        <div class="info-content">
            <div class="row">
                <div class="col">
                    <div class="field">
                        <span class="field-label">Nome:</span>
                        <span class="field-value"><?= esc($atendimentoExame['nome_paciente']) ?></span>
                    </div>
                    <div class="field">
                        <span class="field-label">CPF:</span>
                        <span class="field-value"><?= esc($atendimentoExame['cpf']) ?></span>
                    </div>
                </div>
                <div class="col">
                    <div class="field">
                        <span class="field-label">Data de Nascimento:</span>
                        <span class="field-value"><?= date('d/m/Y', strtotime($atendimentoExame['data_nascimento'])) ?></span>
                    </div>
                    <div class="field">
                        <span class="field-label">Sexo:</span>
                        <span class="field-value"><?= $atendimentoExame['sexo'] === 'M' ? 'Masculino' : 'Feminino' ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações do Atendimento -->
    <div class="info-section">
        <div class="info-header">
            🏥 DADOS DO ATENDIMENTO
        </div>
        <div class="info-content">
            <div class="row">
                <div class="col">
                    <div class="field">
                        <span class="field-label">Médico Solicitante:</span>
                        <span class="field-value"><?= esc($atendimentoExame['nome_medico']) ?></span>
                    </div>
                    <div class="field">
                        <span class="field-label">CRM:</span>
                        <span class="field-value"><?= esc($atendimentoExame['crm']) ?></span>
                    </div>
                </div>
                <div class="col">
                    <div class="field">
                        <span class="field-label">Data do Atendimento:</span>
                        <span class="field-value"><?= date('d/m/Y H:i', strtotime($atendimentoExame['data_atendimento'])) ?></span>
                    </div>
                    <div class="field">
                        <span class="field-label">Nº do Atendimento:</span>
                        <span class="field-value"><?= $atendimentoExame['id_atendimento'] ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações do Exame -->
    <div class="info-section">
        <div class="info-header">
            🧪 EXAME SOLICITADO
        </div>
        <div class="info-content">
            <div class="field" style="margin-bottom: 15px;">
                <span class="field-label">Nome do Exame:</span>
                <span class="field-value" style="font-size: 16px; font-weight: bold; color: #007bff;">
                    <?= esc($atendimentoExame['nome_exame']) ?>
                </span>
            </div>
            
            <div class="row">
                <div class="col">
                    <div class="field">
                        <span class="field-label">Tipo:</span>
                        <span class="tipo-badge tipo-<?= $atendimentoExame['tipo_exame'] ?>">
                            <?= ucfirst($atendimentoExame['tipo_exame']) ?>
                        </span>
                    </div>
                    <?php if ($atendimentoExame['codigo_exame']): ?>
                        <div class="field">
                            <span class="field-label">Código:</span>
                            <span class="field-value"><?= esc($atendimentoExame['codigo_exame']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col">
                    <div class="field">
                        <span class="field-label">Status:</span>
                        <span class="status-badge status-<?= strtolower($atendimentoExame['status']) ?>">
                            <?= $atendimentoExame['status'] ?>
                        </span>
                    </div>
                    <div class="field">
                        <span class="field-label">Data da Solicitação:</span>
                        <span class="field-value"><?= date('d/m/Y H:i', strtotime($atendimentoExame['data_solicitacao'])) ?></span>
                    </div>
                </div>
            </div>

            <?php if ($atendimentoExame['descricao_exame']): ?>
                <div style="margin-top: 15px;">
                    <div class="field-label">Descrição do Exame:</div>
                    <div style="background: #f8f9fa; padding: 10px; border-radius: 5px; margin-top: 5px;">
                        <?= nl2br(esc($atendimentoExame['descricao_exame'])) ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Observações -->
    <div class="info-section">
        <div class="info-header">
            📝 OBSERVAÇÕES CLÍNICAS
        </div>
        <div class="info-content">
            <div class="observacoes-box">
                <?php if ($atendimentoExame['observacao']): ?>
                    <?= nl2br(esc($atendimentoExame['observacao'])) ?>
                <?php else: ?>
                    <em style="color: #999;">Nenhuma observação especial.</em>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Resultado (se disponível) -->
    <?php if ($atendimentoExame['resultado'] && $atendimentoExame['status'] === 'Realizado'): ?>
        <div class="info-section">
            <div class="info-header">
                ✅ RESULTADO DO EXAME
            </div>
            <div class="info-content">
                <?php if ($atendimentoExame['data_realizacao']): ?>
                    <div class="field">
                        <span class="field-label">Data de Realização:</span>
                        <span class="field-value"><?= date('d/m/Y H:i', strtotime($atendimentoExame['data_realizacao'])) ?></span>
                    </div>
                <?php endif; ?>
                <div style="margin-top: 10px;">
                    <div class="field-label">Resultado:</div>
                    <div style="background: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-top: 5px;">
                        <?= nl2br(esc($atendimentoExame['resultado'])) ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Assinaturas -->
    <div class="signature-section">
        <div class="signature-box">
            <div>Médico Solicitante</div>
            <div style="font-size: 10px; margin-top: 5px;">
                <?= esc($atendimentoExame['nome_medico']) ?><br>
                CRM: <?= esc($atendimentoExame['crm']) ?>
            </div>
        </div>
        <div class="signature-box">
            <div>Responsável pela Coleta/Realização</div>
            <div style="font-size: 10px; margin-top: 5px;">
                Nome: ________________________<br>
                Data: ______/______/______
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <div style="text-align: center;">
            <p><strong>Sistema de Pronto Atendimento Municipal</strong></p>
            <p>Documento gerado em <?= date('d/m/Y H:i:s') ?> | Solicitação #<?= $atendimentoExame['id_atendimento_exame'] ?></p>
            <p style="margin-top: 10px; font-style: italic;">
                Este documento é válido apenas com a assinatura do médico solicitante
            </p>
        </div>
    </div>

    <script>
        // Auto-imprimir quando a página carregar (opcional)
        // window.onload = function() {
        //     window.print();
        // }
        
        // Detectar quando a impressão foi cancelada ou concluída
        window.onafterprint = function() {
            // Opcional: fechar a janela após impressão
            // window.close();
        }
    </script>
</body>
</html>
