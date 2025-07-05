<?php

namespace App\Libraries;

use App\Models\AtendimentoModel;
use App\Models\PacienteModel;
use App\Models\NotificacaoModel;
use App\Models\BairroModel;

class NotificacaoAnalyzer
{
    protected $atendimentoModel;
    protected $pacienteModel;
    protected $notificacaoModel;
    protected $bairroModel;

    public function __construct()
    {
        $this->atendimentoModel = new AtendimentoModel();
        $this->pacienteModel = new PacienteModel();
        $this->notificacaoModel = new NotificacaoModel();
        $this->bairroModel = new BairroModel();
    }

    /**
     * Executa todas as análises de BI e gera notificações
     */
    public function executarAnaliseCompleta()
    {
        log_message('info', 'Iniciando análise BI de notificações');

        $notificacoesCriadas = 0;

        try {
            // Análise 1: Pacientes recorrentes
            $notificacoesCriadas += $this->analisarPacientesRecorrentes();

            // Análise 2: Surtos de sintomas por região
            $notificacoesCriadas += $this->analisarSurtosSintomas();

            // Análise 3: Alta demanda por período
            $notificacoesCriadas += $this->analisarAltaDemanda();

            // Análise 4: Anomalias estatísticas
            $notificacoesCriadas += $this->analisarAnomalias();

            // Análise 5: Padrões de classificação de risco
            $notificacoesCriadas += $this->analisarClassificacaoRisco();

            log_message('info', "Análise BI concluída. {$notificacoesCriadas} notificações criadas");

            return [
                'success' => true,
                'notificacoes_criadas' => $notificacoesCriadas
            ];

        } catch (\Exception $e) {
            log_message('error', 'Erro na análise BI: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Analisa pacientes com atendimentos recorrentes
     */
    protected function analisarPacientesRecorrentes()
    {
        $notificacoesCriadas = 0;
        $dataLimite = date('Y-m-d', strtotime('-30 days'));

        // Busca pacientes com mais de 3 atendimentos no último mês
        $query = "
            SELECT 
                p.id, p.nome, p.cpf, p.data_nascimento,
                COUNT(a.id) as total_atendimentos,
                GROUP_CONCAT(DISTINCT a.classificacao_risco) as classificacoes,
                GROUP_CONCAT(DISTINCT a.hipotese_diagnostico SEPARATOR '; ') as diagnosticos,
                MAX(a.created_at) as ultimo_atendimento
            FROM pacientes p
            INNER JOIN atendimentos a ON p.id = a.id_paciente
            WHERE a.created_at >= ? 
            AND a.deleted_at IS NULL
            AND p.deleted_at IS NULL
            GROUP BY p.id
            HAVING total_atendimentos >= 3
            ORDER BY total_atendimentos DESC
        ";

        $pacientesRecorrentes = $this->atendimentoModel->db->query($query, [$dataLimite])->getResultArray();

        foreach ($pacientesRecorrentes as $paciente) {
            // Analisa se os sintomas são similares
            $diagnosticos = array_filter(explode('; ', $paciente['diagnosticos']));
            $sintomaSimilar = $this->verificarSintomasSimilares($diagnosticos);

            if ($sintomaSimilar) {
                $parametros = [
                    'paciente_id' => $paciente['id'],
                    'paciente_nome' => $paciente['nome'],
                    'paciente_cpf' => $paciente['cpf'],
                    'total_atendimentos' => $paciente['total_atendimentos'],
                    'periodo_dias' => 30,
                    'classificacoes' => $paciente['classificacoes'],
                    'sintoma_recorrente' => $sintomaSimilar
                ];

                $severidade = $paciente['total_atendimentos'] >= 5 ? 'alta' : 'media';

                $notificacao = [
                    'tipo' => 'paciente_recorrente',
                    'titulo' => "Paciente com {$paciente['total_atendimentos']} atendimentos em 30 dias",
                    'descricao' => "O paciente {$paciente['nome']} (CPF: {$paciente['cpf']}) apresentou {$paciente['total_atendimentos']} atendimentos no último mês com sintomas recorrentes: {$sintomaSimilar}. Requer acompanhamento médico especializado.",
                    'severidade' => $severidade,
                    'modulo' => 'Atendimentos',
                    'parametros' => $parametros,
                    'data_vencimento' => date('Y-m-d H:i:s', strtotime('+7 days'))
                ];

                if ($this->notificacaoModel->criarNotificacaoUnica($notificacao)) {
                    $notificacoesCriadas++;
                }
            }
        }

        return $notificacoesCriadas;
    }

    /**
     * Analisa surtos de sintomas por região geográfica
     */
    protected function analisarSurtosSintomas()
    {
        $notificacoesCriadas = 0;
        $dataLimite = date('Y-m-d', strtotime('-7 days'));

        // Busca padrões de sintomas por bairro
        $query = "
            SELECT 
                b.id as bairro_id,
                b.nome as bairro_nome,
                a.hipotese_diagnostico,
                COUNT(*) as casos,
                DATE(a.created_at) as data_atendimento
            FROM atendimentos a
            INNER JOIN pacientes p ON a.id_paciente = p.id
            INNER JOIN logradouros l ON p.id_logradouro = l.id
            INNER JOIN bairros b ON l.id_bairro = b.id
            WHERE a.created_at >= ?
            AND a.hipotese_diagnostico IS NOT NULL
            AND a.hipotese_diagnostico != ''
            AND a.deleted_at IS NULL
            GROUP BY b.id, a.hipotese_diagnostico, DATE(a.created_at)
            HAVING casos >= 3
            ORDER BY casos DESC, data_atendimento DESC
        ";

        $surtos = $this->atendimentoModel->db->query($query, [$dataLimite])->getResultArray();

        // Agrupa por bairro e sintoma
        $surtosAgrupados = [];
        foreach ($surtos as $surto) {
            $chave = $surto['bairro_id'] . '_' . md5($surto['hipotese_diagnostico']);
            if (!isset($surtosAgrupados[$chave])) {
                $surtosAgrupados[$chave] = [
                    'bairro_id' => $surto['bairro_id'],
                    'bairro_nome' => $surto['bairro_nome'],
                    'sintoma' => $surto['hipotese_diagnostico'],
                    'total_casos' => 0,
                    'dias_afetados' => 0
                ];
            }
            $surtosAgrupados[$chave]['total_casos'] += $surto['casos'];
            $surtosAgrupados[$chave]['dias_afetados']++;
        }

        foreach ($surtosAgrupados as $surto) {
            if ($surto['total_casos'] >= 5) { // Limite para considerar surto
                $parametros = [
                    'bairro_id' => $surto['bairro_id'],
                    'bairro_nome' => $surto['bairro_nome'],
                    'sintoma' => $surto['sintoma'],
                    'total_casos' => $surto['total_casos'],
                    'dias_afetados' => $surto['dias_afetados'],
                    'periodo_dias' => 7
                ];

                $severidade = $surto['total_casos'] >= 10 ? 'critica' : 'alta';

                $notificacao = [
                    'tipo' => 'surto_sintomas',
                    'titulo' => "Possível surto no bairro {$surto['bairro_nome']}",
                    'descricao' => "Detectados {$surto['total_casos']} casos de '{$surto['sintoma']}' no bairro {$surto['bairro_nome']} nos últimos 7 dias. Investigação epidemiológica recomendada.",
                    'severidade' => $severidade,
                    'modulo' => 'Epidemiologia',
                    'parametros' => $parametros,
                    'data_vencimento' => date('Y-m-d H:i:s', strtotime('+24 hours'))
                ];

                if ($this->notificacaoModel->criarNotificacaoUnica($notificacao)) {
                    $notificacoesCriadas++;
                }
            }
        }

        return $notificacoesCriadas;
    }

    /**
     * Analisa períodos de alta demanda
     */
    protected function analisarAltaDemanda()
    {
        $notificacoesCriadas = 0;

        // Analisa demanda por hora do dia nos últimos 3 dias
        $dataLimite = date('Y-m-d', strtotime('-3 days'));
        
        $query = "
            SELECT 
                HOUR(created_at) as hora,
                DATE(created_at) as data,
                COUNT(*) as atendimentos,
                SUM(CASE WHEN classificacao_risco = 'Vermelho' THEN 1 ELSE 0 END) as casos_criticos
            FROM atendimentos
            WHERE created_at >= ?
            AND deleted_at IS NULL
            GROUP BY HOUR(created_at), DATE(created_at)
            ORDER BY atendimentos DESC
        ";

        $demandaPorHora = $this->atendimentoModel->db->query($query, [$dataLimite])->getResultArray();

        // Calcula média e detecta picos
        $mediaPorHora = [];
        foreach ($demandaPorHora as $registro) {
            $hora = $registro['hora'];
            if (!isset($mediaPorHora[$hora])) {
                $mediaPorHora[$hora] = [];
            }
            $mediaPorHora[$hora][] = $registro['atendimentos'];
        }

        foreach ($demandaPorHora as $registro) {
            $hora = $registro['hora'];
            $media = array_sum($mediaPorHora[$hora]) / count($mediaPorHora[$hora]);
            
            // Se atendimentos > 200% da média, considera alta demanda
            if ($registro['atendimentos'] > ($media * 2) && $registro['atendimentos'] >= 8) {
                $parametros = [
                    'data' => $registro['data'],
                    'hora' => $hora,
                    'atendimentos' => $registro['atendimentos'],
                    'media_hora' => round($media, 1),
                    'casos_criticos' => $registro['casos_criticos'],
                    'percentual_aumento' => round((($registro['atendimentos'] / $media) - 1) * 100, 1)
                ];

                $severidade = $registro['casos_criticos'] >= 3 ? 'critica' : 'alta';

                $notificacao = [
                    'tipo' => 'alta_demanda',
                    'titulo' => "Alta demanda detectada em {$registro['data']} às {$hora}h",
                    'descricao' => "Registrados {$registro['atendimentos']} atendimentos às {$hora}h do dia {$registro['data']}, {$parametros['percentual_aumento']}% acima da média. {$registro['casos_criticos']} casos críticos identificados.",
                    'severidade' => $severidade,
                    'modulo' => 'Gestão',
                    'parametros' => $parametros,
                    'data_vencimento' => date('Y-m-d H:i:s', strtotime('+4 hours'))
                ];

                if ($this->notificacaoModel->criarNotificacaoUnica($notificacao)) {
                    $notificacoesCriadas++;
                }
            }
        }

        return $notificacoesCriadas;
    }

    /**
     * Analisa anomalias estatísticas gerais
     */
    protected function analisarAnomalias()
    {
        $notificacoesCriadas = 0;

        // Análise 1: Taxa de óbitos anormal
        $taxaObitos = $this->analisarTaxaObitos();
        if ($taxaObitos['anomalia']) {
            $notificacao = [
                'tipo' => 'estatistica_anomala',
                'titulo' => 'Taxa de óbitos acima do normal',
                'descricao' => "Taxa de óbitos de {$taxaObitos['taxa']}% detectada nos últimos 7 dias ({$taxaObitos['obitos']} óbitos em {$taxaObitos['total']} atendimentos). Média histórica: {$taxaObitos['media_historica']}%.",
                'severidade' => 'critica',
                'modulo' => 'Qualidade',
                'parametros' => $taxaObitos,
                'data_vencimento' => date('Y-m-d H:i:s', strtotime('+12 hours'))
            ];

            if ($this->notificacaoModel->criarNotificacaoUnica($notificacao)) {
                $notificacoesCriadas++;
            }
        }

        // Análise 2: Tempo médio de atendimento
        $tempoAtendimento = $this->analisarTempoAtendimento();
        if ($tempoAtendimento['anomalia']) {
            $notificacao = [
                'tipo' => 'estatistica_anomala',
                'titulo' => 'Tempo de atendimento elevado',
                'descricao' => "Tempo médio de atendimento de {$tempoAtendimento['tempo_medio']} minutos nos últimos 3 dias. Pode indicar sobrecarga ou problemas operacionais.",
                'severidade' => 'media',
                'modulo' => 'Operações',
                'parametros' => $tempoAtendimento,
                'data_vencimento' => date('Y-m-d H:i:s', strtotime('+6 hours'))
            ];

            if ($this->notificacaoModel->criarNotificacaoUnica($notificacao)) {
                $notificacoesCriadas++;
            }
        }

        return $notificacoesCriadas;
    }

    /**
     * Analisa padrões anômalos de classificação de risco
     */
    protected function analisarClassificacaoRisco()
    {
        $notificacoesCriadas = 0;
        $dataLimite = date('Y-m-d', strtotime('-7 days'));

        $query = "
            SELECT 
                classificacao_risco,
                COUNT(*) as total,
                ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM atendimentos WHERE created_at >= ? AND deleted_at IS NULL), 2) as percentual
            FROM atendimentos
            WHERE created_at >= ?
            AND deleted_at IS NULL
            GROUP BY classificacao_risco
        ";

        $distribuicao = $this->atendimentoModel->db->query($query, [$dataLimite, $dataLimite])->getResultArray();

        // Verifica se há concentração anormal de casos vermelhos
        foreach ($distribuicao as $item) {
            if ($item['classificacao_risco'] === 'Vermelho' && $item['percentual'] > 15) {
                $parametros = [
                    'classificacao' => 'Vermelho',
                    'total_casos' => $item['total'],
                    'percentual' => $item['percentual'],
                    'periodo_dias' => 7,
                    'limite_normal' => 15
                ];

                $notificacao = [
                    'tipo' => 'estatistica_anomala',
                    'titulo' => 'Alta concentração de casos críticos',
                    'descricao' => "Detectados {$item['total']} casos de classificação vermelha ({$item['percentual']}%) nos últimos 7 dias. Percentual acima do esperado (15%) pode indicar surto ou problemas na triagem.",
                    'severidade' => 'alta',
                    'modulo' => 'Triagem',
                    'parametros' => $parametros,
                    'data_vencimento' => date('Y-m-d H:i:s', strtotime('+24 hours'))
                ];

                if ($this->notificacaoModel->criarNotificacaoUnica($notificacao)) {
                    $notificacoesCriadas++;
                }
            }
        }

        return $notificacoesCriadas;
    }

    /**
     * Verifica se há sintomas similares em uma lista de diagnósticos
     */
    protected function verificarSintomasSimilares($diagnosticos)
    {
        if (count($diagnosticos) < 2) return false;

        // Palavras-chave para agrupar sintomas similares
        $gruposSintomas = [
            'respiratorio' => ['tosse', 'dispneia', 'falta de ar', 'pneumonia', 'bronquite', 'asma'],
            'gastrointestinal' => ['diarreia', 'vomito', 'náusea', 'dor abdominal', 'gastrite'],
            'neurologico' => ['cefaleia', 'dor de cabeça', 'tontura', 'convulsão'],
            'cardiovascular' => ['dor no peito', 'palpitação', 'hipertensão', 'infarto'],
            'dermatologico' => ['rash', 'alergia', 'urticária', 'eczema'],
            'musculoesqueletico' => ['dor nas costas', 'artralgia', 'mialgia', 'fratura']
        ];

        foreach ($gruposSintomas as $grupo => $palavrasChave) {
            $diagnosticosDoGrupo = array_filter($diagnosticos, function($diagnostico) use ($palavrasChave) {
                foreach ($palavrasChave as $palavra) {
                    if (stripos($diagnostico, $palavra) !== false) {
                        return true;
                    }
                }
                return false;
            });

            if (count($diagnosticosDoGrupo) >= 2) {
                return $grupo;
            }
        }

        return false;
    }

    /**
     * Analisa taxa de óbitos
     */
    protected function analisarTaxaObitos()
    {
        $dataLimite = date('Y-m-d', strtotime('-7 days'));
        
        // Taxa atual (últimos 7 dias)
        $totalAtendimentos = $this->atendimentoModel->where('created_at >=', $dataLimite)->countAllResults(false);
        $totalObitos = $this->atendimentoModel->where('created_at >=', $dataLimite)->where('obito', 1)->countAllResults();
        
        $taxaAtual = $totalAtendimentos > 0 ? round(($totalObitos / $totalAtendimentos) * 100, 2) : 0;

        // Taxa histórica (últimos 3 meses, excluindo últimos 7 dias)
        $dataHistoricaInicio = date('Y-m-d', strtotime('-90 days'));
        $dataHistoricaFim = date('Y-m-d', strtotime('-8 days'));
        
        $totalHistorico = $this->atendimentoModel
            ->where('created_at >=', $dataHistoricaInicio)
            ->where('created_at <=', $dataHistoricaFim)
            ->countAllResults(false);
        $obitosHistorico = $this->atendimentoModel
            ->where('created_at >=', $dataHistoricaInicio)
            ->where('created_at <=', $dataHistoricaFim)
            ->where('obito', 1)
            ->countAllResults();
        
        $mediaHistorica = $totalHistorico > 0 ? round(($obitosHistorico / $totalHistorico) * 100, 2) : 0;

        return [
            'taxa' => $taxaAtual,
            'obitos' => $totalObitos,
            'total' => $totalAtendimentos,
            'media_historica' => $mediaHistorica,
            'anomalia' => $taxaAtual > ($mediaHistorica * 2) && $taxaAtual > 2, // 2x a média E > 2%
            'periodo_analise' => 7,
            'periodo_historico' => 90
        ];
    }

    /**
     * Analisa tempo médio de atendimento
     */
    protected function analisarTempoAtendimento()
    {
        $dataLimite = date('Y-m-d', strtotime('-3 days'));
        
        // Simula cálculo de tempo médio (seria necessário campo de data_fim_atendimento)
        // Por enquanto, usa diferença entre created_at e updated_at como proxy
        $query = "
            SELECT 
                AVG(TIMESTAMPDIFF(MINUTE, created_at, updated_at)) as tempo_medio_minutos,
                COUNT(*) as total_atendimentos
            FROM atendimentos
            WHERE created_at >= ?
            AND updated_at IS NOT NULL
            AND deleted_at IS NULL
        ";

        $resultado = $this->atendimentoModel->db->query($query, [$dataLimite])->getRowArray();
        
        $tempoMedio = $resultado['tempo_medio_minutos'] ?? 0;
        
        return [
            'tempo_medio' => round($tempoMedio, 1),
            'total_atendimentos' => $resultado['total_atendimentos'] ?? 0,
            'anomalia' => $tempoMedio > 120, // > 2 horas
            'periodo_analise' => 3
        ];
    }
}
