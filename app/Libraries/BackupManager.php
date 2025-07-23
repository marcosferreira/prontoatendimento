<?php

namespace App\Libraries;

use App\Models\BackupModel;
use App\Models\AuditoriaModel;
use CodeIgniter\Database\Config;

class BackupManager
{
    protected $backupModel;
    protected $auditoriaModel;
    protected $db;
    protected $backupPath;

    public function __construct()
    {
        $this->backupModel = new BackupModel();
        $this->auditoriaModel = new AuditoriaModel();
        $this->db = \Config\Database::connect();
        
        // Define diretório de backup
        $this->backupPath = WRITEPATH . 'backups/';
        
        // Cria diretório se não existir
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath, 0755, true);
        }
    }

    /**
     * Cria backup completo do banco de dados
     */
    public function criarBackupCompleto(string $observacoes = ''): array
    {
        try {
            $nomeArquivo = 'backup_completo_' . date('Y-m-d_H-i-s') . '.sql';
            $caminhoCompleto = $this->backupPath . $nomeArquivo;

            // Registra início do backup
            $backupId = $this->backupModel->insert([
                'nome_arquivo' => $nomeArquivo,
                'tipo' => 'completo',
                'status' => 'criando',
                'caminho_arquivo' => $caminhoCompleto,
                'observacoes' => $observacoes
            ]);

            // Executa backup usando mysqldump
            $resultado = $this->executarMysqldump($caminhoCompleto, true);

            if ($resultado['sucesso']) {
                // Atualiza status e tamanho
                $tamanho = file_exists($caminhoCompleto) ? filesize($caminhoCompleto) : 0;
                
                $this->backupModel->atualizarStatus($backupId, 'sucesso', [
                    'tamanho' => $tamanho
                ]);

                // Registra auditoria
                $this->auditoriaModel->registrarAcao(
                    'Backup Criado',
                    'Backup',
                    "Backup completo criado: {$nomeArquivo} (" . $this->formatarTamanho($tamanho) . ")"
                );

                return [
                    'sucesso' => true,
                    'arquivo' => $nomeArquivo,
                    'caminho' => $caminhoCompleto,
                    'tamanho' => $tamanho,
                    'id' => $backupId
                ];
            } else {
                // Atualiza status para erro
                $this->backupModel->atualizarStatus($backupId, 'erro', [
                    'observacoes' => $observacoes . ' | Erro: ' . $resultado['erro']
                ]);

                return [
                    'sucesso' => false,
                    'erro' => $resultado['erro']
                ];
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro ao criar backup completo: ' . $e->getMessage());
            
            if (isset($backupId)) {
                $this->backupModel->atualizarStatus($backupId, 'erro', [
                    'observacoes' => $observacoes . ' | Erro: ' . $e->getMessage()
                ]);
            }

            return [
                'sucesso' => false,
                'erro' => $e->getMessage()
            ];
        }
    }

    /**
     * Cria backup apenas dos dados (sem estrutura)
     */
    public function criarBackupDados(string $observacoes = ''): array
    {
        try {
            $nomeArquivo = 'backup_dados_' . date('Y-m-d_H-i-s') . '.sql';
            $caminhoCompleto = $this->backupPath . $nomeArquivo;

            // Registra início do backup
            $backupId = $this->backupModel->insert([
                'nome_arquivo' => $nomeArquivo,
                'tipo' => 'dados',
                'status' => 'criando',
                'caminho_arquivo' => $caminhoCompleto,
                'observacoes' => $observacoes
            ]);

            // Executa backup apenas dados
            $resultado = $this->executarMysqldump($caminhoCompleto, false);

            if ($resultado['sucesso']) {
                $tamanho = file_exists($caminhoCompleto) ? filesize($caminhoCompleto) : 0;
                
                $this->backupModel->atualizarStatus($backupId, 'sucesso', [
                    'tamanho' => $tamanho
                ]);

                $this->auditoriaModel->registrarAcao(
                    'Backup Criado',
                    'Backup',
                    "Backup de dados criado: {$nomeArquivo} (" . $this->formatarTamanho($tamanho) . ")"
                );

                return [
                    'sucesso' => true,
                    'arquivo' => $nomeArquivo,
                    'caminho' => $caminhoCompleto,
                    'tamanho' => $tamanho,
                    'id' => $backupId
                ];
            } else {
                $this->backupModel->atualizarStatus($backupId, 'erro', [
                    'observacoes' => $observacoes . ' | Erro: ' . $resultado['erro']
                ]);

                return [
                    'sucesso' => false,
                    'erro' => $resultado['erro']
                ];
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro ao criar backup de dados: ' . $e->getMessage());
            
            if (isset($backupId)) {
                $this->backupModel->atualizarStatus($backupId, 'erro', [
                    'observacoes' => $observacoes . ' | Erro: ' . $e->getMessage()
                ]);
            }

            return [
                'sucesso' => false,
                'erro' => $e->getMessage()
            ];
        }
    }

    /**
     * Executa mysqldump
     */
    private function executarMysqldump(string $caminhoArquivo, bool $incluirEstrutura = true): array
    {
        $dbConfig = [
            'username' => env('database.default.username', ''),
            'password' => env('database.default.password', ''),
            'hostname' => env('database.default.hostname', 'localhost'),
            'port' => env('database.default.port', 3306),
            'database' => env('database.default.database', '')
        ];

        $comando = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%d %s %s %s > %s 2>&1',
            escapeshellarg($dbConfig['username']),
            escapeshellarg($dbConfig['password']),
            escapeshellarg($dbConfig['hostname']),
            $dbConfig['port'],
            $incluirEstrutura ? '--routines --triggers' : '--no-create-info --skip-triggers',
            '--single-transaction --quick --lock-tables=false',
            escapeshellarg($dbConfig['database']),
            escapeshellarg($caminhoArquivo)
        );

        $output = [];
        $returnCode = 0;
        
        exec($comando, $output, $returnCode);

        if ($returnCode === 0 && file_exists($caminhoArquivo) && filesize($caminhoArquivo) > 0) {
            return [
                'sucesso' => true,
                'arquivo' => $caminhoArquivo
            ];
        } else {
            $erro = implode("\n", $output);
            log_message('error', "Mysqldump falhou: {$erro}");
            
            return [
                'sucesso' => false,
                'erro' => $erro ?: 'Erro desconhecido no mysqldump'
            ];
        }
    }

    /**
     * Restaura backup do banco de dados
     */
    public function restaurarBackup(string $caminhoArquivo): array
    {
        try {
            if (!file_exists($caminhoArquivo)) {
                return [
                    'sucesso' => false,
                    'erro' => 'Arquivo de backup não encontrado'
                ];
            }

            $dbConfig = [
                'username' => env('database.default.username', ''),
                'password' => env('database.default.password', ''),
                'hostname' => env('database.default.hostname', 'localhost'),
                'port' => env('database.default.port', 3306),
                'database' => env('database.default.database', '')
            ];

            $comando = sprintf(
                'mysql --user=%s --password=%s --host=%s --port=%d %s < %s 2>&1',
                escapeshellarg($dbConfig['username']),
                escapeshellarg($dbConfig['password']),
                escapeshellarg($dbConfig['hostname']),
                $dbConfig['port'],
                escapeshellarg($dbConfig['database']),
                escapeshellarg($caminhoArquivo)
            );

            $output = [];
            $returnCode = 0;
            
            exec($comando, $output, $returnCode);

            if ($returnCode === 0) {
                $this->auditoriaModel->registrarAcao(
                    'Backup Restaurado',
                    'Backup',
                    "Backup restaurado: " . basename($caminhoArquivo)
                );

                return [
                    'sucesso' => true,
                    'arquivo' => basename($caminhoArquivo)
                ];
            } else {
                $erro = implode("\n", $output);
                log_message('error', "Restore falhou: {$erro}");
                
                return [
                    'sucesso' => false,
                    'erro' => $erro ?: 'Erro desconhecido na restauração'
                ];
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro ao restaurar backup: ' . $e->getMessage());
            
            return [
                'sucesso' => false,
                'erro' => $e->getMessage()
            ];
        }
    }

    /**
     * Obtém informações do último backup
     */
    public function getUltimoBackup(): ?array
    {
        return $this->backupModel->getUltimoBackup();
    }

    /**
     * Obtém histórico de backups
     */
    public function getHistorico(int $limite = 10): array
    {
        return $this->backupModel->getHistorico($limite);
    }

    /**
     * Limpa backups antigos
     */
    public function limparBackupsAntigos(int $diasRetencao = 30): int
    {
        return $this->backupModel->limparBackupsAntigos($diasRetencao);
    }

    /**
     * Formata tamanho de arquivo
     */
    private function formatarTamanho(int $bytes): string
    {
        return $this->backupModel->formatarTamanho($bytes);
    }

    /**
     * Verifica se o mysqldump está disponível
     */
    public function verificarMysqldump(): bool
    {
        $output = [];
        $returnCode = 0;
        
        exec('mysqldump --version 2>&1', $output, $returnCode);
        
        return $returnCode === 0;
    }

    /**
     * Obtém estatísticas dos backups
     */
    public function getEstatisticas(): array
    {
        return $this->backupModel->getEstatisticas();
    }
}
