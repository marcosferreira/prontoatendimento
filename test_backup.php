<?php

// Bootstrap CodeIgniter diretamente
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);

// Fake environment variables for testing
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['HTTPS'] = 'off';

require 'app/Config/Paths.php';
$paths = new Config\Paths();

require 'system/bootstrap.php';

$app = new \CodeIgniter\CodeIgniter(new \Config\App());
$app->initialize();

// Test BackupManager
try {
    echo "=== Teste do Sistema de Backup ===\n\n";
    
    // Teste 1: Verificar se BackupManager pode ser instanciado
    echo "1. Testando instanciação do BackupManager...\n";
    $backupManager = new \App\Libraries\BackupManager();
    echo "✓ BackupManager instanciado com sucesso\n\n";
    
    // Teste 2: Verificar mysqldump
    echo "2. Testando disponibilidade do mysqldump...\n";
    $mysqldumpAvailable = $backupManager->verificarMysqldump();
    if ($mysqldumpAvailable) {
        echo "✓ mysqldump está disponível\n\n";
    } else {
        echo "✗ mysqldump NÃO está disponível\n\n";
    }
    
    // Teste 3: Verificar BackupModel
    echo "3. Testando BackupModel...\n";
    $backupModel = new \App\Models\BackupModel();
    echo "✓ BackupModel instanciado com sucesso\n\n";
    
    // Teste 4: Obter último backup
    echo "4. Testando obtenção do último backup...\n";
    $ultimoBackup = $backupManager->getUltimoBackup();
    if ($ultimoBackup) {
        echo "✓ Último backup encontrado: " . $ultimoBackup['nome_arquivo'] . "\n\n";
    } else {
        echo "ℹ Nenhum backup encontrado (normal para primeira execução)\n\n";
    }
    
    // Teste 5: Obter histórico de backups
    echo "5. Testando obtenção do histórico de backups...\n";
    $historico = $backupManager->getHistorico(5);
    echo "✓ Histórico obtido: " . count($historico) . " backup(s) encontrado(s)\n\n";
    
    // Teste 6: Estatísticas
    echo "6. Testando estatísticas...\n";
    $stats = $backupManager->getEstatisticas();
    echo "✓ Estatísticas obtidas:\n";
    echo "   - Total: " . $stats['total'] . "\n";
    echo "   - Sucessos: " . $stats['sucesso'] . "\n";
    echo "   - Erros: " . $stats['erro'] . "\n";
    echo "   - Taxa de sucesso: " . $stats['taxa_sucesso'] . "%\n\n";
    
    if ($mysqldumpAvailable) {
        echo "7. Testando criação de backup de dados...\n";
        $resultado = $backupManager->criarBackupDados('Teste via CLI');
        
        if ($resultado['sucesso']) {
            echo "✓ Backup de dados criado com sucesso!\n";
            echo "   - Arquivo: " . $resultado['arquivo'] . "\n";
            echo "   - Tamanho: " . number_format($resultado['tamanho']) . " bytes\n";
            echo "   - ID: " . $resultado['id'] . "\n\n";
        } else {
            echo "✗ Erro ao criar backup de dados: " . $resultado['erro'] . "\n\n";
        }
    } else {
        echo "7. Pulando teste de criação de backup (mysqldump não disponível)\n\n";
    }
    
    echo "=== Todos os testes concluídos ===\n";
    
} catch (\Exception $e) {
    echo "✗ Erro durante o teste: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
