<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestBackupRoutes extends BaseCommand
{
    protected $group = 'SisPAM';
    protected $name = 'test:backup-routes';
    protected $description = 'Testa as rotas de backup via HTTP';

    public function run(array $params)
    {
        CLI::write('=== Teste das Rotas de Backup ===', 'yellow');
        CLI::write('');

        $baseUrl = 'http://localhost:8080';
        
        // Test routes
        $routes = [
            'ultimoBackup' => '/configuracoes/ultimoBackup',
            'historicoBackups' => '/configuracoes/historicoBackups',
        ];

        foreach ($routes as $name => $route) {
            CLI::write("Testando rota: {$name} ({$route})", 'cyan');
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseUrl . $route);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'X-Requested-With: XMLHttpRequest',
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                CLI::write("✗ Erro cURL: {$error}", 'red');
                continue;
            }
            
            CLI::write("   HTTP Status: {$httpCode}");
            
            if ($httpCode === 200) {
                $data = json_decode($response, true);
                if ($data) {
                    CLI::write("   ✓ JSON válido retornado", 'green');
                    CLI::write("   Resposta: " . substr($response, 0, 200) . (strlen($response) > 200 ? '...' : ''));
                } else {
                    CLI::write("   ✗ Resposta não é JSON válido", 'red');
                    CLI::write("   Resposta: " . substr($response, 0, 200) . (strlen($response) > 200 ? '...' : ''));
                }
            } elseif ($httpCode === 302) {
                CLI::write("   ℹ Redirecionamento (provavelmente para login)", 'yellow');
            } else {
                CLI::write("   ✗ Código HTTP inesperado", 'red');
                CLI::write("   Resposta: " . substr($response, 0, 200) . (strlen($response) > 200 ? '...' : ''));
            }
            
            CLI::write('');
        }
        
        CLI::write('=== Teste de Rotas Concluído ===', 'green');
        CLI::write('');
        CLI::write('NOTA: Se as rotas retornarem 302 (redirecionamento), isso indica', 'yellow');
        CLI::write('que a autenticação está bloqueando o acesso. É necessário estar', 'yellow');
        CLI::write('logado no sistema para acessar as funcionalidades de backup.', 'yellow');
    }
}
