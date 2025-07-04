<?php

namespace App\Models;

use CodeIgniter\Model;

class AtendimentoExameModel extends Model
{
    protected $table            = 'atendimento_exames';
    protected $primaryKey       = 'id_atendimento_exame';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_atendimento',
        'id_exame',
        'resultado',
        'status',
        'data_solicitacao',
        'data_realizacao',
        'observacao'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'id_atendimento' => 'int',
        'id_exame' => 'int',
        'data_solicitacao' => 'datetime',
        'data_realizacao' => 'datetime'
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
        'id_exame' => 'required|integer|is_not_unique[exames.id_exame]',
        'status' => 'required|in_list[Solicitado,Realizado,Cancelado]',
        'data_solicitacao' => 'required|valid_date',
        'data_realizacao' => 'permit_empty|valid_date',
        'resultado' => 'max_length[65535]',
        'observacao' => 'max_length[65535]'
    ];
    
    protected $validationMessages = [
        'id_atendimento' => [
            'required' => 'O atendimento é obrigatório',
            'integer' => 'ID do atendimento deve ser um número',
            'is_not_unique' => 'Atendimento não encontrado'
        ],
        'id_exame' => [
            'required' => 'O exame é obrigatório',
            'integer' => 'ID do exame deve ser um número',
            'is_not_unique' => 'Exame não encontrado'
        ],
        'status' => [
            'required' => 'O status é obrigatório',
            'in_list' => 'Status deve ser: Solicitado, Realizado ou Cancelado'
        ],
        'data_solicitacao' => [
            'required' => 'A data de solicitação é obrigatória',
            'valid_date' => 'Data de solicitação inválida'
        ],
        'data_realizacao' => [
            'valid_date' => 'Data de realização inválida'
        ],
        'resultado' => [
            'max_length' => 'Resultado muito longo'
        ],
        'observacao' => [
            'max_length' => 'Observação muito longa'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setDataSolicitacao'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['setDataRealizacao'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Define data de solicitação automaticamente se não informada
     */
    protected function setDataSolicitacao(array $data)
    {
        if (!isset($data['data']['data_solicitacao'])) {
            $data['data']['data_solicitacao'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Define data de realização automaticamente quando status muda para Realizado
     */
    protected function setDataRealizacao(array $data)
    {
        if (isset($data['data']['status']) && $data['data']['status'] === 'Realizado') {
            if (!isset($data['data']['data_realizacao'])) {
                $data['data']['data_realizacao'] = date('Y-m-d H:i:s');
            }
        }
        return $data;
    }

    /**
     * Busca exames de um atendimento
     */
    public function getExamesByAtendimento($idAtendimento)
    {
        return $this->select('atendimento_exames.*, exames.nome, exames.codigo, exames.tipo, exames.descricao')
                   ->join('exames', 'exames.id_exame = atendimento_exames.id_exame')
                   ->where('atendimento_exames.id_atendimento', $idAtendimento)
                   ->orderBy('atendimento_exames.data_solicitacao', 'DESC')
                   ->findAll();
    }

    /**
     * Busca atendimentos que solicitaram um exame específico
     */
    public function getAtendimentosByExame($idExame)
    {
        return $this->select('atendimento_exames.*, atendimentos.data_atendimento, pacientes.nome as nome_paciente')
                   ->join('atendimentos', 'atendimentos.id_atendimento = atendimento_exames.id_atendimento')
                   ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
                   ->where('atendimento_exames.id_exame', $idExame)
                   ->orderBy('atendimento_exames.data_solicitacao', 'DESC')
                   ->findAll();
    }

    /**
     * Solicita um exame para um atendimento
     */
    public function solicitarExame($idAtendimento, $idExame, $observacao = null)
    {
        // Verifica se já existe o exame solicitado para o atendimento
        $existente = $this->where('id_atendimento', $idAtendimento)
                         ->where('id_exame', $idExame)
                         ->where('status !=', 'Cancelado')
                         ->first();

        if ($existente) {
            return ['success' => false, 'message' => 'Exame já solicitado para este atendimento'];
        }

        $data = [
            'id_atendimento' => $idAtendimento,
            'id_exame' => $idExame,
            'status' => 'Solicitado',
            'observacao' => $observacao
        ];

        $result = $this->insert($data);
        
        if ($result) {
            return ['success' => true, 'id' => $this->getInsertID()];
        }

        return ['success' => false, 'message' => 'Erro ao solicitar exame'];
    }

    /**
     * Registra resultado de um exame
     */
    public function registrarResultado($idAtendimentoExame, $resultado, $observacao = null)
    {
        $data = [
            'resultado' => $resultado,
            'status' => 'Realizado',
            'data_realizacao' => date('Y-m-d H:i:s')
        ];

        if ($observacao !== null) {
            $data['observacao'] = $observacao;
        }

        return $this->update($idAtendimentoExame, $data);
    }

    /**
     * Cancela um exame
     */
    public function cancelarExame($idAtendimentoExame, $motivo = null)
    {
        $data = [
            'status' => 'Cancelado',
            'observacao' => $motivo ? 'Cancelado: ' . $motivo : 'Cancelado'
        ];

        return $this->update($idAtendimentoExame, $data);
    }

    /**
     * Busca exames por status
     */
    public function getExamesByStatus($status)
    {
        return $this->select('atendimento_exames.*, exames.nome, exames.codigo, exames.tipo, pacientes.nome as nome_paciente, atendimentos.data_atendimento')
                   ->join('exames', 'exames.id_exame = atendimento_exames.id_exame')
                   ->join('atendimentos', 'atendimentos.id_atendimento = atendimento_exames.id_atendimento')
                   ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
                   ->where('atendimento_exames.status', $status)
                   ->orderBy('atendimento_exames.data_solicitacao', 'ASC')
                   ->findAll();
    }

    /**
     * Relatório de exames mais solicitados
     */
    public function getExamesMaisSolicitados($limite = 10)
    {
        return $this->select('exames.nome, exames.codigo, exames.tipo, COUNT(atendimento_exames.id_atendimento_exame) as total_solicitacoes')
                   ->join('exames', 'exames.id_exame = atendimento_exames.id_exame')
                   ->groupBy('atendimento_exames.id_exame')
                   ->orderBy('total_solicitacoes', 'DESC')
                   ->limit($limite)
                   ->findAll();
    }

    /**
     * Relatório de exames por período
     */
    public function getExamesPorPeriodo($dataInicio, $dataFim)
    {
        return $this->select('exames.nome, exames.codigo, exames.tipo, COUNT(atendimento_exames.id_atendimento_exame) as total_solicitacoes,
                             SUM(CASE WHEN atendimento_exames.status = "Realizado" THEN 1 ELSE 0 END) as total_realizados,
                             SUM(CASE WHEN atendimento_exames.status = "Cancelado" THEN 1 ELSE 0 END) as total_cancelados')
                   ->join('exames', 'exames.id_exame = atendimento_exames.id_exame')
                   ->where('DATE(atendimento_exames.data_solicitacao) >=', $dataInicio)
                   ->where('DATE(atendimento_exames.data_solicitacao) <=', $dataFim)
                   ->groupBy('atendimento_exames.id_exame')
                   ->orderBy('total_solicitacoes', 'DESC')
                   ->findAll();
    }

    /**
     * Busca exames por tipo
     */
    public function getExamesByTipo($tipo)
    {
        return $this->select('atendimento_exames.*, exames.nome, exames.codigo, exames.descricao, pacientes.nome as nome_paciente, atendimentos.data_atendimento')
                   ->join('exames', 'exames.id_exame = atendimento_exames.id_exame')
                   ->join('atendimentos', 'atendimentos.id_atendimento = atendimento_exames.id_atendimento')
                   ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
                   ->where('exames.tipo', $tipo)
                   ->orderBy('atendimento_exames.data_solicitacao', 'DESC')
                   ->findAll();
    }

    /**
     * Busca exames pendentes (solicitados mas não realizados)
     */
    public function getExamesPendentes()
    {
        return $this->getExamesByStatus('Solicitado');
    }

    /**
     * Busca exames realizados
     */
    public function getExamesRealizados()
    {
        return $this->getExamesByStatus('Realizado');
    }

    /**
     * Busca exames cancelados
     */
    public function getExamesCancelados()
    {
        return $this->getExamesByStatus('Cancelado');
    }

    /**
     * Estatísticas de exames por status
     */
    public function getEstatisticasStatus()
    {
        return $this->select('status, COUNT(*) as total')
                   ->groupBy('status')
                   ->findAll();
    }

    /**
     * Busca exames completos de um atendimento com detalhes
     */
    public function getExamesCompletosAtendimento($idAtendimento)
    {
        return $this->select('atendimento_exames.*, exames.nome, exames.codigo, exames.tipo, exames.descricao')
                   ->join('exames', 'exames.id_exame = atendimento_exames.id_exame')
                   ->where('atendimento_exames.id_atendimento', $idAtendimento)
                   ->orderBy('atendimento_exames.data_solicitacao', 'ASC')
                   ->findAll();
    }

    /**
     * Tempo médio entre solicitação e realização de exames
     */
    public function getTempoMedioRealizacao()
    {
        return $this->select('AVG(TIMESTAMPDIFF(HOUR, data_solicitacao, data_realizacao)) as tempo_medio_horas')
                   ->where('status', 'Realizado')
                   ->where('data_realizacao IS NOT NULL')
                   ->first();
    }

    /**
     * Busca exames com atraso (solicitados há mais de X horas)
     */
    public function getExamesComAtraso($horasLimite = 24)
    {
        return $this->select('atendimento_exames.*, exames.nome, exames.codigo, pacientes.nome as nome_paciente,
                             TIMESTAMPDIFF(HOUR, atendimento_exames.data_solicitacao, NOW()) as horas_pendente')
                   ->join('exames', 'exames.id_exame = atendimento_exames.id_exame')
                   ->join('atendimentos', 'atendimentos.id_atendimento = atendimento_exames.id_atendimento')
                   ->join('pacientes', 'pacientes.id_paciente = atendimentos.id_paciente')
                   ->where('atendimento_exames.status', 'Solicitado')
                   ->where('TIMESTAMPDIFF(HOUR, atendimento_exames.data_solicitacao, NOW()) >', $horasLimite)
                   ->orderBy('atendimento_exames.data_solicitacao', 'ASC')
                   ->findAll();
    }
}
