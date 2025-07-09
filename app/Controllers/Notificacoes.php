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
            // Decodificar e validar parâmetros
            $parametros = json_decode($notificacao['parametros'], true);
            $notificacao['parametros'] = is_array($parametros) ? $parametros : [];
            
            // Decodificar e validar metadata
            $metadata = json_decode($notificacao['metadata'], true);
            $notificacao['metadata'] = is_array($metadata) ? $metadata : [];
            
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

        // Decodifica dados JSON com validação
        $parametros = json_decode($notificacao['parametros'], true);
        $notificacao['parametros'] = is_array($parametros) ? $parametros : [];
        
        $metadata = json_decode($notificacao['metadata'], true);
        $notificacao['metadata'] = is_array($metadata) ? $metadata : [];
        
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
            $parametros = json_decode($notificacao['parametros'], true);
            $notificacao['parametros'] = is_array($parametros) ? $parametros : [];
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
        
        $dataInicio = date('Y-m-d 00:00:00', strtotime("-{$periodo} days"));
        $dataFim = date('Y-m-d 23:59:59');

        $notificacoes = $this->notificacaoModel
            ->where('created_at >=', $dataInicio)
            ->where('created_at <=', $dataFim)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Processa dados
        foreach ($notificacoes as &$notificacao) {
            $parametros = json_decode($notificacao['parametros'], true);
            $notificacao['parametros'] = is_array($parametros) ? $parametros : [];
            
            $metadata = json_decode($notificacao['metadata'], true);
            $notificacao['metadata'] = is_array($metadata) ? $metadata : [];
        }

        $estatisticasPeriodo = $this->calcularEstatisticasPeriodo($notificacoes);

        $data = [
            'title' => 'Relatório de Notificações BI',
            'description' => 'Relatório gerado para o período de ' . $dataInicio . ' a ' . $dataFim,
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
            'description' => 'Gerencie as configurações do sistema de notificações',
            'configuracoes' => $configuracoes
        ];

        return view('notificacoes/configuracoes', $data);
    }

    /**
     * Salva configurações do sistema
     */
    public function salvarConfiguracoes()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Acesso inválido']);
        }

        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        $dados = $this->request->getJSON(true);
        
        // Aqui você implementaria a lógica para salvar as configurações
        // Por exemplo, em um arquivo de configuração ou banco de dados
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Configurações salvas com sucesso!'
        ]);
    }

    /**
     * Restaura configurações padrão
     */
    public function restaurarPadrao()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Acesso inválido']);
        }

        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        // Implementar lógica para restaurar configurações padrão
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Configurações restauradas com sucesso!'
        ]);
    }

    /**
     * Testa envio de email
     */
    public function testarEmail()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Acesso inválido']);
        }

        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        try {
            // Implementar lógica de teste de email
            $email = \Config\Services::email();
            
            $email->setTo(auth()->user()->email ?? 'admin@municipio.gov.br');
            $email->setSubject('Teste - Sistema de Notificações BI');
            $email->setMessage('Este é um email de teste do sistema de notificações BI. Se você recebeu esta mensagem, a configuração de email está funcionando corretamente.');
            
            if ($email->send()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Email de teste enviado com sucesso!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao enviar email: ' . $email->printDebugger()
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao enviar email: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Limpa dados antigos de notificações
     */
    public function limparDadosAntigos()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Acesso inválido']);
        }

        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        try {
            $registrosRemovidos = $this->notificacaoModel->limparNotificacoesAntigas();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Dados antigos removidos com sucesso!',
                'registros_removidos' => $registrosRemovidos
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao limpar dados: ' . $e->getMessage()
            ]);
        }
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
                    [
                        'titulo' => 'Agendar consulta especializada',
                        'descricao' => 'Encaminhar o paciente para uma consulta com especialista adequado.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Revisar histórico médico completo',
                        'descricao' => 'Analisar todo o histórico de atendimentos e exames do paciente.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Avaliar necessidade de exames complementares',
                        'descricao' => 'Solicitar exames adicionais para investigação detalhada.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Considerar encaminhamento para especialista',
                        'descricao' => 'Avaliar a necessidade de acompanhamento especializado.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Implementar plano de cuidado contínuo',
                        'descricao' => 'Estabelecer protocolo de acompanhamento regular.',
                        'link' => null,
                        'texto_link' => null
                    ]
                ];
                break;

            case 'surto_sintomas':
                $acoes = [
                    [
                        'titulo' => 'Notificar vigilância epidemiológica',
                        'descricao' => 'Comunicar imediatamente as autoridades de vigilância epidemiológica.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Investigar fonte comum de exposição',
                        'descricao' => 'Identificar possíveis fontes de contaminação ou exposição.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Implementar medidas de controle',
                        'descricao' => 'Estabelecer medidas de prevenção e controle na região.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Orientar população do bairro',
                        'descricao' => 'Informar e orientar os moradores sobre medidas preventivas.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Intensificar monitoramento da região',
                        'descricao' => 'Aumentar a vigilância e monitoramento na área afetada.',
                        'link' => null,
                        'texto_link' => null
                    ]
                ];
                break;

            case 'alta_demanda':
                $acoes = [
                    [
                        'titulo' => 'Alocar recursos adicionais',
                        'descricao' => 'Disponibilizar mais recursos humanos e materiais.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Ativar protocolo de sobrecarga',
                        'descricao' => 'Implementar procedimentos especiais para alta demanda.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Revisar escala de profissionais',
                        'descricao' => 'Ajustar a escala de trabalho para atender a demanda.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Implementar triagem rápida',
                        'descricao' => 'Acelerar o processo de triagem para otimizar atendimentos.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Comunicar gestão hospitalar',
                        'descricao' => 'Informar a gestão sobre a situação de sobrecarga.',
                        'link' => null,
                        'texto_link' => null
                    ]
                ];
                break;

            case 'estatistica_anomala':
                $acoes = [
                    [
                        'titulo' => 'Investigar causas da anomalia',
                        'descricao' => 'Analisar os fatores que geraram a anomalia estatística.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Revisar processos operacionais',
                        'descricao' => 'Avaliar e ajustar os processos de trabalho.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Auditar qualidade do atendimento',
                        'descricao' => 'Realizar auditoria para verificar a qualidade dos serviços.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Implementar ações corretivas',
                        'descricao' => 'Executar medidas para corrigir as anomalias identificadas.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Monitorar indicadores relacionados',
                        'descricao' => 'Acompanhar de perto os indicadores afetados.',
                        'link' => null,
                        'texto_link' => null
                    ]
                ];
                break;

            default:
                $acoes = [
                    [
                        'titulo' => 'Avaliar situação',
                        'descricao' => 'Analisar a notificação e determinar ações apropriadas.',
                        'link' => null,
                        'texto_link' => null
                    ],
                    [
                        'titulo' => 'Registrar observações',
                        'descricao' => 'Documentar observações relevantes sobre a notificação.',
                        'link' => null,
                        'texto_link' => null
                    ]
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
            'total_resolvidas' => 0,
            'total_ativas' => 0,
            'total_canceladas' => 0,
            'total_criticas' => 0,
            'por_severidade' => [
                'critica' => ['total' => 0, 'resolvidas' => 0],
                'alta' => ['total' => 0, 'resolvidas' => 0],
                'media' => ['total' => 0, 'resolvidas' => 0],
                'baixa' => ['total' => 0, 'resolvidas' => 0]
            ],
            'por_tipo' => [],
            'tempo_medio_resolucao' => 0
        ];

        $temposResolucao = [];

        foreach ($notificacoes as $notificacao) {
            // Status
            if ($notificacao['status'] === 'resolvida') {
                $stats['total_resolvidas']++;
            } elseif ($notificacao['status'] === 'ativa') {
                $stats['total_ativas']++;
            } elseif ($notificacao['status'] === 'cancelada') {
                $stats['total_canceladas']++;
            }

            // Críticas
            if ($notificacao['severidade'] === 'critica') {
                $stats['total_criticas']++;
            }

            // Severidade
            $severidade = $notificacao['severidade'];
            if (isset($stats['por_severidade'][$severidade])) {
                $stats['por_severidade'][$severidade]['total']++;
                if ($notificacao['status'] === 'resolvida') {
                    $stats['por_severidade'][$severidade]['resolvidas']++;
                }
            }

            // Tipo
            $tipo = $notificacao['tipo'];
            if (!isset($stats['por_tipo'][$tipo])) {
                $stats['por_tipo'][$tipo] = [
                    'total' => 0,
                    'tempo_medio' => 0
                ];
            }
            $stats['por_tipo'][$tipo]['total']++;

            // Tempo de resolução
            if ($notificacao['status'] === 'resolvida' && $notificacao['resolvida_em']) {
                $inicio = new \DateTime($notificacao['acionada_em']);
                $fim = new \DateTime($notificacao['resolvida_em']);
                $tempoHoras = ($fim->getTimestamp() - $inicio->getTimestamp()) / 3600;
                $temposResolucao[] = $tempoHoras;
                
                // Adicionar ao tempo médio por tipo
                if (!isset($stats['por_tipo'][$tipo]['tempos'])) {
                    $stats['por_tipo'][$tipo]['tempos'] = [];
                }
                $stats['por_tipo'][$tipo]['tempos'][] = $tempoHoras;
            }
        }

        // Calcular tempo médio geral
        if (!empty($temposResolucao)) {
            $stats['tempo_medio_resolucao'] = round(array_sum($temposResolucao) / count($temposResolucao), 1);
        }

        // Calcular tempo médio por tipo
        foreach ($stats['por_tipo'] as $tipo => &$dados) {
            if (isset($dados['tempos']) && !empty($dados['tempos'])) {
                $dados['tempo_medio'] = round(array_sum($dados['tempos']) / count($dados['tempos']), 1);
                unset($dados['tempos']); // Remove array temporário
            }
        }

        return $stats;
    }
}
