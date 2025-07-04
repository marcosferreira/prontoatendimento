<?php

namespace App\Controllers;

use App\Models\AtendimentoProcedimentoModel;
use App\Models\AtendimentoModel;
use App\Models\ProcedimentoModel;
use CodeIgniter\Controller;

class AtendimentoProcedimentos extends BaseController
{
    protected $atendimentoProcedimentoModel;
    protected $atendimentoModel;
    protected $procedimentoModel;

    public function __construct()
    {
        $this->atendimentoProcedimentoModel = new AtendimentoProcedimentoModel();
        $this->atendimentoModel = new AtendimentoModel();
        $this->procedimentoModel = new ProcedimentoModel();
    }

    /**
     * Lista todos os procedimentos realizados em atendimentos
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $atendimento = $this->request->getGet('atendimento');
        $procedimento = $this->request->getGet('procedimento');
        
        $builder = $this->atendimentoProcedimentoModel
            ->select('atendimento_procedimentos.*, 
                     procedimentos.nome as nome_procedimento, 
                     procedimentos.codigo as codigo_procedimento,
                     atendimentos.data_atendimento,
                     pacientes.nome as nome_paciente,
                     pacientes.cpf')
            ->join('procedimentos', 'procedimentos.id_procedimento = atendimento_procedimentos.id_procedimento')
            ->join('atendimentos', 'atendimentos.id_atendimento = atendimento_procedimentos.id_atendimento')
            ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
            ->orderBy('atendimento_procedimentos.created_at', 'DESC');

        if ($search) {
            $builder->groupStart()
                   ->like('pacientes.nome', $search)
                   ->orLike('procedimentos.nome', $search)
                   ->orLike('procedimentos.codigo', $search)
                   ->groupEnd();
        }

        if ($atendimento) {
            $builder->where('atendimento_procedimentos.id_atendimento', $atendimento);
        }

        if ($procedimento) {
            $builder->where('atendimento_procedimentos.id_procedimento', $procedimento);
        }

        $atendimentoProcedimentos = $builder->paginate(20);
        $pager = $this->atendimentoProcedimentoModel->pager;

        // Estatísticas
        $stats = [
            'total' => $this->atendimentoProcedimentoModel->countAll(),
            'hoje' => $this->atendimentoProcedimentoModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'mes' => $this->atendimentoProcedimentoModel->where('MONTH(created_at)', date('m'))
                                                      ->where('YEAR(created_at)', date('Y'))
                                                      ->countAllResults(),
            'ano' => $this->atendimentoProcedimentoModel->where('YEAR(created_at)', date('Y'))->countAllResults()
        ];

        $procedimentos = $this->procedimentoModel->orderBy('nome', 'ASC')->findAll();

        $data = [
            'title' => 'Procedimentos em Atendimentos',
            'atendimentoProcedimentos' => $atendimentoProcedimentos,
            'pager' => $pager,
            'stats' => $stats,
            'procedimentos' => $procedimentos,
            'search' => $search,
            'atendimento' => $atendimento,
            'procedimento' => $procedimento
        ];

        return view('atendimento_procedimentos/index', $data);
    }

    /**
     * Exibe o formulário para adicionar procedimento a um atendimento
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

        $procedimentos = $this->procedimentoModel->orderBy('nome', 'ASC')->findAll();
        
        // Procedimentos já adicionados a este atendimento
        $procedimentosJaAdicionados = $this->atendimentoProcedimentoModel
            ->select('id_procedimento')
            ->where('id_atendimento', $idAtendimento)
            ->findColumn('id_procedimento');

        $data = [
            'title' => 'Adicionar Procedimento ao Atendimento',
            'atendimento' => $atendimento,
            'procedimentos' => $procedimentos,
            'procedimentosJaAdicionados' => $procedimentosJaAdicionados ?: []
        ];

        return view('atendimento_procedimentos/create', $data);
    }

    /**
     * Salva um novo procedimento para o atendimento
     */
    public function store()
    {
        $data = [
            'id_atendimento' => $this->request->getPost('id_atendimento'),
            'id_procedimento' => $this->request->getPost('id_procedimento'),
            'quantidade' => $this->request->getPost('quantidade') ?: 1,
            'observacao' => $this->request->getPost('observacao')
        ];

        if (!$this->atendimentoProcedimentoModel->save($data)) {
            $errors = $this->atendimentoProcedimentoModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->to('/atendimentos/show/' . $data['id_atendimento'])
                        ->with('success', 'Procedimento adicionado ao atendimento com sucesso!');
    }

    /**
     * Exibe detalhes de um procedimento específico do atendimento
     */
    public function show($id)
    {
        $atendimentoProcedimento = $this->atendimentoProcedimentoModel
            ->select('atendimento_procedimentos.*, 
                     procedimentos.nome as nome_procedimento, 
                     procedimentos.codigo as codigo_procedimento,
                     procedimentos.descricao as descricao_procedimento,
                     atendimentos.data_atendimento,
                     pacientes.nome as nome_paciente,
                     pacientes.cpf,
                     medicos.nome as nome_medico')
            ->join('procedimentos', 'procedimentos.id_procedimento = atendimento_procedimentos.id_procedimento')
            ->join('atendimentos', 'atendimentos.id_atendimento = atendimento_procedimentos.id_atendimento')
            ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
            ->join('medicos', 'medicos.id_medico = atendimentos.id_medico')
            ->find($id);

        if (!$atendimentoProcedimento) {
            return redirect()->to('/atendimento-procedimentos')->with('error', 'Registro não encontrado');
        }

        $data = [
            'title' => 'Detalhes do Procedimento',
            'atendimentoProcedimento' => $atendimentoProcedimento
        ];

        return view('atendimento_procedimentos/show', $data);
    }

    /**
     * Exibe o formulário para editar procedimento do atendimento
     */
    public function edit($id)
    {
        $atendimentoProcedimento = $this->atendimentoProcedimentoModel->find($id);
        
        if (!$atendimentoProcedimento) {
            return redirect()->to('/atendimento-procedimentos')->with('error', 'Registro não encontrado');
        }

        $atendimento = $this->atendimentoModel->getAtendimentoCompleto($atendimentoProcedimento['id_atendimento']);
        $procedimentos = $this->procedimentoModel->orderBy('nome', 'ASC')->findAll();

        $data = [
            'title' => 'Editar Procedimento do Atendimento',
            'atendimentoProcedimento' => $atendimentoProcedimento,
            'atendimento' => $atendimento,
            'procedimentos' => $procedimentos
        ];

        return view('atendimento_procedimentos/edit', $data);
    }

    /**
     * Atualiza o procedimento do atendimento
     */
    public function update($id)
    {
        $atendimentoProcedimento = $this->atendimentoProcedimentoModel->find($id);
        
        if (!$atendimentoProcedimento) {
            return redirect()->to('/atendimento-procedimentos')->with('error', 'Registro não encontrado');
        }

        $data = [
            'id_procedimento' => $this->request->getPost('id_procedimento'),
            'quantidade' => $this->request->getPost('quantidade') ?: 1,
            'observacao' => $this->request->getPost('observacao')
        ];

        if (!$this->atendimentoProcedimentoModel->update($id, $data)) {
            $errors = $this->atendimentoProcedimentoModel->errors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        return redirect()->to('/atendimentos/show/' . $atendimentoProcedimento['id_atendimento'])
                        ->with('success', 'Procedimento atualizado com sucesso!');
    }

    /**
     * Remove um procedimento do atendimento
     */
    public function delete($id)
    {
        $atendimentoProcedimento = $this->atendimentoProcedimentoModel->find($id);
        
        if (!$atendimentoProcedimento) {
            return redirect()->to('/atendimento-procedimentos')->with('error', 'Registro não encontrado');
        }

        if (!$this->atendimentoProcedimentoModel->delete($id)) {
            return redirect()->back()->with('error', 'Erro ao remover procedimento');
        }

        return redirect()->to('/atendimentos/show/' . $atendimentoProcedimento['id_atendimento'])
                        ->with('success', 'Procedimento removido do atendimento com sucesso!');
    }

    /**
     * Lista procedimentos por atendimento específico (AJAX)
     */
    public function byAtendimento($idAtendimento)
    {
        $procedimentos = $this->atendimentoProcedimentoModel->getProcedimentosByAtendimento($idAtendimento);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $procedimentos
        ]);
    }

    /**
     * Lista atendimentos que usaram um procedimento específico (AJAX)
     */
    public function byProcedimento($idProcedimento)
    {
        $atendimentos = $this->atendimentoProcedimentoModel->getAtendimentosByProcedimento($idProcedimento);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $atendimentos
        ]);
    }

    /**
     * Relatório de procedimentos mais utilizados
     */
    public function relatorio()
    {
        $periodo = $this->request->getGet('periodo') ?: 'mes';
        
        $builder = $this->atendimentoProcedimentoModel
            ->select('procedimentos.nome, 
                     procedimentos.codigo,
                     COUNT(*) as total_realizacoes,
                     SUM(atendimento_procedimentos.quantidade) as quantidade_total')
            ->join('procedimentos', 'procedimentos.id_procedimento = atendimento_procedimentos.id_procedimento')
            ->groupBy('atendimento_procedimentos.id_procedimento')
            ->orderBy('total_realizacoes', 'DESC');

        switch ($periodo) {
            case 'hoje':
                $builder->where('DATE(atendimento_procedimentos.created_at)', date('Y-m-d'));
                break;
            case 'semana':
                $builder->where('WEEK(atendimento_procedimentos.created_at)', date('W'))
                       ->where('YEAR(atendimento_procedimentos.created_at)', date('Y'));
                break;
            case 'mes':
                $builder->where('MONTH(atendimento_procedimentos.created_at)', date('m'))
                       ->where('YEAR(atendimento_procedimentos.created_at)', date('Y'));
                break;
            case 'ano':
                $builder->where('YEAR(atendimento_procedimentos.created_at)', date('Y'));
                break;
        }

        $procedimentosMaisUtilizados = $builder->findAll();

        $data = [
            'title' => 'Relatório de Procedimentos',
            'procedimentosMaisUtilizados' => $procedimentosMaisUtilizados,
            'periodo' => $periodo
        ];

        return view('atendimento_procedimentos/relatorio', $data);
    }
}
