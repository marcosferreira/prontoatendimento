<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCamposPacientes extends Migration
{
    public function up()
    {
        // Adicionar novos campos à tabela pacientes
        $fields = [
            'rg' => [
                'type' => 'varchar',
                'constraint' => '20',
                'null' => true,
                'after' => 'cpf'
            ],
            'sexo' => [
                'type' => 'enum',
                'constraint' => ['M', 'F'],
                'null' => false,
                'after' => 'data_nascimento'
            ],
            'telefone' => [
                'type' => 'varchar',
                'constraint' => '15',
                'null' => true,
                'after' => 'sexo'
            ],
            'celular' => [
                'type' => 'varchar',
                'constraint' => '16',
                'null' => true,
                'after' => 'telefone'
            ],
            'email' => [
                'type' => 'varchar',
                'constraint' => '255',
                'null' => true,
                'after' => 'celular'
            ],
            'numero_sus' => [
                'type' => 'varchar',
                'constraint' => '15',
                'null' => true,
                'after' => 'email'
            ],
            'numero' => [
                'type' => 'varchar',
                'constraint' => '10',
                'null' => true,
                'after' => 'endereco'
            ],
            'complemento' => [
                'type' => 'varchar',
                'constraint' => '100',
                'null' => true,
                'after' => 'numero'
            ],
            'cep' => [
                'type' => 'varchar',
                'constraint' => '9',
                'null' => true,
                'after' => 'complemento'
            ],
            'cidade' => [
                'type' => 'varchar',
                'constraint' => '100',
                'null' => true,
                'after' => 'cep'
            ],
            'tipo_sanguineo' => [
                'type' => 'enum',
                'constraint' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
                'null' => true,
                'after' => 'idade'
            ],
            'nome_responsavel' => [
                'type' => 'varchar',
                'constraint' => '255',
                'null' => true,
                'after' => 'tipo_sanguineo'
            ],
            'alergias' => [
                'type' => 'text',
                'null' => true,
                'after' => 'nome_responsavel'
            ],
            'observacoes' => [
                'type' => 'text',
                'null' => true,
                'after' => 'alergias'
            ]
        ];

        $this->forge->addColumn('pacientes', $fields);

        // Renomear campo sus para numero_sus_original (para manter compatibilidade)
        // e atualizar o campo numero_sus
        $this->forge->modifyColumn('pacientes', [
            'sus' => [
                'name' => 'sus_old',
                'type' => 'varchar',
                'constraint' => '15',
                'null' => true
            ]
        ]);
    }

    public function down()
    {
        // Remover os campos adicionados
        $this->forge->dropColumn('pacientes', [
            'rg',
            'sexo', 
            'telefone',
            'celular',
            'email',
            'numero_sus',
            'numero',
            'complemento',
            'cep',
            'cidade',
            'tipo_sanguineo',
            'nome_responsavel',
            'alergias',
            'observacoes'
        ]);

        // Restaurar nome original do campo sus
        $this->forge->modifyColumn('pacientes', [
            'sus_old' => [
                'name' => 'sus',
                'type' => 'varchar',
                'constraint' => '15',
                'null' => true
            ]
        ]);
    }
}
