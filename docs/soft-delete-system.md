# Sistema de Soft Delete - Pronto Atendimento

## Visão Geral

O sistema de Pronto Atendimento implementa **Soft Delete** em todos os seus models principais para garantir a preservação de dados e aumentar a segurança do sistema. O soft delete permite que registros sejam marcados como "excluídos" sem serem removidos fisicamente do banco de dados, possibilitando restauração posterior.

## Modelos com Soft Delete Habilitado

Todos os models principais do sistema possuem soft delete ativo:

### 1. Modelos de Dados Principais
- **AtendimentoModel** - Atendimentos médicos
- **PacienteModel** - Dados dos pacientes
- **MedicoModel** - Cadastro de médicos

### 2. Modelos de Localização
- **BairroModel** - Bairros da cidade
- **LogradouroModel** - Logradouros/endereços

### 3. Modelos de Procedimentos Médicos
- **ExameModel** - Tipos de exames
- **ProcedimentoModel** - Procedimentos médicos
- **AtendimentoExameModel** - Relação atendimento-exame
- **AtendimentoProcedimentoModel** - Relação atendimento-procedimento

## Configuração Técnica

### Configuração nos Models

Todos os models possuem as seguintes configurações padronizadas:

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class ExemploModel extends Model
{
    protected $table            = 'tabela_exemplo';
    protected $primaryKey       = 'id_exemplo';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Configuração do Soft Delete
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';
    
    // Configuração de Timestamps
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    // Campos permitidos
    protected $allowedFields = [
        // campos específicos do model
    ];
    
    // Validações
    protected $validationRules = [
        // regras específicas
    ];
}
```

### Estrutura do Banco de Dados

Todas as tabelas possuem os seguintes campos de timestamp:

```sql
CREATE TABLE exemplo (
    id_exemplo INT AUTO_INCREMENT PRIMARY KEY,
    -- campos específicos da tabela --
    created_at DATETIME NULL,
    updated_at DATETIME NULL,
    deleted_at DATETIME NULL,  -- Campo para soft delete
    
    INDEX idx_exemplo_deleted_at (deleted_at)  -- Índice para performance
);
```

## Gerenciamento via CLI

O sistema possui um comando CLI completo para gerenciar registros com soft delete:

### Comando Principal
```bash
php spark softdelete:manage [comando] [opções]
```

### Comandos Disponíveis

#### 1. Estatísticas (stats)
Mostra estatísticas detalhadas de todos os models:

```bash
php spark softdelete:manage stats
```

**Saída:**
```
=== Estatísticas de Soft Delete ===

Model                          | Total | Ativos | Excluídos
-------------------------------|-------|--------|----------
AtendimentoModel              | 1,250 | 1,200  | 50
PacienteModel                 | 5,600 | 5,580  | 20
MedicoModel                   | 45    | 43     | 2
...
```

#### 2. Limpeza (cleanup)
Remove definitivamente registros excluídos há mais de X dias:

```bash
# Limpeza padrão (30 dias)
php spark softdelete:manage cleanup

# Limpeza personalizada (60 dias)
php spark softdelete:manage cleanup --days=60

# Limpeza forçada (sem confirmação)
php spark softdelete:manage cleanup --days=90 --force
```

**Opções:**
- `--days`: Número de dias (padrão: 30)
- `--force`: Executa sem solicitar confirmação

#### 3. Restauração (restore)
Restaura um registro específico:

```bash
php spark softdelete:manage restore --model=PacienteModel --id=123
```

**Opções obrigatórias:**
- `--model`: Nome do model (ex: PacienteModel)
- `--id`: ID do registro a ser restaurado

### Exemplos Práticos

```bash
# Ver estatísticas gerais
php spark softdelete:manage stats

# Limpar registros antigos (60 dias) com confirmação
php spark softdelete:manage cleanup --days=60

# Limpar registros antigos (90 dias) sem confirmação
php spark softdelete:manage cleanup --days=90 --force

# Restaurar um paciente específico
php spark softdelete:manage restore --model=PacienteModel --id=456

# Restaurar um atendimento específico
php spark softdelete:manage restore --model=AtendimentoModel --id=789
```

## Funcionalidades do Sistema

### 1. Exclusão Suave
Quando um registro é "excluído":
- O campo `deleted_at` recebe a data/hora atual
- O registro permanece no banco de dados
- As consultas normais não retornam registros excluídos
- O registro pode ser restaurado a qualquer momento

### 2. Consultas Automáticas
O CodeIgniter 4 automaticamente filtra registros excluídos:

```php
// Retorna apenas registros ativos
$pacientes = $pacienteModel->findAll();

// Para incluir registros excluídos
$todosPacientes = $pacienteModel->withDeleted()->findAll();

// Para ver apenas registros excluídos
$pacientesExcluidos = $pacienteModel->onlyDeleted()->findAll();
```

### 3. Restauração
Registros podem ser restaurados facilmente:

```php
// Via model
$pacienteModel = new PacienteModel();
$pacienteModel->update($id, ['deleted_at' => null]);

// Via comando CLI
php spark softdelete:manage restore --model=PacienteModel --id=123
```

### 4. Limpeza Automática
O sistema permite limpeza automática de registros antigos:
- Configurável por número de dias
- Remove definitivamente do banco
- Pode ser agendada via cron

## Benefícios de Segurança

### 1. Proteção contra Exclusão Acidental
- Dados não são perdidos permanentemente
- Possibilidade de auditoria
- Recuperação rápida de informações importantes

### 2. Compliance e Auditoria
- Rastro completo de operações
- Histórico de exclusões
- Facilita auditorias internas e externas

### 3. Integridade Referencial
- Relacionamentos mantidos
- Consistência de dados preservada
- Evita problemas de foreign key

## Migração de Implementação

A implementação foi feita através da migração:

```php
// app/Database/Migrations/2025-07-04-000001_AddSoftDeleteToTables.php
<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteToTables extends Migration
{
    public function up()
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
            $this->forge->addColumn($table, [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'updated_at'
                ]
            ]);
            
            // Índice para performance
            $this->db->query("CREATE INDEX idx_{$table}_deleted_at ON {$table} (deleted_at)");
        }
    }
}
```

## Monitoramento e Manutenção

### 1. Monitoramento Regular
- Execute `php spark softdelete:manage stats` regularmente
- Monitore o crescimento de registros excluídos
- Analise padrões de exclusão

### 2. Limpeza Periódica
- Configure limpeza automática via cron
- Defina políticas de retenção adequadas
- Mantenha backups antes da limpeza

### 3. Exemplo de Cron Job
```bash
# Limpeza automática mensal (registros > 90 dias)
0 2 1 * * cd /path/to/project && php spark softdelete:manage cleanup --days=90 --force
```

## Considerações de Performance

### 1. Índices
Todas as tabelas possuem índice no campo `deleted_at` para otimizar consultas.

### 2. Consultas Otimizadas
O CodeIgniter automaticamente adiciona a condição `WHERE deleted_at IS NULL` nas consultas.

### 3. Limpeza Regular
A limpeza periódica evita crescimento excessivo das tabelas.

## Troubleshooting

### Problemas Comuns

1. **Erro "Helper não encontrado"**
   ```
   Solução: Verificar se o helper 'softdelete' está sendo carregado
   ```

2. **Registro não restaura**
   ```
   Verificar se o ID existe e se está realmente excluído
   ```

3. **Performance lenta**
   ```
   Verificar índices nas colunas deleted_at
   Executar limpeza de registros antigos
   ```

## Conclusão

O sistema de soft delete implementado no Pronto Atendimento oferece:
- ✅ Proteção total contra perda de dados
- ✅ Facilidade de gerenciamento via CLI
- ✅ Performance otimizada
- ✅ Compliance com boas práticas
- ✅ Flexibilidade de configuração

Esta implementação garante que o sistema seja robusto, seguro e facilmente auditável, atendendo aos requisitos de um sistema de saúde crítico.
