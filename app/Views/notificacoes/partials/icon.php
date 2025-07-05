<?php
// Verificar se a variável $tipo existe
if (!isset($tipo)) {
    $tipo = 'default';
}

// Ícones para cada tipo de notificação
$icones = [
    'paciente_recorrente' => 'bi-person-fill-exclamation',
    'surto_sintomas' => 'bi-virus',
    'alta_demanda' => 'bi-speedometer2',
    'medicamento_critico' => 'bi-capsule',
    'equipamento_falha' => 'bi-gear-fill',
    'estatistica_anomala' => 'bi-graph-down-arrow'
];

$icone = $icones[$tipo] ?? 'bi-bell-fill';
?>

<i class="bi <?= $icone ?>"></i>
