# Estrutura da Tabela Pacientes

## Visão Geral

A tabela `pacientes` é uma das principais do sistema, armazenando todas as informações pessoais e médicas dos pacientes atendidos no Pronto Atendimento.

## Estrutura da Tabela

### Campos de Identificação

| Campo | Tipo | Tamanho | Obrigatório | Descrição |
|-------|------|---------|-------------|-----------|
| `id_paciente` | INT | 11 | ✅ | Chave primária, auto incremento |
| `nome` | VARCHAR | 255 | ✅ | Nome completo do paciente |
| `cpf` | VARCHAR | 14 | ✅ | CPF (único no sistema) |
| `rg` | VARCHAR | 20 | ❌ | RG do paciente |

### Informações Pessoais

| Campo | Tipo | Tamanho | Obrigatório | Descrição |
|-------|------|---------|-------------|-----------|
| `data_nascimento` | DATE | - | ✅ | Data de nascimento |
| `sexo` | ENUM | M/F | ✅ | Sexo biológico |
| `idade` | INT | 3 | ❌ | Idade calculada automaticamente |
| `tipo_sanguineo` | ENUM | A+,A-,B+,B-,AB+,AB-,O+,O- | ❌ | Tipo sanguíneo |

### Endereço e Localização

| Campo | Tipo | Tamanho | Obrigatório | Descrição |
|-------|------|---------|-------------|-----------|
| `endereco` | TEXT | - | ❌ | Endereço completo |
| `numero` | VARCHAR | 10 | ❌ | Número da residência |
| `complemento` | VARCHAR | 100 | ❌ | Complemento do endereço |
| `cep` | VARCHAR | 9 | ❌ | CEP (formato: 00000-000) |
| `cidade` | VARCHAR | 100 | ❌ | Cidade de residência |
| `id_logradouro` | INT | 11 | ❌ | FK para tabela logradouros |

### Contato

| Campo | Tipo | Tamanho | Obrigatório | Descrição |
|-------|------|---------|-------------|-----------|
| `telefone` | VARCHAR | 15 | ❌ | Telefone fixo |
| `celular` | VARCHAR | 16 | ❌ | Telefone celular |
| `email` | VARCHAR | 255 | ❌ | Email do paciente |

### Informações do SUS

| Campo | Tipo | Tamanho | Obrigatório | Descrição |
|-------|------|---------|-------------|-----------|
| `sus` | VARCHAR | 15 | ❌ | Número SUS antigo (compatibilidade) |
| `numero_sus` | VARCHAR | 15 | ❌ | Número SUS principal |

### Informações Médicas

| Campo | Tipo | Tamanho | Obrigatório | Descrição |
|-------|------|---------|-------------|-----------|
| `alergias` | TEXT | - | ❌ | Histórico de alergias |
| `observacoes` | TEXT | - | ❌ | Observações gerais |

### Responsável Legal

| Campo | Tipo | Tamanho | Obrigatório | Descrição |
|-------|------|---------|-------------|-----------|
| `nome_responsavel` | VARCHAR | 255 | ❌ | Nome do responsável (menores) |

### Controle de Sistema

| Campo | Tipo | Tamanho | Obrigatório | Descrição |
|-------|------|---------|-------------|-----------|
| `created_at` | DATETIME | - | ❌ | Data de criação |
| `updated_at` | DATETIME | - | ❌ | Data de atualização |
| `deleted_at` | DATETIME | - | ❌ | Data de exclusão (soft delete) |

## Relacionamentos

### Foreign Keys

- `id_logradouro` → `logradouros.id_logradouro` (SET NULL, CASCADE)

### Relacionamentos Externos

- **Atendimentos**: Um paciente pode ter vários atendimentos
- **Logradouros**: Um paciente pode estar vinculado a um logradouro

## Índices

### Automáticos
- **PRIMARY KEY**: `id_paciente`
- **UNIQUE**: `cpf`
- **INDEX**: `deleted_at` (para soft delete)

### Recomendados para Performance
```sql
CREATE INDEX idx_pacientes_nome ON pacientes (nome);
CREATE INDEX idx_pacientes_cpf ON pacientes (cpf);
CREATE INDEX idx_pacientes_data_nascimento ON pacientes (data_nascimento);
CREATE INDEX idx_pacientes_logradouro ON pacientes (id_logradouro);
```

## Validações no Model

### Regras de Validação

```php
protected $validationRules = [
    'nome' => 'required|max_length[255]',
    'cpf' => 'required|exact_length[14]|is_unique[pacientes.cpf,id_paciente,{id_paciente}]',
    'data_nascimento' => 'required|valid_date',
    'sexo' => 'required|in_list[M,F]',
    'email' => 'valid_email|max_length[255]',
    'telefone' => 'max_length[15]',
    'celular' => 'max_length[16]',
    'cep' => 'max_length[9]',
    'tipo_sanguineo' => 'in_list[A+,A-,B+,B-,AB+,AB-,O+,O-]'
];
```

### Type Casting

```php
protected array $casts = [
    'idade' => 'int',
    'id_logradouro' => 'int',
    'data_nascimento' => 'date'
];
```

## Soft Delete

A tabela utiliza **soft delete** para preservar dados:

- **Campo**: `deleted_at`
- **Comportamento**: Registros não são excluídos fisicamente
- **Recuperação**: Possível restaurar registros excluídos
- **Consultas**: Por padrão, retorna apenas registros ativos

### Comandos Relacionados

```bash
# Ver estatísticas de pacientes
php spark softdelete:manage stats

# Restaurar um paciente
php spark softdelete:manage restore --model=PacienteModel --id=123
```

## Migração Consolidada

A estrutura foi consolidada na migração `2025-06-26-105355_CreatePacienteTable.php`, eliminando a necessidade de múltiplas migrações para adicionar campos.

### Histórico de Consolidação

- **Antes**: 2 migrações separadas
  - `CreatePacienteTable.php` (estrutura básica)
  - `AddCamposPacientes.php` (campos adicionais)

- **Depois**: 1 migração consolidada
  - `CreatePacienteTable.php` (estrutura completa)

## Considerações de Desenvolvimento

### Campos Obrigatórios Mínimos

Para cadastrar um paciente, são obrigatórios:
- `nome`
- `cpf` 
- `data_nascimento`
- `sexo`

### Campos Calculados

- `idade`: Pode ser calculada automaticamente baseada em `data_nascimento`

### Campos de Compatibilidade

- `sus`: Mantido para compatibilidade com sistemas antigos
- `numero_sus`: Campo principal para número SUS

### Exemplo de Uso

```php
$pacienteModel = new PacienteModel();

// Inserir novo paciente
$dadosPaciente = [
    'nome' => 'João Silva',
    'cpf' => '123.456.789-00',
    'data_nascimento' => '1990-05-15',
    'sexo' => 'M',
    'telefone' => '(11) 1234-5678',
    'email' => 'joao@email.com'
];

$id = $pacienteModel->insert($dadosPaciente);

// Buscar paciente
$paciente = $pacienteModel->find($id);

// Buscar com logradouro
$pacienteCompleto = $pacienteModel
    ->select('pacientes.*, logradouros.nome_logradouro')
    ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'left')
    ->find($id);
```

## Manutenção e Monitoramento

### Comandos Úteis

```bash
# Verificar integridade dos dados
SELECT COUNT(*) FROM pacientes WHERE cpf IS NULL OR cpf = '';

# Verificar duplicatas de CPF
SELECT cpf, COUNT(*) FROM pacientes GROUP BY cpf HAVING COUNT(*) > 1;

# Estatísticas por faixa etária
SELECT 
    CASE 
        WHEN idade BETWEEN 0 AND 17 THEN 'Menor'
        WHEN idade BETWEEN 18 AND 59 THEN 'Adulto'
        ELSE 'Idoso'
    END as faixa_etaria,
    COUNT(*) as total
FROM pacientes 
WHERE deleted_at IS NULL
GROUP BY faixa_etaria;
```

Esta estrutura garante flexibilidade, integridade e facilita a manutenção do sistema de gestão de pacientes.
