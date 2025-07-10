<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTelefoneEmailToMedicos extends Migration
{
    public function up()
    {
        $fields = [
            'telefone' => [
                'type'       => 'varchar',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'especialidade'
            ],
            'email' => [
                'type'       => 'varchar',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'telefone'
            ]
        ];
        
        $this->forge->addColumn('medicos', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('medicos', ['telefone', 'email']);
    }
}
