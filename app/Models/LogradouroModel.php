<?php

namespace App\Models;

use CodeIgniter\Model;

class LogradouroModel extends Model
{
    protected $table            = 'logradouros';
    protected $primaryKey       = 'id_logradouro';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome_logradouro',
        'tipo_logradouro',
        'cep',
        'id_bairro',
        'observacoes'
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
    protected $validationRules      = [
        'nome_logradouro' => 'required|max_length[150]',
        'tipo_logradouro' => 'required|in_list[Rua,Avenida,Travessa,Alameda,Praça,Estrada,Rodovia,Via,Beco,Largo]',
        'cep'             => 'permit_empty|max_length[10]',
        'id_bairro'       => 'required|is_natural_no_zero',
        'observacoes'     => 'permit_empty'
    ];
    protected $validationMessages   = [
        'nome_logradouro' => [
            'required'   => 'O nome do logradouro é obrigatório',
            'max_length' => 'O nome do logradouro não pode ter mais de 150 caracteres'
        ],
        'tipo_logradouro' => [
            'required' => 'O tipo do logradouro é obrigatório',
            'in_list'  => 'Tipo de logradouro inválido'
        ],
        'cep' => [
            'max_length' => 'O CEP não pode ter mais de 10 caracteres'
        ],
        'id_bairro' => [
            'required'           => 'O bairro é obrigatório',
            'is_natural_no_zero' => 'Bairro inválido'
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
     * Busca todos os logradouros com informações do bairro
     */
    public function getLogradourosWithBairro()
    {
        return $this->select('logradouros.*, bairros.nome_bairro, bairros.area')
                   ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro')
                   ->orderBy('logradouros.tipo_logradouro, logradouros.nome_logradouro')
                   ->findAll();
    }

    /**
     * Busca logradouros por bairro
     */
    public function getLogradourosByBairro($idBairro)
    {
        return $this->where('id_bairro', $idBairro)
                   ->orderBy('tipo_logradouro, nome_logradouro')
                   ->findAll();
    }

    /**
     * Busca logradouro por ID com informações do bairro
     */
    public function getLogradouroWithBairro($id)
    {
        return $this->select('logradouros.*, bairros.nome_bairro, bairros.area')
                   ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro')
                   ->find($id);
    }

    /**
     * Busca logradouros por CEP
     */
    public function getLogradourosByCep($cep)
    {
        return $this->like('cep', $cep)
                   ->orderBy('tipo_logradouro, nome_logradouro')
                   ->findAll();
    }

    /**
     * Busca logradouros por nome
     */
    public function searchLogradouros($termo)
    {
        return $this->select('logradouros.*, bairros.nome_bairro, bairros.area')
                   ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro')
                   ->groupStart()
                       ->like('logradouros.nome_logradouro', $termo)
                       ->orLike('logradouros.cep', $termo)
                       ->orLike('bairros.nome_bairro', $termo)
                   ->groupEnd()
                   ->orderBy('logradouros.tipo_logradouro, logradouros.nome_logradouro')
                   ->findAll();
    }

    /**
     * Conta logradouros por bairro
     */
    public function countLogradourosByBairro($idBairro)
    {
        return $this->where('id_bairro', $idBairro)->countAllResults();
    }

    /**
     * Lista tipos de logradouro disponíveis
     */
    public function getTiposLogradouro()
    {
        return [
            'Rua' => 'Rua',
            'Avenida' => 'Avenida',
            'Travessa' => 'Travessa',
            'Alameda' => 'Alameda',
            'Praça' => 'Praça',
            'Estrada' => 'Estrada',
            'Rodovia' => 'Rodovia',
            'Via' => 'Via',
            'Beco' => 'Beco',
            'Largo' => 'Largo'
        ];
    }
}
