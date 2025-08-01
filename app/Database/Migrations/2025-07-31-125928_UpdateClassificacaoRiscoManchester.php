<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateClassificacaoRiscoManchester extends Migration
{
    public function up()
    {
        // Atualizar a tabela atendimentos para incluir 'Laranja' e reordenar conforme protocolo Manchester
        $fields = [
            'classificacao_risco' => [
                'type'       => 'enum',
                'constraint' => ['Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul'],
                'null'       => false,
                'comment'    => 'Protocolo Manchester: Vermelho (EMERGÊNCIA - 0min), Laranja (MUITO URGENTE - 10min), Amarelo (URGENTE - 60min), Verde (POUCO URGENTE - 120min), Azul (NÃO URGENTE - 240min)',
            ],
        ];

        $this->forge->modifyColumn('atendimentos', $fields);
    }

    public function down()
    {
        // Reverter para o enum original sem 'Laranja'
        $fields = [
            'classificacao_risco' => [
                'type'       => 'enum',
                'constraint' => ['Verde', 'Amarelo', 'Vermelho', 'Azul'],
                'null'       => false,
            ],
        ];

        $this->forge->modifyColumn('atendimentos', $fields);
    }
}
