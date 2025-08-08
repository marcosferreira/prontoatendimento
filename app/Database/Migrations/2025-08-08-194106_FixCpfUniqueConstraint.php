<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixCpfUniqueConstraint extends Migration
{
    public function up()
    {
        $prefix = $this->db->DBPrefix;
        
        // 1. Atualizar todos os CPFs vazios para NULL
        $this->db->query("UPDATE {$prefix}pacientes SET cpf = NULL WHERE cpf = '' OR cpf = '0' OR cpf = '00000000000' OR cpf = '000.000.000-00'");
        
        // 2. Remover o índice único atual do CPF
        try {
            $this->db->query("ALTER TABLE {$prefix}pacientes DROP INDEX cpf");
        } catch (\Exception $e) {
            // Ignora se o índice não existir
        }
        
        // 3. Remover também o índice regular se existir
        try {
            $this->db->query("ALTER TABLE {$prefix}pacientes DROP INDEX idx_paciente_cpf");
        } catch (\Exception $e) {
            // Ignora se o índice não existir
        }
        
        // 4. Remover índice único criado anteriormente se existir
        try {
            $this->db->query("ALTER TABLE {$prefix}pacientes DROP INDEX idx_cpf_unique");
        } catch (\Exception $e) {
            // Ignora se o índice não existir
        }
        
        // 5. Criar apenas índice de busca para performance (não único) - permite duplicatas
        $this->db->query("CREATE INDEX idx_cpf_search ON {$prefix}pacientes (cpf)");
    }

    public function down()
    {
        $prefix = $this->db->DBPrefix;
        
        // Reverter as alterações
        
        // 1. Remover índice de busca criado
        try {
            $this->db->query("DROP INDEX idx_cpf_search ON {$prefix}pacientes");
        } catch (\Exception $e) {
            // Ignora erro se o índice não existir
        }
        
        // 2. Recriar índice único original (CUIDADO: pode falhar se houver duplicatas)
        // $this->db->query("CREATE UNIQUE INDEX cpf ON {$prefix}pacientes (cpf)");
        
        log_message('warning', 'Migration rollback para FixCpfUniqueConstraint executada. CPF agora permite duplicatas.');
    }
}
