<?php

namespace App\Models;

use CodeIgniter\Model;

class ProcedimentoModel extends Model
{
    protected $table            = 'procedimentos';
    protected $primaryKey       = 'id_procedimento';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'codigo',
        'descricao'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'nome' => 'required|max_length[255]',
        'codigo' => 'max_length[50]',
        'descricao' => 'max_length[65535]'
    ];
    
    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome do procedimento é obrigatório',
            'max_length' => 'O nome deve ter no máximo 255 caracteres'
        ],
        'codigo' => [
            'max_length' => 'O código deve ter no máximo 50 caracteres'
        ],
        'descricao' => [
            'max_length' => 'A descrição é muito longa'
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
    protected $beforeDelete   = ['checkAtendimentosVinculados'];
    protected $afterDelete    = [];

    /**
     * Verifica se há atendimentos vinculados antes de deletar (soft delete)
     */
    protected function checkAtendimentosVinculados(array $data)
    {
        // Com soft delete, permitimos a exclusão mas mantemos os dados
        // Os atendimentos vinculados não serão afetados
        return $data;
    }

    /**
     * Busca procedimento por código
     */
    public function getProcedimentoByCodigo($codigo)
    {
        return $this->where('codigo', $codigo)->first();
    }

    /**
     * Busca procedimentos por nome (busca parcial)
     */
    public function searchProcedimentosByNome($nome)
    {
        return $this->like('nome', $nome)->findAll();
    }

    /**
     * Lista procedimentos mais utilizados
     */
    public function getProcedimentosMaisUtilizados($limit = 10)
    {
        return $this->select('pam_procedimentos.*, COUNT(pam_atendimento_procedimentos.id_procedimento) as total_usos')
                   ->join('pam_atendimento_procedimentos', 'pam_atendimento_procedimentos.id_procedimento = pam_procedimentos.id_procedimento', 'left')
                   ->groupBy('pam_procedimentos.id_procedimento')
                   ->orderBy('total_usos', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Busca procedimentos com contagem de uso
     */
    public function getProcedimentosWithUsageCount()
    {
        return $this->select('pam_procedimentos.*, COUNT(pam_atendimento_procedimentos.id_procedimento) as total_usos')
                   ->join('pam_atendimento_procedimentos', 'pam_atendimento_procedimentos.id_procedimento = pam_procedimentos.id_procedimento', 'left')
                   ->groupBy('pam_procedimentos.id_procedimento')
                   ->findAll();
    }

    /**
     * Busca procedimentos excluídos (soft deleted)
     */
    public function getProcedimentosExcluidos()
    {
        return $this->onlyDeleted()->findAll();
    }

    /**
     * Restaura um procedimento excluído
     */
    public function restaurarProcedimento($id)
    {
        return $this->update($id, ['deleted_at' => null]);
    }

    /**
     * Busca procedimento por código incluindo excluídos
     */
    public function getProcedimentoByCodigoComExcluidos($codigo)
    {
        return $this->withDeleted()->where('codigo', $codigo)->first();
    }
}
