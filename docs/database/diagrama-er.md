# Diagrama de Entidade-Relacionamento (DER)

```mermaid
erDiagram
    BAIRRO {
        int id_bairro PK
        varchar nome_bairro
        varchar area
        timestamp created_at
    }
    
    PACIENTE {
        int id_paciente PK
        varchar nome
        varchar sus
        varchar cpf UK
        text endereco
        int id_bairro FK
        date data_nascimento
        int idade
        timestamp created_at
        timestamp updated_at
    }
    
    MEDICO {
        int id_medico PK
        varchar nome
        varchar crm UK
        varchar especialidade
        varchar status
        timestamp created_at
        timestamp updated_at
    }
    
    ATENDIMENTO {
        int id_atendimento PK
        int id_paciente FK
        int id_medico FK
        timestamp data_atendimento
        varchar classificacao_risco
        text consulta_enfermagem
        varchar hgt_glicemia
        varchar pressao_arterial
        text hipotese_diagnostico
        text observacao
        varchar encaminhamento
        boolean obito
        timestamp created_at
        timestamp updated_at
    }
    
    PROCEDIMENTO {
        int id_procedimento PK
        varchar nome
        varchar codigo
        text descricao
        timestamp created_at
    }
    
    EXAME {
        int id_exame PK
        varchar nome
        varchar codigo
        varchar tipo
        text descricao
        timestamp created_at
    }
    
    ATENDIMENTO_PROCEDIMENTO {
        int id_atendimento_procedimento PK
        int id_atendimento FK
        int id_procedimento FK
        int quantidade
        text observacao
        timestamp created_at
    }
    
    ATENDIMENTO_EXAME {
        int id_atendimento_exame PK
        int id_atendimento FK
        int id_exame FK
        text resultado
        varchar status
        timestamp data_solicitacao
        timestamp data_realizacao
        text observacao
        timestamp created_at
    }

    %% Relacionamentos
    BAIRRO ||--o{ PACIENTE : "pertence_a"
    PACIENTE ||--o{ ATENDIMENTO : "realiza"
    MEDICO ||--o{ ATENDIMENTO : "atende"
    ATENDIMENTO ||--o{ ATENDIMENTO_PROCEDIMENTO : "possui"
    PROCEDIMENTO ||--o{ ATENDIMENTO_PROCEDIMENTO : "e_realizado_em"
    ATENDIMENTO ||--o{ ATENDIMENTO_EXAME : "solicita"
    EXAME ||--o{ ATENDIMENTO_EXAME : "e_solicitado_em"
```

## Descrição dos Relacionamentos

### 1. BAIRRO → PACIENTE (1:N)
- **Cardinalidade**: Um bairro pode ter muitos pacientes
- **Descrição**: Cada paciente pertence a um bairro específico
- **Chave Estrangeira**: `id_bairro` em PACIENTE

### 2. PACIENTE → ATENDIMENTO (1:N)
- **Cardinalidade**: Um paciente pode ter muitos atendimentos
- **Descrição**: Histórico de atendimentos do paciente
- **Chave Estrangeira**: `id_paciente` em ATENDIMENTO

### 3. MÉDICO → ATENDIMENTO (1:N)
- **Cardinalidade**: Um médico pode realizar muitos atendimentos
- **Descrição**: Atendimentos realizados por cada médico
- **Chave Estrangeira**: `id_medico` em ATENDIMENTO

### 4. ATENDIMENTO ↔ PROCEDIMENTO (N:N)
- **Cardinalidade**: Muitos para muitos
- **Descrição**: Um atendimento pode incluir vários procedimentos e um procedimento pode ser realizado em vários atendimentos
- **Tabela Intermediária**: ATENDIMENTO_PROCEDIMENTO

### 5. ATENDIMENTO ↔ EXAME (N:N)
- **Cardinalidade**: Muitos para muitos
- **Descrição**: Um atendimento pode solicitar vários exames e um tipo de exame pode ser solicitado em vários atendimentos
- **Tabela Intermediária**: ATENDIMENTO_EXAME

## Regras de Negócio Implementadas

### Integridade Referencial
- Todos os relacionamentos utilizam chaves estrangeiras com constraints
- Exclusão em cascata configurada quando apropriado

### Validações
- CPF único por paciente
- CRM único por médico
- Classificação de risco limitada a valores predefinidos
- Status de exames controlado por enum

### Auditoria
- Campos `created_at` e `updated_at` em todas as tabelas principais
- Triggers automáticos para atualização de timestamps

### Funcionalidades Automáticas
- Cálculo automático da idade baseado na data de nascimento
- Atualização automática de timestamps em modificações
