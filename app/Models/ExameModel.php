<?php

namespace App\Models;

use CodeIgniter\Model;

class ExameModel extends Model
{
    protected $table            = 'exames';
    protected $primaryKey       = 'id_exame';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'codigo',
        'tipo',
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
        'tipo' => 'required|in_list[laboratorial,imagem,funcional,outros]',
        'descricao' => 'max_length[65535]'
    ];
    
    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome do exame é obrigatório',
            'max_length' => 'O nome deve ter no máximo 255 caracteres'
        ],
        'codigo' => [
            'max_length' => 'O código deve ter no máximo 50 caracteres'
        ],
        'tipo' => [
            'required' => 'O tipo do exame é obrigatório',
            'in_list' => 'Tipo deve ser: laboratorial, imagem, funcional ou outros'
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
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Busca exame por código
     */
    public function getExameByCodigo($codigo)
    {
        return $this->where('codigo', $codigo)->first();
    }

    /**
     * Busca exames por tipo
     */
    public function getExamesByTipo($tipo)
    {
        return $this->where('tipo', $tipo)->findAll();
    }

    /**
     * Busca exames laboratoriais
     */
    public function getExamesLaboratoriais()
    {
        return $this->getExamesByTipo('laboratorial');
    }

    /**
     * Busca exames de imagem
     */
    public function getExamesImagem()
    {
        return $this->getExamesByTipo('imagem');
    }

    /**
     * Busca exames funcionais
     */
    public function getExamesFuncionais()
    {
        return $this->getExamesByTipo('funcional');
    }

    /**
     * Busca exames por nome (busca parcial)
     */
    public function searchExamesByNome($nome)
    {
        return $this->like('nome', $nome)->findAll();
    }

    /**
     * Lista tipos de exame disponíveis
     */
    public function getTiposExame()
    {
        return [
            'laboratorial' => 'Laboratorial',
            'imagem' => 'Imagem',
            'funcional' => 'Funcional',
            'outros' => 'Outros'
        ];
    }

    /**
     * Contagem de exames por tipo
     */
    public function getContagemPorTipo()
    {
        return $this->select('tipo, COUNT(*) as total')
                   ->groupBy('tipo')
                   ->findAll();
    }
}
