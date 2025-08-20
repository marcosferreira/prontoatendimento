<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCidadeToPacientes extends Migration
{
    public function up()
    {
        // Adicionar campos de cidade e logradouro externo à tabela pacientes
        $this->forge->addColumn('pacientes', [
            'cidade_externa' => [
                'type'       => 'varchar',
                'constraint' => '100',
                'null'       => true,
                'comment'    => 'Nome da cidade quando o paciente não reside na cidade local'
            ],
            'logradouro_externo' => [
                'type'       => 'varchar',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Endereço completo quando o paciente reside em outra cidade'
            ],
            'cep_externo' => [
                'type'       => 'varchar',
                'constraint' => '10',
                'null'       => true,
                'comment'    => 'CEP quando o paciente reside em outra cidade'
            ]
        ]);
    }

    public function down()
    {
        // Remover os campos adicionados
        $this->forge->dropColumn('pacientes', ['cidade_externa', 'logradouro_externo', 'cep_externo']);
    }
}
