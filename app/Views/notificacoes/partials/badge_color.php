<?php
// Verificar se a variável $severidade existe
if (!isset($severidade)) {
    $severidade = 'baixa';
}

// Cores dos badges por severidade
$cores = [
    'critica' => 'danger',
    'alta' => 'warning',
    'media' => 'info',
    'baixa' => 'success'
];

echo $cores[$severidade] ?? 'secondary';
?>
