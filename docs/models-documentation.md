# Models do Sistema de Pronto Atendimento

Este documento descreve os models criados para o sistema de pronto atendimento, baseados nas migrations existentes.

## Estrutura dos Models

### 1. BairroModel
**Tabela:** `bairros`
**Chave Primária:** `id_bairro`

**Campos permitidos:**
- `nome_bairro` (obrigatório, max 100 caracteres)
- `area` (opcional, max 100 caracteres)

**Funcionalidades especiais:**
- Verificação de pacientes vinculados antes da exclusão
- Busca por área
- Busca por nome
- Listagem com contagem de pacientes

### 2. PacienteModel
**Tabela:** `pacientes`
**Chave Primária:** `id_paciente`

**Campos permitidos:**
- `nome` (obrigatório, max 255 caracteres)
- `sus` (opcional, max 15 caracteres)
- `cpf` (obrigatório, único, max 14 caracteres)
- `endereco` (opcional, texto)
- `id_bairro` (opcional, FK para bairros)
- `data_nascimento` (obrigatório, data)
- `idade` (calculado automaticamente)

**Funcionalidades especiais:**
- Cálculo automático da idade baseado na data de nascimento
- Validação de CPF único
- Busca por bairro
- Busca por CPF
- Listagem com dados do bairro

### 3. MedicoModel
**Tabela:** `medicos`
**Chave Primária:** `id_medico`

**Campos permitidos:**
- `nome` (obrigatório, max 255 caracteres)
- `crm` (obrigatório, único, max 20 caracteres)
- `especialidade` (opcional, max 100 caracteres)
- `status` (Ativo/Inativo, padrão: Ativo)

**Funcionalidades especiais:**
- Verificação de atendimentos vinculados antes da exclusão
- Busca apenas médicos ativos
- Busca por CRM
- Busca por especialidade
- Contagem de atendimentos por médico

### 4. ProcedimentoModel
**Tabela:** `procedimentos`
**Chave Primária:** `id_procedimento`

**Campos permitidos:**
- `nome` (obrigatório, max 255 caracteres)
- `codigo` (opcional, max 50 caracteres - código TUSS/SUS)
- `descricao` (opcional, texto)

**Funcionalidades especiais:**
- Verificação de atendimentos vinculados antes da exclusão
- Busca por código
- Busca parcial por nome
- Relatório de procedimentos mais utilizados
- Contagem de uso

### 5. ExameModel
**Tabela:** `exames`
**Chave Primária:** `id_exame`

**Campos permitidos:**
- `nome` (obrigatório, max 255 caracteres)
- `codigo` (opcional, max 50 caracteres)
- `tipo` (obrigatório: laboratorial, imagem, funcional, outros)
- `descricao` (opcional, texto)

**Funcionalidades especiais:**
- Busca por tipo de exame
- Métodos específicos para cada tipo
- Busca parcial por nome
- Contagem por tipo

### 6. AtendimentoModel
**Tabela:** `atendimentos`
**Chave Primária:** `id_atendimento`

**Campos permitidos:**
- `id_paciente` (obrigatório, FK para pacientes)
- `id_medico` (obrigatório, FK para medicos)
- `data_atendimento` (obrigatório, datetime)
- `classificacao_risco` (obrigatório: Verde, Amarelo, Vermelho, Azul)
- `consulta_enfermagem` (opcional, texto)
- `hgt_glicemia` (opcional, decimal 5,2)
- `pressao_arterial` (opcional, formato texto)
- `hipotese_diagnostico` (opcional, texto)
- `observacao` (opcional, texto)
- `encaminhamento` (opcional: Alta, Internação, Transferência, Especialista, Retorno, Óbito)
- `obito` (boolean, padrão: false)

**Funcionalidades especiais:**
- Validação automática entre óbito e encaminhamento
- Busca por paciente, médico, classificação de risco
- Busca de atendimentos completos com dados relacionados
- Busca por período
- Estatísticas por classificação de risco
- Listagem de óbitos

### 7. AtendimentoProcedimentoModel
**Tabela:** `atendimento_procedimentos`
**Chave Primária:** `id_atendimento_procedimento`

**Campos permitidos:**
- `id_atendimento` (obrigatório, FK para atendimentos)
- `id_procedimento` (obrigatório, FK para procedimentos)
- `quantidade` (obrigatório, inteiro > 0, padrão: 1)
- `observacao` (opcional, texto)

**Funcionalidades especiais:**
- Gestão de procedimentos por atendimento
- Adição inteligente (soma quantidades se já existe)
- Remoção de procedimentos
- Atualização de quantidades
- Relatórios de procedimentos mais utilizados
- Relatórios por período

### 8. AtendimentoExameModel
**Tabela:** `atendimento_exames`
**Chave Primária:** `id_atendimento_exame`

**Campos permitidos:**
- `id_atendimento` (obrigatório, FK para atendimentos)
- `id_exame` (obrigatório, FK para exames)
- `resultado` (opcional, texto)
- `status` (obrigatório: Solicitado, Realizado, Cancelado, padrão: Solicitado)
- `data_solicitacao` (obrigatório, datetime - automático se não informado)
- `data_realizacao` (opcional, datetime - automático quando status = Realizado)
- `observacao` (opcional, texto)

**Funcionalidades especiais:**
- Gestão de exames por atendimento
- Controle de status dos exames (Solicitado → Realizado/Cancelado)
- Registro automático de datas de solicitação e realização
- Prevenção de duplicação de exames no mesmo atendimento
- Relatórios de exames mais solicitados
- Relatórios por período e tipo
- Controle de exames pendentes e com atraso
- Estatísticas de tempo médio de realização

## Relacionamentos

```
bairros (1) -----> (N) pacientes
medicos (1) -----> (N) atendimentos
pacientes (1) -----> (N) atendimentos
atendimentos (1) -----> (N) atendimento_procedimentos
procedimentos (1) -----> (N) atendimento_procedimentos
atendimentos (1) -----> (N) atendimento_exames
exames (1) -----> (N) atendimento_exames
```

## Características Gerais

### Validações
- Todos os models possuem validações appropriadas
- Mensagens de erro em português
- Verificação de integridade referencial

### Timestamps
- Todos os models utilizam timestamps automáticos
- Campos `created_at` e `updated_at`

### Funcionalidades Comuns
- Métodos de busca específicos
- Validações de dados
- Prevenção de exclusão quando há dependências
- Relatórios e estatísticas

### Callbacks
- Cálculo automático de idade (PacienteModel)
- Validação de óbito e encaminhamento (AtendimentoModel)
- Verificação de dependências antes da exclusão

## Uso Recomendado

```php
// Exemplo de uso dos models
$pacienteModel = new \App\Models\PacienteModel();
$medicoModel = new \App\Models\MedicoModel();
$atendimentoModel = new \App\Models\AtendimentoModel();
$atendimentoExameModel = new \App\Models\AtendimentoExameModel();

// Buscar pacientes com bairro
$pacientesComBairro = $pacienteModel->getPacientesWithBairro();

// Buscar médicos ativos
$medicosAtivos = $medicoModel->getMedicosAtivos();

// Buscar atendimentos de hoje
$atendimentosHoje = $atendimentoModel->getAtendimentosHoje();

// Solicitar exame para um atendimento
$resultado = $atendimentoExameModel->solicitarExame($idAtendimento, $idExame, 'Suspeita de anemia');

// Registrar resultado de exame
$atendimentoExameModel->registrarResultado($idAtendimentoExame, 'Hemoglobina: 12,5 g/dL');

// Buscar exames pendentes
$examesPendentes = $atendimentoExameModel->getExamesPendentes();
```

Os models foram projetados para facilitar o desenvolvimento do sistema, fornecendo métodos úteis e mantendo a integridade dos dados.
