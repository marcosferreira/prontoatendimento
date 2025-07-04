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
        
        $builder = $this->atendimentoExameModel
            ->select('atendimento_exames.*, 
                     exames.nome as nome_exame, 
                     exames.codigo as codigo_exame,
                     exames.tipo as tipo_exame,
                     atendimentos.data_atendimento,
                     pacientes.nome as nome_paciente,
                     pacientes.cpf')
            ->join('exames', 'exames.id_exame = atendimento_exames.id_exame')
            ->join('atendimentos', 'atendimentos.id_atendimento = atendimento_exames.id_atendimento')
            ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
            ->orderBy('pam_atendimento_exames.data_solicitacao', 'DESC');

        if ($search) {
            $builder->groupStart()
                   ->like('pacientes.nome', $search)
                   ->orLike('exames.nome', $search)
                   ->orLike('exames.codigo', $search)
                   ->groupEnd();
        }

        if ($atendimento) {
            $builder->where('atendimento_exames.id_atendimento', $atendimento);
        }

        if ($exame) {
            $builder->where('atendimento_exames.id_exame', $exame);
        }

        if ($status) {
            $builder->where('pam_atendimento_exames.status', $status);
        }

        $atendimentoExames = $builder->paginate(20);
        $pager = $this->atendimentoExameModel->pager;

        // Estatísticas
        $stats = [
            'total' => $this->atendimentoExameModel->countAll(),
            'solicitados' => $this->atendimentoExameModel->where('status', 'Solicitado')->countAllResults(),
            'realizados' => $this->atendimentoExameModel->where('status', 'Realizado')->countAllResults(),
            'cancelados' => $this->atendimentoExameModel->where('status', 'Cancelado')->countAllResults(),
            'hoje' => $this->atendimentoExameModel->where('DATE(data_solicitacao)', date('Y-m-d'))->countAllResults()
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

        $atendimento = $this->atendimentoModel->getAtendimentoCompleto($idAtendimento);
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
        $data = [
            'id_atendimento' => $this->request->getPost('id_atendimento'),
            'id_exame' => $this->request->getPost('id_exame'),
            'status' => 'Solicitado',
            'data_solicitacao' => date('Y-m-d H:i:s'),
            'observacao' => $this->request->getPost('observacao')
        ];

        if (!$this->atendimentoExameModel->save($data)) {
            $errors = $this->atendimentoExameModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->to('/atendimentos/show/' . $data['id_atendimento'])
                        ->with('success', 'Exame solicitado com sucesso!');
    }

    /**
     * Exibe detalhes de um exame específico do atendimento
     */
    public function show($id)
    {
        $atendimentoExame = $this->atendimentoExameModel
            ->select('atendimento_exames.*, 
                     exames.nome as nome_exame, 
                     exames.codigo as codigo_exame,
                     exames.tipo as tipo_exame,
                     exames.descricao as descricao_exame,
                     atendimentos.data_atendimento,
                     pacientes.nome as nome_paciente,
                     pacientes.cpf,
                     medicos.nome as nome_medico')
            ->join('exames', 'exames.id_exame = atendimento_exames.id_exame')
            ->join('atendimentos', 'atendimentos.id_atendimento = atendimento_exames.id_atendimento')
            ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
            ->join('medicos', 'medicos.id_medico = atendimentos.id_medico')
            ->find($id);

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
        $atendimentoExame = $this->atendimentoExameModel->find($id);
        
        if (!$atendimentoExame) {
            return redirect()->to('/atendimento-exames')->with('error', 'Registro não encontrado');
        }

        $atendimento = $this->atendimentoModel->getAtendimentoCompleto($atendimentoExame['id_atendimento']);
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
        $atendimentoExame = $this->atendimentoExameModel->find($id);
        
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
            $data['data_realizacao'] = $this->request->getPost('data_realizacao') ?: date('Y-m-d H:i:s');
        }

        // Se está mudando de "Realizado" para outro status, limpar data de realização
        if ($data['status'] !== 'Realizado' && $atendimentoExame['status'] === 'Realizado') {
            $data['data_realizacao'] = null;
        }

        if (!$this->atendimentoExameModel->update($id, $data)) {
            $errors = $this->atendimentoExameModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->to('/atendimentos/show/' . $atendimentoExame['id_atendimento'])
                        ->with('success', 'Exame atualizado com sucesso!');
    }

    /**
     * Remove um exame do atendimento
     */
    public function delete($id)
    {
        $atendimentoExame = $this->atendimentoExameModel->find($id);
        
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

        $atendimentoExame = $this->atendimentoExameModel->find($id);
        
        if (!$atendimentoExame) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Exame não encontrado'
            ]);
        }

        $data = ['status' => $status];

        // Se está mudando para "Realizado", definir data de realização
        if ($status === 'Realizado' && $atendimentoExame['status'] !== 'Realizado') {
            $data['data_realizacao'] = date('Y-m-d H:i:s');
        }

        // Se está mudando de "Realizado" para outro status, limpar data de realização
        if ($status !== 'Realizado' && $atendimentoExame['status'] === 'Realizado') {
            $data['data_realizacao'] = null;
        }

        if ($this->atendimentoExameModel->update($id, $data)) {
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
        $atendimentoExame = $this->atendimentoExameModel
            ->select('atendimento_exames.*, 
                     exames.nome as nome_exame, 
                     exames.codigo as codigo_exame,
                     exames.tipo as tipo_exame,
                     exames.descricao as descricao_exame,
                     atendimentos.data_atendimento,
                     pacientes.nome as nome_paciente,
                     pacientes.cpf,
                     pacientes.data_nascimento,
                     pacientes.sexo,
                     medicos.nome as nome_medico,
                     medicos.crm')
            ->join('exames', 'exames.id_exame = atendimento_exames.id_exame')
            ->join('atendimentos', 'atendimentos.id_atendimento = atendimento_exames.id_atendimento')
            ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
            ->join('medicos', 'medicos.id_medico = atendimentos.id_medico')
            ->find($id);

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
