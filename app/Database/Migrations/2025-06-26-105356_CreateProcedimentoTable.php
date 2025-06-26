<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProcedimentoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_procedimento' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'codigo' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'Código TUSS/SUS',
            ],
            'descricao' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
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
