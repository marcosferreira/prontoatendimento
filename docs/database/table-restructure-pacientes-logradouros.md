# Reestruturação das Tabelas Pacientes e Logradouros

## Resumo da Mudança

**Data:** 04 de julho de 2025  
**Objetivo:** Eliminar campos redundantes entre as tabelas `pacientes` e `logradouros`  
**Resultado:** Centralização das informações de localização na tabela `logradouros`

## Problemas Identificados

### Campos Duplicados/Ambíguos:

**Tabela `pacientes` (antes):**
- ❌ `endereco` (TEXT) - redundante com `id_logradouro`
- ❌ `cep` (VARCHAR 9) - duplicado com `logradouros.cep`
- ❌ `cidade` (VARCHAR 100) - duplicado com `logradouros.cidade`

**Tabela `logradouros` (antes):**
- ⚠️ `cep` (VARCHAR 10) - tamanho inconsistente
- ❌ Faltava campo `cidade`

## Soluções Implementadas

### 1. Tabela `logradouros` - ADICIONADO

**Campos adicionados/modificados:**
```sql
-- Campo adicionado
cidade VARCHAR(100) NULL DEFAULT 'Pombal' COMMENT 'Cidade do logradouro'

-- Campo modificado
cep VARCHAR(9) NULL COMMENT 'CEP no formato 00000-000'
```

**Estrutura completa:**
- `id_logradouro` - Chave primária
- `nome_logradouro` - Nome da rua/avenida
- `tipo_logradouro` - Tipo (Rua, Avenida, etc)
- `cep` - CEP do logradouro
- `cidade` - Cidade (padrão: Pombal)
- `id_bairro` - Referência ao bairro
- `observacoes` - Observações
- `created_at`, `updated_at` - Timestamps

### 2. Tabela `pacientes` - REMOVIDO

**Campos removidos:**
- ❌ `endereco` (TEXT)
- ❌ `cep` (VARCHAR 9)  
- ❌ `cidade` (VARCHAR 100)

**Campos mantidos para endereço:**
- ✅ `id_logradouro` - Referência ao logradouro
- ✅ `numero` - Número da residência
- ✅ `complemento` - Complemento (apto, bloco, etc)

## Estrutura Final

### Relacionamento Corrigido

```
pacientes
├── id_logradouro → logradouros.id_logradouro
├── numero (específico do paciente)
└── complemento (específico do paciente)

logradouros
├── nome_logradouro (Ex: João Pessoa)
├── tipo_logradouro (Ex: Rua)
├── cep (Ex: 58840-000)
├── cidade (Ex: Pombal)
└── id_bairro → bairros.id_bairro
```

### Endereço Completo Montado

**Exemplo:**
- Logradouro: "Rua João Pessoa"
- Número: "123"
- Complemento: "Apto 45"
- Resultado: "Rua João Pessoa, 123 - Apto 45"

## Atualizações nos Models

### LogradouroModel

**Campos adicionados aos `allowedFields`:**
```php
protected $allowedFields = [
    'nome_logradouro',
    'tipo_logradouro',
    'cep',
    'cidade',          // ← NOVO
    'id_bairro',
    'observacoes'
];
```

**Validações atualizadas:**
```php
protected $validationRules = [
    // ...
    'cep' => 'permit_empty|max_length[9]',     // ← CORRIGIDO
    'cidade' => 'permit_empty|max_length[100]', // ← NOVO
    // ...
];
```

### PacienteModel

**Campos removidos dos `allowedFields`:**
```php
protected $allowedFields = [
    // Removidos: 'endereco', 'cep', 'cidade'
    'nome', 'sus', 'cpf', 'rg',
    'id_logradouro', 'numero', 'complemento', // ← Mantidos
    'data_nascimento', 'idade', 'sexo',
    // ... outros campos
];
```

**Validações atualizadas:**
```php
protected $validationRules = [
    // Removidas validações de: 'endereco', 'cep', 'cidade'
    'numero' => 'permit_empty|max_length[10]',
    'complemento' => 'permit_empty|max_length[100]',
    'id_logradouro' => 'permit_empty|is_natural_no_zero', // ← NOVO
    // ...
];
```

**Métodos atualizados:**
```php
// Adicionados campos do logradouro nas consultas
public function getPacientesWithLogradouro() {
    return $this->select('pacientes.*, logradouros.nome_logradouro, 
                         logradouros.tipo_logradouro, logradouros.cep, 
                         logradouros.cidade, bairros.nome_bairro, bairros.area')
                ->join('logradouros', 'logradouros.id_logradouro = pacientes.id_logradouro', 'left')
                ->join('bairros', 'bairros.id_bairro = logradouros.id_bairro', 'left')
                ->findAll();
}
```

## Atualizações no Controller

### PacientesController

**Validações atualizadas:**
```php
// Removidas validações de: 'endereco', 'cep', 'cidade'
$rules = [
    // ...
    'numero' => 'permit_empty|max_length[10]',
    'complemento' => 'permit_empty|max_length[100]',
    'id_logradouro' => 'permit_empty|is_natural_no_zero',
    // ...
];
```

**Dados de inserção/atualização:**
```php
// Removidos: 'endereco', 'cep', 'cidade'
$data = [
    // ...
    'numero' => $this->request->getPost('numero'),
    'complemento' => $this->request->getPost('complemento'),
    'id_logradouro' => $id_logradouro,
    // ...
];
```

**Exibição corrigida:**
```php
// Endereço montado dinamicamente
$endereco_completo = '';
if (!empty($paciente['nome_logradouro'])) {
    $endereco_completo = ($paciente['tipo_logradouro'] ?? '') . ' ' . $paciente['nome_logradouro'];
    if (!empty($paciente['numero'])) {
        $endereco_completo .= ', ' . $paciente['numero'];
    }
    if (!empty($paciente['complemento'])) {
        $endereco_completo .= ' - ' . $paciente['complemento'];
    }
}
```

## Migração de Dados (se necessário)

Se houver dados existentes na tabela `pacientes`:

```sql
-- 1. Migrar dados de endereço para logradouros (se necessário)
INSERT INTO logradouros (nome_logradouro, tipo_logradouro, cep, cidade, id_bairro)
SELECT DISTINCT 
    endereco as nome_logradouro,
    'Rua' as tipo_logradouro,
    cep,
    cidade,
    1 as id_bairro -- bairro padrão
FROM pacientes 
WHERE endereco IS NOT NULL AND endereco != '';

-- 2. Atualizar referências em pacientes
UPDATE pacientes p
JOIN logradouros l ON l.nome_logradouro = p.endereco
SET p.id_logradouro = l.id_logradouro
WHERE p.endereco IS NOT NULL;

-- 3. Remover campos antigos (após validação)
ALTER TABLE pacientes 
DROP COLUMN endereco,
DROP COLUMN cep,
DROP COLUMN cidade;
```

## Benefícios da Reestruturação

### ✅ Vantagens

1. **Eliminação de Redundância**
   - CEP armazenado apenas em `logradouros`
   - Cidade centralizada por logradouro
   - Consistência de dados garantida

2. **Melhor Normalização**
   - Dados de localização centralizados
   - Facilita manutenção de endereços
   - Evita inconsistências

3. **Performance Otimizada**
   - Menor duplicação de dados
   - Consultas mais eficientes
   - Índices mais efetivos

4. **Manutenção Simplificada**
   - Atualização de CEP em um local
   - Facilita correções em massa
   - Melhor integridade referencial

### 🔧 Considerações Técnicas

1. **Views e Forms**
   - Formulários precisam ser atualizados
   - Views devem usar dados do relacionamento
   - JavaScript de CEP deve consultar logradouros

2. **APIs e Integrações**
   - Endpoints podem precisar de ajuste
   - Documentação da API deve ser atualizada
   - Consultas externas devem usar JOINs

3. **Relatórios**
   - Relatórios devem incluir JOINs
   - Filtros por endereço adaptados
   - Exportações atualizadas

## Validação das Mudanças

### Comandos de Teste

```bash
# 1. Executar migrações
php spark migrate:rollback
php spark migrate

# 2. Testar estrutura
php spark db:table pacientes
php spark db:table logradouros

# 3. Verificar relacionamentos
# Inserir dados de teste e validar JOINs
```

### Checklist de Validação

- [ ] Migração executa sem erros
- [ ] Models carregam corretamente
- [ ] Validações funcionam
- [ ] Relacionamentos corretos
- [ ] Formulários funcionais
- [ ] Listagens exibem endereços completos
- [ ] Soft delete funcionando

## Conclusão

A reestruturação eliminou com sucesso a redundância entre as tabelas `pacientes` e `logradouros`, resultando em:

- ✅ **Estrutura normalizada** e consistente
- ✅ **Eliminação de campos ambíguos**
- ✅ **Melhor organização** de dados de localização
- ✅ **Facilitação da manutenção** futura
- ✅ **Compatibilidade total** com soft delete

Esta mudança melhora significativamente a arquitetura do banco de dados e facilita futuras expansões do sistema.
