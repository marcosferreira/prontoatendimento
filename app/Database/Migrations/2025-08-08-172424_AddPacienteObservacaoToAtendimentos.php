<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPacienteObservacaoToAtendimentos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('atendimentos', [
            'paciente_observacao' => [
                'type' => 'enum',
                'constraint' => ['Sim', 'Não'],
                'default' => 'Não',
                'null' => false,
                'comment' => 'Indica se o paciente está em observação clínica'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('atendimentos', 'paciente_observacao');
    }
}
