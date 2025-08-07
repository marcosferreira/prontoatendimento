<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNomePaiMaeToPacientes extends Migration
{
    public function up()
    {
        // Adicionar novos campos à tabela pacientes
        $this->forge->addColumn('pacientes', [
            'nome_mae' => [
                'type'       => 'varchar',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Nome completo da mãe do paciente',
                'after'      => 'nome_responsavel'
            ],
            'nome_pai' => [
                'type'       => 'varchar',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Nome completo do pai do paciente',
                'after'      => 'nome_mae'
            ]
        ]);
    }

    public function down()
    {
        // Remover os campos adicionados
        $this->forge->dropColumn('pacientes', ['nome_mae', 'nome_pai']);
    }
}
