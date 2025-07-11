<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMedicoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_medico' => [
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
            'crm' => [
                'type'       => 'varchar',
                'constraint' => 20,
                'null'       => false,
            ],
            'especialidade' => [
                'type'       => 'varchar',
                'constraint' => 100,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'enum',
                'constraint' => ['Ativo', 'Inativo'],
                'default'    => 'Ativo',
                'null'       => false,
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

        $this->forge->addKey('id_medico', true);
        $this->forge->createTable('medicos');
    }

    public function down()
    {
        $this->forge->dropTable('medicos');
    }
}
