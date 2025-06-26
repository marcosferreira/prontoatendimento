<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExameTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_exame' => [
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
            ],
            'tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['laboratorial', 'imagem', 'funcional', 'outros'],
                'null'       => false,
                'default'    => 'laboratorial',
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

        $this->forge->addKey('id_exame', true);
        $this->forge->createTable('exames');
    }

    public function down()
    {
        $this->forge->dropTable('exames');
    }
}
