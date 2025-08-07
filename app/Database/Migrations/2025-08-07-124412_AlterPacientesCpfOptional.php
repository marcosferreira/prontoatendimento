<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPacientesCpfOptional extends Migration
{
    public function up()
    {
        // Alterar campo CPF de NOT NULL para NULL na tabela pacientes
        $this->forge->modifyColumn('pacientes', [
            'cpf' => [
                'type'       => 'varchar',
                'constraint' => '14',
                'null'       => true, // Alterado de false para true
                'comment'    => 'CPF do paciente (opcional)',
            ]
        ]);
    }

    public function down()
    {
        // Reverter alteração - voltar CPF para NOT NULL
        // ATENÇÃO: Esta operação pode falhar se existirem registros com CPF NULL
        $this->forge->modifyColumn('pacientes', [
            'cpf' => [
                'type'       => 'varchar',
                'constraint' => '14',
                'null'       => false, // Voltando para obrigatório
            ]
        ]);
    }
}
