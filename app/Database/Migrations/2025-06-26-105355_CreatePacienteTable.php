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
                'comment'    => 'Número SUS antigo (manter para compatibilidade)',
            ],
            'cpf' => [
                'type'       => 'varchar',
                'constraint' => '14',
                'unique'     => true,
                'null'       => false,
            ],
            'rg' => [
                'type'       => 'varchar',
                'constraint' => '20',
                'null'       => true,
            ],
            'id_logradouro' => [
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Referência ao logradouro (rua, avenida, etc)',
            ],
            'numero' => [
                'type'       => 'varchar',
                'constraint' => '10',
                'null'       => true,
                'comment'    => 'Número da residência no logradouro',
            ],
            'complemento' => [
                'type'       => 'varchar',
                'constraint' => '100',
                'null'       => true,
                'comment'    => 'Complemento do endereço (apto, bloco, etc)',
            ],
            'data_nascimento' => [
                'type' => 'date',
                'null' => false,
            ],
            'sexo' => [
                'type'       => 'enum',
                'constraint' => ['M', 'F'],
                'null'       => false,
            ],
            'idade' => [
                'type'       => 'int',
                'constraint' => 3,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Idade calculada automaticamente',
            ],
            'tipo_sanguineo' => [
                'type'       => 'enum',
                'constraint' => ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'],
                'null'       => true,
            ],
            'telefone' => [
                'type'       => 'varchar',
                'constraint' => '15',
                'null'       => true,
            ],
            'celular' => [
                'type'       => 'varchar',
                'constraint' => '16',
                'null'       => true,
            ],
            'email' => [
                'type'       => 'varchar',
                'constraint' => '255',
                'null'       => true,
            ],
            'numero_sus' => [
                'type'       => 'varchar',
                'constraint' => '15',
                'null'       => true,
                'comment'    => 'Número SUS principal',
            ],
            'nome_responsavel' => [
                'type'       => 'varchar',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Nome do responsável (para menores de idade)',
            ],
            'alergias' => [
                'type' => 'text',
                'null' => true,
                'comment' => 'Histórico de alergias do paciente',
            ],
            'observacoes' => [
                'type' => 'text',
                'null' => true,
                'comment' => 'Observações gerais sobre o paciente',
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
        $this->forge->addForeignKey('id_logradouro', 'logradouros', 'id_logradouro', 'SET NULL', 'CASCADE');
        $this->forge->createTable('pacientes');
    }

    public function down()
    {
        $this->forge->dropTable('pacientes');
    }
}
