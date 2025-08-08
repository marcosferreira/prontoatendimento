<?php

namespace App\Controllers;

use App\Models\AtendimentoModel;
use App\Models\PacienteModel;
use App\Models\MedicoModel;
use App\Models\ProcedimentoModel;
use App\Models\ExameModel;
use App\Models\AtendimentoProcedimentoModel;
use App\Models\AtendimentoExameModel;
use App\Models\BairroModel;
use App\Models\LogradouroModel;
use CodeIgniter\Controller;

class Atendimentos extends BaseController
{
    protected $atendimentoModel;
    protected $pacienteModel;
    protected $medicoModel;
    protected $procedimentoModel;
    protected $exameModel;
    protected $atendimentoProcedimentoModel;
    protected $atendimentoExameModel;
    protected $bairroModel;
    protected $logradouroModel;

    public function __construct()
    {
        $this->atendimentoModel = new AtendimentoModel();
        $this->pacienteModel = new PacienteModel();
        $this->medicoModel = new MedicoModel();
        $this->procedimentoModel = new ProcedimentoModel();
        $this->exameModel = new ExameModel();
        $this->atendimentoProcedimentoModel = new AtendimentoProcedimentoModel();
        $this->atendimentoExameModel = new AtendimentoExameModel();
        $this->bairroModel = new BairroModel();
        $this->logradouroModel = new LogradouroModel();
    }

    /**
     * Lista todos os atendimentos
     */
    public function index()
    {
        $search = $this->request->getGet('search');
        $classificacao = $this->request->getGet('classificacao');
        $data_inicio = $this->request->getGet('data_inicio');
        $data_fim = $this->request->getGet('data_fim');
        $medico = $this->request->getGet('medico');
        
        $query = $this->atendimentoModel->select('atendimentos.*, pacientes.nome as paciente_nome, pacientes.cpf, medicos.nome as medico_nome, medicos.crm')
                                       ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
                                       ->join('medicos', 'medicos.id_medico = atendimentos.id_medico');
        
        if ($search) {
            $query = $query->groupStart()
                          ->like('pacientes.nome', $search)
                          ->orLike('pacientes.cpf', $search)
                          ->orLike('medicos.nome', $search)
                          ->orLike('medicos.crm', $search)
                          ->groupEnd();
        }
        
        if ($classificacao) {
            $query = $query->where('pam_atendimentos.classificacao_risco', $classificacao);
        }
        
        if ($data_inicio) {
            $query = $query->where('DATE(pam_atendimentos.data_atendimento) >=', $data_inicio);
        }
        
        if ($data_fim) {
            $query = $query->where('DATE(pam_atendimentos.data_atendimento) <=', $data_fim);
        }
        
        if ($medico) {
            $query = $query->where('pam_atendimentos.id_medico', $medico);
        }
        
        $atendimentos = $query->orderBy('pam_atendimentos.data_atendimento', 'DESC')->findAll();

        // Estatísticas
        $stats = [
            'total' => $this->atendimentoModel->countAllResults(),
            'hoje' => $this->atendimentoModel->where('DATE(data_atendimento)', date('Y-m-d'))->countAllResults(),
            'mes' => $this->atendimentoModel->where('MONTH(data_atendimento)', date('m'))
                                           ->where('YEAR(data_atendimento)', date('Y'))
                                           ->countAllResults(),
            'vermelho' => $this->atendimentoModel->where('classificacao_risco', 'Vermelho')->countAllResults(),
            'laranja' => $this->atendimentoModel->where('classificacao_risco', 'Laranja')->countAllResults(),
            'amarelo' => $this->atendimentoModel->where('classificacao_risco', 'Amarelo')->countAllResults(),
            'verde' => $this->atendimentoModel->where('classificacao_risco', 'Verde')->countAllResults(),
            'azul' => $this->atendimentoModel->where('classificacao_risco', 'Azul')->countAllResults(),
            'obitos' => $this->atendimentoModel->where('obito', true)->countAllResults()
        ];

        // Buscar médicos para filtro
        $medicos = $this->medicoModel->where('status', 'Ativo')->orderBy('nome', 'ASC')->findAll();

        $data = [
            'title' => 'Atendimentos',
            'description' => 'Gerenciar Atendimentos',
            'atendimentos' => $atendimentos,
            'stats' => $stats,
            'medicos' => $medicos,
            'search' => $search,
            'classificacao_filtro' => $classificacao,
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
            'medico_filtro' => $medico,
            'classificacoes' => ['Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul'],
            'encaminhamentos' => ['Alta', 'Internação', 'Transferência', 'Especialista', 'Retorno', 'Óbito']
        ];

        return view('atendimentos/index', $data);
    }

    /**
     * Exibe formulário para criar novo atendimento
     */
    public function create()
    {
        $pacientes = $this->pacienteModel->orderBy('nome', 'ASC')->findAll();
        $medicos = $this->medicoModel->where('status', 'Ativo')->orderBy('nome', 'ASC')->findAll();
        $procedimentos = $this->procedimentoModel->orderBy('nome', 'ASC')->findAll();
        $exames = $this->exameModel->orderBy('nome', 'ASC')->findAll();
        
        // Buscar bairros e logradouros para a modal de cadastro de paciente
        $bairros = $this->bairroModel->orderBy('nome_bairro', 'ASC')->findAll();
        $logradouros = $this->logradouroModel->select('pam_logradouros.id_logradouro, pam_logradouros.nome_logradouro, pam_logradouros.tipo_logradouro, pam_logradouros.id_bairro, pam_bairros.nome_bairro as bairro_nome')
                                            ->join('pam_bairros', 'pam_bairros.id_bairro = pam_logradouros.id_bairro')
                                            ->orderBy('pam_logradouros.nome_logradouro', 'ASC')
                                            ->findAll();

        $data = [
            'title' => 'Novo Atendimento',
            'description' => 'Cadastrar Novo Atendimento',
            'pacientes' => $pacientes,
            'medicos' => $medicos,
            'procedimentos' => $procedimentos,
            'exames' => $exames,
            'bairros' => $bairros,
            'logradouros' => $logradouros,
            'classificacoes' => ['Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul'],
            'encaminhamentos' => ['Alta', 'Internação', 'Transferência', 'Especialista', 'Retorno', 'Óbito'],
            'status_opcoes' => ['Em Andamento', 'Finalizado', 'Cancelado', 'Aguardando', 'Suspenso'],
            'paciente_observacao_opcoes' => ['Sim', 'Não']
        ];

        return view('atendimentos/create', $data);
    }

    /**
     * Salva um novo atendimento
     */
    public function store()
    {
        $rules = [
            'id_paciente' => 'required|integer|is_not_unique[pacientes.id_paciente]',
            'id_medico' => 'required|integer|is_not_unique[medicos.id_medico]',
            'data_atendimento' => 'required|regex_match[/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/]',
            'classificacao_risco' => 'required|in_list[Vermelho,Laranja,Amarelo,Verde,Azul]',
            'consulta_enfermagem' => 'permit_empty',
            'hgt_glicemia' => 'permit_empty|decimal',
            'pressao_arterial' => 'permit_empty|max_length[20]',
            'temperatura' => 'permit_empty|decimal',
            'hipotese_diagnostico' => 'permit_empty',
            'observacao' => 'permit_empty',
            'encaminhamento' => 'permit_empty|in_list[Alta,Internação,Transferência,Especialista,Retorno,Óbito]',
            'obito' => 'permit_empty|in_list[0,1]',
            'status' => 'permit_empty|in_list[Em Andamento,Finalizado,Cancelado,Aguardando,Suspenso]',
            'paciente_observacao' => 'permit_empty|in_list[Sim,Não]'
        ];

        $messages = [
            'id_paciente' => [
                'required' => 'O paciente é obrigatório',
                'integer' => 'ID do paciente deve ser um número',
                'is_not_unique' => 'Paciente não encontrado'
            ],
            'id_medico' => [
                'required' => 'O médico é obrigatório',
                'integer' => 'ID do médico deve ser um número',
                'is_not_unique' => 'Médico não encontrado'
            ],
            'data_atendimento' => [
                'required' => 'A data do atendimento é obrigatória',
                'regex_match' => 'Data do atendimento deve estar no formato AAAA-MM-DDTHH:MM (ex: 2024-12-25T14:30). Use o seletor de data/hora.'
            ],
            'classificacao_risco' => [
                'required' => 'A classificação de risco é obrigatória',
                'in_list' => 'Classificação deve ser: Vermelho, Laranja, Amarelo, Verde ou Azul'
            ],
            'hgt_glicemia' => [
                'decimal' => 'HGT/Glicemia deve ser um número decimal'
            ],
            'pressao_arterial' => [
                'max_length' => 'Pressão arterial deve ter no máximo 20 caracteres'
            ],
            'temperatura' => [
                'decimal' => 'Temperatura deve ser um número decimal'
            ],
            'encaminhamento' => [
                'in_list' => 'Encaminhamento deve ser: Alta, Internação, Transferência, Especialista, Retorno ou Óbito'
            ],
            'obito' => [
                'in_list' => 'Opção de óbito deve ser 0 (não) ou 1 (sim)'
            ],
            'status' => [
                'in_list' => 'Status deve ser: Em Andamento, Finalizado, Cancelado, Aguardando ou Suspenso'
            ]
        ];

        

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }
        
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Converter formato da data de datetime-local (YYYY-MM-DDTHH:MM) para objeto Time do CodeIgniter
            $dataAtendimento = $this->request->getPost('data_atendimento');
            if ($dataAtendimento) {
                // Converter para formato MySQL e criar objeto Time
                $dataFormatada = str_replace('T', ' ', $dataAtendimento) . ':00';
                $dataAtendimento = \CodeIgniter\I18n\Time::parse($dataFormatada);
            }

            

            // Preparar dados para inserção
            $hgtGlicemia = $this->request->getPost('hgt_glicemia');
            $temperatura = $this->request->getPost('temperatura');
            $pressaoArterial = $this->request->getPost('pressao_arterial');
            $encaminhamento = $this->request->getPost('encaminhamento');

            // Salvar o atendimento
            $atendimentoData = [
                'id_paciente' => $this->request->getPost('id_paciente'),
                'id_medico' => $this->request->getPost('id_medico'),
                'data_atendimento' => $dataAtendimento,
                'classificacao_risco' => $this->request->getPost('classificacao_risco'),
                'consulta_enfermagem' => $this->request->getPost('consulta_enfermagem'),
                'hgt_glicemia' => (!empty($hgtGlicemia) && is_numeric($hgtGlicemia)) ? (float)$hgtGlicemia : null,
                'pressao_arterial' => !empty($pressaoArterial) ? trim($pressaoArterial) : null,
                'temperatura' => (!empty($temperatura) && is_numeric($temperatura)) ? (float)$temperatura : null,
                'hipotese_diagnostico' => $this->request->getPost('hipotese_diagnostico'),
                'observacao' => $this->request->getPost('observacao'),
                'encaminhamento' => !empty($encaminhamento) ? $encaminhamento : null,
                'obito' => $this->request->getPost('obito') ? 1 : 0,
                'status' => $this->request->getPost('status') ?? 'Em Andamento',
                'paciente_observacao' => $this->request->getPost('paciente_observacao') ?? 'Não'
            ];

            $idAtendimento = $this->atendimentoModel->skipValidation(true)->insert($atendimentoData);

            // Debug: verificar se o insert foi bem-sucedido
            if (!$idAtendimento) {
                $errors = $this->atendimentoModel->errors();
                log_message('error', 'Erro ao inserir atendimento: ' . json_encode($errors));
                return redirect()->back()->withInput()->with('error', 'Erro ao salvar atendimento: ' . json_encode($errors));
            }

            log_message('info', 'Atendimento inserido com ID: ' . $idAtendimento);

            // Salvar procedimentos selecionados
            $procedimentos = $this->request->getPost('procedimentos') ?? [];
            foreach ($procedimentos as $idProcedimento) {
                $quantidade = $this->request->getPost("quantidade_proc_{$idProcedimento}") ?? 1;
                $observacao = $this->request->getPost("observacao_proc_{$idProcedimento}") ?? '';
                
                $this->atendimentoProcedimentoModel->insert([
                    'id_atendimento' => $idAtendimento,
                    'id_procedimento' => $idProcedimento,
                    'quantidade' => $quantidade,
                    'observacao' => $observacao
                ]);
            }

            // Salvar exames solicitados
            $exames = $this->request->getPost('exames') ?? [];
            foreach ($exames as $idExame) {
                $observacao = $this->request->getPost("observacao_exame_{$idExame}") ?? '';
                
                $this->atendimentoExameModel->insert([
                    'id_atendimento' => $idAtendimento,
                    'id_exame' => $idExame,
                    'data_solicitacao' => \CodeIgniter\I18n\Time::now(),
                    'status' => 'Solicitado',
                    'observacao' => $observacao
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Erro ao cadastrar atendimento.');
            }

            return redirect()->to('/atendimentos')->with('success', 'Atendimento cadastrado com sucesso!');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Erro ao cadastrar atendimento: ' . $e->getMessage());
        }
    }

    /**
     * Exibe detalhes de um atendimento específico
     */
    public function show($id = null)
    {
        $atendimento = $this->atendimentoModel->select('atendimentos.*, pacientes.nome as paciente_nome, pacientes.cpf, pacientes.data_nascimento, pacientes.sexo, medicos.nome as medico_nome, medicos.crm, medicos.especialidade')
                                            ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
                                            ->join('medicos', 'medicos.id_medico = atendimentos.id_medico')
                                            ->find($id);
        
        if (!$atendimento) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Atendimento não encontrado');
        }

        // Buscar procedimentos realizados
        $procedimentos = $this->atendimentoProcedimentoModel->select('atendimento_procedimentos.*, procedimentos.nome as procedimento_nome, procedimentos.codigo')
                                                           ->join('procedimentos', 'procedimentos.id_procedimento = atendimento_procedimentos.id_procedimento')
                                                           ->where('id_atendimento', $id)
                                                           ->findAll();

        // Buscar exames solicitados - usando consulta direta para evitar problemas com casting de campos NULL
        $db = \Config\Database::connect();
        $examesQuery = $db->table('atendimento_exames ae')
                         ->select('ae.*, e.nome, e.codigo, e.tipo,
                                   ae.data_solicitacao as data_solicitacao_raw,
                                   ae.data_realizacao as data_realizacao_raw')
                         ->join('exames e', 'e.id_exame = ae.id_exame')
                         ->where('ae.id_atendimento', $id)
                         ->where('ae.deleted_at IS NULL')
                         ->get();
        
        $exames = $examesQuery->getResultArray();

        $data = [
            'title' => 'Detalhes do Atendimento',
            'description' => 'Atendimento #' . $id,
            'atendimento' => $atendimento,
            'procedimentos' => $procedimentos,
            'exames' => $exames
        ];

        return view('atendimentos/show', $data);
    }

    /**
     * Exibe formulário para editar atendimento
     */
    public function edit($id = null)
    {
        $atendimento = $this->atendimentoModel->find($id);
        
        if (!$atendimento) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Atendimento não encontrado');
        }

        $pacientes = $this->pacienteModel->orderBy('nome', 'ASC')->findAll();
        $medicos = $this->medicoModel->where('status', 'Ativo')->orderBy('nome', 'ASC')->findAll();
        $procedimentos = $this->procedimentoModel->orderBy('nome', 'ASC')->findAll();
        $exames = $this->exameModel->orderBy('nome', 'ASC')->findAll();

        // Buscar procedimentos e exames já vinculados
        $procedimentosVinculados = $this->atendimentoProcedimentoModel->where('id_atendimento', $id)->findAll();
        
        // Buscar exames vinculados - usando consulta direta para evitar problemas com casting
        $db = \Config\Database::connect();
        $examesVinculadosQuery = $db->table('atendimento_exames')
                                   ->where('id_atendimento', $id)
                                   ->where('deleted_at IS NULL')
                                   ->get();
        $examesVinculados = $examesVinculadosQuery->getResultArray();

        $data = [
            'title' => 'Editar Atendimento',
            'description' => 'Editar Atendimento #' . $id,
            'atendimento' => $atendimento,
            'pacientes' => $pacientes,
            'medicos' => $medicos,
            'procedimentos' => $procedimentos,
            'exames' => $exames,
            'procedimentos_vinculados' => $procedimentosVinculados,
            'exames_vinculados' => $examesVinculados,
            'classificacoes' => ['Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul'],
            'encaminhamentos' => ['Alta', 'Internação', 'Transferência', 'Especialista', 'Retorno', 'Óbito'],
            'status_opcoes' => ['Em Andamento', 'Finalizado', 'Cancelado', 'Aguardando', 'Suspenso'],
            'paciente_observacao_opcoes' => ['Sim', 'Não']
        ];

        return view('atendimentos/edit', $data);
    }

    /**
     * Atualiza dados do atendimento
     */
    public function update($id = null)
    {
        $atendimento = $this->atendimentoModel->find($id);
        
        if (!$atendimento) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Atendimento não encontrado');
        }

        $rules = [
            'id_paciente' => 'required|integer|is_not_unique[pacientes.id_paciente]',
            'id_medico' => 'required|integer|is_not_unique[medicos.id_medico]',
            'data_atendimento' => 'required|regex_match[/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}$/]',
            'classificacao_risco' => 'required|in_list[Vermelho,Laranja,Amarelo,Verde,Azul]',
            'consulta_enfermagem' => 'permit_empty',
            'hgt_glicemia' => 'permit_empty|decimal',
            'pressao_arterial' => 'permit_empty|max_length[20]',
            'temperatura' => 'permit_empty|decimal',
            'hipotese_diagnostico' => 'permit_empty',
            'observacao' => 'permit_empty',
            'encaminhamento' => 'permit_empty|in_list[Alta,Internação,Transferência,Especialista,Retorno,Óbito]',
            'obito' => 'permit_empty|in_list[0,1]',
            'status' => 'permit_empty|in_list[Em Andamento,Finalizado,Cancelado,Aguardando,Suspenso]',
            'paciente_observacao' => 'permit_empty|in_list[Sim,Não]'
        ];

        $messages = [
            'id_paciente' => [
                'required' => 'O paciente é obrigatório',
                'integer' => 'ID do paciente deve ser um número',
                'is_not_unique' => 'Paciente não encontrado'
            ],
            'id_medico' => [
                'required' => 'O médico é obrigatório',
                'integer' => 'ID do médico deve ser um número',
                'is_not_unique' => 'Médico não encontrado'
            ],
            'data_atendimento' => [
                'required' => 'A data do atendimento é obrigatória',
                'regex_match' => 'Data do atendimento deve estar no formato AAAA-MM-DDTHH:MM (ex: 2024-12-25T14:30). Use o seletor de data/hora.'
            ],
            'classificacao_risco' => [
                'required' => 'A classificação de risco é obrigatória',
                'in_list' => 'Classificação deve ser: Vermelho, Laranja, Amarelo, Verde ou Azul'
            ],
            'hgt_glicemia' => [
                'decimal' => 'HGT/Glicemia deve ser um número decimal'
            ],
            'pressao_arterial' => [
                'max_length' => 'Pressão arterial deve ter no máximo 20 caracteres'
            ],
            'temperatura' => [
                'decimal' => 'Temperatura deve ser um número decimal'
            ],
            'encaminhamento' => [
                'in_list' => 'Encaminhamento deve ser: Alta, Internação, Transferência, Especialista, Retorno ou Óbito'
            ],
            'obito' => [
                'in_list' => 'Opção de óbito deve ser 0 (não) ou 1 (sim)'
            ],
            'status' => [
                'in_list' => 'Status deve ser: Em Andamento, Finalizado, Cancelado, Aguardando ou Suspenso'
            ],
            'paciente_observacao' => [
                'in_list' => 'Paciente observação deve ser: Sim ou Não'
            ]
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        // Converter formato da data de datetime-local (YYYY-MM-DDTHH:MM) para objeto Time do CodeIgniter
        $dataAtendimento = $this->request->getPost('data_atendimento');
        if ($dataAtendimento) {
            // Converter para formato MySQL e criar objeto Time
            $dataFormatada = str_replace('T', ' ', $dataAtendimento) . ':00';
            $dataAtendimento = \CodeIgniter\I18n\Time::parse($dataFormatada);
        }

        // Preparar dados para atualização
        $hgtGlicemia = $this->request->getPost('hgt_glicemia');
        $temperatura = $this->request->getPost('temperatura');
        $pressaoArterial = $this->request->getPost('pressao_arterial');
        $encaminhamento = $this->request->getPost('encaminhamento');

        $data = [
            'id_paciente' => $this->request->getPost('id_paciente'),
            'id_medico' => $this->request->getPost('id_medico'),
            'data_atendimento' => $dataAtendimento,
            'classificacao_risco' => $this->request->getPost('classificacao_risco'),
            'consulta_enfermagem' => $this->request->getPost('consulta_enfermagem'),
            'hgt_glicemia' => (!empty($hgtGlicemia) && is_numeric($hgtGlicemia)) ? (float)$hgtGlicemia : null,
            'pressao_arterial' => !empty($pressaoArterial) ? trim($pressaoArterial) : null,
            'temperatura' => (!empty($temperatura) && is_numeric($temperatura)) ? (float)$temperatura : null,
            'hipotese_diagnostico' => $this->request->getPost('hipotese_diagnostico'),
            'observacao' => $this->request->getPost('observacao'),
            'encaminhamento' => !empty($encaminhamento) ? $encaminhamento : null,
            'obito' => $this->request->getPost('obito') ? 1 : 0,
            'status' => $this->request->getPost('status') ?: 'Em Andamento',
            'paciente_observacao' => $this->request->getPost('paciente_observacao') ?: 'Não'
        ];

        if ($this->atendimentoModel->skipValidation(true)->update($id, $data)) {
            return redirect()->to('/atendimentos')->with('success', 'Atendimento atualizado com sucesso!');
        } else {
            return redirect()->back()->withInput()->with('error', 'Erro ao atualizar atendimento.');
        }
    }

    /**
     * Exclui um atendimento (soft delete)
     */
    public function delete($id = null)
    {
        $atendimento = $this->atendimentoModel->find($id);
        
        if (!$atendimento) {
            return $this->response->setJSON(['error' => 'Atendimento não encontrado'])->setStatusCode(404);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Excluir procedimentos vinculados
            $this->atendimentoProcedimentoModel->where('id_atendimento', $id)->delete();
            
            // Excluir exames vinculados
            $this->atendimentoExameModel->where('id_atendimento', $id)->delete();
            
            // Excluir atendimento
            $this->atendimentoModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['error' => 'Erro ao excluir atendimento'])->setStatusCode(500);
            }

            return $this->response->setJSON(['success' => 'Atendimento excluído com sucesso!']);

        } catch (\Exception $e) {
            $db->transRollback();
            return $this->response->setJSON(['error' => 'Erro ao excluir atendimento: ' . $e->getMessage()])->setStatusCode(500);
        }
    }

    /**
     * Relatório de atendimentos
     */
    public function relatorio()
    {
        $data_inicio = $this->request->getGet('data_inicio') ?? date('Y-m-01');
        $data_fim = $this->request->getGet('data_fim') ?? date('Y-m-d');
        $medico = $this->request->getGet('medico');
        $classificacao = $this->request->getGet('classificacao');

        $query = $this->atendimentoModel->select('pam_atendimentos.*, pacientes.nome as paciente_nome, medicos.nome as medico_nome')
            ->join('pacientes', 'pacientes.id_paciente = pam_atendimentos.id_paciente')
            ->join('medicos', 'medicos.id_medico = pam_atendimentos.id_medico')
            ->where('DATE(pam_atendimentos.data_atendimento) >=', $data_inicio)
            ->where('DATE(pam_atendimentos.data_atendimento) <=', $data_fim);

        if ($medico) {
            $query = $query->where('pam_atendimentos.id_medico', $medico);
        }
        if ($classificacao) {
            $query = $query->where('pam_atendimentos.classificacao_risco', $classificacao);
        }

        $atendimentos = $query->orderBy('pam_atendimentos.data_atendimento', 'DESC')->findAll();

        // Estatísticas para os cards
        $estatisticas = [
            'total_atendimentos' => count($atendimentos),
            'atendimentos_concluidos' => count(array_filter($atendimentos, fn($a) => $a['status'] == 'Finalizado')),
            'em_andamento' => count(array_filter($atendimentos, fn($a) => $a['status'] == 'Em Andamento')),
            'casos_urgentes' => count(array_filter($atendimentos, fn($a) => $a['classificacao_risco'] == 'Vermelho')),
            'diagnosticos_informados' => count(array_filter($atendimentos, fn($a) => !empty(trim($a['hipotese_diagnostico'])))),
            'obitos' => count(array_filter($atendimentos, fn($a) => $a['obito'] == true)),
        ];

        // Médicos para filtro
        $medicos = $this->medicoModel->where('status', 'Ativo')->orderBy('nome', 'ASC')->findAll();

        // Filtros para view
        $filtros = [
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
            'medico' => $medico,
            'classificacao' => $classificacao
        ];

        // Gráficos (exemplo simples, pode ser melhorado conforme necessidade)
        $graficos = [
            'classificacao' => [],
            'mensal' => [],
            'medicos' => [],
            'encaminhamentos' => [],
            'diagnosticos' => []
        ];
        // Classificação de risco
        $classificacoes = ['Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul'];
        foreach ($classificacoes as $c) {
            $total = count(array_filter($atendimentos, fn($a) => $a['classificacao_risco'] == $c));
            $graficos['classificacao'][] = [
                'classificacao' => $c,
                'total' => $total
            ];
        }
        // Atendimentos por período (Mês ou Dia)
        $startDate = new \DateTime($data_inicio);
        $endDate = new \DateTime($data_fim);
        $dateDiff = $endDate->diff($startDate)->days;
        
        $graficoMensalTitulo = '';
        
        if ($dateDiff > 90) { // Group by month
            $graficoMensalTitulo = 'Atendimentos por Mês';
            $periodFormat = 'Y-m';
            $labelFormat = 'M/Y';
            $interval = new \DateInterval('P1M');
            // To include the last month in DatePeriod
            $endDate->modify('first day of next month');
        } else { // Group by day
            $graficoMensalTitulo = 'Atendimentos por Dia';
            $periodFormat = 'Y-m-d';
            $labelFormat = 'd/m';
            $interval = new \DateInterval('P1D');
            // To include the last day in DatePeriod
            $endDate->modify('+1 day');
        }

        // Aggregate data from attendances
        $aggregatedData = [];
        foreach ($atendimentos as $a) {
            $key = date($periodFormat, strtotime($a['data_atendimento']));
            if (!isset($aggregatedData[$key])) {
                $aggregatedData[$key] = 0;
            }
            $aggregatedData[$key]++;
        }

        // Create a full range of periods to ensure continuity
        $periodIterator = new \DatePeriod($startDate, $interval, $endDate);
        
        $finalChartData = [];
        foreach ($periodIterator as $date) {
            $key = $date->format($periodFormat);
            $label = $date->format($labelFormat);
            $total = $aggregatedData[$key] ?? 0;
            
            $finalChartData[] = [
                'mes' => $label, // Keep 'mes' key for compatibility
                'total' => $total
            ];
        }

        $graficos['mensal'] = $finalChartData;
        // Médicos
        $medicosGraf = [];
        foreach ($atendimentos as $a) {
            $nome = $a['medico_nome'] ?? 'Desconhecido';
            if (!isset($medicosGraf[$nome])) $medicosGraf[$nome] = 0;
            $medicosGraf[$nome]++;
        }
        arsort($medicosGraf);
        $medicosGraf = array_slice($medicosGraf, 0, 10);
        foreach ($medicosGraf as $medicoNome => $total) {
            $graficos['medicos'][] = [
                'medico' => $medicoNome,
                'total' => $total
            ];
        }
        // Encaminhamentos
        $encaminhamentosGraf = [];
        foreach ($atendimentos as $a) {
            $enc = $a['encaminhamento'] ?? 'Em Atendimento';
            if (!isset($encaminhamentosGraf[$enc])) $encaminhamentosGraf[$enc] = 0;
            $encaminhamentosGraf[$enc]++;
        }
        arsort($encaminhamentosGraf);
        foreach ($encaminhamentosGraf as $enc => $total) {
            $graficos['encaminhamentos'][] = [
                'encaminhamento' => $enc,
                'total' => $total
            ];
        }

        // Diagnósticos mais comuns
        $diagnosticosGraf = [];
        foreach ($atendimentos as $a) {
            $diag = trim($a['hipotese_diagnostico'] ?? '');
            if (!empty($diag)) {
                $diag = strtolower($diag);
                if (!isset($diagnosticosGraf[$diag])) $diagnosticosGraf[$diag] = 0;
                $diagnosticosGraf[$diag]++;
            }
        }
        arsort($diagnosticosGraf);
        $diagnosticosGraf = array_slice($diagnosticosGraf, 0, 10);
        foreach ($diagnosticosGraf as $diag => $total) {
            $graficos['diagnosticos'][] = [
                'diagnostico' => ucfirst($diag),
                'total' => $total
            ];
        }

        // Tabela detalhada (exemplo: agrupando por mês)
        $dadosTabela = [];
        $tabelaTemp = [];
        foreach ($atendimentos as $a) {
            $periodo = date('Y-m', strtotime($a['data_atendimento']));
            if (!isset($tabelaTemp[$periodo])) {
                $tabelaTemp[$periodo] = [
                    'periodo' => $periodo,
                    'vermelho' => 0,
                    'laranja' => 0,
                    'amarelo' => 0,
                    'verde' => 0,
                    'azul' => 0,
                    'total' => 0
                ];
            }
            $class = strtolower($a['classificacao_risco']);
            if (isset($tabelaTemp[$periodo][$class])) {
                $tabelaTemp[$periodo][$class]++;
            }
            $tabelaTemp[$periodo]['total']++;
        }
        foreach ($tabelaTemp as $linha) {
            $urgentes = $linha['vermelho'] + $linha['laranja'];
            $taxa_urgencia = $linha['total'] > 0 ? ($urgentes / $linha['total']) * 100 : 0;
            $linha['taxa_urgencia'] = $taxa_urgencia;
            $dadosTabela[] = $linha;
        }

        $data = [
            'title' => 'Relatório de Atendimentos',
            'description' => "Período: {$data_inicio} a {$data_fim}",
            'atendimentos' => $atendimentos,
            'estatisticas' => $estatisticas,
            'medicos' => $medicos,
            'filtros' => $filtros,
            'graficos' => $graficos,
            'dadosTabela' => $dadosTabela,
            'classificacoes' => ['Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul']
        ];

        return view('atendimentos/relatorio', $data);
    }

    /**
     * Exporta relatório de atendimentos para Excel
     */
    public function export()
    {
        // Verificar se é uma requisição de exportação
        if (!$this->request->getGet('export')) {
            return redirect()->to('/atendimentos/relatorio')->with('error', 'Requisição de exportação inválida.');
        }

        $data_inicio = $this->request->getGet('data_inicio') ?? date('Y-m-01');
        $data_fim = $this->request->getGet('data_fim') ?? date('Y-m-d');
        $medico = $this->request->getGet('medico');
        $classificacao = $this->request->getGet('classificacao');

        try {
            $query = $this->atendimentoModel->select('pam_atendimentos.*, pacientes.nome as paciente_nome, pacientes.cpf, medicos.nome as medico_nome, medicos.crm')
                ->join('pacientes', 'pacientes.id_paciente = pam_atendimentos.id_paciente')
                ->join('medicos', 'medicos.id_medico = pam_atendimentos.id_medico')
                ->where('DATE(pam_atendimentos.data_atendimento) >=', $data_inicio)
                ->where('DATE(pam_atendimentos.data_atendimento) <=', $data_fim);

            if ($medico) {
                $query = $query->where('pam_atendimentos.id_medico', $medico);
            }
            if ($classificacao) {
                $query = $query->where('pam_atendimentos.classificacao_risco', $classificacao);
            }

            $atendimentos = $query->orderBy('pam_atendimentos.data_atendimento', 'DESC')->findAll();

            // Gerar nome do arquivo com informações dos filtros
            $filename = 'relatorio_atendimentos_' . $data_inicio . '_a_' . $data_fim;
            if ($medico) {
                $medico_info = $this->medicoModel->find($medico);
                $filename .= '_' . str_replace(' ', '_', $medico_info['nome'] ?? 'medico');
            }
            if ($classificacao) {
                $filename .= '_' . strtolower($classificacao);
            }
            $filename .= '_' . date('Y-m-d_H-i-s') . '.csv';
            
            // Preparar dados CSV
            $csvData = [];
            
            // Cabeçalhos
            $csvData[] = [
                'ID',
                'Data/Hora',
                'Paciente',
                'CPF',
                'Médico',
                'CRM',
                'Classificação de Risco',
                'Status',
                'Encaminhamento',
                'Óbito',
                'Pressão Arterial',
                'Temperatura (°C)',
                'HGT/Glicemia (mg/dL)',
                'Hipótese Diagnóstico',
                'Consulta Enfermagem',
                'Observações'
            ];

            // Dados
            foreach ($atendimentos as $atendimento) {
                $csvData[] = [
                    $atendimento['id_atendimento'],
                    date('d/m/Y H:i', strtotime($atendimento['data_atendimento'])),
                    $atendimento['paciente_nome'],
                    $atendimento['cpf'],
                    $atendimento['medico_nome'],
                    $atendimento['crm'],
                    $atendimento['classificacao_risco'],
                    $atendimento['status'] ?? 'Em Andamento',
                    $atendimento['encaminhamento'] ?? 'Não informado',
                    $atendimento['obito'] ? 'Sim' : 'Não',
                    $atendimento['pressao_arterial'] ?? 'Não informado',
                    $atendimento['temperatura'] ?? 'Não informado',
                    $atendimento['hgt_glicemia'] ?? 'Não informado',
                    $atendimento['hipotese_diagnostico'] ?? 'Não informado',
                    $atendimento['consulta_enfermagem'] ?? 'Não informado',
                    $atendimento['observacao'] ?? 'Nenhuma'
                ];
            }

            // Criar conteúdo CSV
            $csvContent = '';
            foreach ($csvData as $row) {
                $csvContent .= implode(';', array_map(function($field) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }, $row)) . "\n";
            }

            // Adicionar BOM para UTF-8
            $csvContent = chr(0xEF) . chr(0xBB) . chr(0xBF) . $csvContent;

            // Retornar resposta como download
            return $this->response
                ->setHeader('Content-Type', 'application/csv; charset=utf-8')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->setHeader('Pragma', 'no-cache')
                ->setHeader('Expires', '0')
                ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                ->setBody($csvContent);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao exportar relatório: ' . $e->getMessage());
            return redirect()->to('/atendimentos/relatorio')->with('error', 'Erro ao exportar relatório: ' . $e->getMessage());
        }
    }
}
