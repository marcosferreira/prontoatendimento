<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCustomFieldsToUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'nome' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'after'      => 'username',
            ],
            'cpf' => [
                'type'       => 'VARCHAR',
                'constraint' => 14,
                'null'       => true,
                'unique'     => true,
                'after'      => 'nome',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['nome', 'cpf']);
    }
}
