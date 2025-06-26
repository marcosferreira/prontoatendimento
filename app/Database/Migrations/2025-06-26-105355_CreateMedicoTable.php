<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMedicoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_medico' => [
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
            'crm' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'unique'     => true,
                'null'       => false,
            ],
            'especialidade' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Ativo', 'Inativo'],
                'default'    => 'Ativo',
                'null'       => false,
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

        $this->forge->addKey('id_medico', true);
        $this->forge->createTable('medicos');
    }

    public function down()
    {
        $this->forge->dropTable('medicos');
    }
}
