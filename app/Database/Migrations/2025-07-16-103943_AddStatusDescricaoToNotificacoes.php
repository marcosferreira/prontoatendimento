<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusDescricaoToNotificacoes extends Migration
{
    public function up()
    {
        $this->forge->addColumn('notificacoes', [
            'status_descricao' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'status',
                'comment'    => 'Descrição detalhada ao resolver ou cancelar a notificação'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('notificacoes', 'status_descricao');
    }
}
