<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ConfiguracaoModel;

class InitConfiguracoes extends BaseCommand
{
    protected $group = 'SisPAM';
    protected $name = 'init:configuracoes';
    protected $description = 'Inicializa as configurações padrão do sistema SisPAM';

    public function run(array $params)
    {
        CLI::write('Inicializando configurações padrão do SisPAM...', 'yellow');

        try {
            $configuracaoModel = new ConfiguracaoModel();
            
            if ($configuracaoModel->createDefaultConfigs()) {
                CLI::write('✓ Configurações padrão criadas com sucesso!', 'green');
            } else {
                CLI::write('✗ Erro ao criar configurações padrão.', 'red');
                return;
            }

            CLI::write('Sistema inicializado com sucesso!', 'green');
            CLI::write('');
            CLI::write('As seguintes configurações foram criadas:', 'cyan');
            CLI::write('- Informações da Unidade (nome, CNPJ, endereço, telefone)');
            CLI::write('- Parâmetros do Sistema (timeout, triagem, capacidade, notificações)');
            CLI::write('- Configurações de Aparência (tema, cores)');
            CLI::write('- Configurações de Backup (automático, frequência, retenção)');
            CLI::write('');
            CLI::write('Acesse /configuracoes para gerenciar as configurações.', 'cyan');

        } catch (\Exception $e) {
            CLI::write('✗ Erro: ' . $e->getMessage(), 'red');
        }
    }
}
