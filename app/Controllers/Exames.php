<?php

namespace App\Controllers;

use App\Models\ExameModel;
use CodeIgniter\Controller;

class Exames extends BaseController
{
    protected $exameModel;

    public function __construct()
    {
        $this->exameModel = new ExameModel();
    }

    /**
     * Lista todos os exames
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $tipo = $this->request->getGet('tipo');
        
        $query = $this->exameModel;
        
        if ($search) {
            $query = $query->like('nome', $search)
                          ->orLike('codigo', $search)
                          ->orLike('descricao', $search);
        }
        
        if ($tipo) {
            $query = $query->where('tipo', $tipo);
        }
        
        $exames = $query->orderBy('nome', 'ASC')->findAll();

        // Estatísticas por tipo
        $stats = [
            'total' => $this->exameModel->countAllResults(),
            'laboratorial' => $this->exameModel->where('tipo', 'laboratorial')->countAllResults(),
            'imagem' => $this->exameModel->where('tipo', 'imagem')->countAllResults(),
            'funcional' => $this->exameModel->where('tipo', 'funcional')->countAllResults(),
            'outros' => $this->exameModel->where('tipo', 'outros')->countAllResults(),
            'hoje' => $this->exameModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'mes' => $this->exameModel->where('MONTH(created_at)', date('m'))
                                     ->where('YEAR(created_at)', date('Y'))
                                     ->countAllResults(),
            'ano' => $this->exameModel->where('YEAR(created_at)', date('Y'))->countAllResults()
        ];

        $data = [
            'title' => 'Exames',
            'description' => 'Gerenciar Exames',
            'exames' => $exames,
            'stats' => $stats,
            'search' => $search,
            'tipo_filtro' => $tipo,
            'tipos_exame' => ['laboratorial', 'imagem', 'funcional', 'outros']
        ];

        return view('exames/index', $data);
    }

    /**
     * Exibe formulário para criar novo exame
     */
    public function create()
    {
        $data = [
            'title' => 'Novo Exame',
            'description' => 'Cadastrar Novo Exame',
            'tipos_exame' => ['laboratorial', 'imagem', 'funcional', 'outros']
        ];

        return view('exames/create', $data);
    }

    /**
     * Salva um novo exame
     */
    public function store()
    {
        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'codigo' => 'permit_empty|max_length[50]',
            'tipo' => 'required|in_list[laboratorial,imagem,funcional,outros]',
            'descricao' => 'permit_empty'
        ];

        $messages = [
            'nome' => [
                'required' => 'O nome do exame é obrigatório',
                'min_length' => 'O nome deve ter pelo menos 3 caracteres',
                'max_length' => 'O nome deve ter no máximo 255 caracteres'
            ],
            'codigo' => [
                'max_length' => 'O código deve ter no máximo 50 caracteres'
            ],
            'tipo' => [
                'required' => 'O tipo do exame é obrigatório',
                'in_list' => 'Tipo deve ser: laboratorial, imagem, funcional ou outros'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'nome' => $this->request->getPost('nome'),
            'codigo' => $this->request->getPost('codigo'),
            'tipo' => $this->request->getPost('tipo'),
            'descricao' => $this->request->getPost('descricao')
        ];

        if ($this->exameModel->save($data)) {
            return redirect()->to('/exames')->with('success', 'Exame cadastrado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erro ao cadastrar exame.');
        }
    }

    /**
     * Exibe detalhes de um exame específico
     */
    public function show($id = null)
    {
        $exame = $this->exameModel->find($id);
        
        if (!$exame) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Exame não encontrado');
        }

        // Buscar estatísticas de uso deste exame
        $atendimentoExameModel = new \App\Models\AtendimentoExameModel();
        $totalSolicitacoes = $atendimentoExameModel->where('id_exame', $id)->countAllResults();
        $solicitacoesHoje = $atendimentoExameModel->where('id_exame', $id)
                                                 ->where('DATE(data_solicitacao)', date('Y-m-d'))
                                                 ->countAllResults();
        $solicitacoesMes = $atendimentoExameModel->where('id_exame', $id)
                                                ->where('MONTH(data_solicitacao)', date('m'))
                                                ->where('YEAR(data_solicitacao)', date('Y'))
                                                ->countAllResults();
        $realizados = $atendimentoExameModel->where('id_exame', $id)
                                           ->where('status', 'Realizado')
                                           ->countAllResults();
        $pendentes = $atendimentoExameModel->where('id_exame', $id)
                                          ->where('status', 'Solicitado')
                                          ->countAllResults();

        $data = [
            'title' => 'Detalhes do Exame',
            'description' => 'Informações do Exame: ' . $exame['nome'],
            'exame' => $exame,
            'stats' => [
                'total_solicitacoes' => $totalSolicitacoes,
                'solicitacoes_hoje' => $solicitacoesHoje,
                'solicitacoes_mes' => $solicitacoesMes,
                'realizados' => $realizados,
                'pendentes' => $pendentes
            ]
        ];

        return view('exames/show', $data);
    }

    /**
     * Exibe formulário para editar exame
     */
    public function edit($id = null)
    {
        $exame = $this->exameModel->find($id);
        
        if (!$exame) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Exame não encontrado');
        }

        $data = [
            'title' => 'Editar Exame',
            'description' => 'Editar Exame: ' . $exame['nome'],
            'exame' => $exame,
            'tipos_exame' => ['laboratorial', 'imagem', 'funcional', 'outros']
        ];

        return view('exames/edit', $data);
    }

    /**
     * Atualiza dados do exame
     */
    public function update($id = null)
    {
        $exame = $this->exameModel->find($id);
        
        if (!$exame) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Exame não encontrado');
        }

        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'codigo' => 'permit_empty|max_length[50]',
            'tipo' => 'required|in_list[laboratorial,imagem,funcional,outros]',
            'descricao' => 'permit_empty'
        ];

        $messages = [
            'nome' => [
                'required' => 'O nome do exame é obrigatório',
                'min_length' => 'O nome deve ter pelo menos 3 caracteres',
                'max_length' => 'O nome deve ter no máximo 255 caracteres'
            ],
            'codigo' => [
                'max_length' => 'O código deve ter no máximo 50 caracteres'
            ],
            'tipo' => [
                'required' => 'O tipo do exame é obrigatório',
                'in_list' => 'Tipo deve ser: laboratorial, imagem, funcional ou outros'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'nome' => $this->request->getPost('nome'),
            'codigo' => $this->request->getPost('codigo'),
            'tipo' => $this->request->getPost('tipo'),
            'descricao' => $this->request->getPost('descricao')
        ];

        if ($this->exameModel->update($id, $data)) {
            return redirect()->to('/exames')->with('success', 'Exame atualizado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar exame.');
        }
    }

    /**
     * Exclui um exame (soft delete)
     */
    public function delete($id = null)
    {
        $exame = $this->exameModel->find($id);
        
        if (!$exame) {
            return $this->response->setJSON(['error' => 'Exame não encontrado'])->setStatusCode(404);
        }

        // Verificar se o exame tem solicitações vinculadas
        $atendimentoExameModel = new \App\Models\AtendimentoExameModel();
        $solicitacoes = $atendimentoExameModel->where('id_exame', $id)->countAllResults();
        
        if ($solicitacoes > 0) {
            return $this->response->setJSON([
                'error' => 'Não é possível excluir este exame pois ele possui solicitações vinculadas.'
            ])->setStatusCode(400);
        }

        if ($this->exameModel->delete($id)) {
            return $this->response->setJSON(['success' => 'Exame excluído com sucesso!']);
        } else {
            return $this->response->setJSON(['error' => 'Erro ao excluir exame'])->setStatusCode(500);
        }
    }

    /**
     * Busca exames via AJAX
     */
    public function search()
    {
        $term = $this->request->getPost('term') ?? '';
        $tipo = $this->request->getPost('tipo') ?? '';
        
        $query = $this->exameModel->like('nome', $term)
                                 ->orLike('codigo', $term);
        
        if ($tipo) {
            $query = $query->where('tipo', $tipo);
        }
        
        $exames = $query->orderBy('nome', 'ASC')->findAll();

        return $this->response->setJSON($exames);
    }

    /**
     * Retorna exames por tipo para uso em selects
     */
    public function getByTipo($tipo = null)
    {
        $query = $this->exameModel->orderBy('nome', 'ASC');
        
        if ($tipo) {
            $query = $query->where('tipo', $tipo);
        }
        
        $exames = $query->findAll();
        return $this->response->setJSON($exames);
    }

    /**
     * Retorna todos os exames para uso em selects
     */
    public function getAll()
    {
        $exames = $this->exameModel->orderBy('nome', 'ASC')->findAll();
        return $this->response->setJSON($exames);
    }

    /**
     * Retorna view modal para um exame específico
     */
    public function modal($id = null)
    {
        $exame = $this->exameModel->find($id);
        
        if (!$exame) {
            return $this->response->setJSON(['error' => 'Exame não encontrado'])->setStatusCode(404);
        }

        $data = [
            'exame' => $exame
        ];

        return view('exames/modal_view', $data);
    }
}
