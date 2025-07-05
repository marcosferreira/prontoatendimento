<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ConfiguracaoSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Configurações da Unidade
            [
                'chave' => 'unidade_nome',
                'valor' => 'Pronto Atendimento Municipal',
                'descricao' => 'Nome da unidade de saúde',
                'tipo' => 'string',
                'categoria' => 'unidade',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'chave' => 'unidade_cnpj',
                'valor' => '',
                'descricao' => 'CNPJ da unidade',
                'tipo' => 'string',
                'categoria' => 'unidade',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'chave' => 'unidade_endereco',
                'valor' => '',
                'descricao' => 'Endereço da unidade',
                'tipo' => 'string',
                'categoria' => 'unidade',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'chave' => 'unidade_telefone',
                'valor' => '',
                'descricao' => 'Telefone da unidade',
                'tipo' => 'string',
                'categoria' => 'unidade',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Configurações do Sistema
            [
                'chave' => 'sistema_timeout_sessao',
                'valor' => '60',
                'descricao' => 'Timeout de sessão em minutos',
                'tipo' => 'integer',
                'categoria' => 'sistema',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'chave' => 'sistema_tempo_triagem',
                'valor' => '15',
                'descricao' => 'Tempo limite para triagem em minutos',
                'tipo' => 'integer',
                'categoria' => 'sistema',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'chave' => 'sistema_capacidade_maxima',
                'valor' => '50',
                'descricao' => 'Capacidade máxima de atendimento',
                'tipo' => 'integer',
                'categoria' => 'sistema',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'chave' => 'sistema_notificacoes_email',
                'valor' => '1',
                'descricao' => 'Ativar notificações por email',
                'tipo' => 'boolean',
                'categoria' => 'sistema',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Configurações de Aparência
            [
                'chave' => 'aparencia_tema',
                'valor' => 'claro',
                'descricao' => 'Tema do sistema',
                'tipo' => 'string',
                'categoria' => 'aparencia',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'chave' => 'aparencia_cor_primaria',
                'valor' => '#1e3a8a',
                'descricao' => 'Cor primária do sistema',
                'tipo' => 'string',
                'categoria' => 'aparencia',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Configurações de Backup
            [
                'chave' => 'backup_automatico_ativo',
                'valor' => '1',
                'descricao' => 'Backup automático ativado',
                'tipo' => 'boolean',
                'categoria' => 'backup',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'chave' => 'backup_frequencia',
                'valor' => 'diario',
                'descricao' => 'Frequência do backup automático',
                'tipo' => 'string',
                'categoria' => 'backup',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'chave' => 'backup_horario',
                'valor' => '02:00',
                'descricao' => 'Horário do backup automático',
                'tipo' => 'string',
                'categoria' => 'backup',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'chave' => 'backup_retencao_dias',
                'valor' => '30',
                'descricao' => 'Dias de retenção dos backups',
                'tipo' => 'integer',
                'categoria' => 'backup',
                'editavel' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Verifica se já existem configurações
        $existing = $this->db->table('configuracoes')->countAll();
        
        if ($existing == 0) {
            $this->db->table('configuracoes')->insertBatch($data);
            echo "Configurações padrão inseridas com sucesso.\n";
        } else {
            echo "Configurações já existem. Seeder ignorado.\n";
        }
    }
}
