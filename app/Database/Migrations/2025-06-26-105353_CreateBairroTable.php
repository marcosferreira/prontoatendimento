<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBairroTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_bairro' => [
                'type'           => 'int',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome_bairro' => [
                'type'       => 'varchar',
                'constraint' => 100,
                'null'       => false,
            ],
            'area' => [
                'type'       => 'varchar',
                'constraint' => 100,
                'null'       => true,
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

        $this->forge->addKey('id_bairro', true);
        $this->forge->createTable('bairros');
    }

    public function down()
    {
        $this->forge->dropTable('bairros');
    }
}
