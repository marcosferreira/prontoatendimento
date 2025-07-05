<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\NotificacaoModel;

class CreateTestNotifications extends BaseCommand
{
    protected $group = 'Tests';
    protected $name = 'test:notifications';
    protected $description = 'Cria notificações de teste para desenvolvimento.';

    public function run(array $params)
    {
        $notificacaoModel = new NotificacaoModel();

        // Limpar notificações existentes (opcional)
        $confirmar = CLI::prompt('Deseja limpar todas as notificações existentes?', ['y', 'n']);
        if ($confirmar === 'y') {
            $notificacaoModel->where('id >', 0)->delete();
            CLI::write('Notificações existentes removidas.', 'yellow');
        }

        // Dados de teste
        $notificacoesTest = [
            [
                'tipo' => 'paciente_recorrente',
                'titulo' => 'Paciente com 5+ atendimentos em 30 dias',
                'descricao' => 'Paciente João Silva apresentou 6 atendimentos nos últimos 30 dias, indicando possível problema crônico não diagnosticado.',
                'severidade' => 'alta',
                'modulo' => 'analise_bi',
                'parametros' => [
                    'paciente_id' => 123,
                    'total_atendimentos' => 6,
                    'periodo_dias' => 30
                ],
                'status' => 'ativa',
                'acionada_em' => date('Y-m-d H:i:s')
            ],
            [
                'tipo' => 'surto_sintomas',
                'titulo' => 'Possível surto de gripe no Bairro Centro',
                'descricao' => 'Detectado aumento de 400% nos casos de sintomas gripais no Bairro Centro nas últimas 48 horas.',
                'severidade' => 'critica',
                'modulo' => 'analise_bi',
                'parametros' => [
                    'bairro_id' => 1,
                    'sintoma' => 'gripe',
                    'casos_detectados' => 12,
                    'aumento_percentual' => 400
                ],
                'status' => 'ativa',
                'acionada_em' => date('Y-m-d H:i:s', strtotime('-2 hours'))
            ],
            [
                'tipo' => 'alta_demanda',
                'titulo' => 'Pico de demanda detectado',
                'descricao' => 'Sistema detectou sobrecarga no atendimento com 150% da capacidade normal.',
                'severidade' => 'media',
                'modulo' => 'analise_bi',
                'parametros' => [
                    'capacidade_atual' => 150,
                    'capacidade_normal' => 100,
                    'fila_espera' => 25
                ],
                'status' => 'ativa',
                'acionada_em' => date('Y-m-d H:i:s', strtotime('-1 hour'))
            ],
            [
                'tipo' => 'estatistica_anomala',
                'titulo' => 'Anomalia nos tempos de atendimento',
                'descricao' => 'Tempo médio de atendimento aumentou 50% comparado à média histórica.',
                'severidade' => 'baixa',
                'modulo' => 'analise_bi',
                'parametros' => [
                    'tempo_medio_atual' => 45,
                    'tempo_medio_historico' => 30,
                    'variacao_percentual' => 50
                ],
                'status' => 'ativa',
                'acionada_em' => date('Y-m-d H:i:s', strtotime('-30 minutes'))
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
                    'parametros' => ['teste' => true],
                    'status' => rand(0, 1) ? 'ativa' : 'resolvida',
                    'acionada_em' => $data
                ];
            }
        }

        // Inserir no banco
        $inseridas = 0;
        $erros = 0;

        foreach ($notificacoesTest as $notificacao) {
            try {
                if ($notificacaoModel->insert($notificacao)) {
                    $inseridas++;
                } else {
                    $erros++;
                    CLI::write('Erro ao inserir: ' . implode(', ', $notificacaoModel->errors()), 'red');
                }
            } catch (\Exception $e) {
                $erros++;
                CLI::write('Exceção: ' . $e->getMessage(), 'red');
            }
        }

        CLI::write("✅ Processo concluído!", 'green');
        CLI::write("📊 Notificações inseridas: {$inseridas}", 'green');
        if ($erros > 0) {
            CLI::write("❌ Erros: {$erros}", 'red');
        }

        // Mostrar estatísticas
        $this->mostrarEstatisticas($notificacaoModel);
    }

    private function mostrarEstatisticas($model)
    {
        CLI::write("\n📈 Estatísticas atuais:", 'yellow');
        
        $stats = $model->getEstatisticas();
        
        CLI::write("Total ativas: " . $stats['total_ativas'], 'white');
        CLI::write("Críticas: " . $stats['criticas'], 'red');
        CLI::write("Altas: " . $stats['altas'], 'yellow');
        CLI::write("Médias: " . $stats['medias'], 'blue');
        CLI::write("Baixas: " . $stats['baixas'], 'green');
        
        CLI::write("\nPor tipo:", 'yellow');
        foreach ($stats['por_tipo'] as $tipo => $total) {
            CLI::write("- {$tipo}: {$total}", 'white');
        }
        
        CLI::write("\nTendência 7 dias:", 'yellow');
        foreach ($stats['tendencia_7_dias'] as $dia) {
            CLI::write("- {$dia['data']}: {$dia['total']}", 'white');
        }
    }
}
