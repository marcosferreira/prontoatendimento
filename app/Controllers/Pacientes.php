<?php

namespace App\Controllers;

use App\Models\PacienteModel;
use App\Models\BairroModel;
use App\Models\LogradouroModel;
use CodeIgniter\Controller;

class Pacientes extends BaseController
{
    protected $pacienteModel;
    protected $bairroModel;
    protected $logradouroModel;

    public function __construct()
    {
        $this->pacienteModel = new PacienteModel();
        $this->bairroModel = new BairroModel();
        $this->logradouroModel = new LogradouroModel();
    }

    /**
     * Lista todos os pacientes
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        
        if ($search) {
            $pacientes = $this->pacienteModel->buscarPacientes($search);
        } else {
            $pacientes = $this->pacienteModel->getPacientesWithLogradouro();
        }

        // Estatísticas
        $stats = [
            'total' => $this->pacienteModel->countAll(),
            'hoje' => $this->pacienteModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'mes' => $this->pacienteModel->where('MONTH(created_at)', date('m'))
                                        ->where('YEAR(created_at)', date('Y'))
                                        ->countAllResults(),
            'ano' => $this->pacienteModel->where('YEAR(created_at)', date('Y'))->countAllResults()
        ];

        $bairros = $this->bairroModel->findAll();
        $logradouros = $this->logradouroModel->select('logradouros.*, bairros.nome_bairro')
                                            ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro')
                                            ->orderBy('bairros.nome_bairro', 'ASC')
                                            ->orderBy('logradouros.nome_logradouro', 'ASC')
                                            ->findAll();

        $data = [
            'title' => 'Pacientes',
            'description' => 'Gerenciar Pacientes',
            'pacientes' => $pacientes,
            'stats' => $stats,
            'bairros' => $bairros,
            'logradouros' => $logradouros,
            'search' => $search
        ];

        return view('pacientes/index', $data);
    }

    /**
     * Exibe formulário para criar novo paciente
     */
    public function create()
    {
        $bairros = $this->bairroModel->orderBy('nome_bairro', 'ASC')->findAll();
        $logradouros = $this->logradouroModel->select('logradouros.*, bairros.nome_bairro')
                                            ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro')
                                            ->orderBy('bairros.nome_bairro', 'ASC')
                                            ->orderBy('logradouros.nome_logradouro', 'ASC')
                                            ->findAll();
        
        // Capturar bairro pré-selecionado da URL
        $bairroSelecionado = $this->request->getGet('bairro');
        $logradouroSelecionado = $this->request->getGet('logradouro');

        $data = [
            'title' => 'Novo Paciente',
            'description' => 'Cadastrar Novo Paciente',
            'bairros' => $bairros,
            'logradouros' => $logradouros,
            'bairro_selecionado' => $bairroSelecionado,
            'logradouro_selecionado' => $logradouroSelecionado
        ];

        return view('pacientes/create', $data);
    }

    /**
     * Salva um novo paciente
     */
    public function store()
    {
        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'cpf' => 'required|exact_length[14]|is_unique[pacientes.cpf]',
            'data_nascimento' => 'required|valid_date',
            'sexo' => 'required|in_list[M,F]',
            'email' => 'permit_empty|valid_email',
            'numero_sus' => 'permit_empty|max_length[15]',
            'telefone' => 'permit_empty|max_length[15]',
            'celular' => 'permit_empty|max_length[16]',
            'endereco' => 'permit_empty|max_length[500]',
            'id_logradouro' => 'permit_empty|is_natural_no_zero',
            'observacoes' => 'permit_empty|max_length[1000]'
        ];

        $messages = [
            'nome' => [
                'required' => 'O nome é obrigatório.',
                'min_length' => 'O nome deve ter pelo menos 3 caracteres.',
                'max_length' => 'O nome deve ter no máximo 255 caracteres.'
            ],
            'cpf' => [
                'required' => 'O CPF é obrigatório.',
                'exact_length' => 'O CPF deve ter 14 caracteres (formato: 000.000.000-00).',
                'is_unique' => 'Este CPF já está cadastrado no sistema.'
            ],
            'data_nascimento' => [
                'required' => 'A data de nascimento é obrigatória.',
                'valid_date' => 'Data de nascimento inválida.'
            ],
            'sexo' => [
                'required' => 'O sexo é obrigatório.',
                'in_list' => 'Sexo deve ser M (Masculino) ou F (Feminino).'
            ],
            'email' => [
                'valid_email' => 'E-mail inválido.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $id_bairro = $this->request->getPost('id_bairro') ?? $this->request->getGet('bairro');
        $id_bairro = ($id_bairro && $id_bairro !== '') ? $id_bairro : null;

        $id_logradouro = $this->request->getPost('id_logradouro') ?? $this->request->getGet('logradouro');
        $id_logradouro = ($id_logradouro && $id_logradouro !== '') ? $id_logradouro : null;

        $data = [
            'nome' => $this->request->getPost('nome'),
            'cpf' => $this->request->getPost('cpf'),
            'rg' => $this->request->getPost('rg'),
            'data_nascimento' => $this->request->getPost('data_nascimento'),
            'sexo' => $this->request->getPost('sexo'),
            'telefone' => $this->request->getPost('telefone'),
            'celular' => $this->request->getPost('celular'),
            'email' => $this->request->getPost('email'),
            'numero_sus' => $this->request->getPost('numero_sus'),
            'endereco' => $this->request->getPost('endereco'),
            'numero' => $this->request->getPost('numero'),
            'complemento' => $this->request->getPost('complemento'),
            'cep' => $this->request->getPost('cep'),
            'cidade' => $this->request->getPost('cidade'),
            'id_logradouro' => $id_logradouro,
            'tipo_sanguineo' => $this->request->getPost('tipo_sanguineo'),
            'nome_responsavel' => $this->request->getPost('nome_responsavel'),
            'alergias' => $this->request->getPost('alergias'),
            'observacoes' => $this->request->getPost('observacoes')
        ];

        if ($this->pacienteModel->save($data)) {
            return redirect()->to('pacientes')->with('success', 'Paciente cadastrado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erro ao cadastrar paciente.');
        }
    }

    /**
     * Exibe detalhes de um paciente
     */
    public function show($id)
    {
        $paciente = $this->pacienteModel->getPacienteWithLogradouro($id);

        if (!$paciente) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Paciente não encontrado');
        }

        $data = [
            'title' => 'Detalhes do Paciente',
            'description' => 'Visualizar Paciente',
            'paciente' => $paciente
        ];

        return view('pacientes/show', $data);
    }

    /**
     * Exibe formulário para editar paciente
     */
    public function edit($id)
    {
        $paciente = $this->pacienteModel->find($id);

        if (!$paciente) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Paciente não encontrado');
        }

        $bairros = $this->bairroModel->orderBy('nome_bairro', 'ASC')->findAll();
        $logradouros = $this->logradouroModel->select('logradouros.*, bairros.nome_bairro')
                                            ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro')
                                            ->orderBy('bairros.nome_bairro', 'ASC')
                                            ->orderBy('logradouros.nome_logradouro', 'ASC')
                                            ->findAll();

        $data = [
            'title' => 'Editar Paciente',
            'description' => 'Editar Dados do Paciente',
            'paciente' => $paciente,
            'bairros' => $bairros,
            'logradouros' => $logradouros
        ];

        return view('pacientes/edit', $data);
    }

    /**
     * Atualiza dados do paciente
     */
    public function update($id)
    {
        $paciente = $this->pacienteModel->find($id);

        if (!$paciente) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Paciente não encontrado');
        }

        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'cpf' => "required|exact_length[14]|is_unique[pacientes.cpf,id_paciente,{$id}]",
            'data_nascimento' => 'required|valid_date',
            'sexo' => 'required|in_list[M,F]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'numero_sus' => 'permit_empty|max_length[15]',
            'telefone' => 'permit_empty|max_length[15]',
            'celular' => 'permit_empty|max_length[16]',
            'endereco' => 'permit_empty|max_length[500]',
            'numero' => 'permit_empty|max_length[10]',
            'complemento' => 'permit_empty|max_length[100]',
            'cep' => 'permit_empty|max_length[9]',
            'cidade' => 'permit_empty|max_length[100]',
            'rg' => 'permit_empty|max_length[20]',
            'id_logradouro' => 'permit_empty|is_natural_no_zero',
            'tipo_sanguineo' => 'permit_empty|max_length[5]',
            'nome_responsavel' => 'permit_empty|max_length[255]',
            'alergias' => 'permit_empty|max_length[1000]',
            'observacoes' => 'permit_empty|max_length[1000]'
        ];

        $messages = [
            'nome' => [
                'required' => 'O nome é obrigatório.',
                'min_length' => 'O nome deve ter pelo menos 3 caracteres.',
                'max_length' => 'O nome deve ter no máximo 255 caracteres.'
            ],
            'cpf' => [
                'required' => 'O CPF é obrigatório.',
                'exact_length' => 'O CPF deve ter 14 caracteres (formato: 000.000.000-00).',
                'is_unique' => 'Este CPF já está cadastrado para outro paciente.'
            ],
            'data_nascimento' => [
                'required' => 'A data de nascimento é obrigatória.',
                'valid_date' => 'Data de nascimento inválida.'
            ],
            'sexo' => [
                'required' => 'O sexo é obrigatório.',
                'in_list' => 'Sexo deve ser M (Masculino) ou F (Feminino).'
            ],
            'email' => [
                'valid_email' => 'E-mail inválido.'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'nome' => $this->request->getPost('nome'),
            'cpf' => $this->request->getPost('cpf'),
            'rg' => $this->request->getPost('rg'),
            'data_nascimento' => $this->request->getPost('data_nascimento'),
            'sexo' => $this->request->getPost('sexo'),
            'telefone' => $this->request->getPost('telefone'),
            'celular' => $this->request->getPost('celular'),
            'email' => $this->request->getPost('email'),
            'numero_sus' => $this->request->getPost('numero_sus'),
            'endereco' => $this->request->getPost('endereco'),
            'numero' => $this->request->getPost('numero'),
            'complemento' => $this->request->getPost('complemento'),
            'cep' => $this->request->getPost('cep'),
            'cidade' => $this->request->getPost('cidade'),
            'id_logradouro' => $this->request->getPost('id_logradouro') ?? null,
            'tipo_sanguineo' => $this->request->getPost('tipo_sanguineo'),
            'nome_responsavel' => $this->request->getPost('nome_responsavel'),
            'alergias' => $this->request->getPost('alergias'),
            'observacoes' => $this->request->getPost('observacoes')
        ];

        if ($this->pacienteModel->update($id, $data)) {
            return redirect()->to('pacientes')->with('success', 'Paciente atualizado com sucesso!');
        } else {
            // Capturar erros detalhados do modelo
            $errors = $this->pacienteModel->errors();
            $errorMessage = !empty($errors) ? implode(', ', $errors) : 'Erro desconhecido ao atualizar paciente.';
            
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar paciente: ' . $errorMessage);
        }
    }

    /**
     * Exclui um paciente
     */
    public function delete($id)
    {
        $paciente = $this->pacienteModel->find($id);

        if (!$paciente) {
            return redirect()->to('pacientes')->with('error', 'Paciente não encontrado.');
        }

        // Verificar se tem atendimentos vinculados
        $db = \Config\Database::connect();
        $atendimentos = $db->table('atendimentos')->where('id_paciente', $id)->countAllResults();

        if ($atendimentos > 0) {
            return redirect()->to('pacientes')->with('error', 'Não é possível excluir este paciente pois possui atendimentos vinculados.');
        }

        if ($this->pacienteModel->delete($id)) {
            return redirect()->to('pacientes')->with('success', 'Paciente excluído com sucesso!');
        } else {
            return redirect()->to('pacientes')->with('error', 'Erro ao excluir paciente.');
        }
    }

    /**
     * Busca pacientes via AJAX
     */
    public function search()
    {
        $term = $this->request->getGet('term');
        
        if (strlen($term) < 2) {
            return $this->response->setJSON([]);
        }

        $pacientes = $this->pacienteModel->buscarPacientes($term, 10);
        
        $result = [];
        foreach ($pacientes as $paciente) {
            $result[] = [
                'id' => $paciente['id_paciente'],
                'label' => $paciente['nome'] . ' - ' . $paciente['cpf'],
                'value' => $paciente['nome'],
                'cpf' => $paciente['cpf'],
                'idade' => $paciente['idade'],
                'data_nascimento' => $paciente['data_nascimento']
            ];
        }

        return $this->response->setJSON($result);
    }

    /**
     * Exibe modal de visualização rápida
     */
    public function modal($id)
    {
        $paciente = $this->pacienteModel->getPacienteWithLogradouro($id);

        if (!$paciente) {
            return $this->response->setStatusCode(404, 'Paciente não encontrado');
        }

        // Buscar atendimentos recentes do paciente (últimos 5)
        $atendimentoModel = new \App\Models\AtendimentoModel();
        $atendimentos_recentes = $atendimentoModel->select('atendimentos.*, medicos.nome as nome_medico')
                                                 ->join('medicos', 'medicos.id_medico = atendimentos.id_medico', 'left')
                                                 ->where('atendimentos.id_paciente', $id)
                                                 ->orderBy('atendimentos.data_atendimento', 'DESC')
                                                 ->limit(5)
                                                 ->findAll();

        $data = [
            'paciente' => $paciente,
            'atendimentos_recentes' => $atendimentos_recentes
        ];

        return view('pacientes/modal_view', $data);
    }

    /**
     * Gera relatório para impressão
     */
    public function print($id)
    {
        $paciente = $this->pacienteModel->getPacienteWithLogradouro($id);

        if (!$paciente) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Paciente não encontrado');
        }

        $data = [
            'paciente' => $paciente
        ];

        return view('pacientes/print', $data);
    }

    /**
     * Valida CPF via AJAX
     */
    public function validateCpf()
    {
        $cpf = $this->request->getPost('cpf');
        $id = $this->request->getPost('id');

        if (!$cpf) {
            return $this->response->setJSON(['valid' => false, 'message' => 'CPF não informado']);
        }

        // Verificar se já existe
        $query = $this->pacienteModel->where('cpf', $cpf);
        
        if ($id) {
            $query->where('id_paciente !=', $id);
        }
        
        $exists = $query->first();

        if ($exists) {
            return $this->response->setJSON([
                'valid' => false, 
                'message' => 'Este CPF já está cadastrado para outro paciente'
            ]);
        }

        return $this->response->setJSON(['valid' => true]);
    }

    /**
     * Exporta lista de pacientes para Excel
     */
    public function export()
    {
        $pacientes = $this->pacienteModel->getPacientesWithLogradouro();

        // Headers para download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="pacientes_' . date('Y-m-d') . '.xls"');
        header('Cache-Control: max-age=0');

        // Cabeçalho da tabela
        echo '<table border="1">';
        echo '<tr>';
        echo '<th>ID</th>';
        echo '<th>Nome</th>';
        echo '<th>CPF</th>';
        echo '<th>Data Nascimento</th>';
        echo '<th>Idade</th>';
        echo '<th>Sexo</th>';
        echo '<th>Telefone</th>';
        echo '<th>Celular</th>';
        echo '<th>Email</th>';
        echo '<th>Endereço</th>';
        echo '<th>Logradouro</th>';
        echo '<th>Bairro</th>';
        echo '<th>Cadastrado em</th>';
        echo '</tr>';

        // Dados
        foreach ($pacientes as $paciente) {
            echo '<tr>';
            echo '<td>' . $paciente['id_paciente'] . '</td>';
            echo '<td>' . $paciente['nome'] . '</td>';
            echo '<td>' . $paciente['cpf'] . '</td>';
            echo '<td>' . date('d/m/Y', strtotime($paciente['data_nascimento'])) . '</td>';
            echo '<td>' . $paciente['idade'] . '</td>';
            echo '<td>' . ($paciente['sexo'] == 'M' ? 'Masculino' : 'Feminino') . '</td>';
            echo '<td>' . $paciente['telefone'] . '</td>';
            echo '<td>' . $paciente['celular'] . '</td>';
            echo '<td>' . $paciente['email'] . '</td>';
            echo '<td>' . $paciente['endereco'] . '</td>';
            echo '<td>' . ($paciente['nome_logradouro'] ?? '') . '</td>';
            echo '<td>' . ($paciente['nome_bairro'] ?? '') . '</td>';
            echo '<td>' . date('d/m/Y H:i', strtotime($paciente['created_at'])) . '</td>';
            echo '</tr>';
        }

        echo '</table>';
        exit;
    }

    /**
     * Busca logradouros por bairro via AJAX
     */
    public function getLogradourosByBairro()
    {
        $idBairro = $this->request->getGet('id_bairro');
        
        if (!$idBairro) {
            return $this->response->setJSON([]);
        }

        $logradouros = $this->logradouroModel->where('id_bairro', $idBairro)
                                            ->orderBy('nome_logradouro', 'ASC')
                                            ->findAll();

        return $this->response->setJSON($logradouros);
    }
}
