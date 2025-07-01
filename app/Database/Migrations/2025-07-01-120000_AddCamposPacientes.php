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
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true,
                'after' => 'cpf'
            ],
            'sexo' => [
                'type' => 'ENUM',
                'constraint' => ['M', 'F'],
                'null' => false,
                'after' => 'data_nascimento'
            ],
            'telefone' => [
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => true,
                'after' => 'sexo'
            ],
            'celular' => [
                'type' => 'VARCHAR',
                'constraint' => '16',
                'null' => true,
                'after' => 'telefone'
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'celular'
            ],
            'numero_sus' => [
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => true,
                'after' => 'email'
            ],
            'numero' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
                'null' => true,
                'after' => 'endereco'
            ],
            'complemento' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'numero'
            ],
            'cep' => [
                'type' => 'VARCHAR',
                'constraint' => '9',
                'null' => true,
                'after' => 'complemento'
            ],
            'cidade' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'cep'
            ],
            'tipo_sanguineo' => [
                'type' => 'ENUM',
                'constraint' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
                'null' => true,
                'after' => 'idade'
            ],
            'nome_responsavel' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'tipo_sanguineo'
            ],
            'alergias' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'nome_responsavel'
            ],
            'observacoes' => [
                'type' => 'TEXT',
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
                'type' => 'VARCHAR',
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
                'type' => 'VARCHAR',
                'constraint' => '15',
                'null' => true
            ]
        ]);
    }
}
