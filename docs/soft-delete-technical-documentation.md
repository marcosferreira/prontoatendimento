# Documentação Técnica - Sistema de Soft Delete

## Arquitetura do Sistema

### Visão Geral da Implementação

O sistema de soft delete foi implementado seguindo as melhores práticas do CodeIgniter 4, utilizando:

- **BaseModel** do CodeIgniter com `$useSoftDeletes = true`
- **Migrations** para alteração das tabelas
- **Command CLI** personalizado para gerenciamento
- **Helper functions** para operações auxiliares
- **Índices otimizados** para performance

## Estrutura de Arquivos

```
app/
├── Commands/
│   └── SoftDeleteManager.php          # Comando CLI principal
├── Models/                            # Todos os models com soft delete
│   ├── AtendimentoModel.php
│   ├── AtendimentoExameModel.php
│   ├── AtendimentoProcedimentoModel.php
│   ├── BairroModel.php
│   ├── ExameModel.php
│   ├── LogradouroModel.php
│   ├── MedicoModel.php
│   ├── PacienteModel.php              # ✅ Estrutura consolidada
│   └── ProcedimentoModel.php
├── Database/
│   └── Migrations/
│       ├── 2025-06-26-105355_CreatePacienteTable.php  # ✅ Consolidada
│       └── 2025-07-04-000001_AddSoftDeleteToTables.php
└── Helpers/
    └── softdelete_helper.php          # Funções auxiliares (assumido)
```

## Detalhamento Técnico dos Models

### Configuração Base

Todos os models seguem o padrão:

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class ExampleModel extends Model
{
    // Configurações básicas
    protected $table            = 'tabela_exemplo';
    protected $primaryKey       = 'id_exemplo';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Soft Delete - HABILITADO
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';
    
    // Proteção de campos
    protected $protectFields    = true;
    protected $allowedFields    = [
        // campos específicos
    ];
    
    // Configurações avançadas
    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    
    // Type casting
    protected array $casts = [];
    protected array $castHandlers = [];
    
    // Timestamps automáticos
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    // Validações
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
```

### Models Específicos

#### 1. AtendimentoModel
```php
protected $allowedFields = [
    'id_paciente',
    'id_medico',
    'data_atendimento',
    'classificacao_risco',
    'consulta_enfermagem',
    'hgt_glicemia',
    'pressao_arterial',
    'hipotese_diagnostico',
    'observacao',
    'encaminhamento',
    'obito'
];

protected array $casts = [
    'id_paciente' => 'int',
    'id_medico' => 'int',
    'data_atendimento' => 'datetime',
    'hgt_glicemia' => 'float',
    'obito' => 'boolean'
];
```

#### 2. PacienteModel
```php
protected $allowedFields = [
    'nome',
    'sus',
    'cpf',
    'rg',
    'endereco',
    'numero',
    'complemento',
    'cep',
    'cidade',
    'id_logradouro',
    'data_nascimento',
    'idade',
    'sexo',
    'telefone',
    'celular',
    'email',
    'numero_sus',
    'tipo_sanguineo',
    'nome_responsavel',
    'alergias',
    'observacoes'
];

protected array $casts = [
    'idade' => 'int'
];
```

## Comando CLI - SoftDeleteManager

### Estrutura da Classe

```php
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
        'command' => 'Comando a executar: stats, cleanup, restore'
    ];
    
    protected $options = [
        '--model' => 'Nome do model (para restore)',
        '--id' => 'ID do registro (para restore)',
        '--days' => 'Dias para limpeza (padrão: 30)',
        '--force' => 'Confirma a execução sem perguntar'
    ];
}
```

### Métodos Principais

#### 1. run() - Dispatcher Principal
```php
public function run(array $params)
{
    helper('softdelete');  // Carrega helper customizado
    
    $command = $params[0] ?? null;
    
    switch ($command) {
        case 'stats':
            $this->showStats();
            break;
        case 'cleanup':
            $this->cleanup($params);
            break;
        case 'restore':
            $this->restore($params);
            break;
        default:
            $this->showUsage();
            break;
    }
}
```

#### 2. showStats() - Exibição de Estatísticas
```php
protected function showStats()
{
    CLI::write('=== Estatísticas de Soft Delete ===', 'green');
    CLI::newLine();
    
    // Função helper que retorna array com estatísticas
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
```

#### 3. cleanup() - Limpeza de Registros
```php
protected function cleanup(array $params)
{
    $days = CLI::getOption('days') ?? 30;
    $force = CLI::getOption('force') ?? false;
    
    // Lista todos os models com soft delete
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
            // Função helper para limpeza
            $deleted = cleanup_old_deleted_records($modelName, $days);
            $totalDeleted += $deleted;
            
            if ($deleted > 0) {
                CLI::write("  {$modelName}: {$deleted} registros removidos", 'green');
            }
        } catch (\Exception $e) {
            CLI::write("  {$modelName}: ERRO - " . $e->getMessage(), 'red');
        }
    }
    
    CLI::write("Total removidos definitivamente: {$totalDeleted}", 'green');
}
```

#### 4. restore() - Restauração de Registros
```php
protected function restore(array $params)
{
    $modelName = CLI::getOption('model');
    $id = CLI::getOption('id');
    
    if (!$modelName || !$id) {
        CLI::write('É necessário informar --model e --id', 'red');
        return;
    }
    
    try {
        // Função helper para restauração
        $success = restore_record($modelName, $id);
        
        if ($success) {
            CLI::write("Registro {$id} restaurado com sucesso!", 'green');
        } else {
            CLI::write("Falha ao restaurar registro {$id}.", 'red');
        }
    } catch (\Exception $e) {
        CLI::write("Erro: " . $e->getMessage(), 'red');
    }
}
```

## Migração de Database

### Estrutura da Migração

```php
<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteToTables extends Migration
{
    public function up()
    {
        // Lista de todas as tabelas que precisam de soft delete
        $tables = [
            'atendimentos',
            'atendimento_exames',
            'atendimento_procedimentos', 
            'bairros',
            'exames',
            'logradouros',
            'medicos',
            'pacientes',
            'procedimentos'
        ];
        
        foreach ($tables as $table) {
            try {
                // Adicionar coluna deleted_at
                $this->forge->addColumn($table, [
                    'deleted_at' => [
                        'type' => 'DATETIME',
                        'null' => true,
                        'after' => 'updated_at'  // Posicionar após updated_at
                    ]
                ]);
                
                // Criar índice para performance
                $this->db->query("CREATE INDEX idx_{$table}_deleted_at ON {$table} (deleted_at)");
                
            } catch (\Exception $e) {
                // Log do erro mas continua com outras tabelas
                log_message('error', "Erro ao adicionar soft delete à tabela {$table}: " . $e->getMessage());
            }
        }
    }
    
    public function down()
    {
        $tables = [
            'atendimentos',
            'atendimento_exames',
            'atendimento_procedimentos',
            'bairros', 
            'exames',
            'logradouros',
            'medicos',
            'pacientes',
            'procedimentos'
        ];
        
        foreach ($tables as $table) {
            try {
                // Remover índice
                $this->db->query("DROP INDEX idx_{$table}_deleted_at ON {$table}");
                
                // Remover coluna
                $this->forge->dropColumn($table, 'deleted_at');
                
            } catch (\Exception $e) {
                log_message('error', "Erro ao remover soft delete da tabela {$table}: " . $e->getMessage());
            }
        }
    }
}
```

## Helper Functions (Assumidas)

### softdelete_helper.php

```php
<?php

/**
 * Obtém estatísticas de soft delete para todos os models
 */
function get_soft_delete_stats(): array
{
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
    
    $stats = [];
    
    foreach ($models as $modelName) {
        try {
            $modelClass = "App\\Models\\{$modelName}";
            
            if (!class_exists($modelClass)) {
                $stats[$modelName] = ['erro' => 'Classe não encontrada'];
                continue;
            }
            
            $model = new $modelClass();
            
            // Total de registros (incluindo excluídos)
            $total = $model->withDeleted()->countAllResults();
            
            // Registros ativos
            $ativos = $model->countAllResults();
            
            // Registros excluídos
            $excluidos = $model->onlyDeleted()->countAllResults();
            
            $stats[$modelName] = [
                'total' => $total,
                'ativos' => $ativos,
                'excluidos' => $excluidos
            ];
            
        } catch (\Exception $e) {
            $stats[$modelName] = ['erro' => $e->getMessage()];
        }
    }
    
    return $stats;
}

/**
 * Remove definitivamente registros excluídos há mais de X dias
 */
function cleanup_old_deleted_records(string $modelName, int $days): int
{
    $modelClass = "App\\Models\\{$modelName}";
    
    if (!class_exists($modelClass)) {
        throw new \Exception("Model {$modelName} não encontrado");
    }
    
    $model = new $modelClass();
    $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
    
    // Buscar registros excluídos há mais de X dias
    $oldRecords = $model->onlyDeleted()
                       ->where('deleted_at <', $cutoffDate)
                       ->findAll();
    
    $deletedCount = 0;
    
    foreach ($oldRecords as $record) {
        // Excluir permanentemente (purge)
        if ($model->purgeDeleted() || $model->delete($record[$model->primaryKey], true)) {
            $deletedCount++;
        }
    }
    
    return $deletedCount;
}

/**
 * Restaura um registro específico
 */
function restore_record(string $modelName, $id): bool
{
    $modelClass = "App\\Models\\{$modelName}";
    
    if (!class_exists($modelClass)) {
        throw new \Exception("Model {$modelName} não encontrado");
    }
    
    $model = new $modelClass();
    
    // Verificar se o registro existe e está excluído
    $record = $model->onlyDeleted()->find($id);
    
    if (!$record) {
        throw new \Exception("Registro {$id} não encontrado ou não está excluído");
    }
    
    // Restaurar (definir deleted_at como null)
    return $model->update($id, ['deleted_at' => null]);
}
```

## Considerações de Performance

### Índices Criados

Para cada tabela, é criado um índice na coluna `deleted_at`:

```sql
CREATE INDEX idx_pacientes_deleted_at ON pacientes (deleted_at);
CREATE INDEX idx_atendimentos_deleted_at ON atendimentos (deleted_at);
-- ... para todas as tabelas
```

### Queries Automáticas

O CodeIgniter 4 automaticamente adiciona as condições:

```sql
-- Query normal (apenas registros ativos)
SELECT * FROM pacientes WHERE deleted_at IS NULL;

-- Com withDeleted() 
SELECT * FROM pacientes;

-- Com onlyDeleted()
SELECT * FROM pacientes WHERE deleted_at IS NOT NULL;
```

### Estratégias de Otimização

1. **Particionamento** (para futuro):
   ```sql
   -- Particionar por ano da exclusão
   PARTITION BY YEAR(deleted_at);
   ```

2. **Arquivamento periódico**:
   - Mover registros antigos para tabelas de arquivo
   - Manter apenas registros recentes nas tabelas principais

3. **Índices compostos** (se necessário):
   ```sql
   CREATE INDEX idx_pacientes_status ON pacientes (deleted_at, data_nascimento);
   ```

## Monitoramento e Logs

### Logs de Operações

Todas as operações são logadas:

```php
// No cleanup
log_message('info', "Soft delete cleanup: {$deletedCount} registros removidos de {$modelName}");

// No restore
log_message('info', "Registro {$id} restaurado em {$modelName}");

// Em erros
log_message('error', "Erro no soft delete: " . $e->getMessage());
```

### Métricas Recomendadas

- Taxa de crescimento de registros excluídos
- Tempo de execução das limpezas
- Número de restaurações por período
- Performance das queries com soft delete

## Extensões Futuras

### 1. Soft Delete com Usuário
```php
protected $deletedByField = 'deleted_by';

// Ao excluir
$model->update($id, [
    'deleted_at' => date('Y-m-d H:i:s'),
    'deleted_by' => user_id()
]);
```

### 2. Motivo da Exclusão
```php
protected $deleteReasonField = 'delete_reason';

// Com motivo
$model->update($id, [
    'deleted_at' => date('Y-m-d H:i:s'),
    'delete_reason' => 'Duplicado'
]);
```

### 3. Versionamento
```php
// Manter histórico de versões antes da exclusão
$versionModel->insert([
    'original_id' => $id,
    'model_name' => $modelName,
    'data_snapshot' => json_encode($originalData),
    'deleted_at' => date('Y-m-d H:i:s')
]);
```

Esta documentação técnica fornece todos os detalhes necessários para desenvolvedores trabalharem com o sistema de soft delete implementado.
