<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterClassificacaoRiscoOptional extends Migration
{
    public function up()
    {
        // Modificar a coluna classificacao_risco para permitir NULL
        $this->forge->modifyColumn('atendimentos', [
            'classificacao_risco' => [
                'type'       => 'enum',
                'constraint' => ['Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul'],
                'null'       => true,
                'comment'    => 'Vermelho (EMERGÊNCIA - 0min), Laranja (MUITO URGENTE - 10min), Amarelo (URGENTE - 60min), Verde (POUCO URGENTE - 120min), Azul (NÃO URGENTE - 240min)',
            ]
        ]);
    }

    public function down()
    {
        // Reverter a coluna classificacao_risco para NOT NULL
        $this->forge->modifyColumn('atendimentos', [
            'classificacao_risco' => [
                'type'       => 'enum',
                'constraint' => ['Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul'],
                'null'       => false,
                'comment'    => 'Vermelho (EMERGÊNCIA - 0min), Laranja (MUITO URGENTE - 10min), Amarelo (URGENTE - 60min), Verde (POUCO URGENTE - 120min), Azul (NÃO URGENTE - 240min)',
            ]
        ]);
    }
}
