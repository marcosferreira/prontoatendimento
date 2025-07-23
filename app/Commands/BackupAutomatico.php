<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\BackupManager;
use App\Models\ConfiguracaoModel;

class BackupAutomatico extends BaseCommand
{
    protected $group = 'SisPAM';
    protected $name = 'backup:automatico';
    protected $description = 'Executa backup automático do sistema conforme configurações';

    protected $backupManager;
    protected $configuracaoModel;

    public function run(array $params)
    {
        $this->backupManager = new BackupManager();
        $this->configuracaoModel = new ConfiguracaoModel();
        CLI::write('Iniciando backup automático...', 'yellow');

        try {
            // Verifica se backup automático está ativo
            $backupConfigs = $this->configuracaoModel->getByCategoria('backup');
            
            if (!($backupConfigs['backup_automatico_ativo'] ?? false)) {
                CLI::write('Backup automático está desativado.', 'cyan');
                return;
            }

            // Verifica se mysqldump está disponível
            if (!$this->backupManager->verificarMysqldump()) {
                CLI::write('✗ mysqldump não está disponível no sistema.', 'red');
                CLI::write('Instale o MySQL client para usar a funcionalidade de backup.', 'yellow');
                return;
            }

            CLI::write('Criando backup completo...', 'cyan');
            
            $resultado = $this->backupManager->criarBackupCompleto('Backup automático via CRON');

            if ($resultado['sucesso']) {
                CLI::write("✓ Backup criado com sucesso: {$resultado['arquivo']}", 'green');
                CLI::write("Tamanho: " . $this->formatarTamanho($resultado['tamanho']), 'cyan');
                
                // Limpa backups antigos conforme política de retenção
                $diasRetencao = (int)($backupConfigs['backup_retencao_dias'] ?? 30);
                $removidos = $this->backupManager->limparBackupsAntigos($diasRetencao);
                
                if ($removidos > 0) {
                    CLI::write("✓ {$removidos} backup(s) antigo(s) removido(s) (retenção: {$diasRetencao} dias)", 'cyan');
                }
                
                // Exibe estatísticas
                $stats = $this->backupManager->getEstatisticas();
                CLI::write("Estatísticas: {$stats['total']} total, {$stats['sucesso']} sucessos, {$stats['erro']} erros", 'cyan');
                
            } else {
                CLI::write("✗ Erro ao criar backup: {$resultado['erro']}", 'red');
                return;
            }

        } catch (\Exception $e) {
            CLI::write('✗ Erro no backup automático: ' . $e->getMessage(), 'red');
            log_message('error', 'Erro no backup automático: ' . $e->getMessage());
        }
    }

    private function formatarTamanho(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        
        $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        
        return round($bytes / pow(1024, $i), 2) . ' ' . $unidades[$i];
    }
}
