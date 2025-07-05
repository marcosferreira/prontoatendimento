<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateConfiguracaoTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'chave' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'valor' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'descricao' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'tipo' => [
                'type' => 'ENUM',
                'constraint' => ['string', 'integer', 'boolean', 'float', 'json'],
                'default' => 'string',
                'null' => false,
            ],
            'categoria' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'editavel' => [
                'type' => 'BOOLEAN',
                'default' => true,
                'null' => false,
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

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('chave');
        $this->forge->addKey('categoria');
        $this->forge->createTable('configuracoes');
    }

    public function down()
    {
        $this->forge->dropTable('configuracoes');
    }
}
