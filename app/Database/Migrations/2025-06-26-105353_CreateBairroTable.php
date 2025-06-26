<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBairroTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bairro' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome_bairro' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'area' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
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

        $this->forge->addKey('id_bairro', true);
        $this->forge->createTable('bairros');
    }

    public function down()
    {
        $this->forge->dropTable('bairros');
    }
}
