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
        // Tenta remover a FK pelo nome padrão ou pelo nome alternativo
        try {
            $this->forge->dropForeignKey('medicos', 'medicos_id_user_foreign');
        } catch (\Throwable $e) {
            // Se não existir, ignora
        }
        // Alternativamente, tenta pelo nome gerado pelo MySQL (caso diferente)
        try {
            $this->forge->dropForeignKey('medicos', 'medicos_id_user_foreign_1');
        } catch (\Throwable $e) {
            // Se não existir, ignora
        }
        $this->forge->dropColumn('medicos', 'id_user');
    }
}
