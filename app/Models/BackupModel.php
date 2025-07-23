<?php

namespace App\Models;

use CodeIgniter\Model;

class BackupModel extends Model
{
    protected $table = 'backups';
    protected $primaryKey = 'id_backup';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'nome_arquivo',
        'tipo',
        'tamanho',
        'status',
        'caminho_arquivo',
        'observacoes',
        'created_at'
    ];

    // Timestamps automáticos
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = null; // Não usar soft delete para backups

    // Validações
    protected $validationRules = [
        'nome_arquivo' => 'required|max_length[255]',
        'tipo' => 'required|in_list[completo,dados,incremental]',
        'status' => 'required|in_list[criando,sucesso,erro]',
        'caminho_arquivo' => 'permit_empty|max_length[500]'
    ];

    protected $validationMessages = [
        'nome_arquivo' => [
            'required' => 'O nome do arquivo é obrigatório',
            'max_length' => 'O nome do arquivo não pode ter mais que 255 caracteres'
        ],
        'tipo' => [
            'required' => 'O tipo de backup é obrigatório',
            'in_list' => 'Tipo de backup inválido'
        ],
        'status' => [
            'required' => 'O status é obrigatório',
            'in_list' => 'Status inválido'
        ]
    ];

    /**
     * Obtém histórico de backups com paginação
     */
    public function getHistorico(int $limite = 10, int $offset = 0): array
    {
        return $this->orderBy('created_at', 'DESC')
                    ->limit($limite, $offset)
                    ->findAll();
    }

    /**
     * Obtém o último backup realizado
     */
    public function getUltimoBackup(): ?array
    {
        return $this->where('status', 'sucesso')
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }

    /**
     * Registra um novo backup
     */
    public function registrarBackup(array $dados): bool
    {
        $backup = [
            'nome_arquivo' => $dados['nome_arquivo'],
            'tipo' => $dados['tipo'],
            'tamanho' => $dados['tamanho'] ?? 0,
            'status' => $dados['status'] ?? 'criando',
            'caminho_arquivo' => $dados['caminho_arquivo'] ?? null,
            'observacoes' => $dados['observacoes'] ?? null
        ];

        return $this->save($backup);
    }

    /**
     * Atualiza status do backup
     */
    public function atualizarStatus(int $id, string $status, array $dadosAdicionais = []): bool
    {
        $dados = ['status' => $status] + $dadosAdicionais;
        return $this->update($id, $dados);
    }

    /**
     * Remove backups antigos baseado na política de retenção
     */
    public function limparBackupsAntigos(int $diasRetencao = 30): int
    {
        $dataLimite = date('Y-m-d H:i:s', strtotime("-{$diasRetencao} days"));
        
        // Busca backups a serem removidos
        $backupsAntigos = $this->where('created_at <', $dataLimite)
                               ->where('status', 'sucesso')
                               ->findAll();

        $removidos = 0;
        foreach ($backupsAntigos as $backup) {
            // Remove arquivo físico se existir
            if (!empty($backup['caminho_arquivo']) && file_exists($backup['caminho_arquivo'])) {
                @unlink($backup['caminho_arquivo']);
            }
            
            // Remove registro do banco
            if ($this->delete($backup['id_backup'])) {
                $removidos++;
            }
        }

        return $removidos;
    }

    /**
     * Obtém estatísticas dos backups
     */
    public function getEstatisticas(): array
    {
        $total = $this->countAll();
        $sucesso = $this->where('status', 'sucesso')->countAllResults(false);
        $erro = $this->where('status', 'erro')->countAllResults();
        
        return [
            'total' => $total,
            'sucesso' => $sucesso,
            'erro' => $erro,
            'taxa_sucesso' => $total > 0 ? round(($sucesso / $total) * 100, 2) : 0
        ];
    }

    /**
     * Formata tamanho de arquivo
     */
    public function formatarTamanho(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        
        $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        
        return round($bytes / pow(1024, $i), 2) . ' ' . $unidades[$i];
    }
}
