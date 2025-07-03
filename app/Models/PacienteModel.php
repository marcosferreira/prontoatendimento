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
        'id_logradouro',
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
        'cpf' => 'required|max_length[14]',
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
    
    protected $skipValidation       = true;
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
     * Busca pacientes por logradouro
     */
    public function getPacientesByLogradouro($idLogradouro)
    {
        return $this->where('id_logradouro', $idLogradouro)->findAll();
    }

    /**
     * Busca paciente por CPF
     */
    public function getPacienteByCpf($cpf)
    {
        return $this->where('cpf', $cpf)->first();
    }

    /**
     * Busca pacientes com seus logradouros
     */
    public function getPacientesWithLogradouro()
    {
        $pacientes = $this->select('pacientes.*, logradouros.nome_logradouro, logradouros.cep, bairros.nome_bairro, bairros.area')
                         ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'left')
                         ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro', 'left')
                         ->findAll();

        // Calcular idade para cada paciente se necessário
        foreach ($pacientes as &$paciente) {
            if (isset($paciente['data_nascimento'])) {
                $dataNascimento = new \DateTime($paciente['data_nascimento']);
                $hoje = new \DateTime();
                $paciente['idade'] = $hoje->diff($dataNascimento)->y;
            }
        }

        return $pacientes;
    }

    /**
     * Busca um paciente específico com seu logradouro
     */
    public function getPacienteWithLogradouro($id)
    {
        $paciente = $this->select('pacientes.*, logradouros.nome_logradouro, logradouros.cep, bairros.nome_bairro, bairros.area')
                        ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'left')
                        ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro', 'left')
                        ->where('pacientes.id_paciente', $id)
                        ->first();

        // Calcular idade se não estiver definida ou se a data de nascimento existir
        if ($paciente && isset($paciente['data_nascimento'])) {
            $dataNascimento = new \DateTime($paciente['data_nascimento']);
            $hoje = new \DateTime();
            $paciente['idade'] = $hoje->diff($dataNascimento)->y;
        }

        return $paciente;
    }

    /**
     * Regras de validação específicas para inserção (incluindo is_unique para CPF)
     */
    public function getInsertValidationRules()
    {
        return array_merge($this->validationRules, [
            'cpf' => 'required|is_unique[pacientes.cpf]|max_length[14]'
        ]);
    }

    /**
     * Regras de validação específicas para atualização (excluindo o próprio registro do is_unique)
     */
    public function getUpdateValidationRules($id)
    {
        return array_merge($this->validationRules, [
            'cpf' => "required|is_unique[pacientes.cpf,id_paciente,{$id}]|max_length[14]"
        ]);
    }

    /**
     * Busca pacientes por bairro (através do logradouro)
     */
    public function getPacientesByBairro($idBairro)
    {
        return $this->select('pacientes.*')
                    ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'inner')
                    ->where('logradouros.id_bairro', $idBairro)
                    ->findAll();
    }

    /**
     * Busca pacientes por nome ou CPF
     */
    public function buscarPacientes($termo, $limit = null)
    {
        $query = $this->select('pacientes.*, logradouros.nome_logradouro, logradouros.cep, bairros.nome_bairro, bairros.area')
                      ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'left')
                      ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro', 'left')
                      ->groupStart()
                          ->like('pacientes.nome', $termo)
                          ->orLike('pacientes.cpf', $termo)
                      ->groupEnd();

        if ($limit) {
            $query = $query->limit($limit);
        }

        $pacientes = $query->findAll();

        // Calcular idade para cada paciente se necessário
        foreach ($pacientes as &$paciente) {
            if (isset($paciente['data_nascimento'])) {
                $dataNascimento = new \DateTime($paciente['data_nascimento']);
                $hoje = new \DateTime();
                $paciente['idade'] = $hoje->diff($dataNascimento)->y;
            }
        }

        return $pacientes;
    }
}
