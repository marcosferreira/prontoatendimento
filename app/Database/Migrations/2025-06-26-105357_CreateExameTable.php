<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExameTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_exame' => [
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
            ],
            'tipo' => [
                'type'       => 'enum',
                'constraint' => ['laboratorial', 'imagem', 'funcional', 'outros'],
                'null'       => false,
                'default'    => 'laboratorial',
            ],
            'descricao' => [
                'type' => 'text',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'datetime',
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
