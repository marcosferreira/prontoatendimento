# Modelo de Entidades - Pronto Atendimento Municipal

## Análise dos Dados

Com base nos dados coletados do atendimento diário do pronto atendimento municipal, foram identificadas as seguintes entidades e relacionamentos:

## Entidades Principais

### 1. PACIENTE
- **id_paciente** (PK) - Chave primária auto-incremento
- **nome** - Nome completo do paciente
- **sus** - Número do cartão SUS
- **cpf** - CPF do paciente
- **endereco** - Endereço completo
- **id_bairro** (FK) - Referência para tabela de bairros
- **data_nascimento** - Data de nascimento
- **idade** - Idade calculada ou informada
- **created_at** - Data de criação do registro
- **updated_at** - Data de última atualização

### 2. BAIRRO
- **id_bairro** (PK) - Chave primária auto-incremento
- **nome_bairro** - Nome do bairro
- **area** - Área/região do bairro
- **created_at** - Data de criação do registro

### 3. MEDICO
- **id_medico** (PK) - Chave primária auto-incremento
- **nome** - Nome completo do médico
- **crm** - Número do CRM
- **especialidade** - Especialidade médica
- **status** - Ativo/Inativo
- **created_at** - Data de criação do registro
- **updated_at** - Data de última atualização

### 4. ATENDIMENTO
- **id_atendimento** (PK) - Chave primária auto-incremento
- **id_paciente** (FK) - Referência para paciente
- **id_medico** (FK) - Referência para médico
- **data_atendimento** - Data e hora do atendimento
- **classificacao_risco** - Classificação de risco (Verde, Amarelo, Laranja, Vermelho, Azul)
- **consulta_enfermagem** - Dados da consulta de enfermagem
- **hgt_glicemia** - Valor da glicemia
- **pressao_arterial** - Pressão arterial sistêmica
- **hipotese_diagnostico** - Hipótese diagnóstica ou diagnóstico
- **observacao** - Observações gerais
- **encaminhamento** - Tipo de encaminhamento
- **obito** - Indicador de óbito (BOOLEAN)
- **created_at** - Data de criação do registro
- **updated_at** - Data de última atualização

### 5. PROCEDIMENTO
- **id_procedimento** (PK) - Chave primária auto-incremento
- **nome** - Nome do procedimento
- **codigo** - Código do procedimento (TUSS/SUS)
- **descricao** - Descrição detalhada
- **created_at** - Data de criação do registro

### 6. EXAME
- **id_exame** (PK) - Chave primária auto-incremento
- **nome** - Nome do exame
- **codigo** - Código do exame
- **tipo** - Tipo de exame (laboratorial, imagem, etc.)
- **descricao** - Descrição detalhada
- **created_at** - Data de criação do registro

### 7. ATENDIMENTO_PROCEDIMENTO (Tabela de relacionamento N:N)
- **id_atendimento_procedimento** (PK) - Chave primária auto-incremento
- **id_atendimento** (FK) - Referência para atendimento
- **id_procedimento** (FK) - Referência para procedimento
- **quantidade** - Quantidade realizada
- **observacao** - Observações específicas do procedimento
- **created_at** - Data de criação do registro

### 8. ATENDIMENTO_EXAME (Tabela de relacionamento N:N)
- **id_atendimento_exame** (PK) - Chave primária auto-incremento
- **id_atendimento** (FK) - Referência para atendimento
- **id_exame** (FK) - Referência para exame
- **resultado** - Resultado do exame
- **status** - Status (Solicitado, Realizado, Cancelado)
- **data_solicitacao** - Data de solicitação
- **data_realizacao** - Data de realização
- **observacao** - Observações específicas do exame
- **created_at** - Data de criação do registro

## Relacionamentos

### Um para Muitos (1:N)
- **BAIRRO** → **PACIENTE** (Um bairro pode ter muitos pacientes)
- **PACIENTE** → **ATENDIMENTO** (Um paciente pode ter muitos atendimentos)
- **MEDICO** → **ATENDIMENTO** (Um médico pode realizar muitos atendimentos)

### Muitos para Muitos (N:N)
- **ATENDIMENTO** ↔ **PROCEDIMENTO** (através da tabela ATENDIMENTO_PROCEDIMENTO)
- **ATENDIMENTO** ↔ **EXAME** (através da tabela ATENDIMENTO_EXAME)

## Índices Recomendados

### Índices de Performance
- `idx_paciente_cpf` - Índice único em PACIENTE.cpf
- `idx_paciente_sus` - Índice em PACIENTE.sus
- `idx_atendimento_data` - Índice em ATENDIMENTO.data_atendimento
- `idx_atendimento_paciente` - Índice em ATENDIMENTO.id_paciente
- `idx_atendimento_medico` - Índice em ATENDIMENTO.id_medico
- `idx_medico_crm` - Índice único em MEDICO.crm

### Índices de Busca
- `idx_paciente_nome` - Índice em PACIENTE.nome para buscas por nome
- `idx_bairro_nome` - Índice em BAIRRO.nome_bairro
- `idx_atendimento_classificacao` - Índice em ATENDIMENTO.classificacao_risco

## Constraints e Validações

### Constraints de Integridade
- CPF deve ser único e válido
- CRM deve ser único
- Data de atendimento não pode ser futura
- Classificação de risco deve estar em valores predefinidos
- Pressão arterial deve seguir formato válido

### Triggers Sugeridos
- Cálculo automático da idade baseado na data de nascimento
- Atualização automática do campo updated_at
- Validação de dados vitais dentro de parâmetros aceitáveis
