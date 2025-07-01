<?php

namespace App\Models;

use CodeIgniter\Model;

class PacienteModel extends Model
{
    protected $table            = 'pacientes';
    protected $primaryKey       = 'id_paciente';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'sus',
        'cpf',
        'rg',
        'endereco',
        'numero',
        'complemento',
        'cep',
        'cidade',
        'id_bairro',
        'data_nascimento',
        'idade',
        'sexo',
        'telefone',
        'celular',
        'email',
        'numero_sus',
        'tipo_sanguineo',
        'nome_responsavel',
        'alergias',
        'observacoes'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [
        'idade' => 'int'
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
        'nome' => 'required|max_length[255]',
        'cpf' => 'required|is_unique[pacientes.cpf]|max_length[14]',
        'data_nascimento' => 'required|valid_date',
        'sexo' => 'required|in_list[M,F]',
        'email' => 'permit_empty|valid_email|max_length[255]',
        'telefone' => 'permit_empty|max_length[15]',
        'celular' => 'permit_empty|max_length[16]',
        'numero_sus' => 'permit_empty|max_length[15]',
        'endereco' => 'permit_empty|max_length[500]',
        'numero' => 'permit_empty|max_length[10]',
        'complemento' => 'permit_empty|max_length[100]',
        'cep' => 'permit_empty|max_length[9]',
        'cidade' => 'permit_empty|max_length[100]',
        'rg' => 'permit_empty|max_length[20]',
        'nome_responsavel' => 'permit_empty|max_length[255]',
        'alergias' => 'permit_empty|max_length[1000]',
        'observacoes' => 'permit_empty|max_length[1000]',
        'sus' => 'permit_empty|max_length[15]',
        'idade' => 'permit_empty|integer|greater_than_equal_to[0]|less_than[200]'
    ];
    
    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome é obrigatório',
            'max_length' => 'O nome deve ter no máximo 255 caracteres'
        ],
        'cpf' => [
            'required' => 'O CPF é obrigatório',
            'is_unique' => 'Este CPF já está cadastrado',
            'max_length' => 'O CPF deve ter no máximo 14 caracteres'
        ],
        'data_nascimento' => [
            'required' => 'A data de nascimento é obrigatória',
            'valid_date' => 'Data de nascimento inválida'
        ]
    ];
    
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['calculateAge'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['calculateAge'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Calcula a idade automaticamente baseada na data de nascimento
     */
    protected function calculateAge(array $data)
    {
        if (isset($data['data']['data_nascimento'])) {
            $dataNascimento = new \DateTime($data['data']['data_nascimento']);
            $hoje = new \DateTime();
            $idade = $hoje->diff($dataNascimento)->y;
            $data['data']['idade'] = $idade;
        }
        return $data;
    }

    /**
     * Busca pacientes por bairro
     */
    public function getPacientesByBairro($idBairro)
    {
        return $this->where('id_bairro', $idBairro)->findAll();
    }

    /**
     * Busca paciente por CPF
     */
    public function getPacienteByCpf($cpf)
    {
        return $this->where('cpf', $cpf)->first();
    }

    /**
     * Busca pacientes com seus bairros
     */
    public function getPacientesWithBairro()
    {
        return $this->select('pacientes.*, bairros.nome_bairro, bairros.area')
                   ->join('bairros', 'bairros.id_bairro = pacientes.id_bairro', 'left')
                   ->findAll();
    }
}
