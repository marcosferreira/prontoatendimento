<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAtendimentoExameTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_atendimento_exame' => [
                'type'           => 'int',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_exame' => [
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'resultado' => [
                'type' => 'text',
                'null' => true,
            ],
            'status' => [
                'type'       => 'enum',
                'constraint' => ['Solicitado', 'Realizado', 'Cancelado'],
                'default'    => 'Solicitado',
                'null'       => false,
            ],
            'data_solicitacao' => [
                'type' => 'datetime',
                'null' => false,
            ],
            'data_realizacao' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'observacao' => [
                'type' => 'text',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_atendimento_exame', true);
        $this->forge->addKey(['id_atendimento', 'id_exame']);
        $this->forge->addForeignKey('id_atendimento', 'atendimentos', 'id_atendimento', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_exame', 'exames', 'id_exame', 'CASCADE', 'CASCADE');
        $this->forge->createTable('atendimento_exames');
    }

    public function down()
    {
        $this->forge->dropTable('atendimento_exames');
    }
}
