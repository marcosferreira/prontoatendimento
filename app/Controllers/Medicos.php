<?php

namespace App\Controllers;

use App\Models\MedicoModel;
use CodeIgniter\Controller;

class Medicos extends BaseController
{
    protected $medicoModel;

    public function __construct()
    {
        $this->medicoModel = new MedicoModel();
    }

    /**
     * Lista todos os médicos
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        
        if ($search) {
            $medicos = $this->medicoModel->like('nome', $search)
                                       ->orLike('crm', $search)
                                       ->orLike('especialidade', $search)
                                       ->orderBy('nome', 'ASC')
                                       ->findAll();
        } else {
            $medicos = $this->medicoModel->orderBy('nome', 'ASC')->findAll();
        }

        // Estatísticas
        $stats = [
            'total' => $this->medicoModel->countAll(),
            'ativos' => $this->medicoModel->where('status', 'Ativo')->countAllResults(),
            'inativos' => $this->medicoModel->where('status', 'Inativo')->countAllResults(),
            'hoje' => $this->medicoModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'mes' => $this->medicoModel->where('MONTH(created_at)', date('m'))
                                      ->where('YEAR(created_at)', date('Y'))
                                      ->countAllResults(),
            'ano' => $this->medicoModel->where('YEAR(created_at)', date('Y'))->countAllResults()
        ];

        $data = [
            'title' => 'Médicos',
            'description' => 'Gerenciar Médicos',
            'medicos' => $medicos,
            'stats' => $stats,
            'search' => $search,
        ];

        return view('medicos/index', $data);
    }

    /**
     * Exibe formulário para criar novo médico
     */
    public function create()
    {
        $data = [
            'title' => 'Novo Médico',
            'description' => 'Cadastrar Novo Médico'
        ];

        return view('medicos/create', $data);
    }

    /**
     * Salva um novo médico
     */
    public function store()
    {
        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'crm' => 'required|min_length[4]|max_length[20]|is_unique[medicos.crm]',
            'especialidade' => 'required|max_length[100]',
            'telefone' => 'permit_empty|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'status' => 'required|in_list[Ativo,Inativo]'
        ];

        $messages = [
            'nome' => [
                'required' => 'O nome do médico é obrigatório',
                'min_length' => 'O nome deve ter pelo menos 3 caracteres',
                'max_length' => 'O nome deve ter no máximo 255 caracteres'
            ],
            'crm' => [
                'required' => 'O CRM é obrigatório',
                'min_length' => 'O CRM deve ter pelo menos 4 caracteres',
                'max_length' => 'O CRM deve ter no máximo 20 caracteres',
                'is_unique' => 'Este CRM já está cadastrado'
            ],
            'especialidade' => [
                'required' => 'A especialidade é obrigatória',
                'max_length' => 'A especialidade deve ter no máximo 100 caracteres'
            ],
            'telefone' => [
                'max_length' => 'O telefone deve ter no máximo 20 caracteres'
            ],
            'email' => [
                'valid_email' => 'O e-mail deve ter um formato válido',
                'max_length' => 'O e-mail deve ter no máximo 255 caracteres'
            ],
            'status' => [
                'required' => 'O status é obrigatório',
                'in_list' => 'Status deve ser Ativo ou Inativo'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            // Se é uma requisição AJAX, retornar JSON
            if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro de validação',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'nome' => $this->request->getPost('nome'),
            'crm' => $this->request->getPost('crm'),
            'especialidade' => $this->request->getPost('especialidade'),
            'telefone' => $this->request->getPost('telefone'),
            'email' => $this->request->getPost('email'),
            'status' => $this->request->getPost('status')
        ];

        if ($this->medicoModel->save($data)) {
            // Se é uma requisição AJAX, retornar JSON
            if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                $medicoId = $this->medicoModel->getInsertID();
                $medicoSalvo = $this->medicoModel->find($medicoId);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Médico cadastrado com sucesso!',
                    'medico' => $medicoSalvo
                ]);
            }
            return redirect()->to('/medicos')->with('success', 'Médico cadastrado com sucesso!');
        } else {
            // Se é uma requisição AJAX, retornar JSON
            if ($this->request->isAJAX() || $this->request->getHeaderLine('X-Requested-With') === 'XMLHttpRequest') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao cadastrar médico'
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Erro ao cadastrar médico.');
        }
    }

    /**
     * Exibe detalhes de um médico específico
     */
    public function show($id = null)
    {
        $medico = $this->medicoModel->find($id);
        
        if (!$medico) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Médico não encontrado');
        }

        // Buscar estatísticas de atendimentos deste médico
        $atendimentoModel = new \App\Models\AtendimentoModel();
        $totalAtendimentos = $atendimentoModel->where('id_medico', $id)->countAllResults();
        $atendimentosHoje = $atendimentoModel->where('id_medico', $id)
                                            ->where('DATE(data_atendimento)', date('Y-m-d'))
                                            ->countAllResults();
        $atendimentosMes = $atendimentoModel->where('id_medico', $id)
                                           ->where('MONTH(data_atendimento)', date('m'))
                                           ->where('YEAR(data_atendimento)', date('Y'))
                                           ->countAllResults();

        $data = [
            'title' => 'Detalhes do Médico',
            'description' => 'Informações do Médico: ' . $medico['nome'],
            'medico' => $medico,
            'stats' => [
                'total_atendimentos' => $totalAtendimentos,
                'atendimentos_hoje' => $atendimentosHoje,
                'atendimentos_mes' => $atendimentosMes
            ]
        ];

        return view('medicos/show', $data);
    }

    /**
     * Exibe formulário para editar médico
     */
    public function edit($id = null)
    {
        $medico = $this->medicoModel->find($id);
        
        if (!$medico) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Médico não encontrado');
        }

        $data = [
            'title' => 'Editar Médico',
            'description' => 'Editar Médico: ' . $medico['nome'],
            'medico' => $medico
        ];

        return view('medicos/edit', $data);
    }

    /**
     * Atualiza dados do médico
     */
    public function update($id = null)
    {
        $medico = $this->medicoModel->find($id);
        
        if (!$medico) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Médico não encontrado');
        }

        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'crm' => "required|min_length[4]|max_length[20]|is_unique[medicos.crm,id_medico,{$id}]",
            'especialidade' => 'permit_empty|max_length[100]',
            'status' => 'required|in_list[Ativo,Inativo]'
        ];

        $messages = [
            'nome' => [
                'required' => 'O nome do médico é obrigatório',
                'min_length' => 'O nome deve ter pelo menos 3 caracteres',
                'max_length' => 'O nome deve ter no máximo 255 caracteres'
            ],
            'crm' => [
                'required' => 'O CRM é obrigatório',
                'min_length' => 'O CRM deve ter pelo menos 4 caracteres',
                'max_length' => 'O CRM deve ter no máximo 20 caracteres',
                'is_unique' => 'Este CRM já está cadastrado'
            ],
            'especialidade' => [
                'max_length' => 'A especialidade deve ter no máximo 100 caracteres'
            ],
            'status' => [
                'required' => 'O status é obrigatório',
                'in_list' => 'Status deve ser Ativo ou Inativo'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'nome' => $this->request->getPost('nome'),
            'crm' => $this->request->getPost('crm'),
            'especialidade' => $this->request->getPost('especialidade'),
            'status' => $this->request->getPost('status')
        ];

        if ($this->medicoModel->update($id, $data)) {
            return redirect()->to('/medicos')->with('success', 'Médico atualizado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar médico.');
        }
    }

    /**
     * Exclui um médico (soft delete)
     */
    public function delete($id = null)
    {
        $medico = $this->medicoModel->find($id);
        
        if (!$medico) {
            return $this->response->setJSON(['error' => 'Médico não encontrado'])->setStatusCode(404);
        }

        // Verificar se o médico tem atendimentos vinculados
        $atendimentoModel = new \App\Models\AtendimentoModel();
        $atendimentos = $atendimentoModel->where('id_medico', $id)->countAllResults();
        
        if ($atendimentos > 0) {
            return $this->response->setJSON([
                'error' => 'Não é possível excluir este médico pois ele possui atendimentos vinculados.'
            ])->setStatusCode(400);
        }

        if ($this->medicoModel->delete($id)) {
            return $this->response->setJSON(['success' => 'Médico excluído com sucesso!']);
        } else {
            return $this->response->setJSON(['error' => 'Erro ao excluir médico'])->setStatusCode(500);
        }
    }

    /**
     * Busca médicos via AJAX
     */
    public function search()
    {
        $term = $this->request->getPost('term') ?? '';
        
        $medicos = $this->medicoModel->like('nome', $term)
                                   ->orLike('crm', $term)
                                   ->orLike('especialidade', $term)
                                   ->where('status', 'Ativo')
                                   ->orderBy('nome', 'ASC')
                                   ->findAll();

        return $this->response->setJSON($medicos);
    }

    /**
     * Retorna médicos ativos para uso em selects
     */
    public function getAtivos()
    {
        $medicos = $this->medicoModel->where('status', 'Ativo')
                                   ->orderBy('nome', 'ASC')
                                   ->findAll();

        return $this->response->setJSON($medicos);
    }
}
