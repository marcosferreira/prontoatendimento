<?php

require_once 'vendor/autoload.php';

use Config\Database;
use Config\Services;

// Conectar ao banco
$db = Database::connect();

// Inserir algumas notificações de teste
$notificacoesTest = [
    [
        'tipo' => 'paciente_recorrente',
        'titulo' => 'Paciente com 5+ atendimentos em 30 dias',
        'descricao' => 'Paciente João Silva apresentou 6 atendimentos nos últimos 30 dias, indicando possível problema crônico não diagnosticado.',
        'severidade' => 'alta',
        'modulo' => 'analise_bi',
        'parametros' => json_encode([
            'paciente_id' => 123,
            'total_atendimentos' => 6,
            'periodo_dias' => 30
        ]),
        'status' => 'ativa',
        'acionada_em' => date('Y-m-d H:i:s'),
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
    ],
    [
        'tipo' => 'surto_sintomas',
        'titulo' => 'Possível surto de gripe no Bairro Centro',
        'descricao' => 'Detectado aumento de 400% nos casos de sintomas gripais no Bairro Centro nas últimas 48 horas.',
        'severidade' => 'critica',
        'modulo' => 'analise_bi',
        'parametros' => json_encode([
            'bairro_id' => 1,
            'sintoma' => 'gripe',
            'casos_detectados' => 12,
            'aumento_percentual' => 400
        ]),
        'status' => 'ativa',
        'acionada_em' => date('Y-m-d H:i:s', strtotime('-2 hours')),
        'created_at' => date('Y-m-d H:i:s', strtotime('-2 hours')),
        'updated_at' => date('Y-m-d H:i:s', strtotime('-2 hours'))
    ],
    [
        'tipo' => 'alta_demanda',
        'titulo' => 'Pico de demanda detectado',
        'descricao' => 'Sistema detectou sobrecarga no atendimento com 150% da capacidade normal.',
        'severidade' => 'media',
        'modulo' => 'analise_bi',
        'parametros' => json_encode([
            'capacidade_atual' => 150,
            'capacidade_normal' => 100,
            'fila_espera' => 25
        ]),
        'status' => 'ativa',
        'acionada_em' => date('Y-m-d H:i:s', strtotime('-1 hour')),
        'created_at' => date('Y-m-d H:i:s', strtotime('-1 hour')),
        'updated_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))
    ],
    [
        'tipo' => 'estatistica_anomala',
        'titulo' => 'Anomalia nos tempos de atendimento',
        'descricao' => 'Tempo médio de atendimento aumentou 50% comparado à média histórica.',
        'severidade' => 'baixa',
        'modulo' => 'analise_bi',
        'parametros' => json_encode([
            'tempo_medio_atual' => 45,
            'tempo_medio_historico' => 30,
            'variacao_percentual' => 50
        ]),
        'status' => 'ativa',
        'acionada_em' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
        'created_at' => date('Y-m-d H:i:s', strtotime('-30 minutes')),
        'updated_at' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
    ]
];

// Inserir dados de teste dos últimos 7 dias
for ($i = 6; $i >= 0; $i--) {
    $data = date('Y-m-d H:i:s', strtotime("-{$i} days"));
    $quantidade = rand(1, 3);
    
    for ($j = 0; $j < $quantidade; $j++) {
        $tipos = ['paciente_recorrente', 'surto_sintomas', 'alta_demanda', 'estatistica_anomala'];
        $severidades = ['baixa', 'media', 'alta', 'critica'];
        
        $notificacoesTest[] = [
            'tipo' => $tipos[array_rand($tipos)],
            'titulo' => 'Notificação de teste ' . ($i * 10 + $j),
            'descricao' => 'Descrição da notificação de teste para demonstração dos gráficos.',
            'severidade' => $severidades[array_rand($severidades)],
            'modulo' => 'analise_bi',
            'parametros' => json_encode(['teste' => true]),
            'status' => rand(0, 1) ? 'ativa' : 'resolvida',
            'acionada_em' => $data,
            'created_at' => $data,
            'updated_at' => $data
        ];
    }
}

// Inserir no banco
try {
    foreach ($notificacoesTest as $notificacao) {
        $db->table('notificacoes')->insert($notificacao);
    }
    
    echo "✅ Dados de teste inseridos com sucesso!\n";
    echo "Total de notificações criadas: " . count($notificacoesTest) . "\n";
    
    // Verificar estatísticas
    $stats = $db->query("
        SELECT 
            status,
            severidade,
            COUNT(*) as total
        FROM notificacoes 
        GROUP BY status, severidade
        ORDER BY status, severidade
    ")->getResultArray();
    
    echo "\n📊 Estatísticas atuais:\n";
    foreach ($stats as $stat) {
        echo "- {$stat['status']} / {$stat['severidade']}: {$stat['total']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erro ao inserir dados: " . $e->getMessage() . "\n";
}
