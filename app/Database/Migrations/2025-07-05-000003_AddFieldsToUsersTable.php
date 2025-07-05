<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFieldsToUsersTable extends Migration
{
    public function up()
    {
        // Adiciona campos à tabela users
        $fields = [
            'nome' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'username',
            ],
            'cpf' => [
                'type' => 'VARCHAR',
                'constraint' => 14,
                'null' => true,
                'after' => 'nome',
            ],
            'last_active' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'active',
            ],
            'force_pass_reset' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'null' => false,
                'after' => 'last_active',
            ],
        ];

        try {
            $this->forge->addColumn('users', $fields);
        } catch (\Exception $e) {
            // Campos podem já existir, continua
        }

        // Adiciona índice no CPF
        try {
            $this->db->query('ALTER TABLE users ADD INDEX idx_users_cpf (cpf)');
        } catch (\Exception $e) {
            // Índice pode já existir, continua
        }
    }

    public function down()
    {
        $fieldsToRemove = ['nome', 'cpf', 'last_active', 'force_pass_reset'];
        
        try {
            $this->forge->dropColumn('users', $fieldsToRemove);
        } catch (\Exception $e) {
            // Campos podem não existir, continua
        }

        // Remove índice
        try {
            $this->db->query('ALTER TABLE users DROP INDEX idx_users_cpf');
        } catch (\Exception $e) {
            // Índice pode não existir, continua
        }
    }
}
