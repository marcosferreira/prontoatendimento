<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteToTables extends Migration
{
    public function up()
    {
        // Adicionar campo deleted_at para cada tabela
        $tables = [
            'atendimentos',
            'atendimento_exames', 
            'atendimento_procedimentos',
            'bairros',
            'exames',
            'logradouros',
            'medicos',
            'pacientes',
            'procedimentos'
        ];

        foreach ($tables as $table) {
            try {
                // Tentar adicionar a coluna deleted_at
                $this->forge->addColumn($table, [
                    'deleted_at' => [
                        'type' => 'DATETIME',
                        'null' => true,
                        'after' => 'updated_at'
                    ]
                ]);
                
                // Criar índice para melhor performance
                $this->db->query("CREATE INDEX idx_{$table}_deleted_at ON {$table} (deleted_at)");
                
            } catch (\Exception $e) {
                // Se der erro (provavelmente porque a coluna já existe), continuar
                log_message('info', "Soft delete migration: Could not add deleted_at to {$table}: " . $e->getMessage());
            }
        }
    }

    public function down()
    {
        // Remover campo deleted_at de cada tabela
        $tables = [
            'atendimentos',
            'atendimento_exames',
            'atendimento_procedimentos', 
            'bairros',
            'exames',
            'logradouros',
            'medicos',
            'pacientes',
            'procedimentos'
        ];

        foreach ($tables as $table) {
            try {
                // Remover índice
                $this->db->query("DROP INDEX IF EXISTS idx_{$table}_deleted_at");
                
                // Remover coluna
                $this->forge->dropColumn($table, 'deleted_at');
                
            } catch (\Exception $e) {
                // Se der erro, continuar
                log_message('info', "Soft delete migration rollback: Could not remove deleted_at from {$table}: " . $e->getMessage());
            }
        }
    }
}
