<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class SoftDeleteManager extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'softdelete:manage';
    protected $description = 'Gerencia registros com soft delete no sistema';

    protected $usage = 'softdelete:manage [command] [options]';
    protected $arguments = [
        'command' => 'Comando a executar: stats, cleanup, restore, list'
    ];
    protected $options = [
        '--model' => 'Nome do model (para restore)',
        '--id' => 'ID do registro (para restore)',
        '--days' => 'Dias para limpeza (padrão: 30)',
        '--force' => 'Confirma a execução sem perguntar'
    ];

    public function run(array $params)
    {
        helper('softdelete');

        // Parse manual dos argumentos para contornar problemas com CLI::getOption
        $command = null;
        $options = [];
        
        // Extrair comando e opções dos parâmetros
        foreach ($params as $key => $param) {
            if (is_numeric($key)) {
                // Parâmetro posicional (comando)
                if ($command === null) {
                    $command = $param;
                }
            } else {
                // Parâmetro nomeado (--option=value)
                if (strpos($key, '--') === 0) {
                    $optionName = substr($key, 2);
                    $options[$optionName] = $param;
                } else {
                    // Caso alternativo: se a chave não tem --, pode ser option=value
                    if (strpos($key, '=') !== false) {
                        $parts = explode('=', $key, 2);
                        if (count($parts) === 2) {
                            $optionName = str_replace('--', '', $parts[0]);
                            $options[$optionName] = $parts[1];
                        }
                    }
                }
            }
        }

        // Fallback: tentar usar CLI::getOption para as opções específicas
        if (empty($options)) {
            $options['model'] = CLI::getOption('model');
            $options['id'] = CLI::getOption('id');
            $options['days'] = CLI::getOption('days');
            $options['force'] = CLI::getOption('force');
            
            // Remover valores nulos
            $options = array_filter($options, function($value) {
                return $value !== null;
            });
        }

        switch ($command) {
            case 'stats':
                $this->showStats();
                break;
            case 'cleanup':
                $this->cleanup($params, $options);
                break;
            case 'restore':
                $this->restore($params, $options);
                break;
            case 'list':
                $this->listDeleted($params, $options);
                break;
            case null:
            case '':
            default:
                $this->showUsage();
                break;
        }
    }

    protected function showStats()
    {
        CLI::write('=== Estatísticas de Soft Delete ===', 'green');
        CLI::newLine();

        $stats = get_soft_delete_stats();

        $table = [];
        foreach ($stats as $model => $data) {
            if (isset($data['erro'])) {
                $table[] = [$model, 'ERRO', 'ERRO', 'ERRO'];
            } else {
                $table[] = [
                    $model,
                    CLI::color($data['total'], 'blue'),
                    CLI::color($data['ativos'], 'green'),
                    CLI::color($data['excluidos'], 'yellow')
                ];
            }
        }

        CLI::table($table, [
            'Model',
            'Total',
            'Ativos',
            'Excluídos'
        ]);
    }

    protected function cleanup(array $params, array $options)
    {
        $days = $options['days'] ?? 30;
        $force = isset($options['force']);

        CLI::write("=== Limpeza de Registros Excluídos ===", 'yellow');
        CLI::write("Serão removidos definitivamente registros excluídos há mais de {$days} dias.", 'yellow');
        CLI::newLine();

        if (!$force) {
            $confirm = CLI::prompt('Deseja continuar? (y/N)', ['y', 'n']);
            if ($confirm !== 'y') {
                CLI::write('Operação cancelada.', 'red');
                return;
            }
        }

        $models = [
            'AtendimentoModel',
            'AtendimentoExameModel',
            'AtendimentoProcedimentoModel',
            'BairroModel',
            'ExameModel',
            'LogradouroModel',
            'MedicoModel',
            'PacienteModel',
            'ProcedimentoModel'
        ];

        $totalDeleted = 0;

        foreach ($models as $modelName) {
            try {
                $deleted = cleanup_old_deleted_records($modelName, $days);
                $totalDeleted += $deleted;
                
                if ($deleted > 0) {
                    CLI::write("  {$modelName}: {$deleted} registros removidos", 'green');
                }
            } catch (\Exception $e) {
                CLI::write("  {$modelName}: ERRO - " . $e->getMessage(), 'red');
            }
        }

        CLI::newLine();
        CLI::write("Total de registros removidos definitivamente: {$totalDeleted}", 'green');
    }

    protected function restore(array $params, array $options)
    {
        $modelName = $options['model'] ?? null;
        $id = $options['id'] ?? null;

        if (!$modelName || !$id) {
            CLI::write('É necessário informar --model e --id para restaurar um registro.', 'red');
            CLI::write('Uso: php spark softdelete:manage restore --model=LogradouroModel --id=78', 'yellow');
            return;
        }

        CLI::write("Tentando restaurar registro {$id} do model {$modelName}...", 'yellow');

        try {
            // Tentar carregar o modelo diretamente
            $fullModelName = "App\\Models\\{$modelName}";
            
            if (!class_exists($fullModelName)) {
                CLI::write("Modelo {$fullModelName} não encontrado.", 'red');
                CLI::write("Modelos disponíveis: LogradouroModel, BairroModel, PacienteModel, etc.", 'yellow');
                return;
            }

            $model = new $fullModelName();
            
            // Verificar se o registro existe (mesmo excluído)
            $record = $model->withDeleted()->find($id);
            if (!$record) {
                CLI::write("Registro {$id} não encontrado no modelo {$modelName}.", 'red');
                return;
            }

            // Verificar se está realmente excluído
            if (empty($record['deleted_at'])) {
                CLI::write("Registro {$id} não está excluído.", 'yellow');
                return;
            }

            CLI::write("Registro encontrado. Data de exclusão: {$record['deleted_at']}", 'light_gray');

            // Restaurar o registro usando builder para contornar validação do Model
            $db = \Config\Database::connect();
            $builder = $db->table($model->getTable());
            $success = $builder->where($model->primaryKey, $id)->update(['deleted_at' => null]);
            
            if ($success) {
                CLI::write("Registro {$id} do model {$modelName} restaurado com sucesso!", 'green');
                
                // Verificar se foi restaurado
                $restored = $model->find($id);
                if ($restored) {
                    CLI::write("Confirmação: Registro agora está ativo.", 'green');
                } else {
                    CLI::write("Aviso: Não foi possível confirmar a restauração via model.", 'yellow');
                }
            } else {
                CLI::write("Falha ao restaurar registro {$id} do model {$modelName}.", 'red');
                CLI::write("Verifique se o registro existe e está realmente excluído.", 'yellow');
            }
        } catch (\Exception $e) {
            CLI::write("Erro ao restaurar: " . $e->getMessage(), 'red');
            CLI::write("Trace: " . $e->getTraceAsString(), 'light_gray');
        }
    }

    protected function listDeleted(array $params, array $options)
    {
        $modelName = $options['model'] ?? null;

        if (!$modelName) {
            CLI::write('É necessário informar --model para listar registros excluídos.', 'red');
            CLI::write('Uso: php spark softdelete:manage list --model=LogradouroModel', 'yellow');
            return;
        }

        try {
            $fullModelName = "App\\Models\\{$modelName}";
            
            if (!class_exists($fullModelName)) {
                CLI::write("Modelo {$fullModelName} não encontrado.", 'red');
                return;
            }

            $model = new $fullModelName();
            $deletedRecords = $model->onlyDeleted()->findAll();

            if (empty($deletedRecords)) {
                CLI::write("Nenhum registro excluído encontrado para {$modelName}.", 'yellow');
                return;
            }

            CLI::write("=== Registros Excluídos - {$modelName} ===", 'green');
            CLI::newLine();

            $table = [];
            foreach ($deletedRecords as $record) {
                $primaryKey = $model->primaryKey;
                $id = $record[$primaryKey];
                $deletedAt = $record['deleted_at'] ?? 'N/A';
                
                // Tentar mostrar um campo descritivo
                $description = '';
                if (isset($record['nome_logradouro'])) {
                    $description = $record['tipo_logradouro'] . ' ' . $record['nome_logradouro'];
                } elseif (isset($record['nome_bairro'])) {
                    $description = $record['nome_bairro'];
                } elseif (isset($record['nome_paciente'])) {
                    $description = $record['nome_paciente'];
                } elseif (isset($record['nome'])) {
                    $description = $record['nome'];
                } else {
                    $description = 'ID: ' . $id;
                }

                $table[] = [
                    $id,
                    $description,
                    $deletedAt
                ];
            }

            CLI::table($table, [
                'ID',
                'Descrição',
                'Excluído em'
            ]);

        } catch (\Exception $e) {
            CLI::write("Erro ao listar registros: " . $e->getMessage(), 'red');
        }
    }

    protected function showUsage()
    {
        CLI::write('=== Gerenciador de Soft Delete ===', 'green');
        CLI::newLine();
        CLI::write('Comandos disponíveis:', 'yellow');
        CLI::write('  stats              - Mostra estatísticas de registros excluídos');
        CLI::write('  list               - Lista registros excluídos de um model');
        CLI::write('  cleanup            - Remove definitivamente registros antigos');
        CLI::write('  restore            - Restaura um registro específico');
        CLI::newLine();
        CLI::write('Exemplos:', 'yellow');
        CLI::write('  php spark softdelete:manage stats');
        CLI::write('  php spark softdelete:manage cleanup --days=60 --force');
        CLI::write('  php spark softdelete:manage restore --model=PacienteModel --id=123');
        CLI::write('  php spark softdelete:manage list --model=LogradouroModel');
        CLI::newLine();
        CLI::write('Opções:', 'yellow');
        CLI::write('  --model    Nome do model (para list e restore)');
        CLI::write('  --id       ID do registro (para restore)');
        CLI::write('  --days     Dias para limpeza (padrão: 30)');
        CLI::write('  --force    Confirma a execução sem perguntar');
    }
}
