<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePacienteTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_paciente' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'sus' => [
                'type'       => 'VARCHAR',
                'constraint' => '15',
                'null'       => true,
            ],
            'cpf' => [
                'type'       => 'VARCHAR',
                'constraint' => '14',
                'unique'     => true,
                'null'       => false,
            ],
            'endereco' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'id_bairro' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'data_nascimento' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'idade' => [
                'type'       => 'INT',
                'constraint' => 3,
                'unsigned'   => true,
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

        $this->forge->addKey('id_paciente', true); // Primary key
        $this->forge->addForeignKey('id_bairro', 'bairros', 'id_bairro', 'SET NULL', 'CASCADE');
        $this->forge->createTable('pacientes');
    }

    public function down()
    {
        $this->forge->dropTable('pacientes');
    }
}
