<?php 
// Verificar se as variáveis existem
if (!isset($parametros) || empty($parametros)) return; 
if (!isset($tipo)) $tipo = 'default';
?>

<div class="row g-2">
    <?php if ($tipo === 'paciente_recorrente'): ?>
        <div class="col-md-6">
            <small class="text-muted">Paciente:</small><br>
            <strong><?= esc($parametros['paciente_nome'] ?? 'N/A') ?></strong>
        </div>
        <div class="col-md-3">
            <small class="text-muted">CPF:</small><br>
            <code><?= esc($parametros['paciente_cpf'] ?? 'N/A') ?></code>
        </div>
        <div class="col-md-3">
            <small class="text-muted">Atendimentos:</small><br>
            <span class="badge bg-danger"><?= $parametros['total_atendimentos'] ?? 0 ?></span>
        </div>
        <?php if (isset($parametros['sintoma_recorrente'])): ?>
            <div class="col-12">
                <small class="text-muted">Sintoma recorrente:</small><br>
                <span class="text-warning"><?= ucfirst($parametros['sintoma_recorrente']) ?></span>
            </div>
        <?php endif; ?>
        
    <?php elseif ($tipo === 'surto_sintomas'): ?>
        <div class="col-md-4">
            <small class="text-muted">Localização:</small><br>
            <strong><?= esc($parametros['bairro_nome'] ?? 'N/A') ?></strong>
        </div>
        <div class="col-md-4">
            <small class="text-muted">Casos:</small><br>
            <span class="badge bg-danger"><?= $parametros['total_casos'] ?? 0 ?></span>
        </div>
        <div class="col-md-4">
            <small class="text-muted">Período:</small><br>
            <?= ($parametros['periodo_dias'] ?? 0) ?> dias
        </div>
        <div class="col-12">
            <small class="text-muted">Sintoma:</small><br>
            <span class="text-warning"><?= esc($parametros['sintoma'] ?? 'N/A') ?></span>
        </div>
        
    <?php elseif ($tipo === 'alta_demanda'): ?>
        <div class="col-md-3">
            <small class="text-muted">Data:</small><br>
            <?php 
            $data = $parametros['data'] ?? $parametros['data_simulada'] ?? null;
            echo $data ? date('d/m/Y', strtotime($data)) : 'N/A';
            ?>
        </div>
        <div class="col-md-3">
            <small class="text-muted">Horário:</small><br>
            <?= isset($parametros['hora']) ? $parametros['hora'] . ':00h' : (isset($parametros['hora_simulada']) ? $parametros['hora_simulada'] . 'h' : 'N/A') ?>
        </div>
        <div class="col-md-3">
            <small class="text-muted">Atendimentos:</small><br>
            <span class="badge bg-warning"><?= $parametros['atendimentos'] ?? $parametros['atendimentos_simulados'] ?? 0 ?></span>
        </div>
        <div class="col-md-3">
            <small class="text-muted">Críticos:</small><br>
            <span class="badge bg-danger"><?= $parametros['casos_criticos'] ?? 0 ?></span>
        </div>
        <div class="col-12">
            <small class="text-muted">Aumento:</small>
            <span class="text-danger">+<?= $parametros['percentual_aumento'] ?? 0 ?>% da média</span>
        </div>
        
    <?php elseif ($tipo === 'estatistica_anomala'): ?>
        <?php foreach ($parametros as $chave => $valor): ?>
            <?php if (is_array($valor)) continue; ?>
            <div class="col-md-6">
                <small class="text-muted"><?= ucfirst(str_replace('_', ' ', $chave)) ?>:</small><br>
                <?php if (is_numeric($valor)): ?>
                    <span class="badge bg-info"><?= $valor ?></span>
                <?php else: ?>
                    <?= esc($valor) ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
