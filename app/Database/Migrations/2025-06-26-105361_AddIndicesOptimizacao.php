<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIndicesOptimizacao extends Migration
{
    public function up()
    {
        // Índices para tabela pacientes
        $this->forge->addKey('cpf', false, true, 'idx_paciente_cpf');
        $this->forge->addKey('sus', false, false, 'idx_paciente_sus');
        $this->forge->addKey('nome', false, false, 'idx_paciente_nome');
        
        // Índices para tabela bairros
        $this->forge->addKey('nome_bairro', false, false, 'idx_bairro_nome');
        
        // Índices para tabela médicos
        $this->forge->addKey('crm', false, true, 'idx_medico_crm');
        
        // Aplicar índices usando SQL direto para maior controle
        $this->db->query('CREATE INDEX idx_paciente_cpf ON pacientes(cpf)');
        $this->db->query('CREATE INDEX idx_paciente_sus ON pacientes(sus)');
        $this->db->query('CREATE INDEX idx_paciente_nome ON pacientes(nome)');
        $this->db->query('CREATE INDEX idx_bairro_nome ON bairros(nome_bairro)');
        $this->db->query('CREATE INDEX idx_atendimento_data ON atendimentos(data_atendimento)');
        $this->db->query('CREATE INDEX idx_atendimento_paciente ON atendimentos(id_paciente)');
        $this->db->query('CREATE INDEX idx_atendimento_medico ON atendimentos(id_medico)');
        $this->db->query('CREATE INDEX idx_atendimento_classificacao ON atendimentos(classificacao_risco)');
    }

    public function down()
    {
        // Remover índices criados
        $this->db->query('DROP INDEX IF EXISTS idx_paciente_cpf ON pacientes');
        $this->db->query('DROP INDEX IF EXISTS idx_paciente_sus ON pacientes');
        $this->db->query('DROP INDEX IF EXISTS idx_paciente_nome ON pacientes');
        $this->db->query('DROP INDEX IF EXISTS idx_bairro_nome ON bairros');
        $this->db->query('DROP INDEX IF EXISTS idx_atendimento_data ON atendimentos');
        $this->db->query('DROP INDEX IF EXISTS idx_atendimento_paciente ON atendimentos');
        $this->db->query('DROP INDEX IF EXISTS idx_atendimento_medico ON atendimentos');
        $this->db->query('DROP INDEX IF EXISTS idx_atendimento_classificacao ON atendimentos');
    }
}
