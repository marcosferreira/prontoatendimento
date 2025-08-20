<?php

namespace App\Controllers;

use App\Models\LogradouroModel;
use App\Models\BairroModel;
use CodeIgniter\Controller;

class Logradouros extends BaseController
{
    protected $logradouroModel;
    protected $bairroModel;

    public function __construct()
    {
        $this->logradouroModel = new LogradouroModel();
        $this->bairroModel = new BairroModel();
    }

    /**
     * Lista todos os logradouros
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $bairro = $this->request->getGet('bairro');
        
        if ($search || $bairro) {
            if ($search && $bairro) {
                $logradouros = $this->logradouroModel->select('logradouros.*, bairros.nome_bairro, bairros.area')
                                                   ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro')
                                                   ->where('logradouros.id_bairro', $bairro)
                                                   ->groupStart()
                                                       ->like('logradouros.nome_logradouro', $search)
                                                       ->orLike('logradouros.cep', $search)
                                                   ->groupEnd()
                                                   ->orderBy('logradouros.tipo_logradouro, logradouros.nome_logradouro')
                                                   ->paginate(10);
            } elseif ($search) {
                $logradouros = $this->logradouroModel->select('logradouros.*, bairros.nome_bairro, bairros.area')
                                                   ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro')
                                                   ->groupStart()
                                                       ->like('logradouros.nome_logradouro', $search)
                                                       ->orLike('logradouros.cep', $search)
                                                       ->orLike('bairros.nome_bairro', $search)
                                                   ->groupEnd()
                                                   ->orderBy('logradouros.tipo_logradouro, logradouros.nome_logradouro')
                                                   ->paginate(10);
            } else {
                $logradouros = $this->logradouroModel->select('logradouros.*, bairros.nome_bairro, bairros.area')
                                                   ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro')
                                                   ->where('logradouros.id_bairro', $bairro)
                                                   ->orderBy('logradouros.tipo_logradouro, logradouros.nome_logradouro')
                                                   ->paginate(10);
            }
        } else {
            $logradouros = $this->logradouroModel->select('logradouros.*, bairros.nome_bairro, bairros.area')
                                               ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro')
                                               ->orderBy('logradouros.tipo_logradouro, logradouros.nome_logradouro')
                                               ->paginate(10);
        }

        // Estatísticas
        $stats = [
            'total' => $this->logradouroModel->countAllResults(),
            'hoje' => $this->logradouroModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'mes' => $this->logradouroModel->where('MONTH(created_at)', date('m'))
                                          ->where('YEAR(created_at)', date('Y'))
                                          ->countAllResults(),
            'ano' => $this->logradouroModel->where('YEAR(created_at)', date('Y'))->countAllResults()
        ];

        // Lista de bairros para filtro
        $bairros = $this->bairroModel->orderBy('nome_bairro')->findAll();

        $data = [
            'title' => 'Logradouros',
            'description' => 'Gerenciar Logradouros',
            'pager' => $this->logradouroModel->pager,
            'logradouros' => $logradouros,
            'bairros' => $bairros,
            'stats' => $stats,
            'search' => $search,
            'bairro_selecionado' => $bairro,
        ];

        return view('logradouros/index', $data);
    }

    /**
     * Exibe formulário para criar novo logradouro
     */
    public function create()
    {
        $bairros = $this->bairroModel->orderBy('nome_bairro')->findAll();
        $tipos = $this->logradouroModel->getTiposLogradouro();
        $estados = $this->logradouroModel->getEstados();
        $bairroSelecionado = $this->request->getGet('bairro'); // Bairro pré-selecionado

        $data = [
            'title' => 'Novo Logradouro',
            'description' => 'Cadastrar Novo Logradouro',
            'bairros' => $bairros,
            'tipos' => $tipos,
            'estados' => $estados,
            'bairro_selecionado' => $bairroSelecionado
        ];

        return view('logradouros/create', $data);
    }

    /**
     * Salva um novo logradouro
     */
    public function store()
    {
        $rules = [
            'nome_logradouro' => 'required|min_length[3]|max_length[150]',
            'tipo_logradouro' => 'required|in_list[Rua,Avenida,Travessa,Alameda,Praça,Estrada,Sítio,Rodovia,Via,Beco,Largo]',
            'cep' => 'permit_empty|max_length[9]',
            'cidade' => 'permit_empty|max_length[100]',
            'estado' => 'required|in_list[AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO]',
            'id_bairro' => 'required|is_natural_no_zero',
            'observacoes' => 'permit_empty'
        ];

        $messages = [
            'nome_logradouro' => [
                'required' => 'O nome do logradouro é obrigatório',
                'min_length' => 'O nome do logradouro deve ter pelo menos 3 caracteres',
                'max_length' => 'O nome do logradouro deve ter no máximo 150 caracteres'
            ],
            'tipo_logradouro' => [
                'required' => 'O tipo do logradouro é obrigatório',
                'in_list' => 'Tipo de logradouro inválido'
            ],
            'cep' => [
                'max_length' => 'O CEP deve ter no máximo 9 caracteres'
            ],
            'cidade' => [
                'max_length' => 'O nome da cidade deve ter no máximo 100 caracteres'
            ],
            'estado' => [
                'required' => 'O estado é obrigatório',
                'in_list' => 'Estado inválido. Deve ser uma sigla válida (AC, AL, AM, etc.)'
            ],
            'id_bairro' => [
                'required' => 'O bairro é obrigatório',
                'is_natural_no_zero' => 'Bairro inválido'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Verificar se o bairro existe
        $bairro = $this->bairroModel->find($this->request->getPost('id_bairro'));
        if (!$bairro) {
            return redirect()->back()->withInput()->with('error', 'Bairro selecionado não encontrado.');
        }

        $data = [
            'nome_logradouro' => $this->request->getPost('nome_logradouro'),
            'tipo_logradouro' => $this->request->getPost('tipo_logradouro'),
            'cep' => $this->request->getPost('cep'),
            'cidade' => $this->request->getPost('cidade'),
            'estado' => $this->request->getPost('estado'),
            'id_bairro' => $this->request->getPost('id_bairro'),
            'observacoes' => $this->request->getPost('observacoes')
        ];

        if ($this->logradouroModel->save($data)) {
            return redirect()->to('logradouros')->with('success', 'Logradouro cadastrado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erro ao cadastrar logradouro.');
        }
    }

    /**
     * Exibe detalhes de um logradouro
     */
    public function show($id)
    {
        $logradouro = $this->logradouroModel->getLogradouroWithBairro($id);

        if (!$logradouro) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Logradouro não encontrado');
        }

        $data = [
            'title' => 'Detalhes do Logradouro',
            'description' => 'Visualizar Logradouro',
            'logradouro' => $logradouro
        ];

        return view('logradouros/show', $data);
    }

    /**
     * Exibe formulário para editar logradouro
     */
    public function edit($id)
    {
        $logradouro = $this->logradouroModel->find($id);

        if (!$logradouro) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Logradouro não encontrado');
        }

        $bairros = $this->bairroModel->orderBy('nome_bairro')->findAll();
        $tipos = $this->logradouroModel->getTiposLogradouro();
        $estados = $this->logradouroModel->getEstados();

        $data = [
            'title' => 'Editar Logradouro',
            'description' => 'Editar Dados do Logradouro',
            'logradouro' => $logradouro,
            'bairros' => $bairros,
            'tipos' => $tipos,
            'estados' => $estados
        ];

        return view('logradouros/edit', $data);
    }

    /**
     * Atualiza um logradouro
     */
    public function update($id)
    {
        $logradouro = $this->logradouroModel->find($id);

        if (!$logradouro) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Logradouro não encontrado');
        }

        $rules = [
            'nome_logradouro' => 'required|min_length[3]|max_length[150]',
            'tipo_logradouro' => 'required|in_list[Rua,Avenida,Travessa,Alameda,Praça,Estrada,Sítio,Rodovia,Via,Beco,Largo]',
            'cep' => 'permit_empty|max_length[9]',
            'cidade' => 'permit_empty|max_length[100]',
            'estado' => 'required|in_list[AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO]',
            'id_bairro' => 'required|is_natural_no_zero',
            'observacoes' => 'permit_empty'
        ];

        $messages = [
            'nome_logradouro' => [
                'required' => 'O nome do logradouro é obrigatório',
                'min_length' => 'O nome do logradouro deve ter pelo menos 3 caracteres',
                'max_length' => 'O nome do logradouro deve ter no máximo 150 caracteres'
            ],
            'tipo_logradouro' => [
                'required' => 'O tipo do logradouro é obrigatório',
                'in_list' => 'Tipo de logradouro inválido'
            ],
            'cep' => [
                'max_length' => 'O CEP deve ter no máximo 9 caracteres'
            ],
            'cidade' => [
                'max_length' => 'O nome da cidade deve ter no máximo 100 caracteres'
            ],
            'estado' => [
                'required' => 'O estado é obrigatório',
                'in_list' => 'Estado inválido. Deve ser uma sigla válida (AC, AL, AM, etc.)'
            ],
            'id_bairro' => [
                'required' => 'O bairro é obrigatório',
                'is_natural_no_zero' => 'Bairro inválido'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Verificar se o bairro existe
        $bairro = $this->bairroModel->find($this->request->getPost('id_bairro'));
        if (!$bairro) {
            return redirect()->back()->withInput()->with('error', 'Bairro selecionado não encontrado.');
        }

        $data = [
            'nome_logradouro' => $this->request->getPost('nome_logradouro'),
            'tipo_logradouro' => $this->request->getPost('tipo_logradouro'),
            'cep' => $this->request->getPost('cep'),
            'cidade' => $this->request->getPost('cidade'),
            'estado' => $this->request->getPost('estado'),
            'id_bairro' => $this->request->getPost('id_bairro'),
            'observacoes' => $this->request->getPost('observacoes')
        ];

        if ($this->logradouroModel->update($id, $data)) {
            return redirect()->to('logradouros')->with('success', 'Logradouro atualizado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar logradouro.');
        }
    }

    /**
     * Exclui um logradouro
     */
    public function delete($id)
    {
        $logradouro = $this->logradouroModel->find($id);

        if (!$logradouro) {
            return redirect()->back()->with('error', 'Logradouro não encontrado.');
        }

        if ($this->logradouroModel->delete($id)) {
            return redirect()->to('logradouros')->with('success', 'Logradouro excluído com sucesso!');
        } else {
            return redirect()->back()->with('error', 'Erro ao excluir logradouro.');
        }
    }

    /**
     * API: Busca logradouros por bairro (JSON)
     */
    public function getByBairro($idBairro)
    {
        $logradouros = $this->logradouroModel->getLogradourosByBairro($idBairro);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $logradouros
        ]);
    }

    /**
     * API: Busca logradouros por CEP (JSON)
     */
    public function getByCep($cep)
    {
        $logradouros = $this->logradouroModel->getLogradourosByCep($cep);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $logradouros
        ]);
    }

    /**
     * API: Busca logradouros (JSON)
     */
    public function search()
    {
        $termo = $this->request->getGet('q');
        
        if (!$termo) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Termo de busca obrigatório'
            ]);
        }

        $logradouros = $this->logradouroModel->searchLogradouros($termo);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $logradouros
        ]);
    }

    /**
     * Exportar logradouros para CSV
     */
    public function export()
    {
        $logradouros = $this->logradouroModel->getLogradourosWithBairro();
        
        $filename = 'logradouros_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Cabeçalho do CSV
        fputcsv($output, [
            'ID',
            'Tipo',
            'Nome do Logradouro',
            'CEP',
            'Cidade',
            'Estado',
            'Bairro',
            'Área',
            'Observações',
            'Data de Cadastro'
        ]);
        
        // Dados
        foreach ($logradouros as $logradouro) {
            fputcsv($output, [
                $logradouro['id_logradouro'],
                $logradouro['tipo_logradouro'],
                $logradouro['nome_logradouro'],
                $logradouro['cep'] ?? '',
                $logradouro['cidade'] ?? '',
                $logradouro['estado'] ?? '',
                $logradouro['nome_bairro'],
                $logradouro['area'] ?? '',
                $logradouro['observacoes'] ?? '',
                date('d/m/Y H:i', strtotime($logradouro['created_at']))
            ]);
        }
        
        fclose($output);
        exit;
    }
}
