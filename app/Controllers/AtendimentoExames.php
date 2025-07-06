<?php

namespace App\Controllers;

use App\Models\AtendimentoExameModel;
use App\Models\AtendimentoModel;
use App\Models\ExameModel;
use CodeIgniter\Controller;

class AtendimentoExames extends BaseController
{
    protected $atendimentoExameModel;
    protected $atendimentoModel;
    protected $exameModel;

    public function __construct()
    {
        $this->atendimentoExameModel = new AtendimentoExameModel();
        $this->atendimentoModel = new AtendimentoModel();
        $this->exameModel = new ExameModel();
    }

    /**
     * Lista todos os exames solicitados/realizados em atendimentos
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $atendimento = $this->request->getGet('atendimento');
        $exame = $this->request->getGet('exame');
        $status = $this->request->getGet('status');
        
        // Usar Query Builder direto para evitar problemas de casting com campos NULL
        $db = \Config\Database::connect();
        $builder = $db->table('atendimento_exames ae')
            ->select('ae.*, 
                     e.nome as nome_exame, 
                     e.codigo as codigo_exame,
                     e.tipo as tipo_exame,
                     a.data_atendimento,
                     p.nome as nome_paciente,
                     p.cpf,
                     ae.data_solicitacao as data_solicitacao_raw,
                     ae.data_realizacao as data_realizacao_raw')
            ->join('exames e', 'e.id_exame = ae.id_exame')
            ->join('atendimentos a', 'a.id_atendimento = ae.id_atendimento')
            ->join('pacientes p', 'p.id_paciente = a.id_paciente')
            ->where('ae.deleted_at IS NULL')
            ->orderBy('ae.data_solicitacao', 'DESC');

        if ($search) {
            $builder->groupStart()
                   ->like('p.nome', $search)
                   ->orLike('e.nome', $search)
                   ->orLike('e.codigo', $search)
                   ->groupEnd();
        }

        if ($atendimento) {
            $builder->where('ae.id_atendimento', $atendimento);
        }

        if ($exame) {
            $builder->where('ae.id_exame', $exame);
        }

        if ($status) {
            $builder->where('ae.status', $status);
        }

        // Configurar paginação manual
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Contar total de registros
        $totalRecords = $builder->countAllResults(false);

        // Buscar registros paginados
        $atendimentoExames = $builder->limit($perPage, $offset)->get()->getResultArray();

        // Configurar paginação
        $pager = \Config\Services::pager();
        $pager->store('default', $page, $perPage, $totalRecords);

        // Estatísticas usando consultas diretas para evitar problemas de casting
        $stats = [
            'total' => $db->table('atendimento_exames')->where('deleted_at IS NULL')->countAllResults(),
            'solicitados' => $db->table('atendimento_exames')->where('status', 'Solicitado')->where('deleted_at IS NULL')->countAllResults(),
            'realizados' => $db->table('atendimento_exames')->where('status', 'Realizado')->where('deleted_at IS NULL')->countAllResults(),
            'cancelados' => $db->table('atendimento_exames')->where('status', 'Cancelado')->where('deleted_at IS NULL')->countAllResults(),
            'hoje' => $db->table('atendimento_exames')->where('DATE(data_solicitacao)', date('Y-m-d'))->where('deleted_at IS NULL')->countAllResults()
        ];

        $exames = $this->exameModel->orderBy('nome', 'ASC')->findAll();

        $data = [
            'title' => 'Exames em Atendimentos',
            'description' => 'Lista de exames solicitados e realizados em atendimentos',
            'atendimentoExames' => $atendimentoExames,
            'pager' => $pager,
            'stats' => $stats,
            'exames' => $exames,
            'search' => $search,
            'atendimento' => $atendimento,
            'exame' => $exame,
            'status' => $status,
            'statusOptions' => ['Solicitado', 'Realizado', 'Cancelado']
        ];

        return view('atendimento_exames/index', $data);
    }

    /**
     * Exibe o formulário para solicitar exame em um atendimento
     */
    public function create()
    {
        $idAtendimento = $this->request->getGet('atendimento');
        
        if (!$idAtendimento) {
            return redirect()->to('/atendimentos')->with('error', 'Atendimento não especificado');
        }

        $atendimento = $this->atendimentoModel->select('atendimentos.*, pacientes.nome as paciente_nome, pacientes.cpf, medicos.nome as medico_nome, medicos.crm')
                                            ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
                                            ->join('medicos', 'medicos.id_medico = atendimentos.id_medico')
                                            ->find($idAtendimento);
        if (!$atendimento) {
            return redirect()->to('/atendimentos')->with('error', 'Atendimento não encontrado');
        }

        $exames = $this->exameModel->orderBy('tipo', 'ASC')->orderBy('nome', 'ASC')->findAll();
        
        // Exames já solicitados para este atendimento
        $examesJaSolicitados = $this->atendimentoExameModel
            ->select('id_exame')
            ->where('id_atendimento', $idAtendimento)
            ->findColumn('id_exame');

        $data = [
            'title' => 'Solicitar Exame para Atendimento',
            'description' => 'Selecione um exame para solicitar no atendimento',
            'atendimento' => $atendimento,
            'exames' => $exames,
            'examesJaSolicitados' => $examesJaSolicitados ?: []
        ];

        return view('atendimento_exames/create', $data);
    }

    /**
     * Salva uma nova solicitação de exame
     */
    public function store()
    {
        $rules = [
            'id_atendimento' => 'required|integer|is_not_unique[atendimentos.id_atendimento]',
            'id_exame' => 'required|integer|is_not_unique[exames.id_exame]',
            'status' => 'required|in_list[Solicitado,Realizado,Cancelado]',
            'data_realizacao' => 'permit_empty|regex_match[/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/]',
            'resultado' => 'permit_empty',
            'observacao' => 'permit_empty'
        ];

        $messages = [
            'id_atendimento' => [
                'required' => 'O atendimento é obrigatório',
                'integer' => 'ID do atendimento deve ser um número',
                'is_not_unique' => 'Atendimento não encontrado'
            ],
            'id_exame' => [
                'required' => 'O exame é obrigatório',
                'integer' => 'ID do exame deve ser um número',
                'is_not_unique' => 'Exame não encontrado'
            ],
            'status' => [
                'required' => 'O status é obrigatório',
                'in_list' => 'Status deve ser: Solicitado, Realizado ou Cancelado'
            ],
            'data_realizacao' => [
                'regex_match' => 'Data de realização deve estar no formato AAAA-MM-DDTHH:MM'
            ]
        ];

        // Validações condicionais para status "Realizado"
        $status = $this->request->getPost('status');
        if ($status === 'Realizado') {
            $rules['data_realizacao'] = 'required|regex_match[/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/]';
            $rules['resultado'] = 'required';
            
            $messages['data_realizacao']['required'] = 'Data de realização é obrigatória quando status for "Realizado"';
            $messages['resultado'] = [
                'required' => 'Resultado é obrigatório quando status for "Realizado"'
            ];
        }

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'id_atendimento' => $this->request->getPost('id_atendimento'),
            'id_exame' => $this->request->getPost('id_exame'),
            'status' => $status,
            'data_solicitacao' => \CodeIgniter\I18n\Time::now(),
            'observacao' => $this->request->getPost('observacao')
        ];

        // Adicionar campos específicos para status "Realizado"
        if ($status === 'Realizado') {
            $dataRealizacao = $this->request->getPost('data_realizacao');
            if ($dataRealizacao) {
                // Converter formato datetime-local para objeto Time
                $dataFormatada = str_replace('T', ' ', $dataRealizacao) . ':00';
                $data['data_realizacao'] = \CodeIgniter\I18n\Time::parse($dataFormatada);
            } else {
                // Se não informada, usar data atual
                $data['data_realizacao'] = \CodeIgniter\I18n\Time::now();
            }
            
            $data['resultado'] = $this->request->getPost('resultado');
        }

        if (!$this->atendimentoExameModel->save($data)) {
            $errors = $this->atendimentoExameModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->to('/atendimentos/show/' . $data['id_atendimento'])
                        ->with('success', 'Exame cadastrado com sucesso!');
    }

    /**
     * Exibe detalhes de um exame específico do atendimento
     */
    public function show($id)
    {
        // Usar consulta direta para evitar problemas com casting de campos datetime NULL
        $db = \Config\Database::connect();
        $atendimentoExame = $db->table('atendimento_exames ae')
            ->select('ae.*, 
                     e.nome as nome_exame, 
                     e.codigo as codigo_exame,
                     e.tipo as tipo_exame,
                     e.descricao as descricao_exame,
                     a.data_atendimento,
                     p.nome as nome_paciente,
                     p.cpf,
                     m.nome as nome_medico,
                     ae.data_solicitacao as data_solicitacao_raw,
                     ae.data_realizacao as data_realizacao_raw')
            ->join('exames e', 'e.id_exame = ae.id_exame')
            ->join('atendimentos a', 'a.id_atendimento = ae.id_atendimento')
            ->join('pacientes p', 'p.id_paciente = a.id_paciente')
            ->join('medicos m', 'm.id_medico = a.id_medico')
            ->where('ae.id_atendimento_exame', $id)
            ->where('ae.deleted_at IS NULL')
            ->get()
            ->getRowArray();

        if (!$atendimentoExame) {
            return redirect()->to('/atendimento-exames')->with('error', 'Registro não encontrado');
        }

        $data = [
            'title' => 'Detalhes do Exame',
            'description' => 'Detalhes do exame solicitado no atendimento',
            'atendimentoExame' => $atendimentoExame
        ];

        return view('atendimento_exames/show', $data);
    }

    /**
     * Exibe o formulário para editar exame do atendimento
     */
    public function edit($id)
    {
        // Usar consulta direta para evitar problemas com casting
        $db = \Config\Database::connect();
        $atendimentoExame = $db->table('atendimento_exames')
                              ->where('id_atendimento_exame', $id)
                              ->where('deleted_at IS NULL')
                              ->get()
                              ->getRowArray();
        
        if (!$atendimentoExame) {
            return redirect()->to('/atendimento-exames')->with('error', 'Registro não encontrado');
        }

        $atendimento = $this->atendimentoModel->select('atendimentos.*, pacientes.nome as paciente_nome, pacientes.cpf, medicos.nome as medico_nome, medicos.crm')
                                            ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
                                            ->join('medicos', 'medicos.id_medico = atendimentos.id_medico')
                                            ->find($atendimentoExame['id_atendimento']);
        $exames = $this->exameModel->orderBy('tipo', 'ASC')->orderBy('nome', 'ASC')->findAll();

        $data = [
            'title' => 'Editar Exame do Atendimento',
            'description' => 'Edite os detalhes do exame solicitado no atendimento',
            'atendimentoExame' => $atendimentoExame,
            'atendimento' => $atendimento,
            'exames' => $exames,
            'statusOptions' => ['Solicitado', 'Realizado', 'Cancelado']
        ];

        return view('atendimento_exames/edit', $data);
    }

    /**
     * Atualiza o exame do atendimento
     */
    public function update($id)
    {
        // Usar consulta direta para evitar problemas com casting
        $db = \Config\Database::connect();
        $atendimentoExame = $db->table('atendimento_exames')
                              ->where('id_atendimento_exame', $id)
                              ->where('deleted_at IS NULL')
                              ->get()
                              ->getRowArray();
        
        if (!$atendimentoExame) {
            return redirect()->to('/atendimento-exames')->with('error', 'Registro não encontrado');
        }

        $data = [
            'id_exame' => $this->request->getPost('id_exame'),
            'status' => $this->request->getPost('status'),
            'resultado' => $this->request->getPost('resultado'),
            'observacao' => $this->request->getPost('observacao')
        ];

        // Se está mudando para "Realizado", definir data de realização
        if ($data['status'] === 'Realizado' && $atendimentoExame['status'] !== 'Realizado') {
            $dataRealizacao = $this->request->getPost('data_realizacao');
            if ($dataRealizacao) {
                // Se foi fornecida uma data específica, converter para objeto Time
                $data['data_realizacao'] = \CodeIgniter\I18n\Time::parse($dataRealizacao);
            } else {
                // Usar data/hora atual
                $data['data_realizacao'] = \CodeIgniter\I18n\Time::now();
            }
        }

        // Se está mudando de "Realizado" para outro status, limpar data de realização
        $limparDataRealizacao = false;
        if ($data['status'] !== 'Realizado' && $atendimentoExame['status'] === 'Realizado') {
            $limparDataRealizacao = true;
        }

        if (!$this->atendimentoExameModel->update($id, $data)) {
            $errors = $this->atendimentoExameModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        // Se precisa limpar a data de realização, fazer update direto na base
        if ($limparDataRealizacao) {
            $db = \Config\Database::connect();
            $db->table('atendimento_exames')
               ->where('id_atendimento_exame', $id)
               ->update(['data_realizacao' => null]);
        }

        return redirect()->to('/atendimentos/show/' . $atendimentoExame['id_atendimento'])
                        ->with('success', 'Exame atualizado com sucesso!');
    }

    /**
     * Remove um exame do atendimento
     */
    public function delete($id)
    {
        // Usar consulta direta para evitar problemas com casting
        $db = \Config\Database::connect();
        $atendimentoExame = $db->table('atendimento_exames')
                              ->where('id_atendimento_exame', $id)
                              ->where('deleted_at IS NULL')
                              ->get()
                              ->getRowArray();
        
        if (!$atendimentoExame) {
            return redirect()->to('/atendimento-exames')->with('error', 'Registro não encontrado');
        }

        if (!$this->atendimentoExameModel->delete($id)) {
            return redirect()->back()->with('error', 'Erro ao remover exame');
        }

        return redirect()->to('/atendimentos/show/' . $atendimentoExame['id_atendimento'])
                        ->with('success', 'Exame removido do atendimento com sucesso!');
    }

    /**
     * Lista exames por atendimento específico (AJAX)
     */
    public function byAtendimento($idAtendimento)
    {
        $exames = $this->atendimentoExameModel->getExamesByAtendimento($idAtendimento);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $exames
        ]);
    }

    /**
     * Lista atendimentos que solicitaram um exame específico (AJAX)
     */
    public function byExame($idExame)
    {
        $atendimentos = $this->atendimentoExameModel->getAtendimentosByExame($idExame);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $atendimentos
        ]);
    }

    /**
     * Atualiza apenas o status de um exame (AJAX)
     */
    public function updateStatus()
    {
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        // Usar consulta direta para evitar problemas com casting
        $db = \Config\Database::connect();
        $atendimentoExame = $db->table('atendimento_exames')
                              ->where('id_atendimento_exame', $id)
                              ->where('deleted_at IS NULL')
                              ->get()
                              ->getRowArray();
        
        if (!$atendimentoExame) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Exame não encontrado'
            ]);
        }

        $data = ['status' => $status];

        // Se está mudando para "Realizado", definir data de realização
        if ($status === 'Realizado' && $atendimentoExame['status'] !== 'Realizado') {
            $data['data_realizacao'] = \CodeIgniter\I18n\Time::now();
        }

        // Se está mudando de "Realizado" para outro status, limpar data de realização
        $limparDataRealizacao = false;
        if ($status !== 'Realizado' && $atendimentoExame['status'] === 'Realizado') {
            $limparDataRealizacao = true;
        }

        if ($this->atendimentoExameModel->update($id, $data)) {
            // Se precisa limpar a data de realização, fazer update direto na base
            if ($limparDataRealizacao) {
                $db = \Config\Database::connect();
                $db->table('atendimento_exames')
                   ->where('id_atendimento_exame', $id)
                   ->update(['data_realizacao' => null]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status atualizado com sucesso!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao atualizar status',
                'errors' => $this->atendimentoExameModel->errors()
            ]);
        }
    }

    /**
     * Relatório de exames mais solicitados
     */
    public function relatorio()
    {
        $periodo = $this->request->getGet('periodo') ?: 'mes';
        $tipo = $this->request->getGet('tipo');
        
        $builder = $this->atendimentoExameModel
            ->select('exames.nome, 
                     exames.codigo,
                     exames.tipo,
                     COUNT(*) as total_solicitacoes,
                     SUM(CASE WHEN pam_atendimento_exames.status = "Realizado" THEN 1 ELSE 0 END) as total_realizados,
                     SUM(CASE WHEN pam_atendimento_exames.status = "Cancelado" THEN 1 ELSE 0 END) as total_cancelados')
            ->join('exames', 'exames.id_exame = atendimento_exames.id_exame')
            ->groupBy('atendimento_exames.id_exame')
            ->orderBy('total_solicitacoes', 'DESC');

        if ($tipo) {
            $builder->where('exames.tipo', $tipo);
        }

        switch ($periodo) {
            case 'hoje':
                $builder->where('DATE(pam_atendimento_exames.data_solicitacao)', date('Y-m-d'));
                break;
            case 'semana':
                $builder->where('WEEK(pam_atendimento_exames.data_solicitacao)', date('W'))
                       ->where('YEAR(pam_atendimento_exames.data_solicitacao)', date('Y'));
                break;
            case 'mes':
                $builder->where('MONTH(pam_atendimento_exames.data_solicitacao)', date('m'))
                       ->where('YEAR(pam_atendimento_exames.data_solicitacao)', date('Y'));
                break;
            case 'ano':
                $builder->where('YEAR(pam_atendimento_exames.data_solicitacao)', date('Y'));
                break;
        }

        $examesMaisSolicitados = $builder->findAll();

        // Estatísticas por tipo de exame
        $estatisticasTipo = $this->atendimentoExameModel
            ->select('exames.tipo,
                     COUNT(*) as total,
                     SUM(CASE WHEN pam_atendimento_exames.status = "Realizado" THEN 1 ELSE 0 END) as realizados')
            ->join('exames', 'exames.id_exame = atendimento_exames.id_exame')
            ->groupBy('exames.tipo')
            ->findAll();

        $data = [
            'title' => 'Relatório de Exames',
            'description' => 'Relatório dos exames mais solicitados no período selecionado',
            'examesMaisSolicitados' => $examesMaisSolicitados,
            'estatisticasTipo' => $estatisticasTipo,
            'periodo' => $periodo,
            'tipo' => $tipo,
            'tiposExame' => ['laboratorial', 'imagem', 'funcional', 'outros']
        ];

        return view('atendimento_exames/relatorio', $data);
    }

    /**
     * Imprime solicitação de exame
     */
    public function print($id)
    {
        // Usar consulta direta para evitar problemas com casting
        $db = \Config\Database::connect();
        $atendimentoExame = $db->table('atendimento_exames ae')
            ->select('ae.*, 
                     e.nome as nome_exame, 
                     e.codigo as codigo_exame,
                     e.tipo as tipo_exame,
                     e.descricao as descricao_exame,
                     a.data_atendimento,
                     p.nome as nome_paciente,
                     p.cpf,
                     p.data_nascimento,
                     p.sexo,
                     m.nome as nome_medico,
                     m.crm,
                     ae.data_solicitacao as data_solicitacao_raw,
                     ae.data_realizacao as data_realizacao_raw')
            ->join('exames e', 'e.id_exame = ae.id_exame')
            ->join('atendimentos a', 'a.id_atendimento = ae.id_atendimento')
            ->join('pacientes p', 'p.id_paciente = a.id_paciente')
            ->join('medicos m', 'm.id_medico = a.id_medico')
            ->where('ae.id_atendimento_exame', $id)
            ->where('ae.deleted_at IS NULL')
            ->get()
            ->getRowArray();

        if (!$atendimentoExame) {
            return redirect()->to('/atendimento-exames')->with('error', 'Registro não encontrado');
        }

        $data = [
            'title' => 'Solicitação de Exame',
            'description' => 'Impressão da solicitação de exame realizada no atendimento',
            'atendimentoExame' => $atendimentoExame
        ];

        return view('atendimento_exames/print', $data);
    }
}
