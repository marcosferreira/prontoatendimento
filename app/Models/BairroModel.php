<?php

namespace App\Models;

use CodeIgniter\Model;

class BairroModel extends Model
{
    protected $table            = 'bairros';
    protected $primaryKey       = 'id_bairro';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome_bairro',
        'area'
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
        'nome_bairro' => 'required|max_length[100]',
        'area' => 'max_length[100]'
    ];
    
    protected $validationMessages = [
        'nome_bairro' => [
            'required' => 'O nome do bairro é obrigatório',
            'max_length' => 'O nome do bairro deve ter no máximo 100 caracteres'
        ],
        'area' => [
            'max_length' => 'A área deve ter no máximo 100 caracteres'
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
    protected $beforeDelete   = ['checkPacientesVinculados'];
    protected $afterDelete    = [];

    /**
     * Verifica se há pacientes vinculados antes de deletar
     */
    protected function checkPacientesVinculados(array $data)
    {
        $pacienteModel = new PacienteModel();
        $pacientesVinculados = $pacienteModel->where('id_bairro', $data['id'])->countAllResults();
        
        if ($pacientesVinculados > 0) {
            throw new \RuntimeException('Não é possível excluir o bairro. Existem pacientes vinculados a ele.');
        }
        
        return $data;
    }

    /**
     * Busca bairros por área
     */
    public function getBairrosByArea($area)
    {
        return $this->where('area', $area)->findAll();
    }

    /**
     * Busca bairro por nome
     */
    public function getBairroByNome($nome)
    {
        return $this->where('nome_bairro', $nome)->first();
    }

    /**
     * Lista bairros com contagem de pacientes
     */
    public function getBairrosWithPacientesCount()
    {
        return $this->select('bairros.*, COUNT(pam_pacientes.id_paciente) as total_pacientes')
                   ->join('pacientes', 'pacientes.id_bairro = bairros.id_bairro', 'left')
                   ->groupBy('bairros.id_bairro')
                   ->findAll();
    }
}
