<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNotificacoesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tipo' => [
                'type'       => 'ENUM',
                'constraint' => ['paciente_recorrente', 'surto_sintomas', 'alta_demanda', 'medicamento_critico', 'equipamento_falha', 'estatistica_anomala'],
                'null'       => false,
            ],
            'titulo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'descricao' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'severidade' => [
                'type'       => 'ENUM',
                'constraint' => ['baixa', 'media', 'alta', 'critica'],
                'null'       => false,
            ],
            'modulo' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'parametros' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['ativa', 'resolvida', 'cancelada'],
                'default'    => 'ativa',
            ],
            'data_vencimento' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'acionada_em' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'resolvida_em' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'usuario_responsavel' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'metadata' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['tipo', 'status']);
        $this->forge->addKey(['severidade', 'status']);
        $this->forge->addKey(['created_at']);
        $this->forge->addKey(['status', 'acionada_em']);
        $this->forge->addKey(['data_vencimento']);

        $this->forge->createTable('notificacoes');

        // Comentários da tabela
        $this->db->query("ALTER TABLE pam_notificacoes COMMENT = 'Tabela para armazenar notificações BI do sistema de pronto atendimento'");
        
        // Comentários dos campos
        $this->db->query("ALTER TABLE pam_notificacoes MODIFY COLUMN tipo ENUM('paciente_recorrente', 'surto_sintomas', 'alta_demanda', 'medicamento_critico', 'equipamento_falha', 'estatistica_anomala') COMMENT 'Tipo da notificação baseada na análise BI'");
        $this->db->query("ALTER TABLE pam_notificacoes MODIFY COLUMN titulo VARCHAR(255) COMMENT 'Título resumido da notificação'");
        $this->db->query("ALTER TABLE pam_notificacoes MODIFY COLUMN descricao TEXT COMMENT 'Descrição detalhada da notificação'");
        $this->db->query("ALTER TABLE pam_notificacoes MODIFY COLUMN severidade ENUM('baixa', 'media', 'alta', 'critica') COMMENT 'Nível de severidade da notificação'");
        $this->db->query("ALTER TABLE pam_notificacoes MODIFY COLUMN modulo VARCHAR(100) COMMENT 'Módulo do sistema que gerou a notificação'");
        $this->db->query("ALTER TABLE pam_notificacoes MODIFY COLUMN parametros JSON COMMENT 'Parâmetros específicos da notificação em formato JSON'");
        $this->db->query("ALTER TABLE pam_notificacoes MODIFY COLUMN status ENUM('ativa', 'resolvida', 'cancelada') DEFAULT 'ativa' COMMENT 'Status atual da notificação'");
        $this->db->query("ALTER TABLE pam_notificacoes MODIFY COLUMN data_vencimento DATETIME COMMENT 'Data limite para resolução da notificação'");
        $this->db->query("ALTER TABLE pam_notificacoes MODIFY COLUMN acionada_em DATETIME COMMENT 'Data e hora em que a notificação foi acionada'");
        $this->db->query("ALTER TABLE pam_notificacoes MODIFY COLUMN resolvida_em DATETIME COMMENT 'Data e hora em que a notificação foi resolvida'");
        $this->db->query("ALTER TABLE pam_notificacoes MODIFY COLUMN usuario_responsavel INT(11) UNSIGNED COMMENT 'ID do usuário responsável pela resolução'");
        $this->db->query("ALTER TABLE pam_notificacoes MODIFY COLUMN metadata JSON COMMENT 'Metadados adicionais da notificação'");
    }

    public function down()
    {
        $this->forge->dropTable('notificacoes');
    }
}
