<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificacaoAtendimentosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_notificacao_atendimento' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_notificacao' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'id_atendimento' => [
                'type' => 'int',
                'constraint' => 11,
                'unsigned' => true,
                'null' => false,
            ],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);

        $this->forge->addKey('id_notificacao_atendimento', true);
        $this->forge->addUniqueKey(['id_notificacao', 'id_atendimento']);
        $this->forge->addForeignKey('id_notificacao', 'notificacoes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_atendimento', 'atendimentos', 'id_atendimento', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notificacao_atendimentos');
    }

    public function down()
    {
        $this->forge->dropTable('notificacao_atendimentos');
    }
}
