<?php

namespace App\Models;

use CodeIgniter\Model;

class PacienteModel extends Model
{
    protected $table            = 'pacientes';
    protected $primaryKey       = 'id_paciente';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nome',
        'sus',
        'cpf',
        'rg',
        'id_logradouro',
        'numero',
        'complemento',
        'data_nascimento',
        'idade',
        'sexo',
        'telefone',
        'celular',
        'email',
        'numero_sus',
        'tipo_sanguineo',
        'nome_responsavel',
        'nome_mae',
        'nome_pai',
        'alergias',
        'observacoes',
        'cidade_externa',
        'logradouro_externo',
        'cep_externo'
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
        'cpf' => 'permit_empty|max_length[14]', // Alterado de required para permit_empty
        'data_nascimento' => 'required|valid_date',
        'sexo' => 'required|in_list[M,F]',
        'email' => 'permit_empty|valid_email|max_length[255]',
        'telefone' => 'permit_empty|max_length[15]',
        'celular' => 'permit_empty|max_length[16]',
        'numero_sus' => 'permit_empty|max_length[15]',
        'numero' => 'permit_empty|max_length[10]',
        'complemento' => 'permit_empty|max_length[100]',
        'rg' => 'permit_empty|max_length[20]',
        'nome_responsavel' => 'permit_empty|max_length[255]',
        'nome_mae' => 'permit_empty|max_length[255]',
        'nome_pai' => 'permit_empty|max_length[255]',
        'alergias' => 'permit_empty|max_length[1000]',
        'observacoes' => 'permit_empty|max_length[1000]',
        'sus' => 'permit_empty|max_length[15]',
        'idade' => 'permit_empty|integer|greater_than_equal_to[0]|less_than[200]',
        'id_logradouro' => 'permit_empty|is_natural_no_zero',
        'cidade_externa' => 'permit_empty|max_length[100]',
        'logradouro_externo' => 'permit_empty|max_length[255]',
        'cep_externo' => 'permit_empty|max_length[10]'
    ];
    
    protected $validationMessages = [
        'nome' => [
            'required' => 'O nome é obrigatório',
            'max_length' => 'O nome deve ter no máximo 255 caracteres'
        ],
        'cpf' => [
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
        $pacientes = $this->select('pacientes.*, logradouros.nome_logradouro, logradouros.tipo_logradouro, logradouros.cep, logradouros.cidade, bairros.nome_bairro, bairros.area')
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
     * Busca pacientes com seus logradouros com paginação
     */
    public function getPacientesWithLogradouroPaginated($perPage = 20)
    {
        $query = $this->select('pacientes.*, logradouros.nome_logradouro, logradouros.tipo_logradouro, logradouros.cep, logradouros.cidade, bairros.nome_bairro, bairros.area')
                      ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'left')
                      ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro', 'left')
                      ->orderBy('pacientes.nome', 'ASC');

        $pacientes = $query->paginate($perPage);

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
        $paciente = $this->select('pacientes.*, logradouros.nome_logradouro, logradouros.tipo_logradouro, logradouros.cep, logradouros.cidade, bairros.nome_bairro, bairros.area')
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
     * Regras de validação específicas para inserção
     */
    public function getInsertValidationRules()
    {
        return array_merge($this->validationRules, [
            'cpf' => 'permit_empty|max_length[14]' // CPF opcional, permite duplicatas
        ]);
    }

    /**
     * Regras de validação específicas para atualização
     */
    public function getUpdateValidationRules($id)
    {
        return array_merge($this->validationRules, [
            'cpf' => 'permit_empty|max_length[14]' // CPF opcional, permite duplicatas
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
     * Busca pacientes excluídos (soft deleted)
     */
    public function getPacientesExcluidos()
    {
        return $this->onlyDeleted()->findAll();
    }

    /**
     * Restaura um paciente excluído
     */
    public function restaurarPaciente($id)
    {
        return $this->update($id, ['deleted_at' => null]);
    }

    /**
     * Busca pacientes incluindo os excluídos
     */
    public function getPacientesComExcluidos()
    {
        return $this->withDeleted()->findAll();
    }

    /**
     * Busca paciente por CPF incluindo excluídos
     */
    public function getPacienteByCpfComExcluidos($cpf)
    {
        return $this->withDeleted()->where('cpf', $cpf)->first();
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

    /**
     * Busca pacientes por nome ou CPF com paginação
     */
    public function buscarPacientesPaginated($termo, $perPage = 20)
    {
        $query = $this->select('pacientes.*, logradouros.nome_logradouro, logradouros.tipo_logradouro, logradouros.cep, logradouros.cidade, bairros.nome_bairro, bairros.area')
                      ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'left')
                      ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro', 'left')
                      ->groupStart()
                          ->like('pacientes.nome', $termo)
                          ->orLike('pacientes.cpf', $termo)
                          ->orLike('pacientes.numero_sus', $termo)
                      ->groupEnd()
                      ->orderBy('pacientes.nome', 'ASC');

        $pacientes = $query->paginate($perPage);

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
     * Verifica se o paciente reside em outra cidade
     */
    public function isEnderecoExterno($paciente)
    {
        return !empty($paciente['cidade_externa']);
    }

    /**
     * Retorna o endereço completo do paciente (local ou externo)
     */
    public function getEnderecoCompleto($paciente)
    {
        if ($this->isEnderecoExterno($paciente)) {
            // Endereço externo
            $endereco = $paciente['logradouro_externo'] ?? '';
            if (!empty($paciente['numero'])) {
                $endereco .= ', ' . $paciente['numero'];
            }
            if (!empty($paciente['complemento'])) {
                $endereco .= ' - ' . $paciente['complemento'];
            }
            if (!empty($paciente['cidade_externa'])) {
                $endereco .= ' - ' . $paciente['cidade_externa'];
            }
            if (!empty($paciente['cep_externo'])) {
                $endereco .= ' - CEP: ' . $paciente['cep_externo'];
            }
        } else {
            // Endereço local com logradouro cadastrado
            $endereco = '';
            if (!empty($paciente['tipo_logradouro']) && !empty($paciente['nome_logradouro'])) {
                $endereco = ($paciente['tipo_logradouro'] ?? '') . ' ' . $paciente['nome_logradouro'];
            }
            if (!empty($paciente['numero'])) {
                $endereco .= ', ' . $paciente['numero'];
            }
            if (!empty($paciente['complemento'])) {
                $endereco .= ' - ' . $paciente['complemento'];
            }
            if (!empty($paciente['nome_bairro'])) {
                $endereco .= ' - ' . $paciente['nome_bairro'];
            }
            if (!empty($paciente['cep'])) {
                $endereco .= ' - CEP: ' . $paciente['cep'];
            }
        }

        return trim($endereco, ' -,');
    }

    /**
     * Retorna a cidade do paciente (local ou externa)
     */
    public function getCidadePaciente($paciente)
    {
        return $this->isEnderecoExterno($paciente) 
            ? $paciente['cidade_externa'] 
            : ($paciente['cidade'] ?? 'Cidade Local');
    }

    /**
     * Retorna apenas pacientes de cidades externas
     */
    public function getPacientesExternos($limit = null, $offset = null)
    {
        $builder = $this->select('pacientes.*, logradouros.tipo_logradouro, logradouros.nome_logradouro, bairros.nome_bairro')
                        ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'left')
                        ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro', 'left')
                        ->where('pacientes.cidade_externa IS NOT NULL')
                        ->where('pacientes.cidade_externa !=', '')
                        ->orderBy('pacientes.nome', 'ASC');
        
        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->findAll();
    }

    /**
     * Retorna pacientes de uma cidade externa específica
     */
    public function getPacientesExternosPorCidade($cidade, $limit = null, $offset = null)
    {
        $builder = $this->select('pacientes.*, logradouros.tipo_logradouro, logradouros.nome_logradouro, bairros.nome_bairro')
                        ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'left')
                        ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro', 'left')
                        ->where('pacientes.cidade_externa', $cidade)
                        ->orderBy('pacientes.nome_paciente', 'ASC');
        
        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }
        
        return $builder->findAll();
    }

    /**
     * Retorna estatísticas das cidades externas
     */
    public function getEstatisticasCidadesExternas()
    {
        return $this->select('cidade_externa, COUNT(*) as total_pacientes')
                   ->where('cidade_externa IS NOT NULL')
                   ->where('cidade_externa !=', '')
                   ->groupBy('cidade_externa')
                   ->orderBy('total_pacientes', 'DESC')
                   ->findAll();
    }

    /**
     * Conta total de pacientes externos
     */
    public function countPacientesExternos()
    {
        return $this->where('cidade_externa IS NOT NULL')
                   ->where('cidade_externa !=', '')
                   ->countAllResults();
    }
}
