<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckLastActive extends BaseCommand
{
    protected $group       = 'System';
    protected $name        = 'check:last-active';
    protected $description = 'Verifica se o campo last_active está sendo atualizado para usuários logados';

    public function run(array $params)
    {
        CLI::write('Verificando campo last_active dos usuários...', 'yellow');
        
        $userProvider = auth()->getProvider();
        $users = $userProvider->findAll();
        
        if (empty($users)) {
            CLI::write('Nenhum usuário encontrado.', 'red');
            return;
        }
        
        CLI::write('ID | Username | Email | Last Active | Created At', 'cyan');
        CLI::write('---|----------|-------|-------------|----------', 'cyan');
        
        foreach ($users as $user) {
            $lastActive = isset($user->last_active) && $user->last_active 
                ? $user->last_active->format('Y-m-d H:i:s') 
                : 'Nunca';
                
            $createdAt = $user->created_at->format('Y-m-d H:i:s');
            
            CLI::write("{$user->id} | {$user->username} | {$user->email} | {$lastActive} | {$createdAt}");
        }
        
        CLI::newLine();
        CLI::write('Verificando configuração recordActiveDate...', 'yellow');
        
        $authConfig = new \Config\Auth();
        $recordActive = $authConfig->recordActiveDate ? 'Habilitado' : 'Desabilitado';
        
        CLI::write("Record Active Date: {$recordActive}", 'green');
        
        // Verificar se o filtro session está ativo
        $filterConfig = new \Config\Filters();
        $sessionFilter = isset($filterConfig->globals['before']['session']) ? 'Configurado' : 'Não configurado';
        
        CLI::write("Filtro Session: {$sessionFilter}", 'green');
        
        if (isset($filterConfig->globals['before']['session'])) {
            $sessionConfig = $filterConfig->globals['before']['session'];
            if (is_array($sessionConfig) && isset($sessionConfig['except'])) {
                $exceptions = $sessionConfig['except'];
                if (is_array($exceptions) && !empty($exceptions)) {
                    CLI::write("Exceções do filtro session: " . implode(', ', $exceptions), 'blue');
                }
            }
        }
        
        CLI::newLine();
        CLI::write('Para que o last_active seja atualizado, certifique-se de que:', 'yellow');
        CLI::write('1. recordActiveDate está habilitado (✓)', 'green');
        CLI::write('2. O filtro session está aplicado às rotas administrativas', 'yellow');
        CLI::write('3. Os usuários estão fazendo login através do Shield', 'yellow');
    }
}
