<?php

namespace App\Models;

use CodeIgniter\Model;

class AtendimentoProcedimentoModel extends Model
{
    protected $table            = 'atendimento_procedimentos';
    protected $primaryKey       = 'id_atendimento_procedimento';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_atendimento',
        'id_procedimento',
        'quantidade',
        'observacao'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id_atendimento' => 'int',
        'id_procedimento' => 'int',
        'quantidade' => 'int'
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
        'id_atendimento' => 'required|integer|is_not_unique[atendimentos.id_atendimento]',
        'id_procedimento' => 'required|integer|is_not_unique[procedimentos.id_procedimento]',
        'quantidade' => 'required|integer|greater_than[0]|less_than_equal_to[99999]',
        'observacao' => 'max_length[65535]'
    ];
    
    protected $validationMessages = [
        'id_atendimento' => [
            'required' => 'O atendimento é obrigatório',
            'integer' => 'ID do atendimento deve ser um número',
            'is_not_unique' => 'Atendimento não encontrado'
        ],
        'id_procedimento' => [
            'required' => 'O procedimento é obrigatório',
            'integer' => 'ID do procedimento deve ser um número',
            'is_not_unique' => 'Procedimento não encontrado'
        ],
        'quantidade' => [
            'required' => 'A quantidade é obrigatória',
            'integer' => 'Quantidade deve ser um número inteiro',
            'greater_than' => 'Quantidade deve ser maior que 0',
            'less_than_equal_to' => 'Quantidade deve ser menor ou igual a 99999'
        ],
        'observacao' => [
            'max_length' => 'Observação muito longa'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Busca procedimentos de um atendimento
     */
    public function getProcedimentosByAtendimento($idAtendimento)
    {
        return $this->select('atendimento_procedimentos.*, procedimentos.nome as procedimento_nome, procedimentos.codigo, procedimentos.descricao')
                   ->join('procedimentos', 'procedimentos.id_procedimento = atendimento_procedimentos.id_procedimento')
                   ->where('atendimento_procedimentos.id_atendimento', $idAtendimento)
                   ->findAll();
    }

    /**
     * Busca atendimentos que usaram um procedimento específico
     */
    public function getAtendimentosByProcedimento($idProcedimento)
    {
        return $this->select('atendimento_procedimentos.*, atendimentos.data_atendimento, pacientes.nome as nome_paciente')
                   ->join('atendimentos', 'atendimentos.id_atendimento = atendimento_procedimentos.id_atendimento')
                   ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
                   ->where('atendimento_procedimentos.id_procedimento', $idProcedimento)
                   ->orderBy('atendimentos.data_atendimento', 'DESC')
                   ->findAll();
    }

    /**
     * Adiciona procedimento a um atendimento
     */
    public function adicionarProcedimento($idAtendimento, $idProcedimento, $quantidade = 1, $observacao = null)
    {
        // Verifica se já existe o procedimento no atendimento
        $existente = $this->where('id_atendimento', $idAtendimento)
                         ->where('id_procedimento', $idProcedimento)
                         ->first();

        if ($existente) {
            // Atualiza a quantidade se já existe
            return $this->update($existente['id_atendimento_procedimento'], [
                'quantidade' => $existente['quantidade'] + $quantidade,
                'observacao' => $observacao ?? $existente['observacao']
            ]);
        } else {
            // Insere novo registro
            return $this->insert([
                'id_atendimento' => $idAtendimento,
                'id_procedimento' => $idProcedimento,
                'quantidade' => $quantidade,
                'observacao' => $observacao
            ]);
        }
    }

    /**
     * Remove procedimento de um atendimento
     */
    public function removerProcedimento($idAtendimento, $idProcedimento)
    {
        return $this->where('id_atendimento', $idAtendimento)
                   ->where('id_procedimento', $idProcedimento)
                   ->delete();
    }

    /**
     * Atualiza quantidade de um procedimento no atendimento
     */
    public function atualizarQuantidade($idAtendimento, $idProcedimento, $novaQuantidade)
    {
        if ($novaQuantidade <= 0) {
            return $this->removerProcedimento($idAtendimento, $idProcedimento);
        }

        return $this->where('id_atendimento', $idAtendimento)
                   ->where('id_procedimento', $idProcedimento)
                   ->set('quantidade', $novaQuantidade)
                   ->update();
    }

    /**
     * Relatório de procedimentos mais utilizados
     */
    public function getProcedimentosMaisUtilizados($limite = 10)
    {
        return $this->select('procedimentos.nome, procedimentos.codigo, SUM(atendimento_procedimentos.quantidade) as total_quantidade, COUNT(atendimento_procedimentos.id_atendimento) as total_atendimentos')
                   ->join('procedimentos', 'procedimentos.id_procedimento = atendimento_procedimentos.id_procedimento')
                   ->groupBy('atendimento_procedimentos.id_procedimento')
                   ->orderBy('total_quantidade', 'DESC')
                   ->limit($limite)
                   ->findAll();
    }

    /**
     * Relatório de procedimentos por período
     */
    public function getProcedimentosPorPeriodo($dataInicio, $dataFim)
    {
        return $this->select('procedimentos.nome, procedimentos.codigo, SUM(atendimento_procedimentos.quantidade) as total_quantidade, COUNT(atendimento_procedimentos.id_atendimento) as total_atendimentos')
                   ->join('procedimentos', 'procedimentos.id_procedimento = atendimento_procedimentos.id_procedimento')
                   ->join('atendimentos', 'atendimentos.id_atendimento = atendimento_procedimentos.id_atendimento')
                   ->where('DATE(atendimentos.data_atendimento) >=', $dataInicio)
                   ->where('DATE(atendimentos.data_atendimento) <=', $dataFim)
                   ->groupBy('atendimento_procedimentos.id_procedimento')
                   ->orderBy('total_quantidade', 'DESC')
                   ->findAll();
    }

    /**
     * Busca procedimentos completos de um atendimento com detalhes
     */
    public function getProcedimentosCompletosAtendimento($idAtendimento)
    {
        return $this->select('atendimento_procedimentos.*, procedimentos.nome as procedimento_nome, procedimentos.codigo, procedimentos.descricao')
                   ->join('procedimentos', 'procedimentos.id_procedimento = atendimento_procedimentos.id_procedimento')
                   ->where('atendimento_procedimentos.id_atendimento', $idAtendimento)
                   ->findAll();
    }

    /**
     * Busca procedimentos de um paciente específico com dados completos
     */
    public function getProcedimentosByPaciente($idPaciente)
    {
        return $this->select('atendimento_procedimentos.*, 
                             procedimentos.nome as procedimento_nome, 
                             procedimentos.codigo,
                             atendimentos.data_atendimento, 
                             pacientes.nome as nome_paciente,
                             medicos.nome as nome_medico')
                   ->join('atendimentos', 'atendimentos.id_atendimento = atendimento_procedimentos.id_atendimento')
                   ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
                   ->join('medicos', 'medicos.id_medico = atendimentos.id_medico', 'left')
                   ->join('procedimentos', 'procedimentos.id_procedimento = atendimento_procedimentos.id_procedimento', 'left')
                   ->where('pacientes.id_paciente', $idPaciente)
                   ->orderBy('atendimentos.data_atendimento', 'DESC')
                   ->findAll();
    }
}
