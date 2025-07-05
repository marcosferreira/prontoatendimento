<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditoriaModel extends Model
{
    protected $table = 'auditoria';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'usuario_id',
        'usuario_nome',
        'acao',
        'modulo',
        'detalhes',
        'ip_address',
        'user_agent',
        'dados_anteriores',
        'dados_novos'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'usuario_id' => 'permit_empty|integer',
        'usuario_nome' => 'required|max_length[255]',
        'acao' => 'required|max_length[100]',
        'modulo' => 'required|max_length[100]',
        'detalhes' => 'permit_empty|max_length[500]',
        'ip_address' => 'permit_empty|valid_ip',
        'user_agent' => 'permit_empty|max_length[500]',
        'dados_anteriores' => 'permit_empty',
        'dados_novos' => 'permit_empty'
    ];

    protected $validationMessages = [
        'usuario_nome' => [
            'required' => 'O nome do usuário é obrigatório.',
            'max_length' => 'O nome do usuário deve ter no máximo 255 caracteres.'
        ],
        'acao' => [
            'required' => 'A ação é obrigatória.',
            'max_length' => 'A ação deve ter no máximo 100 caracteres.'
        ],
        'modulo' => [
            'required' => 'O módulo é obrigatório.',
            'max_length' => 'O módulo deve ter no máximo 100 caracteres.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert = ['addRequestInfo'];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Adiciona informações da requisição antes de inserir
     */
    protected function addRequestInfo(array $data): array
    {
        $request = \Config\Services::request();
        
        if (!isset($data['data']['ip_address'])) {
            $data['data']['ip_address'] = $request->getIPAddress();
        }
        
        if (!isset($data['data']['user_agent'])) {
            $data['data']['user_agent'] = $request->getUserAgent()->getAgentString();
        }

        return $data;
    }

    /**
     * Registra uma ação de auditoria
     */
    public function registrarAcao(
        string $acao,
        string $modulo,
        string $detalhes = '',
        ?int $usuarioId = null,
        ?string $usuarioNome = null,
        $dadosAnteriores = null,
        $dadosNovos = null
    ): bool {
        // Se não foi fornecido usuário, tenta pegar do session
        if (!$usuarioId || !$usuarioNome) {
            $session = session();
            $usuarioId = $session->get('user_id');
            $usuarioNome = $session->get('user_name') ?? 'Sistema';
        }

        $data = [
            'usuario_id' => $usuarioId,
            'usuario_nome' => $usuarioNome ?: 'Sistema',
            'acao' => $acao,
            'modulo' => $modulo,
            'detalhes' => $detalhes,
            'dados_anteriores' => $dadosAnteriores ? json_encode($dadosAnteriores) : null,
            'dados_novos' => $dadosNovos ? json_encode($dadosNovos) : null
        ];

        try {
            return $this->insert($data) !== false;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao registrar auditoria: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca logs de auditoria com filtros
     */
    public function getLogs(array $filtros = [], int $page = 1, int $perPage = 50): array
    {
        $builder = $this->builder();

        // Aplicar filtros
        if (!empty($filtros['acao'])) {
            $builder->where('acao', $filtros['acao']);
        }

        if (!empty($filtros['modulo'])) {
            $builder->where('modulo', $filtros['modulo']);
        }

        if (!empty($filtros['usuario_id'])) {
            $builder->where('usuario_id', $filtros['usuario_id']);
        }

        if (!empty($filtros['usuario_nome'])) {
            $builder->like('usuario_nome', $filtros['usuario_nome']);
        }

        if (!empty($filtros['data_inicio'])) {
            $builder->where('created_at >=', $filtros['data_inicio'] . ' 00:00:00');
        }

        if (!empty($filtros['data_fim'])) {
            $builder->where('created_at <=', $filtros['data_fim'] . ' 23:59:59');
        }

        // Contagem total
        $total = $builder->countAllResults(false);

        // Buscar dados paginados
        $offset = ($page - 1) * $perPage;
        $logs = $builder->orderBy('created_at', 'DESC')
                       ->limit($perPage, $offset)
                       ->get()
                       ->getResultArray();

        return [
            'data' => $logs,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    /**
     * Busca estatísticas de auditoria
     */
    public function getEstatisticas(): array
    {
        $db = \Config\Database::connect();

        // Total de logs
        $totalLogs = $this->countAll();

        // Logs por ação (últimos 30 dias)
        $dataInicio = date('Y-m-d', strtotime('-30 days'));
        $logsPorAcao = $db->query("
            SELECT acao, COUNT(*) as total 
            FROM {$this->table} 
            WHERE created_at >= ? 
            GROUP BY acao 
            ORDER BY total DESC 
            LIMIT 10
        ", [$dataInicio])->getResultArray();

        // Logs por módulo (últimos 30 dias)
        $logsPorModulo = $db->query("
            SELECT modulo, COUNT(*) as total 
            FROM {$this->table} 
            WHERE created_at >= ? 
            GROUP BY modulo 
            ORDER BY total DESC 
            LIMIT 10
        ", [$dataInicio])->getResultArray();

        // Usuários mais ativos (últimos 30 dias)
        $usuariosAtivos = $db->query("
            SELECT usuario_nome, COUNT(*) as total 
            FROM {$this->table} 
            WHERE created_at >= ? AND usuario_nome != 'Sistema'
            GROUP BY usuario_nome 
            ORDER BY total DESC 
            LIMIT 10
        ", [$dataInicio])->getResultArray();

        // Logs por dia (últimos 7 dias)
        $logsPorDia = $db->query("
            SELECT DATE(created_at) as data, COUNT(*) as total 
            FROM {$this->table} 
            WHERE created_at >= ? 
            GROUP BY DATE(created_at) 
            ORDER BY data DESC 
            LIMIT 7
        ", [date('Y-m-d', strtotime('-7 days'))])->getResultArray();

        return [
            'total_logs' => $totalLogs,
            'logs_por_acao' => $logsPorAcao,
            'logs_por_modulo' => $logsPorModulo,
            'usuarios_ativos' => $usuariosAtivos,
            'logs_por_dia' => $logsPorDia
        ];
    }

    /**
     * Limpa logs antigos
     */
    public function limparLogsAntigos(int $diasRetencao = 90): int
    {
        $dataLimite = date('Y-m-d', strtotime("-{$diasRetencao} days"));
        
        try {
            $builder = $this->builder();
            $deletados = $builder->where('created_at <', $dataLimite)->delete();
            
            log_message('info', "Limpeza de auditoria: {$deletados} registros removidos (antes de {$dataLimite})");
            
            return $deletados;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao limpar logs de auditoria: ' . $e->getMessage());
            return 0;
        }
    }
}
