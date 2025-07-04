<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProcedimentoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_procedimento' => [
                'type'           => 'int',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'varchar',
                'constraint' => 255,
                'null'       => false,
            ],
            'codigo' => [
                'type'       => 'varchar',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Código TUSS/SUS',
            ],
            'descricao' => [
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

        $this->forge->addKey('id_procedimento', true);
        $this->forge->createTable('procedimentos');
    }

    public function down()
    {
        $this->forge->dropTable('procedimentos');
    }
}
