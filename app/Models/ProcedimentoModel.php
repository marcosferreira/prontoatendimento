<?php

namespace App\Models;

use CodeIgniter\Model;

class ProcedimentoModel extends Model
{
    protected $table            = 'procedimentos';
    protected $primaryKey       = 'id_procedimento';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
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
     * Verifica se há atendimentos vinculados antes de deletar
     */
    protected function checkAtendimentosVinculados(array $data)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('atendimento_procedimentos');
        $count = $builder->where('id_procedimento', $data['id'])->countAllResults();
        
        if ($count > 0) {
            throw new \RuntimeException('Não é possível excluir o procedimento. Existem atendimentos vinculados a ele.');
        }
        
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
        return $this->select('procedimentos.*, COUNT(atendimento_procedimentos.id_procedimento) as total_usos')
                   ->join('atendimento_procedimentos', 'atendimento_procedimentos.id_procedimento = procedimentos.id_procedimento', 'left')
                   ->groupBy('procedimentos.id_procedimento')
                   ->orderBy('total_usos', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Busca procedimentos com contagem de uso
     */
    public function getProcedimentosWithUsageCount()
    {
        return $this->select('procedimentos.*, COUNT(atendimento_procedimentos.id_procedimento) as total_usos')
                   ->join('atendimento_procedimentos', 'atendimento_procedimentos.id_procedimento = procedimentos.id_procedimento', 'left')
                   ->groupBy('procedimentos.id_procedimento')
                   ->findAll();
    }
}
