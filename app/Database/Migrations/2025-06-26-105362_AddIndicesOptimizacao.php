<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndicesOptimizacao extends Migration
{
    public function up()
    {
        // Obter o prefixo da configuração do banco
        $config = config('Database');
        $prefix = $config->default['DBPrefix'] ?? '';
        
        // Aplicar índices usando SQL direto com o prefixo correto
        // Verificar se índice já existe antes de criar
        try {
            $this->db->query("CREATE INDEX idx_paciente_cpf ON {$prefix}pacientes(cpf)");
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
        
        try {
            $this->db->query("CREATE INDEX idx_paciente_sus ON {$prefix}pacientes(sus)");
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
        
        try {
            $this->db->query("CREATE INDEX idx_paciente_nome ON {$prefix}pacientes(nome)");
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
        
        try {
            $this->db->query("CREATE INDEX idx_bairro_nome ON {$prefix}bairros(nome_bairro)");
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
        
        try {
            $this->db->query("CREATE INDEX idx_atendimento_data ON {$prefix}atendimentos(data_atendimento)");
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
        
        try {
            $this->db->query("CREATE INDEX idx_atendimento_paciente ON {$prefix}atendimentos(id_paciente)");
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
        
        try {
            $this->db->query("CREATE INDEX idx_atendimento_medico ON {$prefix}atendimentos(id_medico)");
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
        
        try {
            $this->db->query("CREATE INDEX idx_atendimento_classificacao ON {$prefix}atendimentos(classificacao_risco)");
        } catch (\Exception $e) {
            // Índice já existe, ignorar
        }
    }

    public function down()
    {
        // Obter o prefixo da configuração do banco
        $config = config('Database');
        $prefix = $config->default['DBPrefix'] ?? '';
        
        // Remover índices criados (usar try/catch pois o índice pode não existir)
        try {
            $this->db->query("DROP INDEX idx_paciente_cpf ON {$prefix}pacientes");
        } catch (\Exception $e) {
            // Índice não existe, ignorar
        }
        
        try {
            $this->db->query("DROP INDEX idx_paciente_sus ON {$prefix}pacientes");
        } catch (\Exception $e) {
            // Índice não existe, ignorar
        }
        
        try {
            $this->db->query("DROP INDEX idx_paciente_nome ON {$prefix}pacientes");
        } catch (\Exception $e) {
            // Índice não existe, ignorar
        }
        
        try {
            $this->db->query("DROP INDEX idx_bairro_nome ON {$prefix}bairros");
        } catch (\Exception $e) {
            // Índice não existe, ignorar
        }
        
        try {
            $this->db->query("DROP INDEX idx_atendimento_data ON {$prefix}atendimentos");
        } catch (\Exception $e) {
            // Índice não existe, ignorar
        }
        
        try {
            $this->db->query("DROP INDEX idx_atendimento_paciente ON {$prefix}atendimentos");
        } catch (\Exception $e) {
            // Índice não existe, ignorar
        }
        
        try {
            $this->db->query("DROP INDEX idx_atendimento_medico ON {$prefix}atendimentos");
        } catch (\Exception $e) {
            // Índice não existe, ignorar
        }
        
        try {
            $this->db->query("DROP INDEX idx_atendimento_classificacao ON {$prefix}atendimentos");
        } catch (\Exception $e) {
            // Índice não existe, ignorar
        }
    }
}
