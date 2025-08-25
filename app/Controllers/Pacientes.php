<?php

namespace App\Controllers;

use App\Models\PacienteModel;
use App\Models\BairroModel;
use App\Models\LogradouroModel;
use App\Models\AtendimentoModel;
use App\Models\AtendimentoExameModel;
use App\Models\AtendimentoProcedimentoModel;
use CodeIgniter\Controller;

class Pacientes extends BaseController
{
    protected $pacienteModel;
    protected $bairroModel;
    protected $logradouroModel;
    protected $atendimentoModel;
    protected $atendimentoExameModel;
    protected $atendimentoProcedimentoModel;

    public function __construct()
    {
        $this->pacienteModel = new PacienteModel();
        $this->bairroModel = new BairroModel();
        $this->logradouroModel = new LogradouroModel();
        $this->atendimentoModel = new AtendimentoModel();
        $this->atendimentoExameModel = new AtendimentoExameModel();
        $this->atendimentoProcedimentoModel = new AtendimentoProcedimentoModel();
    }

    /**
     * Lista todos os pacientes
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $perPage = 20; // Definir quantos registros por página
        
        if ($search) {
            $pacientes = $this->pacienteModel->buscarPacientesPaginated($search, $perPage);
        } else {
            $pacientes = $this->pacienteModel->getPacientesWithLogradouroPaginated($perPage);
        }

        // Obter o objeto pager
        $pager = $this->pacienteModel->pager;

        // Estatísticas - criar novas instâncias do model para cada consulta
        $stats = [
            'total' => $this->pacienteModel->countAllResults(),
            'hoje' => $this->pacienteModel->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'mes' => $this->pacienteModel->where('MONTH(created_at)', date('m'))
                                        ->where('YEAR(created_at)', date('Y'))
                                        ->countAllResults(),
            'idade_media' => round(
                $this->pacienteModel
                    ->select('(AVG(TIMESTAMPDIFF(YEAR, data_nascimento, CURDATE()))) as idade_media')
                    ->where('data_nascimento IS NOT NULL')
                    ->first()['idade_media'] ?? 0
            )
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
            'pager' => $pager,
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
            'cpf' => 'permit_empty|exact_length[14]|is_unique[pacientes.cpf]', // Alterado de required para permit_empty
            'data_nascimento' => 'required|valid_date',
            'sexo' => 'required|in_list[M,F]',
            'email' => 'permit_empty|valid_email',
            'numero_sus' => 'permit_empty|max_length[15]',
            'telefone' => 'permit_empty|max_length[15]',
            'celular' => 'permit_empty|max_length[16]',
            'numero' => 'permit_empty|max_length[10]',
            'complemento' => 'permit_empty|max_length[100]',
            'id_logradouro' => 'permit_empty|is_natural_no_zero',
            'observacoes' => 'permit_empty|max_length[1000]',
            'cidade_externa' => 'permit_empty|max_length[100]',
            'logradouro_externo' => 'permit_empty|max_length[255]',
            'cep_externo' => 'permit_empty|max_length[10]'
        ];

        $messages = [
            'nome' => [
                'required' => 'O nome é obrigatório.',
                'min_length' => 'O nome deve ter pelo menos 3 caracteres.',
                'max_length' => 'O nome deve ter no máximo 255 caracteres.'
            ],
            'cpf' => [
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
            // Se for uma requisição AJAX, retornar JSON
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Dados inválidos',
                    'errors' => $this->validator->getErrors()
                ]);
            }
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
            'numero' => $this->request->getPost('numero'),
            'complemento' => $this->request->getPost('complemento'),
            'id_logradouro' => $id_logradouro,
            'tipo_sanguineo' => $this->request->getPost('tipo_sanguineo'),
            'nome_responsavel' => $this->request->getPost('nome_responsavel'),
            'nome_mae' => $this->request->getPost('nome_mae'),
            'nome_pai' => $this->request->getPost('nome_pai'),
            'alergias' => $this->request->getPost('alergias'),
            'observacoes' => $this->request->getPost('observacoes'),
            'cidade_externa' => $this->request->getPost('cidade_externa'),
            'logradouro_externo' => $this->request->getPost('logradouro_externo'),
            'cep_externo' => $this->request->getPost('cep_externo')
        ];

        if ($this->pacienteModel->save($data)) {
            $pacienteId = $this->pacienteModel->getInsertID();
            
            // Se for uma requisição AJAX, retornar JSON
            if ($this->request->isAJAX()) {
                // Buscar o paciente recém-criado
                $paciente = $this->pacienteModel->find($pacienteId);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Paciente cadastrado com sucesso!',
                    'paciente' => [
                        'id_paciente' => $paciente['id_paciente'],
                        'nome' => $paciente['nome'],
                        'cpf' => $paciente['cpf']
                    ]
                ]);
            }
            
            // Verificar se deve retornar para atendimento
            if ($this->request->getPost('return_to_atendimento')) {
                return redirect()->to('atendimentos/create?paciente=' . $pacienteId)
                    ->with('success', 'Paciente cadastrado com sucesso!');
            }
            
            return redirect()->to('pacientes')->with('success', 'Paciente cadastrado com sucesso!');
        } else {
            // Se for uma requisição AJAX, retornar JSON
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao cadastrar paciente. Tente novamente.'
                ]);
            }
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

        // Buscar atendimentos do paciente
        $atendimentos = $this->atendimentoModel->getAtendimentosCompletosByPaciente($id);

        // Buscar exames do paciente
        $exames = $this->atendimentoExameModel->getExamesByPaciente($id);

        // Buscar procedimentos do paciente
        $procedimentos = $this->atendimentoProcedimentoModel->getProcedimentosByPaciente($id);

        // Estatísticas do paciente
        $stats = [
            'total_atendimentos' => count($atendimentos),
            'exames_realizados' => count($exames),
            'procedimentos_realizados' => count($procedimentos),
            'ultimo_atendimento' => !empty($atendimentos) 
                ? date('d/m/Y', strtotime($atendimentos[0]['data_atendimento'])) 
                : 'Nunca'
        ];

        $data = [
            'title' => 'Detalhes do Paciente',
            'description' => 'Visualizar Paciente',
            'paciente' => $paciente,
            'atendimentos' => $atendimentos,
            'exames' => $exames,
            'procedimentos' => $procedimentos,
            'stats' => $stats
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
            'cpf' => "permit_empty|exact_length[14]|is_unique[pacientes.cpf,id_paciente,{$id}]", // Alterado de required para permit_empty
            'data_nascimento' => 'required|valid_date',
            'sexo' => 'required|in_list[M,F]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'numero_sus' => 'permit_empty|max_length[15]',
            'telefone' => 'permit_empty|max_length[15]',
            'celular' => 'permit_empty|max_length[16]',
            'numero' => 'permit_empty|max_length[10]',
            'complemento' => 'permit_empty|max_length[100]',
            'rg' => 'permit_empty|max_length[20]',
            'id_logradouro' => 'permit_empty|is_natural_no_zero',
            'tipo_sanguineo' => 'permit_empty|max_length[5]',
            'nome_responsavel' => 'permit_empty|max_length[255]',
            'nome_mae' => 'permit_empty|max_length[255]',
            'nome_pai' => 'permit_empty|max_length[255]',
            'alergias' => 'permit_empty|max_length[1000]',
            'observacoes' => 'permit_empty|max_length[1000]',
            'cidade_externa' => 'permit_empty|max_length[100]',
            'logradouro_externo' => 'permit_empty|max_length[255]',
            'cep_externo' => 'permit_empty|max_length[10]'
        ];

        $messages = [
            'nome' => [
                'required' => 'O nome é obrigatório.',
                'min_length' => 'O nome deve ter pelo menos 3 caracteres.',
                'max_length' => 'O nome deve ter no máximo 255 caracteres.'
            ],
            'cpf' => [
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
            'numero' => $this->request->getPost('numero'),
            'complemento' => $this->request->getPost('complemento'),
            'id_logradouro' => $this->request->getPost('id_logradouro') ?? null,
            'tipo_sanguineo' => $this->request->getPost('tipo_sanguineo'),
            'nome_responsavel' => $this->request->getPost('nome_responsavel'),
            'nome_mae' => $this->request->getPost('nome_mae'),
            'nome_pai' => $this->request->getPost('nome_pai'),
            'alergias' => $this->request->getPost('alergias'),
            'observacoes' => $this->request->getPost('observacoes'),
            'cidade_externa' => $this->request->getPost('cidade_externa'),
            'logradouro_externo' => $this->request->getPost('logradouro_externo'),
            'cep_externo' => $this->request->getPost('cep_externo')
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

        // Verificar se tem atendimentos vinculados (incluindo soft deleted)
        $db = \Config\Database::connect();
        $atendimentos = $db->table('atendimentos')
                          ->where('id_paciente', $id)
                          ->where('deleted_at IS NULL') // Apenas atendimentos não excluídos
                          ->countAllResults();

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
     * Verifica se o paciente possui atendimentos (via AJAX)
     */
    public function checkAtendimentos($id)
    {
        $db = \Config\Database::connect();
        $count = $db->table('atendimentos')
                   ->where('id_paciente', $id)
                   ->where('deleted_at IS NULL')
                   ->countAllResults();

        return $this->response->setJSON([
            'hasAtendimentos' => $count > 0,
            'count' => $count
        ]);
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
            
            // Montar endereço completo baseado no logradouro
            $endereco_completo = '';
            if (!empty($paciente['nome_logradouro'])) {
                $endereco_completo = ($paciente['tipo_logradouro'] ?? '') . ' ' . $paciente['nome_logradouro'];
                if (!empty($paciente['numero'])) {
                    $endereco_completo .= ', ' . $paciente['numero'];
                }
                if (!empty($paciente['complemento'])) {
                    $endereco_completo .= ' - ' . $paciente['complemento'];
                }
            }
            echo '<td>' . $endereco_completo . '</td>';
            
            echo '<td>' . ($paciente['nome_bairro'] ?? '') . '</td>';
            echo '<td>' . ($paciente['cidade'] ?? '') . '</td>';
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

    /**
     * Lista pacientes de cidades externas
     */
    public function pacientesExternos()
    {
        $cidadeFiltro = $this->request->getGet('cidade');
        $search = $this->request->getGet('search');

        // Buscar pacientes externos
        if ($cidadeFiltro) {
            $pacientes = $this->pacienteModel->getPacientesExternosPorCidade($cidadeFiltro);
        } else {
            $pacientes = $this->pacienteModel->getPacientesExternos();
        }

        // Filtrar por busca se especificado
        if ($search && !empty($pacientes)) {
            $pacientes = array_filter($pacientes, function($paciente) use ($search) {
                return stripos($paciente['nome'], $search) !== false ||
                       stripos($paciente['cpf'], $search) !== false ||
                       stripos($paciente['cidade_externa'], $search) !== false;
            });
        }

        // Estatísticas das cidades externas
        $estatisticasCidades = $this->pacienteModel->getEstatisticasCidadesExternas();
        $totalExternos = $this->pacienteModel->countPacientesExternos();

        // Preparar lista de cidades para filtro
        $cidades = array_column($estatisticasCidades, 'cidade_externa');

        $data = [
            'title' => 'Pacientes de Outras Cidades',
            'description' => 'Lista de pacientes cadastrados em cidades externas.',
            'pacientes' => $pacientes,
            'estatisticas' => $estatisticasCidades,
            'total_externos' => $totalExternos,
            'cidades' => $cidades,
            'cidade_filtro' => $cidadeFiltro,
            'search' => $search
        ];

        return view('pacientes/externos', $data);
    }

    /**
     * Exibe relatórios mensais de pacientes
     */
    public function relatorios()
    {
        $mes = $this->request->getGet('mes') ?? date('m');
        $ano = $this->request->getGet('ano') ?? date('Y');

        $data = [
            'title' => 'Relatórios de Pacientes',
            'description' => 'Relatórios mensais por idade, enfermagem e médico',
            'mes_selecionado' => $mes,
            'ano_selecionado' => $ano,
            'relatorio_idade' => $this->getRelatorioIdade($mes, $ano),
            'relatorio_enfermagem' => $this->getRelatorioEnfermagem($mes, $ano),
            'relatorio_medico' => $this->getRelatorioMedico($mes, $ano),
            'meses' => [
                '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
                '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
                '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
            ],
            'anos_disponiveis' => $this->getAnosDisponiveis()
        ];

        return view('pacientes/relatorios', $data);
    }

    /**
     * Gera dados do relatório de pacientes por faixa etária
     */
    public function getRelatorioIdade($mes, $ano)
    {
        $db = \Config\Database::connect();
        
        $query = "
            SELECT 
                CASE 
                    WHEN TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) BETWEEN 0 AND 12 THEN '0-12 anos'
                    WHEN TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) BETWEEN 13 AND 17 THEN '13-17 anos'
                    WHEN TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) BETWEEN 18 AND 30 THEN '18-30 anos'
                    WHEN TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) BETWEEN 31 AND 50 THEN '31-50 anos'
                    WHEN TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) BETWEEN 51 AND 70 THEN '51-70 anos'
                    WHEN TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) > 70 THEN 'Acima de 70'
                    ELSE 'Idade não informada'
                END as faixa_etaria,
                COUNT(DISTINCT p.id_paciente) as total_pacientes
            FROM pam_pacientes p
            INNER JOIN pam_atendimentos a ON p.id_paciente = a.id_paciente
            WHERE MONTH(a.data_atendimento) = ? 
            AND YEAR(a.data_atendimento) = ?
            AND p.deleted_at IS NULL
            AND a.deleted_at IS NULL
            GROUP BY faixa_etaria
            ORDER BY 
                CASE 
                    WHEN faixa_etaria = '0-12 anos' THEN 1
                    WHEN faixa_etaria = '13-17 anos' THEN 2
                    WHEN faixa_etaria = '18-30 anos' THEN 3
                    WHEN faixa_etaria = '31-50 anos' THEN 4
                    WHEN faixa_etaria = '51-70 anos' THEN 5
                    WHEN faixa_etaria = 'Acima de 70' THEN 6
                    ELSE 7
                END
        ";

        return $db->query($query, [$mes, $ano])->getResultArray();
    }

    /**
     * Gera dados do relatório de pacientes que passaram pela enfermagem
     */
    public function getRelatorioEnfermagem($mes, $ano)
    {
        $db = \Config\Database::connect();
        
        $query = "
            SELECT 
                COUNT(DISTINCT p.id_paciente) as pacientes_enfermagem,
                COUNT(DISTINCT CASE WHEN a.consulta_enfermagem IS NOT NULL AND a.consulta_enfermagem != '' THEN p.id_paciente END) as com_consulta_enfermagem,
                COUNT(DISTINCT CASE WHEN a.consulta_enfermagem IS NULL OR a.consulta_enfermagem = '' THEN p.id_paciente END) as sem_consulta_enfermagem,
                AVG(TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE())) as idade_media_enfermagem
            FROM pam_pacientes p
            INNER JOIN pam_atendimentos a ON p.id_paciente = a.id_paciente
            WHERE MONTH(a.data_atendimento) = ? 
            AND YEAR(a.data_atendimento) = ?
            AND p.deleted_at IS NULL
            AND a.deleted_at IS NULL
        ";

        $resultado = $db->query($query, [$mes, $ano])->getRowArray();

        // Buscar detalhes por classificação de risco para enfermagem
        $queryClassificacao = "
            SELECT 
                COALESCE(a.classificacao_risco, 'Sem classificação') as classificacao,
                COUNT(DISTINCT p.id_paciente) as total_pacientes
            FROM pam_pacientes p
            INNER JOIN pam_atendimentos a ON p.id_paciente = a.id_paciente
            WHERE MONTH(a.data_atendimento) = ? 
            AND YEAR(a.data_atendimento) = ?
            AND p.deleted_at IS NULL
            AND a.deleted_at IS NULL
            AND (a.consulta_enfermagem IS NOT NULL AND a.consulta_enfermagem != '')
            GROUP BY a.classificacao_risco
            ORDER BY total_pacientes DESC
        ";

        $classificacoes = $db->query($queryClassificacao, [$mes, $ano])->getResultArray();

        return array_merge($resultado, ['classificacoes' => $classificacoes]);
    }

    /**
     * Gera dados do relatório de pacientes que passaram pelo médico
     */
    public function getRelatorioMedico($mes, $ano)
    {
        $db = \Config\Database::connect();
        
        $query = "
            SELECT 
                COUNT(DISTINCT p.id_paciente) as pacientes_medico,
                COUNT(DISTINCT m.id_medico) as medicos_ativos,
                AVG(TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE())) as idade_media_medico
            FROM pam_pacientes p
            INNER JOIN pam_atendimentos a ON p.id_paciente = a.id_paciente
            INNER JOIN pam_medicos m ON a.id_medico = m.id_medico
            WHERE MONTH(a.data_atendimento) = ? 
            AND YEAR(a.data_atendimento) = ?
            AND p.deleted_at IS NULL
            AND a.deleted_at IS NULL
            AND m.deleted_at IS NULL
        ";

        $resultado = $db->query($query, [$mes, $ano])->getRowArray();

        // Buscar ranking de médicos por atendimentos
        $queryMedicos = "
            SELECT 
                m.nome as nome_medico,
                m.crm,
                m.especialidade,
                COUNT(DISTINCT p.id_paciente) as total_pacientes,
                COUNT(a.id_atendimento) as total_atendimentos
            FROM pam_pacientes p
            INNER JOIN pam_atendimentos a ON p.id_paciente = a.id_paciente
            INNER JOIN pam_medicos m ON a.id_medico = m.id_medico
            WHERE MONTH(a.data_atendimento) = ? 
            AND YEAR(a.data_atendimento) = ?
            AND p.deleted_at IS NULL
            AND a.deleted_at IS NULL
            AND m.deleted_at IS NULL
            GROUP BY m.id_medico, m.nome, m.crm, m.especialidade
            ORDER BY total_pacientes DESC
            LIMIT 10
        ";

        $medicos = $db->query($queryMedicos, [$mes, $ano])->getResultArray();

        // Buscar dados por encaminhamento
        $queryEncaminhamentos = "
            SELECT 
                COALESCE(a.encaminhamento, 'Não informado') as encaminhamento,
                COUNT(DISTINCT p.id_paciente) as total_pacientes
            FROM pam_pacientes p
            INNER JOIN pam_atendimentos a ON p.id_paciente = a.id_paciente
            INNER JOIN pam_medicos m ON a.id_medico = m.id_medico
            WHERE MONTH(a.data_atendimento) = ? 
            AND YEAR(a.data_atendimento) = ?
            AND p.deleted_at IS NULL
            AND a.deleted_at IS NULL
            AND m.deleted_at IS NULL
            GROUP BY a.encaminhamento
            ORDER BY total_pacientes DESC
        ";

        $encaminhamentos = $db->query($queryEncaminhamentos, [$mes, $ano])->getResultArray();

        return array_merge($resultado, [
            'ranking_medicos' => $medicos,
            'encaminhamentos' => $encaminhamentos
        ]);
    }

    /**
     * Retorna anos disponíveis com base nos atendimentos
     */
    private function getAnosDisponiveis()
    {
        $db = \Config\Database::connect();
        
        $query = "
            SELECT DISTINCT YEAR(data_atendimento) as ano
            FROM pam_atendimentos
            WHERE deleted_at IS NULL
            ORDER BY ano DESC
        ";

        $resultado = $db->query($query)->getResultArray();
        
        return array_column($resultado, 'ano');
    }

    /**
     * API para obter dados dos relatórios em JSON
     */
    public function relatoriosApi()
    {
        $mes = $this->request->getGet('mes') ?? date('m');
        $ano = $this->request->getGet('ano') ?? date('Y');
        $tipo = $this->request->getGet('tipo');

        $response = [];

        switch ($tipo) {
            case 'idade':
                $response = $this->getRelatorioIdade($mes, $ano);
                break;
            case 'enfermagem':
                $response = $this->getRelatorioEnfermagem($mes, $ano);
                break;
            case 'medico':
                $response = $this->getRelatorioMedico($mes, $ano);
                break;
            default:
                $response = [
                    'idade' => $this->getRelatorioIdade($mes, $ano),
                    'enfermagem' => $this->getRelatorioEnfermagem($mes, $ano),
                    'medico' => $this->getRelatorioMedico($mes, $ano)
                ];
                break;
        }

        return $this->response->setJSON($response);
    }

    /**
     * Exporta relatórios para Excel
     */
    public function exportarRelatorios()
    {
        $mes = $this->request->getPost('mes') ?? date('m');
        $ano = $this->request->getPost('ano') ?? date('Y');

        $relatorioIdade = $this->getRelatorioIdade($mes, $ano);
        $relatorioEnfermagem = $this->getRelatorioEnfermagem($mes, $ano);
        $relatorioMedico = $this->getRelatorioMedico($mes, $ano);

        $meses = [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
            '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
            '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
        ];

        $nomeArquivo = "relatorio_pacientes_{$meses[$mes]}_{$ano}.xls";

        // Headers para download
        header('Content-Type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=\"{$nomeArquivo}\"");
        header('Cache-Control: max-age=0');

        echo '<html><head><meta charset="UTF-8"></head><body>';
        echo "<h1>Relatório de Pacientes - {$meses[$mes]} {$ano}</h1>";
        echo "<br><br>";

        // Relatório por Faixa Etária
        echo '<h2>1. Pacientes por Faixa Etária</h2>';
        echo '<table border="1" cellpadding="5" cellspacing="0">';
        echo '<tr style="background-color: #f8f9fa;">';
        echo '<th>Faixa Etária</th><th>Total de Pacientes</th><th>Percentual</th>';
        echo '</tr>';

        if (!empty($relatorioIdade)) {
            $totalGeral = array_sum(array_column($relatorioIdade, 'total_pacientes'));
            foreach ($relatorioIdade as $item) {
                $percentual = $totalGeral > 0 ? round(($item['total_pacientes'] / $totalGeral) * 100, 1) : 0;
                echo '<tr>';
                echo '<td>' . $item['faixa_etaria'] . '</td>';
                echo '<td style="text-align: right;">' . number_format($item['total_pacientes']) . '</td>';
                echo '<td style="text-align: right;">' . $percentual . '%</td>';
                echo '</tr>';
            }
            echo '<tr style="background-color: #e9ecef; font-weight: bold;">';
            echo '<td>TOTAL</td>';
            echo '<td style="text-align: right;">' . number_format($totalGeral) . '</td>';
            echo '<td style="text-align: right;">100%</td>';
            echo '</tr>';
        } else {
            echo '<tr><td colspan="3" style="text-align: center;">Nenhum dado encontrado</td></tr>';
        }
        echo '</table>';
        echo '<br><br>';

        // Relatório de Enfermagem
        echo '<h2>2. Atendimentos de Enfermagem</h2>';
        echo '<table border="1" cellpadding="5" cellspacing="0">';
        echo '<tr style="background-color: #f8f9fa;">';
        echo '<th>Métrica</th><th>Valor</th>';
        echo '</tr>';

        if (!empty($relatorioEnfermagem)) {
            echo '<tr><td>Pacientes com consulta de enfermagem</td><td style="text-align: right;">' . 
                 number_format($relatorioEnfermagem['com_consulta_enfermagem']) . '</td></tr>';
            echo '<tr><td>Pacientes sem consulta de enfermagem</td><td style="text-align: right;">' . 
                 number_format($relatorioEnfermagem['sem_consulta_enfermagem']) . '</td></tr>';
            echo '<tr><td>Idade média dos pacientes</td><td style="text-align: right;">' . 
                 round($relatorioEnfermagem['idade_media_enfermagem'], 1) . ' anos</td></tr>';
        }
        echo '</table>';

        // Classificações de Risco na Enfermagem
        if (!empty($relatorioEnfermagem['classificacoes'])) {
            echo '<br><h3>2.1. Classificação de Risco na Enfermagem</h3>';
            echo '<table border="1" cellpadding="5" cellspacing="0">';
            echo '<tr style="background-color: #f8f9fa;">';
            echo '<th>Classificação</th><th>Total de Pacientes</th>';
            echo '</tr>';
            foreach ($relatorioEnfermagem['classificacoes'] as $classificacao) {
                echo '<tr>';
                echo '<td>' . $classificacao['classificacao'] . '</td>';
                echo '<td style="text-align: right;">' . number_format($classificacao['total_pacientes']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
        echo '<br><br>';

        // Relatório de Médicos
        echo '<h2>3. Atendimentos Médicos</h2>';
        echo '<table border="1" cellpadding="5" cellspacing="0">';
        echo '<tr style="background-color: #f8f9fa;">';
        echo '<th>Métrica</th><th>Valor</th>';
        echo '</tr>';

        if (!empty($relatorioMedico)) {
            echo '<tr><td>Total de pacientes atendidos</td><td style="text-align: right;">' . 
                 number_format($relatorioMedico['pacientes_medico']) . '</td></tr>';
            echo '<tr><td>Médicos ativos no período</td><td style="text-align: right;">' . 
                 number_format($relatorioMedico['medicos_ativos']) . '</td></tr>';
            echo '<tr><td>Idade média dos pacientes</td><td style="text-align: right;">' . 
                 round($relatorioMedico['idade_media_medico'], 1) . ' anos</td></tr>';
        }
        echo '</table>';

        // Ranking de Médicos
        if (!empty($relatorioMedico['ranking_medicos'])) {
            echo '<br><h3>3.1. Ranking de Médicos por Atendimentos</h3>';
            echo '<table border="1" cellpadding="5" cellspacing="0">';
            echo '<tr style="background-color: #f8f9fa;">';
            echo '<th>Posição</th><th>Médico</th><th>CRM</th><th>Especialidade</th><th>Pacientes</th><th>Atendimentos</th>';
            echo '</tr>';
            foreach ($relatorioMedico['ranking_medicos'] as $index => $medico) {
                echo '<tr>';
                echo '<td style="text-align: center;">' . ($index + 1) . 'º</td>';
                echo '<td>' . $medico['nome_medico'] . '</td>';
                echo '<td>' . $medico['crm'] . '</td>';
                echo '<td>' . ($medico['especialidade'] ?? 'Não informado') . '</td>';
                echo '<td style="text-align: right;">' . number_format($medico['total_pacientes']) . '</td>';
                echo '<td style="text-align: right;">' . number_format($medico['total_atendimentos']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        // Encaminhamentos
        if (!empty($relatorioMedico['encaminhamentos'])) {
            echo '<br><h3>3.2. Tipos de Encaminhamento</h3>';
            echo '<table border="1" cellpadding="5" cellspacing="0">';
            echo '<tr style="background-color: #f8f9fa;">';
            echo '<th>Tipo de Encaminhamento</th><th>Total de Pacientes</th>';
            echo '</tr>';
            foreach ($relatorioMedico['encaminhamentos'] as $encaminhamento) {
                echo '<tr>';
                echo '<td>' . $encaminhamento['encaminhamento'] . '</td>';
                echo '<td style="text-align: right;">' . number_format($encaminhamento['total_pacientes']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }

        echo '<br><br>';
        echo '<p><em>Relatório gerado em: ' . date('d/m/Y H:i:s') . '</em></p>';
        echo '</body></html>';
        exit;
    }
}
