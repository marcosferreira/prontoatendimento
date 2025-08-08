<?php

namespace App\Controllers;

use App\Models\BairroModel;
use CodeIgniter\Controller;

class Bairros extends BaseController
{
    protected $bairroModel;

    public function __construct()
    {
        $this->bairroModel = new BairroModel();
    }

    /**
     * Lista todos os bairros
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        
        if ($search) {
            $bairros = $this->bairroModel->like('nome_bairro', $search)
                                       ->orLike('area', $search)
                                       ->orderBy('nome_bairro', 'ASC')
                                       ->findAll();
        } else {
            $bairros = $this->bairroModel->getBairrosWithPacientesCount();
        }

        // Estatísticas
        $stats = [
            'total' => $this->bairroModel->countAllResults(),
            'hoje' => $this->bairroModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'mes' => $this->bairroModel->where('MONTH(created_at)', date('m'))
                                      ->where('YEAR(created_at)', date('Y'))
                                      ->countAllResults(),
            'ano' => $this->bairroModel->where('YEAR(created_at)', date('Y'))->countAllResults()
        ];

        $bairros = array_map(function($bairro) {
            $bairro['total_pacientes'] = $this->bairroModel->getTotalPacientesByBairro($bairro['id_bairro']);
            return $bairro;
        }, $bairros);

        $data = [
            'title' => 'Bairros',
            'description' => 'Gerenciar Bairros',
            'bairros' => $bairros,
            'stats' => $stats,
            'search' => $search,
        ];

        return view('bairros/index', $data);
    }

    /**
     * Exibe formulário para criar novo bairro
     */
    public function create()
    {
        $data = [
            'title' => 'Novo Bairro',
            'description' => 'Cadastrar Novo Bairro'
        ];

        return view('bairros/create', $data);
    }

    /**
     * Salva um novo bairro
     */
    public function store()
    {
        $rules = [
            'nome_bairro' => 'required|min_length[3]|max_length[100]',
            'area' => 'permit_empty|max_length[100]'
        ];

        $messages = [
            'nome_bairro' => [
                'required' => 'O nome do bairro é obrigatório',
                'min_length' => 'O nome do bairro deve ter pelo menos 3 caracteres',
                'max_length' => 'O nome do bairro deve ter no máximo 100 caracteres'
            ],
            'area' => [
                'max_length' => 'A área deve ter no máximo 100 caracteres'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'nome_bairro' => $this->request->getPost('nome_bairro'),
            'area' => $this->request->getPost('area')
        ];

        if ($this->bairroModel->save($data)) {
            return redirect()->to('bairros')->with('success', 'Bairro cadastrado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erro ao cadastrar bairro.');
        }
    }

    /**
     * Exibe detalhes de um bairro
     */
    public function show($id)
    {
        $bairro = $this->bairroModel->find($id);

        if (!$bairro) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Bairro não encontrado');
        }

        // Buscar pacientes do bairro
        $pacienteModel = new \App\Models\PacienteModel();
        $pacientes = $pacienteModel->getPacientesByBairro($id);

        // Buscar logradouros do bairro
        $logradouros = $this->bairroModel->getLogradouros($id);

        $data = [
            'title' => 'Detalhes do Bairro',
            'description' => 'Visualizar Bairro',
            'bairro' => $bairro,
            'pacientes' => $pacientes,
            'logradouros' => $logradouros
        ];

        return view('bairros/show', $data);
    }

    /**
     * Exibe formulário para editar bairro
     */
    public function edit($id)
    {
        $bairro = $this->bairroModel->find($id);

        if (!$bairro) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Bairro não encontrado');
        }

        $data = [
            'title' => 'Editar Bairro',
            'description' => 'Editar Dados do Bairro',
            'bairro' => $bairro
        ];

        return view('bairros/edit', $data);
    }

    /**
     * Atualiza dados do bairro
     */
    public function update($id)
    {
        $bairro = $this->bairroModel->find($id);

        if (!$bairro) {
            return redirect()->to('bairros')->with('error', 'Bairro não encontrado.');
        }

        $rules = [
            'nome_bairro' => 'required|min_length[3]|max_length[100]',
            'area' => 'permit_empty|max_length[100]'
        ];

        $messages = [
            'nome_bairro' => [
                'required' => 'O nome do bairro é obrigatório',
                'min_length' => 'O nome do bairro deve ter pelo menos 3 caracteres',
                'max_length' => 'O nome do bairro deve ter no máximo 100 caracteres'
            ],
            'area' => [
                'max_length' => 'A área deve ter no máximo 100 caracteres'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $data = [
            'nome_bairro' => $this->request->getPost('nome_bairro'),
            'area' => $this->request->getPost('area')
        ];

        if ($this->bairroModel->update($id, $data)) {
            return redirect()->to('bairros')->with('success', 'Bairro atualizado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar bairro.');
        }
    }

    /**
     * Exclui um bairro
     */
    public function delete($id)
    {
        $bairro = $this->bairroModel->find($id);

        if (!$bairro) {
            return redirect()->to('bairros')->with('error', 'Bairro não encontrado.');
        }

        try {
            if ($this->bairroModel->delete($id)) {
                return redirect()->to('bairros')->with('success', 'Bairro excluído com sucesso!');
            } else {
                return redirect()->to('bairros')->with('error', 'Erro ao excluir bairro.');
            }
        } catch (\RuntimeException $e) {
            return redirect()->to('bairros')->with('error', $e->getMessage());
        }
    }

    /**
     * Busca bairros via AJAX
     */
    public function search()
    {
        $term = $this->request->getGet('term');
        
        if (strlen($term) < 2) {
            return $this->response->setJSON([]);
        }

        $bairros = $this->bairroModel->like('nome_bairro', $term)
                                   ->orLike('area', $term)
                                   ->orderBy('nome_bairro', 'ASC')
                                   ->findAll(10);
        
        $result = [];
        foreach ($bairros as $bairro) {
            $result[] = [
                'id' => $bairro['id_bairro'],
                'label' => $bairro['nome_bairro'] . ($bairro['area'] ? ' - ' . $bairro['area'] : ''),
                'value' => $bairro['nome_bairro'],
                'area' => $bairro['area']
            ];
        }

        return $this->response->setJSON($result);
    }

    /**
     * Valida nome do bairro via AJAX
     */
    public function validateNome()
    {
        $nome = $this->request->getPost('nome_bairro');
        $id = $this->request->getPost('id');

        if (!$nome) {
            return $this->response->setJSON(['valid' => false, 'message' => 'Nome não informado']);
        }

        $query = $this->bairroModel->where('nome_bairro', $nome);
        
        if ($id) {
            $query->where('id_bairro !=', $id);
        }
        
        $exists = $query->first();

        if ($exists) {
            return $this->response->setJSON(['valid' => false, 'message' => 'Este nome já está sendo usado']);
        }

        return $this->response->setJSON(['valid' => true]);
    }

    /**
     * Exporta lista de bairros
     */
    public function export()
    {
        $bairros = $this->bairroModel->getBairrosWithPacientesCount();

        // Headers para download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=bairros_' . date('Y-m-d') . '.csv');

        // Abrir output
        $output = fopen('php://output', 'w');

        // BOM para UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Cabeçalho
        fputcsv($output, [
            'ID',
            'Nome do Bairro',
            'Área',
            'Total de Pacientes',
            'Cadastrado em'
        ]);

        // Dados
        foreach ($bairros as $bairro) {
            fputcsv($output, [
                $bairro['id_bairro'],
                $bairro['nome_bairro'],
                $bairro['area'] ?? '',
                $bairro['total_pacientes'] ?? 0,
                date('d/m/Y H:i', strtotime($bairro['created_at']))
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Exibe modal de visualização rápida
     */
    public function modal($id)
    {
        $bairro = $this->bairroModel->find($id);

        if (!$bairro) {
            return $this->response->setStatusCode(404, 'Bairro não encontrado');
        }

        // Buscar pacientes do bairro
        $pacienteModel = new \App\Models\PacienteModel();
        $pacientes = $pacienteModel->getPacientesByBairro($id);

        $data = [
            'bairro' => $bairro,
            'pacientes' => $pacientes
        ];

        return view('bairros/modal_view', $data);
    }
}
