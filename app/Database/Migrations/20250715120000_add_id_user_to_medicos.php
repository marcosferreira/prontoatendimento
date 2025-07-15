<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdUserToMedicos extends Migration
{
    public function up()
    {
        $this->forge->addColumn('medicos', [
            'id_user' => [
                'type'       => 'int',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id_medico',
            ],
        ]);
        $this->forge->addForeignKey('id_user', 'users', 'id', 'SET NULL', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('medicos', 'medicos_id_user_foreign');
        $this->forge->dropColumn('medicos', 'id_user');
    }
}
