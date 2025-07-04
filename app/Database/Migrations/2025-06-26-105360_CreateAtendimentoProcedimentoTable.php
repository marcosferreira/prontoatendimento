<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAtendimentoProcedimentoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_atendimento_procedimento' => [
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
            'id_procedimento' => [
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'quantidade' => [
                'type'       => 'int',
                'constraint' => 5,
                'unsigned'   => true,
                'default'    => 1,
                'null'       => false,
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
