<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePacienteTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_paciente' => [
                'type'           => 'int',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'varchar',
                'constraint' => '255',
                'null'       => false,
            ],
            'sus' => [
                'type'       => 'varchar',
                'constraint' => '15',
                'null'       => true,
            ],
            'cpf' => [
                'type'       => 'varchar',
                'constraint' => '14',
                'unique'     => true,
                'null'       => false,
            ],
            'endereco' => [
                'type' => 'text',
                'null' => true,
            ],
            'id_bairro' => [
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'data_nascimento' => [
                'type' => 'date',
                'null' => false,
            ],
            'idade' => [
                'type'       => 'int',
                'constraint' => 3,
                'unsigned'   => true,
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

        $this->forge->addKey('id_paciente', true); // Primary key
        $this->forge->addForeignKey('id_bairro', 'bairros', 'id_bairro', 'SET NULL', 'CASCADE');
        $this->forge->createTable('pacientes');
    }

    public function down()
    {
        $this->forge->dropTable('pacientes');
    }
}
