<?php

namespace App\Models;

use CodeIgniter\Model;

class ConfiguracaoModel extends Model
{
    protected $table = 'configuracoes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'chave',
        'valor',
        'descricao',
        'tipo',
        'categoria',
        'editavel'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'chave' => 'required|max_length[100]|is_unique[configuracoes.chave,id,{id}]',
        'valor' => 'required',
        'descricao' => 'permit_empty|max_length[255]',
        'tipo' => 'required|in_list[string,integer,boolean,float,json]',
        'categoria' => 'required|max_length[50]',
        'editavel' => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'chave' => [
            'required' => 'A chave da configuração é obrigatória.',
            'max_length' => 'A chave deve ter no máximo 100 caracteres.',
            'is_unique' => 'Esta chave já existe no sistema.'
        ],
        'valor' => [
            'required' => 'O valor da configuração é obrigatório.'
        ],
        'tipo' => [
            'required' => 'O tipo da configuração é obrigatório.',
            'in_list' => 'Tipo inválido. Use: string, integer, boolean, float ou json.'
        ],
        'categoria' => [
            'required' => 'A categoria é obrigatória.',
            'max_length' => 'A categoria deve ter no máximo 50 caracteres.'
        ],
        'editavel' => [
            'in_list' => 'O campo editável deve ser 0 ou 1.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = ['convertValueType'];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Converte o valor para o tipo correto após buscar no banco
     */
    protected function convertValueType(array $data): array
    {
        if (!isset($data['data']) || !is_array($data['data'])) {
            return $data;
        }

        foreach ($data['data'] as &$row) {
            if (isset($row['valor']) && isset($row['tipo'])) {
                $row['valor'] = $this->castValue($row['valor'], $row['tipo']);
            }
        }

        return $data;
    }

    /**
     * Converte valor para o tipo especificado
     */
    private function castValue($value, string $type)
    {
        switch ($type) {
            case 'integer':
                return (int) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'float':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Busca configuração por chave
     */
    public function getByChave(string $chave)
    {
        $config = $this->where('chave', $chave)->first();
        
        if (!$config) {
            return null;
        }

        return $this->castValue($config['valor'], $config['tipo']);
    }

    /**
     * Busca configurações por categoria
     */
    public function getByCategoria(string $categoria): array
    {
        $configs = $this->where('categoria', $categoria)->findAll();
        $result = [];

        foreach ($configs as $config) {
            $result[$config['chave']] = $this->castValue($config['valor'], $config['tipo']);
        }

        return $result;
    }

    /**
     * Atualiza configuração por chave
     */
    public function updateByChave(string $chave, $valor): bool
    {
        $config = $this->where('chave', $chave)->first();
        
        if (!$config) {
            return false;
        }

        // Converte valor para string para armazenar no banco
        if ($config['tipo'] === 'json') {
            $valor = json_encode($valor);
        } elseif ($config['tipo'] === 'boolean') {
            $valor = $valor ? '1' : '0';
        } else {
            $valor = (string) $valor;
        }

        return $this->where('chave', $chave)->set(['valor' => $valor])->update();
    }

    /**
     * Cria configurações padrão do sistema
     */
    public function createDefaultConfigs(): bool
    {
        $defaults = [
            // Configurações da Unidade
            [
                'chave' => 'unidade_nome',
                'valor' => 'Pronto Atendimento Municipal',
                'descricao' => 'Nome da unidade de saúde',
                'tipo' => 'string',
                'categoria' => 'unidade',
                'editavel' => 1
            ],
            [
                'chave' => 'unidade_cnpj',
                'valor' => '',
                'descricao' => 'CNPJ da unidade',
                'tipo' => 'string',
                'categoria' => 'unidade',
                'editavel' => 1
            ],
            [
                'chave' => 'unidade_endereco',
                'valor' => '',
                'descricao' => 'Endereço da unidade',
                'tipo' => 'string',
                'categoria' => 'unidade',
                'editavel' => 1
            ],
            [
                'chave' => 'unidade_telefone',
                'valor' => '',
                'descricao' => 'Telefone da unidade',
                'tipo' => 'string',
                'categoria' => 'unidade',
                'editavel' => 1
            ],
            
            // Configurações do Sistema
            [
                'chave' => 'sistema_timeout_sessao',
                'valor' => '60',
                'descricao' => 'Timeout de sessão em minutos',
                'tipo' => 'integer',
                'categoria' => 'sistema',
                'editavel' => 1
            ],
            [
                'chave' => 'sistema_tempo_triagem',
                'valor' => '15',
                'descricao' => 'Tempo limite para triagem em minutos',
                'tipo' => 'integer',
                'categoria' => 'sistema',
                'editavel' => 1
            ],
            [
                'chave' => 'sistema_capacidade_maxima',
                'valor' => '50',
                'descricao' => 'Capacidade máxima de atendimento',
                'tipo' => 'integer',
                'categoria' => 'sistema',
                'editavel' => 1
            ],
            [
                'chave' => 'sistema_notificacoes_email',
                'valor' => '1',
                'descricao' => 'Ativar notificações por email',
                'tipo' => 'boolean',
                'categoria' => 'sistema',
                'editavel' => 1
            ],
            
            // Configurações de Aparência
            [
                'chave' => 'aparencia_tema',
                'valor' => 'claro',
                'descricao' => 'Tema do sistema',
                'tipo' => 'string',
                'categoria' => 'aparencia',
                'editavel' => 1
            ],
            [
                'chave' => 'aparencia_cor_primaria',
                'valor' => '#1e3a8a',
                'descricao' => 'Cor primária do sistema',
                'tipo' => 'string',
                'categoria' => 'aparencia',
                'editavel' => 1
            ],
            
            // Configurações de Backup
            [
                'chave' => 'backup_automatico_ativo',
                'valor' => '1',
                'descricao' => 'Backup automático ativado',
                'tipo' => 'boolean',
                'categoria' => 'backup',
                'editavel' => 1
            ],
            [
                'chave' => 'backup_frequencia',
                'valor' => 'diario',
                'descricao' => 'Frequência do backup automático',
                'tipo' => 'string',
                'categoria' => 'backup',
                'editavel' => 1
            ],
            [
                'chave' => 'backup_horario',
                'valor' => '02:00',
                'descricao' => 'Horário do backup automático',
                'tipo' => 'string',
                'categoria' => 'backup',
                'editavel' => 1
            ],
            [
                'chave' => 'backup_retencao_dias',
                'valor' => '30',
                'descricao' => 'Dias de retenção dos backups',
                'tipo' => 'integer',
                'categoria' => 'backup',
                'editavel' => 1
            ]
        ];

        try {
            $this->db->transStart();
            
            foreach ($defaults as $config) {
                // Verifica se já existe
                $existing = $this->where('chave', $config['chave'])->first();
                if (!$existing) {
                    $this->insert($config);
                }
            }
            
            $this->db->transComplete();
            return $this->db->transStatus();
        } catch (\Exception $e) {
            log_message('error', 'Erro ao criar configurações padrão: ' . $e->getMessage());
            return false;
        }
    }
}
