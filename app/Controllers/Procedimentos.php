<?php

namespace App\Controllers;

use App\Models\ProcedimentoModel;
use CodeIgniter\Controller;

class Procedimentos extends BaseController
{
    protected $procedimentoModel;

    public function __construct()
    {
        $this->procedimentoModel = new ProcedimentoModel();
    }

    /**
     * Lista todos os procedimentos
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        
        if ($search) {
            $procedimentos = $this->procedimentoModel->like('nome', $search)
                                                   ->orLike('codigo', $search)
                                                   ->orLike('descricao', $search)
                                                   ->orderBy('nome', 'ASC')
                                                   ->findAll();
        } else {
            $procedimentos = $this->procedimentoModel->orderBy('nome', 'ASC')->findAll();
        }

        // Estatísticas
        $stats = [
            'total' => $this->procedimentoModel->countAll(),
            'hoje' => $this->procedimentoModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'mes' => $this->procedimentoModel->where('MONTH(created_at)', date('m'))
                                            ->where('YEAR(created_at)', date('Y'))
                                            ->countAllResults(),
            'ano' => $this->procedimentoModel->where('YEAR(created_at)', date('Y'))->countAllResults()
        ];

        $data = [
            'title' => 'Procedimentos',
            'description' => 'Gerenciar Procedimentos',
            'procedimentos' => $procedimentos,
            'stats' => $stats,
            'search' => $search,
        ];

        return view('procedimentos/index', $data);
    }

    /**
     * Exibe formulário para criar novo procedimento
     */
    public function create()
    {
        $data = [
            'title' => 'Novo Procedimento',
            'description' => 'Cadastrar Novo Procedimento'
        ];

        // Verificar se é duplicação de procedimento existente
        $duplicateId = $this->request->getGet('duplicate');
        if ($duplicateId) {
            $procedimento = $this->procedimentoModel->find($duplicateId);
            if ($procedimento) {
                $data['procedimento'] = $procedimento;
                $data['title'] = 'Duplicar Procedimento';
                $data['description'] = 'Criar novo procedimento baseado em: ' . $procedimento['nome'];
            }
        }

        return view('procedimentos/create', $data);
    }

    /**
     * Salva um novo procedimento
     */
    public function store()
    {
        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'codigo' => 'permit_empty|max_length[50]',
            'descricao' => 'permit_empty'
        ];

        $messages = [
            'nome' => [
                'required' => 'O nome do procedimento é obrigatório',
                'min_length' => 'O nome deve ter pelo menos 3 caracteres',
                'max_length' => 'O nome deve ter no máximo 255 caracteres'
            ],
            'codigo' => [
                'max_length' => 'O código deve ter no máximo 50 caracteres'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'nome' => $this->request->getPost('nome'),
            'codigo' => $this->request->getPost('codigo'),
            'descricao' => $this->request->getPost('descricao')
        ];

        if ($this->procedimentoModel->save($data)) {
            return redirect()->to('/procedimentos')->with('success', 'Procedimento cadastrado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erro ao cadastrar procedimento.');
        }
    }

    /**
     * Exibe detalhes de um procedimento específico
     */
    public function show($id = null)
    {
        $procedimento = $this->procedimentoModel->find($id);
        
        if (!$procedimento) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Procedimento não encontrado');
        }

        // Buscar estatísticas de uso deste procedimento
        $atendimentoProcedimentoModel = new \App\Models\AtendimentoProcedimentoModel();
        $totalUsos = $atendimentoProcedimentoModel->where('id_procedimento', $id)->countAllResults();
        $usosHoje = $atendimentoProcedimentoModel->where('id_procedimento', $id)
                                                ->where('DATE(created_at)', date('Y-m-d'))
                                                ->countAllResults();
        $usosMes = $atendimentoProcedimentoModel->where('id_procedimento', $id)
                                               ->where('MONTH(created_at)', date('m'))
                                               ->where('YEAR(created_at)', date('Y'))
                                               ->countAllResults();

        $data = [
            'title' => 'Detalhes do Procedimento',
            'description' => 'Informações do Procedimento: ' . $procedimento['nome'],
            'procedimento' => $procedimento,
            'stats' => [
                'total_usos' => $totalUsos,
                'usos_hoje' => $usosHoje,
                'usos_mes' => $usosMes
            ]
        ];

        return view('procedimentos/show', $data);
    }

    /**
     * Exibe formulário para editar procedimento
     */
    public function edit($id = null)
    {
        $procedimento = $this->procedimentoModel->find($id);
        
        if (!$procedimento) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Procedimento não encontrado');
        }

        $data = [
            'title' => 'Editar Procedimento',
            'description' => 'Editar Procedimento: ' . $procedimento['nome'],
            'procedimento' => $procedimento
        ];

        return view('procedimentos/edit', $data);
    }

    /**
     * Atualiza dados do procedimento
     */
    public function update($id = null)
    {
        $procedimento = $this->procedimentoModel->find($id);
        
        if (!$procedimento) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Procedimento não encontrado');
        }

        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'codigo' => 'permit_empty|max_length[50]',
            'descricao' => 'permit_empty'
        ];

        $messages = [
            'nome' => [
                'required' => 'O nome do procedimento é obrigatório',
                'min_length' => 'O nome deve ter pelo menos 3 caracteres',
                'max_length' => 'O nome deve ter no máximo 255 caracteres'
            ],
            'codigo' => [
                'max_length' => 'O código deve ter no máximo 50 caracteres'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'nome' => $this->request->getPost('nome'),
            'codigo' => $this->request->getPost('codigo'),
            'descricao' => $this->request->getPost('descricao')
        ];

        if ($this->procedimentoModel->update($id, $data)) {
            return redirect()->to('/procedimentos')->with('success', 'Procedimento atualizado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar procedimento.');
        }
    }

    /**
     * Exclui um procedimento (soft delete)
     */
    public function delete($id = null)
    {
        $procedimento = $this->procedimentoModel->find($id);
        
        if (!$procedimento) {
            return $this->response->setJSON(['error' => 'Procedimento não encontrado'])->setStatusCode(404);
        }

        // Verificar se o procedimento tem atendimentos vinculados
        $atendimentoProcedimentoModel = new \App\Models\AtendimentoProcedimentoModel();
        $atendimentos = $atendimentoProcedimentoModel->where('id_procedimento', $id)->countAllResults();
        
        if ($atendimentos > 0) {
            return $this->response->setJSON([
                'error' => 'Não é possível excluir este procedimento pois ele possui atendimentos vinculados.'
            ])->setStatusCode(400);
        }

        if ($this->procedimentoModel->delete($id)) {
            return $this->response->setJSON(['success' => 'Procedimento excluído com sucesso!']);
        } else {
            return $this->response->setJSON(['error' => 'Erro ao excluir procedimento'])->setStatusCode(500);
        }
    }

    /**
     * Busca procedimentos via AJAX
     */
    public function search()
    {
        $term = $this->request->getPost('term') ?? '';
        
        $procedimentos = $this->procedimentoModel->like('nome', $term)
                                                ->orLike('codigo', $term)
                                                ->orderBy('nome', 'ASC')
                                                ->findAll();

        return $this->response->setJSON($procedimentos);
    }

    /**
     * Retorna procedimentos para uso em selects
     */
    public function getAll()
    {
        $procedimentos = $this->procedimentoModel->orderBy('nome', 'ASC')->findAll();
        return $this->response->setJSON($procedimentos);
    }
}
