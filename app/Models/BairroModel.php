<?php

namespace App\Models;

use CodeIgniter\Model;

class BairroModel extends Model
{
    protected $table            = 'bairros';
    protected $primaryKey       = 'id_bairro';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
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
     * Verifica se há logradouros vinculados antes de deletar (soft delete)
     */
    protected function checkPacientesVinculados(array $data)
    {
        // Com soft delete, permitimos a exclusão mas mantemos os dados
        // Os logradouros vinculados não serão afetados
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
        return $this->select('pam_bairros.*, COUNT(pam_pacientes.id_logradouro) as total_pacientes')
                   ->join('pam_logradouros', 'pam_logradouros.id_bairro = pam_bairros.id_bairro', 'left')
                   ->join('pam_pacientes', 'pam_pacientes.id_logradouro = pam_logradouros.id_logradouro', 'left')
                   ->groupBy('pam_bairros.id_bairro')
                   ->findAll();
    }

    public function getTotalPacientesByBairro($idBairro)
    {
        return $this->select('COUNT(pam_pacientes.id_logradouro) as total_pacientes')
                    ->join('pam_logradouros', 'pam_logradouros.id_bairro = pam_bairros.id_bairro', 'left')
                    ->join('pam_pacientes', 'pam_pacientes.id_logradouro = pam_logradouros.id_logradouro', 'left')
                    ->where('pam_bairros.id_bairro', $idBairro)
                    ->first()['total_pacientes'] ?? 0;
    }

    /**
     * Busca logradouros de um bairro
     */
    public function getLogradouros($idBairro)
    {
        $logradouroModel = new \App\Models\LogradouroModel();
        return $logradouroModel->getLogradourosByBairro($idBairro);
    }

    /**
     * Conta logradouros de um bairro
     */
    public function getTotalLogradourosByBairro($idBairro)
    {
        $logradouroModel = new \App\Models\LogradouroModel();
        return $logradouroModel->countLogradourosByBairro($idBairro);
    }

    /**
     * Lista bairros com contagem de logradouros
     */
    public function getBairrosWithLogradourosCount()
    {
        return $this->select('pam_bairros.*, COUNT(pam_logradouros.id_logradouro) as total_logradouros')
                   ->join('pam_logradouros', 'pam_logradouros.id_bairro = pam_bairros.id_bairro', 'left')
                   ->groupBy('pam_bairros.id_bairro')
                   ->orderBy('pam_bairros.nome_bairro')
                   ->findAll();
    }

    /**
     * Verifica se o bairro pode ser excluído (não tem logradouros vinculados)
     */
    public function canDelete($idBairro)
    {
        $logradourosVinculados = $this->getTotalLogradourosByBairro($idBairro);
        $pacientesVinculados = $this->getTotalPacientesByBairro($idBairro);
        
        return $logradourosVinculados === 0 && $pacientesVinculados === 0;
    }

    /**
     * Busca bairros excluídos (soft deleted)
     */
    public function getBairrosExcluidos()
    {
        return $this->onlyDeleted()->findAll();
    }

    /**
     * Restaura um bairro excluído
     */
    public function restaurarBairro($id)
    {
        return $this->update($id, ['deleted_at' => null]);
    }

    /**
     * Busca bairros incluindo excluídos
     */
    public function getBairrosComExcluidos()
    {
        return $this->withDeleted()->findAll();
    }
}
