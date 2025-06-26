<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAtendimentoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_atendimento' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_paciente' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_medico' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'data_atendimento' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'classificacao_risco' => [
                'type'       => 'ENUM',
                'constraint' => ['Verde', 'Amarelo', 'Vermelho', 'Azul'],
                'null'       => false,
            ],
            'consulta_enfermagem' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'hgt_glicemia' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'comment'    => 'Valor da glicemia em mg/dL',
            ],
            'pressao_arterial' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'comment'    => 'Formato: 120x80 mmHg',
            ],
            'hipotese_diagnostico' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'observacao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'encaminhamento' => [
                'type'       => 'ENUM',
                'constraint' => ['Alta', 'Internação', 'Transferência', 'Especialista', 'Retorno', 'Óbito'],
                'null'       => true,
            ],
            'obito' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_atendimento', true);
        $this->forge->addForeignKey('id_paciente', 'pacientes', 'id_paciente', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_medico', 'medicos', 'id_medico', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('atendimentos');
    }

    public function down()
    {
        $this->forge->dropTable('atendimentos');
    }
}
