<?php

namespace App\Models;

use CodeIgniter\Model;

class AtendimentoModel extends Model
{
    protected $table            = 'atendimentos';
    protected $primaryKey       = 'id_atendimento';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_paciente',
        'id_medico',
        'data_atendimento',
        'classificacao_risco',
        'consulta_enfermagem',
        'hgt_glicemia',
        'pressao_arterial',
        'temperatura',
        'hipotese_diagnostico',
        'observacao',
        'encaminhamento',
        'obito',
        'status',
        'paciente_observacao'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id_paciente' => 'int',
        'id_medico' => 'int',
        'hgt_glicemia' => '?float',
        'temperatura' => '?float',
        'obito' => 'boolean'
    ];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'id_paciente' => 'required|integer|is_not_unique[pacientes.id_paciente]',
        'id_medico' => 'required|integer|is_not_unique[medicos.id_medico]',
        'data_atendimento' => 'required|valid_date',
        'classificacao_risco' => 'permit_empty|in_list[Vermelho,Laranja,Amarelo,Verde,Azul,Sem classificação]',
        'hgt_glicemia' => 'decimal|greater_than_equal_to[0]|less_than_equal_to[999.99]',
        'pressao_arterial' => 'max_length[20]',
        'encaminhamento' => 'in_list[Alta,Internação,Transferência,Especialista,Retorno,Óbito]',
        'obito' => 'in_list[0,1]',
        'status' => 'in_list[Em Andamento,Finalizado,Cancelado,Aguardando,Suspenso]',
        'paciente_observacao' => 'in_list[Sim,Não]'
    ];
    
    protected $validationMessages = [
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
            'valid_date' => 'Data de atendimento inválida'
        ],
        'classificacao_risco' => [
            'permit_empty' => 'A classificação de risco é opcional',
            'in_list' => 'Classificação deve ser: Vermelho, Laranja, Amarelo, Verde, Azul ou Sem classificação'
        ],
        'hgt_glicemia' => [
            'decimal' => 'Glicemia deve ser um valor decimal',
            'greater_than_equal_to' => 'Glicemia deve ser maior ou igual a 0',
            'less_than_equal_to' => 'Glicemia deve ser menor ou igual a 999.99'
        ],
        'encaminhamento' => [
            'in_list' => 'Encaminhamento inválido'
        ],
        'status' => [
            'in_list' => 'Status deve ser: Em Andamento, Finalizado, Cancelado, Aguardando ou Suspenso'
        ],
        'paciente_observacao' => [
            'in_list' => 'Paciente observação deve ser: Sim ou Não'
        ],
        'paciente_observacao' => [
            'max_length' => 'Observação do paciente deve ter no máximo 1000 caracteres'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['validateObito'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['validateObito'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Valida se óbito está coerente com encaminhamento
     */
    protected function validateObito(array $data)
    {
        if (isset($data['data']['obito']) && $data['data']['obito'] == true) {
            $data['data']['encaminhamento'] = 'Óbito';
        }
        
        if (isset($data['data']['encaminhamento']) && $data['data']['encaminhamento'] == 'Óbito') {
            $data['data']['obito'] = true;
        }
        
        return $data;
    }

    /**
     * Busca atendimentos por paciente
     */
    public function getAtendimentosByPaciente($idPaciente)
    {
        return $this->where('id_paciente', $idPaciente)
                   ->orderBy('data_atendimento', 'DESC')
                   ->findAll();
    }

    /**
     * Busca atendimentos por médico
     */
    public function getAtendimentosByMedico($idMedico)
    {
        return $this->where('id_medico', $idMedico)
                   ->orderBy('data_atendimento', 'DESC')
                   ->findAll();
    }

    /**
     * Busca atendimentos por classificação de risco
     */
    public function getAtendimentosByRisco($classificacao)
    {
        return $this->where('classificacao_risco', $classificacao)
                   ->orderBy('data_atendimento', 'DESC')
                   ->findAll();
    }

    /**
     * Busca atendimentos completos com dados do paciente e médico
     */
    public function getAtendimentosCompletos()
    {
        return $this->select('atendimentos.*, pacientes.nome as nome_paciente, pacientes.cpf, medicos.nome as nome_medico, medicos.crm')
                   ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
                   ->join('medicos', 'medicos.id_medico = atendimentos.id_medico')
                   ->orderBy('atendimentos.data_atendimento', 'DESC')
                   ->findAll();
    }

    /**
     * Busca atendimentos de hoje
     */
    public function getAtendimentosHoje()
    {
        $hoje = date('Y-m-d');
        return $this->where('DATE(data_atendimento)', $hoje)
                   ->orderBy('data_atendimento', 'DESC')
                   ->findAll();
    }

    /**
     * Busca atendimentos por período
     */
    public function getAtendimentosPorPeriodo($dataInicio, $dataFim)
    {
        return $this->where('DATE(data_atendimento) >=', $dataInicio)
                   ->where('DATE(data_atendimento) <=', $dataFim)
                   ->orderBy('data_atendimento', 'DESC')
                   ->findAll();
    }

    /**
     * Estatísticas por classificação de risco
     */
    public function getEstatisticasRisco()
    {
        return $this->select('classificacao_risco, COUNT(*) as total')
                   ->groupBy('classificacao_risco')
                   ->findAll();
    }

    /**
     * Busca casos de óbito
     */
    public function getObitos()
    {
        return $this->where('obito', true)
                   ->orderBy('data_atendimento', 'DESC')
                   ->findAll();
    }

    /**
     * Lista tipos de classificação de risco conforme Protocolo de Manchester
     */
    public function getClassificacoesRisco()
    {
        return [
            'Vermelho' => 'Vermelho - EMERGÊNCIA – atendimento imediato (0 minutos)',
            'Laranja'  => 'Laranja - MUITO URGENTE – atendimento praticamente imediato (10 minutos)',
            'Amarelo'  => 'Amarelo - URGENTE – atendimento rápido, mas pode aguardar (60 minutos)',
            'Verde'    => 'Verde - POUCO URGENTE – pode aguardar atendimento ou ser encaminhado para outros serviços de saúde (120 minutos)',
            'Azul'     => 'Azul - NÃO URGENTE – pode aguardar atendimento ou ser encaminhado para outros serviços de saúde (240 minutos)',
            'Sem classificação' => 'Sem classificação - Quando não há enfermeiro presente para realizar a triagem'
        ];
    }

    /**
     * Retorna o tempo de espera recomendado (em minutos) para cada classificação de risco
     * Protocolo de Manchester
     */
    public function getTempoEsperaManchester($classificacao)
    {
        $tempos = [
            'Vermelho' => 0,      // EMERGÊNCIA – atendimento imediato
            'Laranja'  => 10,     // MUITO URGENTE – atendimento praticamente imediato
            'Amarelo'  => 60,     // URGENTE – pode aguardar
            'Verde'    => 120,    // POUCO URGENTE
            'Azul'     => 240,    // NÃO URGENTE
            'Sem classificação' => null  // Sem tempo definido quando não há classificação
        ];
        return $tempos[$classificacao] ?? null;
    }

    /**
     * Lista tipos de encaminhamento
     */
    public function getTiposEncaminhamento()
    {
        return [
            'Alta' => 'Alta',
            'Internação' => 'Internação',
            'Transferência' => 'Transferência',
            'Especialista' => 'Especialista',
            'Retorno' => 'Retorno',
            'Óbito' => 'Óbito'
        ];
    }

    /**
     * Lista tipos de status do atendimento
     */
    public function getTiposStatus()
    {
        return [
            'Em Andamento' => 'Em Andamento',
            'Finalizado' => 'Finalizado',
            'Cancelado' => 'Cancelado',
            'Aguardando' => 'Aguardando',
            'Suspenso' => 'Suspenso'
        ];
    }

    /**
     * Busca atendimentos excluídos (soft deleted)
     */
    public function getAtendimentosExcluidos()
    {
        return $this->onlyDeleted()->findAll();
    }

    /**
     * Restaura um atendimento excluído
     */
    public function restaurarAtendimento($id)
    {
        return $this->update($id, ['deleted_at' => null]);
    }

    /**
     * Busca atendimentos incluindo excluídos
     */
    public function getAtendimentosComExcluidos()
    {
        return $this->withDeleted()->findAll();
    }

    /**
     * Busca atendimentos completos incluindo excluídos
     */
    public function getAtendimentosCompletosComExcluidos()
    {
        return $this->select('atendimentos.*, pacientes.nome as nome_paciente, pacientes.cpf, medicos.nome as nome_medico, medicos.crm')
                   ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
                   ->join('medicos', 'medicos.id_medico = atendimentos.id_medico', 'left')
                   ->withDeleted()
                   ->orderBy('atendimentos.data_atendimento', 'DESC')
                   ->findAll();
    }

    /**
     * Busca atendimentos completos de um paciente específico
     */
    public function getAtendimentosCompletosByPaciente($idPaciente)
    {
        return $this->select('atendimentos.*, medicos.nome as nome_medico, medicos.crm, medicos.especialidade')
                   ->join('medicos', 'medicos.id_medico = atendimentos.id_medico', 'left')
                   ->where('atendimentos.id_paciente', $idPaciente)
                   ->orderBy('atendimentos.data_atendimento', 'DESC')
                   ->findAll();
    }

    /**
     * Busca pacientes em observação clínica
     */
    public function getPacientesEmObservacao()
    {
        return $this->select('atendimentos.*, pacientes.nome as nome_paciente, pacientes.cpf, medicos.nome as nome_medico, medicos.crm')
                   ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
                   ->join('medicos', 'medicos.id_medico = atendimentos.id_medico', 'left')
                   ->where('atendimentos.paciente_observacao', 'Sim')
                   ->where('atendimentos.status', 'Em Andamento')
                   ->orderBy('atendimentos.data_atendimento', 'DESC')
                   ->findAll();
    }

    /**
     * Verifica se um paciente está em observação
     */
    public function isPacienteEmObservacao($idPaciente)
    {
        $result = $this->where('id_paciente', $idPaciente)
                      ->where('status', 'Em Andamento')
                      ->where('paciente_observacao', 'Sim')
                      ->first();
        
        return !empty($result);
    }

    /**
     * Lista opções para paciente em observação
     */
    public function getOpcoesPacienteObservacao()
    {
        return [
            'Sim' => 'Sim',
            'Não' => 'Não'
        ];
    }
}
