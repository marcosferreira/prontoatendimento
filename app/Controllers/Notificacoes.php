<?php

namespace App\Controllers;

use App\Models\NotificacaoModel;
use App\Libraries\NotificacaoAnalyzer;
use CodeIgniter\HTTP\ResponseInterface;

class Notificacoes extends BaseController
{
    protected $notificacaoModel;
    protected $analyzer;

    public function __construct()
    {
        $this->notificacaoModel = new NotificacaoModel();
        $this->analyzer = new NotificacaoAnalyzer();
    }

    /**
     * Dashboard principal das notificações
     */
    public function index()
    {
        // Busca notificações ativas
        $notificacoesAtivas = $this->notificacaoModel->getNotificacoesAtivas();
        
        // Processa dados para exibição
        foreach ($notificacoesAtivas as &$notificacao) {
            $notificacao['parametros'] = json_decode($notificacao['parametros'], true);
            $notificacao['metadata'] = json_decode($notificacao['metadata'], true);
            $notificacao['tempo_ativa'] = $this->calcularTempoAtiva($notificacao['acionada_em']);
            $notificacao['urgencia'] = $this->calcularUrgencia($notificacao);
        }

        // Estatísticas
        $estatisticas = $this->notificacaoModel->getEstatisticas();

        // Dados para gráficos
        $dadosGraficos = $this->prepararDadosGraficos($estatisticas);

        $data = [
            'title' => 'Central de Notificações BI',
            'description' => 'Monitoramento Inteligente e Alertas do Sistema',
            'notificacoes' => $notificacoesAtivas,
            'estatisticas' => $estatisticas,
            'graficos' => $dadosGraficos
        ];

        return view('notificacoes/index', $data);
    }

    /**
     * Exibe detalhes de uma notificação específica
     */
    public function show($id = null)
    {
        $notificacao = $this->notificacaoModel->find($id);

        if (!$notificacao) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Notificação não encontrada');
        }

        // Decodifica dados JSON
        $notificacao['parametros'] = json_decode($notificacao['parametros'], true);
        $notificacao['metadata'] = json_decode($notificacao['metadata'], true);
        
        // Informações complementares baseadas no tipo
        $dadosComplementares = $this->buscarDadosComplementares($notificacao);
        
        // Ações sugeridas
        $acoesSugeridas = $this->gerarAcoesSugeridas($notificacao);

        $data = [
            'title' => 'Detalhes da Notificação',
            'description' => $notificacao['titulo'],
            'notificacao' => $notificacao,
            'dados_complementares' => $dadosComplementares,
            'acoes_sugeridas' => $acoesSugeridas
        ];

        return view('notificacoes/show', $data);
    }

    /**
     * API para buscar notificações via AJAX
     */
    public function api()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Acesso inválido']);
        }

        $filtros = [
            'severidade' => $this->request->getGet('severidade'),
            'tipo' => $this->request->getGet('tipo'),
            'status' => $this->request->getGet('status') ?: 'ativa'
        ];

        $notificacoes = $this->notificacaoModel->where('status', $filtros['status']);

        if ($filtros['severidade']) {
            $notificacoes->where('severidade', $filtros['severidade']);
        }

        if ($filtros['tipo']) {
            $notificacoes->where('tipo', $filtros['tipo']);
        }

        $resultado = $notificacoes->orderBy('severidade', 'DESC')
                                ->orderBy('created_at', 'DESC')
                                ->findAll();

        // Processa dados
        foreach ($resultado as &$notificacao) {
            $notificacao['parametros'] = json_decode($notificacao['parametros'], true);
            $notificacao['tempo_ativa'] = $this->calcularTempoAtiva($notificacao['acionada_em']);
            $notificacao['urgencia'] = $this->calcularUrgencia($notificacao);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $resultado,
            'total' => count($resultado)
        ]);
    }

    /**
     * Marca notificação como resolvida
     */
    public function resolver($id = null)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Acesso inválido');
        }

        $notificacao = $this->notificacaoModel->find($id);
        
        if (!$notificacao) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Notificação não encontrada'
            ]);
        }

        $observacao = $this->request->getPost('observacao');
        $usuarioId = auth()->user()->id ?? null;

        if ($this->notificacaoModel->marcarResolvida($id, $usuarioId)) {
            // Registra observação se fornecida
            if ($observacao) {
                $metadata = json_decode($notificacao['metadata'], true) ?: [];
                $metadata['observacao_resolucao'] = $observacao;
                $metadata['resolvida_por'] = auth()->user()->username ?? 'Sistema';
                
                $this->notificacaoModel->update($id, [
                    'metadata' => json_encode($metadata)
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notificação marcada como resolvida'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao resolver notificação'
        ]);
    }

    /**
     * Cancela uma notificação
     */
    public function cancelar($id = null)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Acesso inválido');
        }

        $motivo = $this->request->getPost('motivo');
        
        if ($this->notificacaoModel->cancelarNotificacao($id, $motivo)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Notificação cancelada'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao cancelar notificação'
        ]);
    }

    /**
     * Executa análise BI manualmente
     */
    public function executarAnalise()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Acesso inválido');
        }

        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        $resultado = $this->analyzer->executarAnaliseCompleta();

        return $this->response->setJSON($resultado);
    }

    /**
     * API para estatísticas em tempo real
     */
    public function estatisticas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Acesso inválido']);
        }

        $stats = $this->notificacaoModel->getEstatisticas();
        $dadosGraficos = $this->prepararDadosGraficos($stats);

        return $this->response->setJSON([
            'success' => true,
            'estatisticas' => $stats,
            'graficos' => $dadosGraficos
        ]);
    }

    /**
     * Exporta relatório de notificações
     */
    public function relatorio()
    {
        $periodo = $this->request->getGet('periodo') ?: 30;
        $formato = $this->request->getGet('formato') ?: 'html';
        
        $dataInicio = date('Y-m-d', strtotime("-{$periodo} days"));
        $dataFim = date('Y-m-d');

        $notificacoes = $this->notificacaoModel
            ->where('created_at >=', $dataInicio)
            ->where('created_at <=', $dataFim)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Processa dados
        foreach ($notificacoes as &$notificacao) {
            $notificacao['parametros'] = json_decode($notificacao['parametros'], true);
            $notificacao['metadata'] = json_decode($notificacao['metadata'], true);
        }

        $estatisticasPeriodo = $this->calcularEstatisticasPeriodo($notificacoes);

        $data = [
            'title' => 'Relatório de Notificações BI',
            'periodo' => $periodo,
            'data_inicio' => $dataInicio,
            'data_fim' => $dataFim,
            'notificacoes' => $notificacoes,
            'estatisticas' => $estatisticasPeriodo
        ];

        if ($formato === 'pdf') {
            // Implementar geração de PDF
            return view('notificacoes/relatorio_pdf', $data);
        } else {
            return view('notificacoes/relatorio', $data);
        }
    }

    /**
     * Configurações do sistema de notificações
     */
    public function configuracoes()
    {
        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return redirect()->back()->with('error', 'Acesso negado');
        }

        $configuracoes = [
            'analise_automatica' => true,
            'intervalo_analise' => 60, // minutos
            'retencao_dados' => 90, // dias
            'notificar_email' => true,
            'severidade_email' => 'alta'
        ];

        $data = [
            'title' => 'Configurações de Notificações BI',
            'configuracoes' => $configuracoes
        ];

        return view('notificacoes/configuracoes', $data);
    }

    // === MÉTODOS AUXILIARES ===

    /**
     * Calcula tempo que a notificação está ativa
     */
    protected function calcularTempoAtiva($dataAcionada)
    {
        $inicio = new \DateTime($dataAcionada);
        $agora = new \DateTime();
        $diff = $agora->diff($inicio);

        if ($diff->days > 0) {
            return $diff->days . ' dia(s)';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hora(s)';
        } else {
            return $diff->i . ' minuto(s)';
        }
    }

    /**
     * Calcula urgência da notificação baseada em severidade e tempo
     */
    protected function calcularUrgencia($notificacao)
    {
        $severidadeValues = [
            'baixa' => 1,
            'media' => 2,
            'alta' => 3,
            'critica' => 4
        ];

        $tempoAtiva = (new \DateTime())->getTimestamp() - (new \DateTime($notificacao['acionada_em']))->getTimestamp();
        $horasAtiva = $tempoAtiva / 3600;

        $baseSeveridade = $severidadeValues[$notificacao['severidade']] ?? 1;
        $fatorTempo = min($horasAtiva / 24, 2); // Max 2x por tempo

        $urgencia = $baseSeveridade + $fatorTempo;

        if ($urgencia >= 5) return 'maxima';
        if ($urgencia >= 4) return 'alta';
        if ($urgencia >= 2.5) return 'media';
        return 'baixa';
    }

    /**
     * Prepara dados para gráficos
     */
    protected function prepararDadosGraficos($estatisticas)
    {
        // Garantir que as estatísticas existam
        $stats = [
            'criticas' => $estatisticas['criticas'] ?? 0,
            'altas' => $estatisticas['altas'] ?? 0,
            'medias' => $estatisticas['medias'] ?? 0,
            'baixas' => $estatisticas['baixas'] ?? 0,
            'por_tipo' => $estatisticas['por_tipo'] ?? [],
            'tendencia_7_dias' => $estatisticas['tendencia_7_dias'] ?? []
        ];

        // Preparar dados para gráfico de severidade
        $dadosSeveridade = [
            'labels' => ['Crítica', 'Alta', 'Média', 'Baixa'],
            'data' => [
                $stats['criticas'],
                $stats['altas'],
                $stats['medias'],
                $stats['baixas']
            ],
            'colors' => ['#dc3545', '#fd7e14', '#ffc107', '#198754']
        ];

        // Preparar dados para gráfico de tipos
        $tiposLabels = array_keys($stats['por_tipo']);
        $tiposData = array_values($stats['por_tipo']);
        
        // Se não há dados de tipos, criar dados padrão
        if (empty($tiposLabels)) {
            $tiposLabels = ['Nenhum tipo registrado'];
            $tiposData = [0];
        }

        $dadosTipos = [
            'labels' => $tiposLabels,
            'data' => $tiposData
        ];

        // Preparar dados para gráfico de tendência
        $tendenciaLabels = [];
        $tendenciaData = [];
        
        if (!empty($stats['tendencia_7_dias'])) {
            foreach ($stats['tendencia_7_dias'] as $item) {
                $tendenciaLabels[] = $item['data'] ?? date('Y-m-d');
                $tendenciaData[] = $item['total'] ?? 0;
            }
        } else {
            // Criar dados dos últimos 7 dias mesmo sem registros
            for ($i = 6; $i >= 0; $i--) {
                $tendenciaLabels[] = date('Y-m-d', strtotime("-{$i} days"));
                $tendenciaData[] = 0;
            }
        }

        $dadosTendencia = [
            'labels' => $tendenciaLabels,
            'data' => $tendenciaData
        ];

        return [
            'severidade' => $dadosSeveridade,
            'tipos' => $dadosTipos,
            'tendencia' => $dadosTendencia
        ];
    }

    /**
     * Busca dados complementares baseados no tipo de notificação
     */
    protected function buscarDadosComplementares($notificacao)
    {
        $dados = [];

        switch ($notificacao['tipo']) {
            case 'paciente_recorrente':
                if (isset($notificacao['parametros']['paciente_id'])) {
                    $pacienteModel = new \App\Models\PacienteModel();
                    $dados['paciente'] = $pacienteModel->find($notificacao['parametros']['paciente_id']);
                    
                    $atendimentoModel = new \App\Models\AtendimentoModel();
                    $dados['atendimentos_recentes'] = $atendimentoModel
                        ->where('id_paciente', $notificacao['parametros']['paciente_id'])
                        ->where('created_at >=', date('Y-m-d', strtotime('-30 days')))
                        ->orderBy('created_at', 'DESC')
                        ->findAll();
                }
                break;

            case 'surto_sintomas':
                if (isset($notificacao['parametros']['bairro_id'])) {
                    $bairroModel = new \App\Models\BairroModel();
                    $dados['bairro'] = $bairroModel->find($notificacao['parametros']['bairro_id']);
                    
                    // Busca outros casos similares
                    $dados['casos_similares'] = $this->buscarCasosSimilares(
                        $notificacao['parametros']['bairro_id'],
                        $notificacao['parametros']['sintoma']
                    );
                }
                break;
        }

        return $dados;
    }

    /**
     * Gera ações sugeridas baseadas no tipo de notificação
     */
    protected function gerarAcoesSugeridas($notificacao)
    {
        $acoes = [];

        switch ($notificacao['tipo']) {
            case 'paciente_recorrente':
                $acoes = [
                    'Agendar consulta especializada',
                    'Revisar histórico médico completo',
                    'Avaliar necessidade de exames complementares',
                    'Considerar encaminhamento para especialista',
                    'Implementar plano de cuidado contínuo'
                ];
                break;

            case 'surto_sintomas':
                $acoes = [
                    'Notificar vigilância epidemiológica',
                    'Investigar fonte comum de exposição',
                    'Implementar medidas de controle',
                    'Orientar população do bairro',
                    'Intensificar monitoramento da região'
                ];
                break;

            case 'alta_demanda':
                $acoes = [
                    'Alocar recursos adicionais',
                    'Ativar protocolo de sobrecarga',
                    'Revisar escala de profissionais',
                    'Implementar triagem rápida',
                    'Comunicar gestão hospitalar'
                ];
                break;

            case 'estatistica_anomala':
                $acoes = [
                    'Investigar causas da anomalia',
                    'Revisar processos operacionais',
                    'Auditar qualidade do atendimento',
                    'Implementar ações corretivas',
                    'Monitorar indicadores relacionados'
                ];
                break;
        }

        return $acoes;
    }

    /**
     * Busca casos similares no mesmo bairro
     */
    protected function buscarCasosSimilares($bairroId, $sintoma)
    {
        $atendimentoModel = new \App\Models\AtendimentoModel();
        
        return $atendimentoModel
            ->select('atendimentos.*, pacientes.nome as paciente_nome')
            ->join('pacientes', 'pacientes.id = atendimentos.id_paciente')
            ->join('logradouros', 'logradouros.id = pacientes.id_logradouro')
            ->where('logradouros.id_bairro', $bairroId)
            ->like('atendimentos.hipotese_diagnostico', $sintoma)
            ->where('atendimentos.created_at >=', date('Y-m-d', strtotime('-7 days')))
            ->orderBy('atendimentos.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Calcula estatísticas para um período específico
     */
    protected function calcularEstatisticasPeriodo($notificacoes)
    {
        $stats = [
            'total' => count($notificacoes),
            'resolvidas' => 0,
            'ativas' => 0,
            'canceladas' => 0,
            'por_severidade' => [],
            'por_tipo' => [],
            'tempo_medio_resolucao' => 0
        ];

        $temposResolucao = [];

        foreach ($notificacoes as $notificacao) {
            // Status
            $stats[$notificacao['status']]++;

            // Severidade
            if (!isset($stats['por_severidade'][$notificacao['severidade']])) {
                $stats['por_severidade'][$notificacao['severidade']] = 0;
            }
            $stats['por_severidade'][$notificacao['severidade']]++;

            // Tipo
            if (!isset($stats['por_tipo'][$notificacao['tipo']])) {
                $stats['por_tipo'][$notificacao['tipo']] = 0;
            }
            $stats['por_tipo'][$notificacao['tipo']]++;

            // Tempo de resolução
            if ($notificacao['status'] === 'resolvida' && $notificacao['resolvida_em']) {
                $inicio = new \DateTime($notificacao['acionada_em']);
                $fim = new \DateTime($notificacao['resolvida_em']);
                $temposResolucao[] = $fim->getTimestamp() - $inicio->getTimestamp();
            }
        }

        // Tempo médio de resolução em horas
        if (!empty($temposResolucao)) {
            $stats['tempo_medio_resolucao'] = round(array_sum($temposResolucao) / count($temposResolucao) / 3600, 1);
        }

        return $stats;
    }
}
