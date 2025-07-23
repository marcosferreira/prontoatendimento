<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBackupsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_backup' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nome_arquivo' => [
                'type' => 'varchar',
                'constraint' => 255,
                'null' => false,
            ],
            'tipo' => [
                'type' => 'enum',
                'constraint' => ['completo', 'dados', 'incremental'],
                'default' => 'completo',
                'null' => false,
            ],
            'tamanho' => [
                'type' => 'bigint',
                'unsigned' => true,
                'default' => 0,
                'null' => false,
            ],
            'status' => [
                'type' => 'enum',
                'constraint' => ['criando', 'sucesso', 'erro'],
                'default' => 'criando',
                'null' => false,
            ],
            'caminho_arquivo' => [
                'type' => 'varchar',
                'constraint' => 500,
                'null' => true,
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

        $this->forge->addKey('id_backup', true);
        $this->forge->addKey('status');
        $this->forge->addKey('tipo');
        $this->forge->addKey('created_at');

        $this->forge->createTable('backups');
    }

    public function down()
    {
        $this->forge->dropTable('backups');
    }
}
