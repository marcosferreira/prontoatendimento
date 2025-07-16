<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificacaoModel extends Model
{
    protected $table            = 'notificacoes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tipo',
        'titulo',
        'descricao',
        'severidade',
        'modulo',
        'parametros',
        'status',
        'status_descricao', // já incluso
        'data_vencimento',
        'acionada_em',
        'resolvida_em',
        'usuario_responsavel',
        'metadata'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'tipo' => 'required|in_list[paciente_recorrente,surto_sintomas,alta_demanda,medicamento_critico,equipamento_falha,estatistica_anomala]',
        'titulo' => 'required|max_length[255]',
        'descricao' => 'required',
        'severidade' => 'required|in_list[baixa,media,alta,critica]',
        'modulo' => 'required|max_length[100]',
        'status' => 'in_list[ativa,resolvida,cancelada]'
    ];

    protected $validationMessages = [
        'tipo' => [
            'required' => 'O tipo da notificação é obrigatório',
            'in_list' => 'Tipo de notificação inválido'
        ],
        'titulo' => [
            'required' => 'O título é obrigatório',
            'max_length' => 'O título deve ter no máximo 255 caracteres'
        ],
        'descricao' => [
            'required' => 'A descrição é obrigatória'
        ],
        'severidade' => [
            'required' => 'A severidade é obrigatória',
            'in_list' => 'Severidade inválida'
        ],
        'modulo' => [
            'required' => 'O módulo é obrigatório',
            'max_length' => 'O módulo deve ter no máximo 100 caracteres'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['processarParametros'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['processarParametros'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Processa parâmetros JSON antes de inserir/atualizar
     */
    protected function processarParametros(array $data)
    {
        if (isset($data['data']['parametros']) && is_array($data['data']['parametros'])) {
            $data['data']['parametros'] = json_encode($data['data']['parametros']);
        }

        if (isset($data['data']['metadata']) && is_array($data['data']['metadata'])) {
            $data['data']['metadata'] = json_encode($data['data']['metadata']);
        }

        return $data;
    }

    /**
     * Busca notificações ativas
     */
    public function getNotificacoesAtivas()
    {
        return $this->where('status', 'ativa')
                   ->orderBy('severidade', 'DESC')
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Busca notificações por severidade
     */
    public function getBySeveridade($severidade)
    {
        return $this->where('severidade', $severidade)
                   ->where('status', 'ativa')
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Busca notificações por tipo
     */
    public function getByTipo($tipo)
    {
        return $this->where('tipo', $tipo)
                   ->where('status', 'ativa')
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Busca notificações por módulo
     */
    public function getByModulo($modulo)
    {
        return $this->where('modulo', $modulo)
                   ->where('status', 'ativa')
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }

    /**
     * Estatísticas de notificações
     */
    public function getEstatisticas()
    {
        $stats = [
            'total_ativas' => $this->where('status', 'ativa')->countAllResults(),
            'criticas' => $this->where('status', 'ativa')->where('severidade', 'critica')->countAllResults(),
            'altas' => $this->where('status', 'ativa')->where('severidade', 'alta')->countAllResults(),
            'medias' => $this->where('status', 'ativa')->where('severidade', 'media')->countAllResults(),
            'baixas' => $this->where('status', 'ativa')->where('severidade', 'baixa')->countAllResults()
        ];

        // Estatísticas por tipo
        $tipos = $this->select('tipo, COUNT(*) as total')
                     ->where('status', 'ativa')
                     ->groupBy('tipo')
                     ->findAll();

        $stats['por_tipo'] = [];
        foreach ($tipos as $tipo) {
            $stats['por_tipo'][$tipo['tipo']] = (int)$tipo['total'];
        }

        // Tendência dos últimos 7 dias
        $tendencia = $this->select('DATE(created_at) as data, COUNT(*) as total')
                          ->where('created_at >=', date('Y-m-d', strtotime('-7 days')))
                          ->groupBy('DATE(created_at)')
                          ->orderBy('data', 'ASC')
                          ->findAll();

        $stats['tendencia_7_dias'] = [];
        foreach ($tendencia as $item) {
            $stats['tendencia_7_dias'][] = [
                'data' => $item['data'],
                'total' => (int)$item['total']
            ];
        }

        // Garantir que existem dados para os últimos 7 dias (preenchendo zeros se necessário)
        $datasCompletas = [];
        for ($i = 6; $i >= 0; $i--) {
            $data = date('Y-m-d', strtotime("-{$i} days"));
            $existe = false;
            foreach ($stats['tendencia_7_dias'] as $item) {
                if ($item['data'] === $data) {
                    $datasCompletas[] = $item;
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                $datasCompletas[] = ['data' => $data, 'total' => 0];
            }
        }
        $stats['tendencia_7_dias'] = $datasCompletas;

        return $stats;
    }

    /**
     * Marca notificação como resolvida
     */
    public function marcarResolvida($id, $usuarioId = null, $descricao = null)
    {
        $data = [
            'status' => 'resolvida',
            'resolvida_em' => date('Y-m-d H:i:s')
        ];

        if ($usuarioId) {
            $data['usuario_responsavel'] = $usuarioId;
        }
        if ($descricao) {
            $data['status_descricao'] = $descricao;
        }

        return $this->update($id, $data);
    }

    /**
     * Cancela notificação
     */
    public function cancelarNotificacao($id, $motivo = null, $descricao = null)
    {
        $data = [
            'status' => 'cancelada'
        ];

        if ($descricao) {
            $data['status_descricao'] = $descricao;
        }

        if ($motivo) {
            $metadata = ['motivo_cancelamento' => $motivo];
            $data['metadata'] = json_encode($metadata);
        }

        return $this->update($id, $data);
    }

    /**
     * Limpa notificações antigas resolvidas
     */
    public function limparNotificacoesAntigas($dias = 30)
    {
        $dataLimite = date('Y-m-d H:i:s', strtotime("-{$dias} days"));
        
        return $this->where('status', 'resolvida')
                   ->where('resolvida_em <', $dataLimite)
                   ->delete();
    }

    /**
     * Verifica se existe notificação similar ativa
     */
    public function existeNotificacaoSimilar($tipo, $parametros)
    {
        $parametrosJson = is_array($parametros) ? json_encode($parametros) : $parametros;
        
        return $this->where('tipo', $tipo)
                   ->where('parametros', $parametrosJson)
                   ->where('status', 'ativa')
                   ->first();
    }

    /**
     * Cria nova notificação se não existir similar
     */
    public function criarNotificacaoUnica($dados)
    {
        // Verifica se já existe notificação similar ativa
        $existente = $this->existeNotificacaoSimilar(
            $dados['tipo'], 
            $dados['parametros'] ?? []
        );

        if ($existente) {
            return $existente['id']; // Retorna ID da existente
        }

        // Se não existe, cria nova
        $dados['status'] = 'ativa';
        $dados['acionada_em'] = date('Y-m-d H:i:s');

        return $this->insert($dados);
    }
}
