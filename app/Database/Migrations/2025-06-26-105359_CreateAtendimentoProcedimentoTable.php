<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAtendimentoProcedimentoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_atendimento_procedimento' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_atendimento' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'id_procedimento' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'quantidade' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'default'    => 1,
                'null'       => false,
            ],
            'observacao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id_atendimento_procedimento', true);
        $this->forge->addForeignKey('id_atendimento', 'atendimentos', 'id_atendimento', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_procedimento', 'procedimentos', 'id_procedimento', 'CASCADE', 'CASCADE');
        $this->forge->createTable('atendimento_procedimentos');
    }

    public function down()
    {
        $this->forge->dropTable('atendimento_procedimentos');
    }
}
