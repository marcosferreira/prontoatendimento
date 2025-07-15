<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class UpdateUserData extends BaseCommand
{
    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Database';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'users:update-data';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Atualiza dados dos usuários existentes que têm campos nome/cpf vazios';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'users:update-data';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Actually execute a command.
     *
     * @param array $params
     */
    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write('Iniciando atualização dos dados dos usuários...', 'yellow');
        
        // Busca usuários com campos nome ou cpf vazios
        $users = $db->table('users')
                   ->select('id, username')
                   ->where('(nome IS NULL OR nome = "" OR cpf IS NULL OR cpf = "")')
                   ->where('deleted_at', null)
                   ->get()
                   ->getResultArray();
                   
        if (empty($users)) {
            CLI::write('Nenhum usuário encontrado com dados vazios.', 'green');
            return;
        }
        
        CLI::write('Encontrados ' . count($users) . ' usuários para atualizar.', 'yellow');
        
        foreach ($users as $user) {
            $username = $user['username'];
            $userId = $user['id'];
            
            CLI::write("Processando usuário ID {$userId} (username: {$username})...", 'white');
            
            // Busca email do usuário na tabela auth_identities
            $identity = $db->table('auth_identities')
                          ->select('secret')
                          ->where('user_id', $userId)
                          ->where('type', 'email_password')
                          ->get()
                          ->getFirstRow();
            
            $email = $identity ? $identity->secret : null;
            
            // Define dados padrão baseados no que temos
            $updateData = [];
            
            // Se nome está vazio, tenta usar uma parte do email ou username
            $currentUser = $db->table('users')->select('nome')->where('id', $userId)->get()->getFirstRow();
            if (empty($currentUser->nome)) {
                if ($email) {
                    $emailParts = explode('@', $email);
                    $updateData['nome'] = ucfirst($emailParts[0]);
                } else {
                    $updateData['nome'] = ucfirst($username);
                }
            }
            
            // Se CPF está vazio e o username parece ser um CPF, usa o username
            $currentCpf = $db->table('users')->select('cpf')->where('id', $userId)->get()->getFirstRow();
            if (empty($currentCpf->cpf)) {
                // Verifica se username tem formato de CPF (xxx.xxx.xxx-xx ou 11 dígitos)
                if (preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $username) || 
                    (strlen($username) == 11 && is_numeric($username))) {
                    $updateData['cpf'] = $username;
                } else {
                    // Gera um CPF fictício baseado no ID
                    $updateData['cpf'] = sprintf('%03d.%03d.%03d-%02d', 
                        ($userId % 1000), 
                        (($userId * 7) % 1000), 
                        (($userId * 13) % 1000), 
                        ($userId % 100)
                    );
                }
            }
            
            if (!empty($updateData)) {
                $db->table('users')->where('id', $userId)->update($updateData);
                CLI::write("  ✓ Atualizado: " . json_encode($updateData), 'green');
            } else {
                CLI::write("  • Nenhuma atualização necessária", 'white');
            }
        }
        
        CLI::write('Atualização concluída!', 'green');
        CLI::newLine();
        CLI::write('Verificando resultado...', 'yellow');
        
        // Verifica resultado
        $updated = $db->table('users u')
                     ->select('u.id, u.username, u.nome, u.cpf, ai.secret as email, gu.group as grupo_nome')
                     ->join('auth_identities ai', 'ai.user_id = u.id AND ai.type = "email_password"', 'left')
                     ->join('auth_groups_users gu', 'gu.user_id = u.id', 'left')
                     ->where('u.deleted_at', null)
                     ->get()
                     ->getResultArray();
        
        // Converte os dados para o formato esperado pelo CLI::table
        $tableData = [];
        foreach ($updated as $row) {
            $tableData[] = [
                $row['id'],
                $row['username'],
                $row['nome'] ?: 'N/A',
                $row['cpf'] ?: 'N/A',
                $row['email'] ?: 'N/A',
                $row['grupo_nome'] ?: 'N/A'
            ];
        }
        
        CLI::table(['ID', 'Username', 'Nome', 'CPF', 'Email', 'Grupo'], $tableData);
    }
}
