<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLogradouroTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_logradouro' => [
                'type'           => 'int',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nome_logradouro' => [
                'type'       => 'varchar',
                'constraint' => 150,
                'null'       => false,
            ],
            'tipo_logradouro' => [
                'type'       => 'enum',
                'constraint' => ['Rua', 'Avenida', 'Travessa', 'Alameda', 'Praça', 'Estrada', 'Sítio', 'Rodovia', 'Via', 'Beco', 'Largo'],
                'default'    => 'Rua',
                'null'       => false,
            ],
            'cep' => [
                'type'       => 'varchar',
                'constraint' => 10,
                'null'       => true,
            ],
            'id_bairro' => [
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'observacoes' => [
                'type' => 'text',
                'null' => true,
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

        $this->forge->addKey('id_logradouro', true);
        $this->forge->addKey('id_bairro');
        $this->forge->addKey('cep');
        
        // Adicionar chave estrangeira
        $this->forge->addForeignKey('id_bairro', 'bairros', 'id_bairro', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('logradouros');
    }

    public function down()
    {
        $this->forge->dropTable('logradouros');
    }
}
