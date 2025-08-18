<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateClassificacaoRiscoSemClassificacao extends Migration
{
    public function up()
    {
        // Alterar o campo classificacao_risco para permitir "Sem classificação" e ser opcional
        $fields = [
            'classificacao_risco' => [
                'type'       => 'enum',
                'constraint' => ['Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul', 'Sem classificação'],
                'null'       => true,  // Agora permite NULL
                'default'    => 'Sem classificação',  // Default para quando não há enfermeiro
                'comment'    => 'Vermelho (EMERGÊNCIA - 0min), Laranja (MUITO URGENTE - 10min), Amarelo (URGENTE - 60min), Verde (POUCO URGENTE - 120min), Azul (NÃO URGENTE - 240min), Sem classificação (quando não há enfermeiro)',
            ],
        ];

        $this->forge->modifyColumn('atendimentos', $fields);
    }

    public function down()
    {
        // Reverter para o estado anterior
        $fields = [
            'classificacao_risco' => [
                'type'       => 'enum',
                'constraint' => ['Vermelho', 'Laranja', 'Amarelo', 'Verde', 'Azul'],
                'null'       => false,
                'comment'    => 'Vermelho (EMERGÊNCIA - 0min), Laranja (MUITO URGENTE - 10min), Amarelo (URGENTE - 60min), Verde (POUCO URGENTE - 120min), Azul (NÃO URGENTE - 240min)',
            ],
        ];

        $this->forge->modifyColumn('atendimentos', $fields);
    }
}
