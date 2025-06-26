# Migrations do Sistema de Pronto Atendimento

## Resumo das Migrations Criadas

Baseado no modelo de entidades definido em `docs/database/modelo-entidades.md`, foram criadas as seguintes migrations para o sistema de Pronto Atendimento Municipal:

### Ordem de Execução das Migrations

1. **2025-06-26-105353_CreateBairroTable.php** ✅ (Já existia)
   - Cria a tabela `bairros`
   - Campos: id_bairro, nome_bairro, area, created_at, updated_at

2. **2025-06-26-105354_CreatePacienteTable.php** ✅ (Já existia)
   - Cria a tabela `pacientes`
   - Relacionamento: FK para bairros
   - Campos: id_paciente, nome, sus, cpf, endereco, id_bairro, data_nascimento, idade, created_at, updated_at

3. **2025-06-26-105355_CreateMedicoTable.php** ✅ (Criada)
   - Cria a tabela `medicos`
   - Campos: id_medico, nome, crm, especialidade, status, created_at, updated_at
   - Constraints: CRM único

4. **2025-06-26-105356_CreateProcedimentoTable.php** ✅ (Criada)
   - Cria a tabela `procedimentos`
   - Campos: id_procedimento, nome, codigo, descricao, created_at
   - Suporte para códigos TUSS/SUS

5. **2025-06-26-105357_CreateExameTable.php** ✅ (Criada)
   - Cria a tabela `exames`
   - Campos: id_exame, nome, codigo, tipo, descricao, created_at
   - Tipos: laboratorial, imagem, funcional, outros

6. **2025-06-26-105358_CreateAtendimentoTable.php** ✅ (Criada)
   - Cria a tabela `atendimentos`
   - Relacionamentos: FK para pacientes e médicos
   - Campos completos incluindo classificação de risco, dados vitais, diagnóstico, etc.

7. **2025-06-26-105359_CreateAtendimentoProcedimentoTable.php** ✅ (Criada)
   - Cria a tabela de relacionamento N:N `atendimento_procedimentos`
   - Liga atendimentos com procedimentos realizados

8. **2025-06-26-105360_CreateAtendimentoExameTable.php** ✅ (Criada)
   - Cria a tabela de relacionamento N:N `atendimento_exames`
   - Liga atendimentos com exames solicitados/realizados
   - Controla status e datas dos exames

9. **2025-06-26-105361_AddIndicesOptimizacao.php** ✅ (Criada)
   - Adiciona índices de performance e busca conforme recomendado no modelo

## Características Implementadas

### Relacionamentos
- **1:N** - Bairro → Paciente
- **1:N** - Paciente → Atendimento  
- **1:N** - Médico → Atendimento
- **N:N** - Atendimento ↔ Procedimento
- **N:N** - Atendimento ↔ Exame

### Constraints e Validações
- CPF único na tabela pacientes
- CRM único na tabela médicos
- Foreign keys com CASCADE e RESTRICT apropriados
- Campos ENUM para classificação de risco, status, tipos, etc.

### Índices de Performance
- `idx_paciente_cpf` - Busca por CPF
- `idx_paciente_sus` - Busca por cartão SUS
- `idx_paciente_nome` - Busca por nome
- `idx_bairro_nome` - Busca por bairro
- `idx_atendimento_data` - Filtros por data
- `idx_atendimento_classificacao` - Filtros por classificação de risco
- `idx_medico_crm` - Busca por CRM

### Campos Especiais
- **Classificação de Risco**: Verde, Amarelo, Vermelho, Azul
- **Status do Médico**: Ativo, Inativo
- **Tipos de Exame**: laboratorial, imagem, funcional, outros
- **Status de Exame**: Solicitado, Realizado, Cancelado
- **Encaminhamentos**: Alta, Internação, Transferência, Especialista, Retorno, Óbito
- **Controle de Óbito**: Campo boolean dedicado

## Como Executar as Migrations

Para executar todas as migrations criadas, use o comando do CodeIgniter:

```bash
php spark migrate
```

Para reverter todas as migrations:

```bash
php spark migrate:rollback
```

Para verificar o status das migrations:

```bash
php spark migrate:status
```

## Próximos Passos Recomendados

1. **Criar Models**: Desenvolver os models para cada entidade
2. **Seeders**: Criar seeders com dados iniciais (bairros, médicos, procedimentos, exames)
3. **Triggers**: Implementar triggers para cálculo automático de idade
4. **Validações**: Implementar validações de negócio nos models
5. **Controllers**: Desenvolver controllers para CRUD das entidades

## Observações Técnicas

- Todas as tabelas usam timestamps automáticos (created_at, updated_at)
- Foreign keys configuradas com estratégias apropriadas de CASCADE/RESTRICT
- Campos de texto usando TEXT para comportar descrições longas
- Decimal para valores precisos como glicemia
- ENUM para campos com valores pré-definidos
- Suporte a soft deletes pode ser adicionado posteriormente se necessário
