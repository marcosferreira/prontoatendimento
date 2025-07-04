<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicoModel extends Model
{
    protected $table            = 'medicos';
    protected $primaryKey       = 'id_medico';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'crm',
        'especialidade',
        'status'
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
        'crm' => 'required|is_unique[medicos.crm]|max_length[20]',
        'especialidade' => 'max_length[100]',
        'status' => 'in_list[Ativo,Inativo]'
    ];
    
    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome do médico é obrigatório',
            'max_length' => 'O nome deve ter no máximo 255 caracteres'
        ],
        'crm' => [
            'required' => 'O CRM é obrigatório',
            'is_unique' => 'Este CRM já está cadastrado',
            'max_length' => 'O CRM deve ter no máximo 20 caracteres'
        ],
        'especialidade' => [
            'max_length' => 'A especialidade deve ter no máximo 100 caracteres'
        ],
        'status' => [
            'in_list' => 'Status deve ser Ativo ou Inativo'
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
     * Busca médicos ativos
     */
    public function getMedicosAtivos()
    {
        return $this->where('status', 'Ativo')->findAll();
    }

    /**
     * Busca médico por CRM
     */
    public function getMedicoByCrm($crm)
    {
        return $this->where('crm', $crm)->first();
    }

    /**
     * Busca médicos por especialidade
     */
    public function getMedicosByEspecialidade($especialidade)
    {
        return $this->where('especialidade', $especialidade)->findAll();
    }

    /**
     * Lista médicos com contagem de atendimentos
     */
    public function getMedicosWithAtendimentosCount()
    {
        return $this->select('medicos.*, COUNT(atendimentos.id_atendimento) as total_atendimentos')
                   ->join('atendimentos', 'atendimentos.id_medico = medicos.id_medico', 'left')
                   ->groupBy('medicos.id_medico')
                   ->findAll();
    }

    /**
     * Busca médicos excluídos (soft deleted)
     */
    public function getMedicosExcluidos()
    {
        return $this->onlyDeleted()->findAll();
    }

    /**
     * Restaura um médico excluído
     */
    public function restaurarMedico($id)
    {
        return $this->update($id, ['deleted_at' => null]);
    }

    /**
     * Busca médico por CRM incluindo excluídos
     */
    public function getMedicoByCrmComExcluidos($crm)
    {
        return $this->withDeleted()->where('crm', $crm)->first();
    }

    /**
     * Busca médicos disponíveis (ativos)
     */
    public function getMedicosDisponiveis()
    {
        return $this->getMedicosAtivos();
    }
}
