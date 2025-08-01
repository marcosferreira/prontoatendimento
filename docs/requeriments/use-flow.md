# Fluxo de Uso - Sistema de Pronto Atendimento Municipal

## Visão Geral

Este documento detalha o fluxo de uso do sistema MedSystem para o Pronto Atendimento Municipal, abrangendo desde a chegada do paciente até a finalização do atendimento e geração de relatórios.

## 📊 Diagrama Geral do Fluxo de Atendimento

```mermaid
flowchart TD
    A[👤 Paciente chega ao PA] --> B{🔍 Já cadastrado?}
    B -->|Sim| C[📝 Atualizar dados]
    B -->|Não| D[📋 Novo cadastro]
    C --> E[🏥 Triagem - Enfermeiro]
    D --> E
    E --> F{🚨 Classificação de Risco}
    F -->|🔴 Vermelho| G[⚡ Atendimento IMEDIATO]
    F -->|� Laranja| H[⏱️ Aguarda 10min]
    F -->|�🟡 Amarelo| I[⏳ Aguarda 60min]
    F -->|🟢 Verde| J[🕐 Aguarda 120min]
    F -->|🔵 Azul| K[⏰ Aguarda 240min]
    G --> L[👩‍⚕️ Consulta Médica]
    H --> L
    I --> L
    J --> L
    K --> L
    L --> M{🔬 Precisa exames?}
    M -->|Sim| N[🧪 Solicitação de Exames]
    M -->|Não| O[💊 Prescrição]
    N --> P[📋 Realização de Exames]
    P --> Q[📊 Resultados]
    Q --> O
    O --> R{🏠 Desfecho}
    R -->|Alta| S[📄 Documentos de Alta]
    R -->|Internação| T[🛏️ Transferir para Leito]
    R -->|Transferência| U[🚑 Outro Hospital]
    R -->|Retorno| V[📅 Agendar Retorno]
    S --> W[✅ Fim do Atendimento]
    T --> W
    U --> W
    U --> V
```

## Fluxos Principais

### 1. 🚪 Recepção e Triagem

#### 1.1 Chegada do Paciente
**Ator:** Recepcionista
**Módulo:** Pacientes

**Fluxo:**
1. Paciente chega ao pronto atendimento
2. Recepcionista acessa **Menu Lateral > Pacientes**
3. Verifica se paciente já está cadastrado:
   - **Busca por:** CPF, Nome ou Cartão SUS
   - **Se encontrado:** Atualiza dados se necessário
   - **Se não encontrado:** Realiza novo cadastro

**Dados Coletados:**
- Nome completo
- CPF
- Cartão SUS
- Data de nascimento (idade calculada automaticamente)
- Endereço completo
- Bairro/área de residência
- Telefone de contato

#### 1.2 Classificação de Risco
**Ator:** Enfermeiro(a)
**Módulo:** Consultas > Triagem

**Protocolo de Manchester:**
- 🔴 **Vermelho** - EMERGÊNCIA – atendimento imediato (0 minutos)
- 🟠 **Laranja** - MUITO URGENTE – atendimento praticamente imediato (10 minutos)
- 🟡 **Amarelo** - URGENTE – atendimento rápido, mas pode aguardar (60 minutos)
- 🟢 **Verde** - POUCO URGENTE – pode aguardar atendimento ou ser encaminhado para outros serviços de saúde (120 minutos)
- 🔵 **Azul** - NÃO URGENTE – pode aguardar atendimento ou ser encaminhado para outros serviços de saúde (240 minutos)

```mermaid
stateDiagram-v2
    [*] --> Chegada
    Chegada --> Cadastro
    Cadastro --> Triagem
    Triagem --> Vermelho: Emergência
    Triagem --> Laranja: Muito_Urgente
    Triagem --> Amarelo: Urgência
    Triagem --> Verde: Pouco_Urgente
    Triagem --> Azul: Não_Urgente
    
    Vermelho --> Atendimento_Imediato: 0 min
    Laranja --> Fila_Muito_Urgente: 10 min
    Amarelo --> Fila_Urgente: 60 min
    Verde --> Fila_Pouco_Urgente: 120 min
    Azul --> Fila_Não_Urgente: 240 min
    
    Atendimento_Imediato --> Consulta_Medica
    Fila_Muito_Urgente --> Consulta_Medica
    Fila_Urgente --> Consulta_Medica
    Fila_Pouco_Urgente --> Consulta_Medica
    Fila_Não_Urgente --> Consulta_Medica
    
    Consulta_Medica --> [*]
```

**Dados da Triagem:**
- Sinais vitais básicos
- Queixa principal
- Glicemia (HGT) se indicado
- Pressão arterial sistêmica
- Classificação de risco atribuída
- Observações da enfermagem

### 2. 👩‍⚕️ Atendimento Médico

```mermaid
sequenceDiagram
    participant P as 👤 Paciente
    participant M as 👩‍⚕️ Médico
    participant S as 💻 Sistema
    participant L as 🧪 Laboratório
    participant F as 💊 Farmácia
    
    Note over P,F: Fluxo de Atendimento Médico
    
    M->>S: Acessa Dashboard
    S-->>M: Lista pacientes por prioridade
    M->>S: Seleciona paciente
    S-->>M: Carrega prontuário/histórico
    
    M->>P: Realiza anamnese
    M->>P: Exame físico
    M->>S: Registra consulta
    
    alt Necessita Exames
        M->>S: Solicita exames
        S->>L: Gera solicitação
        L-->>S: Confirma recebimento
        P->>L: Realiza exames
        L->>S: Envia resultados
        S-->>M: Notifica resultados
    end
    
    alt Prescrição Necessária
        M->>S: Cria prescrição
        S->>F: Envia prescrição
        F-->>P: Dispensa medicamentos
    end
    
    M->>S: Finaliza atendimento
    S-->>P: Gera documentos de alta
```

#### 2.1 Consulta Médica
**Ator:** Médico
**Módulo:** Consultas > Nova Consulta

**Fluxo de Atendimento:**
1. Médico acessa **Dashboard** para visualizar fila de pacientes
2. Seleciona paciente por ordem de prioridade (classificação de risco)
3. Acessa **Prontuários** para revisar histórico
4. Realiza consulta e registra:
   - Anamnese
   - Exame físico
   - Hipótese diagnóstica
   - Conduta médica

#### 2.2 Solicitação de Exames
**Módulo:** Consultas > Exames

**Tipos de Exames Disponíveis:**
- **Laboratoriais:** Hemograma, bioquímica, urina
- **Imagem:** Raio-X, ultrassom, tomografia
- **Eletrocardiograma**
- **Outros exames específicos**

**Fluxo:**
1. Médico seleciona exames necessários
2. Sistema gera solicitação com código único
3. Paciente é direcionado para coleta/realização
4. Resultados são integrados ao prontuário

#### 2.3 Procedimentos Médicos
**Módulo:** Consultas > Procedimentos

**Procedimentos Comuns:**
- Curativos
- Suturas
- Medicações endovenosas
- Inalações
- Imobilizações
- Drenagens

### 3. 💊 Prescrição e Medicamentos

#### 3.1 Prescrição Médica
**Módulo:** Medicamentos > Nova Prescrição

**Dados da Prescrição:**
- Medicamento (nome genérico/comercial)
- Dosagem e via de administração
- Frequência e duração
- Orientações especiais
- Interações medicamentosas (alertas automáticos)

#### 3.2 Dispensação
**Ator:** Farmacêutico/Técnico
**Módulo:** Medicamentos > Dispensação

**Controles:**
- Verificação de prescrição válida
- Controle de estoque
- Registro de dispensação
- Orientação ao paciente

### 4. 📋 Finalização do Atendimento

```mermaid
journey
    title Jornada Completa do Paciente no Pronto Atendimento
    section 🚪 Chegada e Recepção
      Chegar ao PA: 3: Paciente
      Procurar recepção: 4: Paciente
      Aguardar atendimento: 2: Paciente
      Fazer cadastro/atualizar dados: 4: Recepcionista
      Receber pulseira identificação: 5: Paciente
    section 🏥 Triagem
      Aguardar triagem: 3: Paciente
      Aferir sinais vitais: 4: Enfermeiro
      Avaliar queixa principal: 5: Enfermeiro
      Classificar risco: 5: Enfermeiro
      Orientar sobre espera: 4: Enfermeiro
    section ⏳ Aguardo por Atendimento
      Aguardar chamada médica: 2: Paciente
      Monitorar fila: 3: Sistema
      Chamar por prioridade: 5: Sistema
    section 👩‍⚕️ Atendimento Médico
      Consulta médica: 5: Médico
      Realizar exames: 4: Técnico
      Aguardar resultados: 3: Paciente
      Prescrever medicamentos: 5: Médico
    section 💊 Medicamentos
      Ir à farmácia: 4: Paciente
      Dispensar medicamentos: 5: Farmacêutico
      Orientar uso: 5: Farmacêutico
    section 📄 Finalização
      Receber documentos: 5: Paciente
      Orientações de alta: 5: Médico
      Sair do PA: 5: Paciente
```

#### 4.1 Desfecho do Atendimento
**Módulo:** Consultas > Finalizar Atendimento

**Opções de Encaminhamento:**
- **Alta hospitalar** - Paciente liberado para casa
- **Internação** - Transferência para leito
- **Transferência** - Outro hospital/especialidade
- **Retorno** - Agendamento de retorno
- **Óbito** - Registro de óbito (se aplicável)

#### 4.2 Documentação
**Documentos Gerados:**
- Receita médica
- Atestado médico
- Guia de encaminhamento
- Relatório de atendimento
- Declaração de comparecimento

### 5. 📊 Monitoramento e Relatórios

#### 5.1 Dashboard em Tempo Real

```mermaid
graph TB
    subgraph "📊 Dashboard Principal"
        A[👥 Pacientes em Atendimento]
        B[⏳ Fila de Espera]
        C[⏱️ Tempo Médio]
        D[🏥 Lotação Atual]
        E[🚨 Alertas Críticos]
    end
    
    subgraph "🔴 Emergência - 0 min"
        F1[Paciente 1]
        F2[Paciente 2]
    end
    
    subgraph "🟡 Urgente - 10 min"
        G1[Paciente 3]
        G2[Paciente 4]
        G3[Paciente 5]
    end
    
    subgraph "🟢 Pouco Urgente - 60 min"
        H1[Paciente 6]
        H2[Paciente 7]
    end
    
    subgraph "🔵 Não Urgente - 120 min"
        I1[Paciente 8]
    end
    
    B --> F1
    B --> F2
    B --> G1
    B --> G2
    B --> G3
    B --> H1
    B --> H2
    B --> I1
```

**Módulo:** Dashboard Principal

**Indicadores Principais:**
- Pacientes em atendimento
- Fila de espera por classificação
- Tempo médio de atendimento
- Lotação atual
- Alertas de pacientes críticos

#### 5.2 Relatórios Gerenciais
**Módulo:** Relatórios

**Relatórios Disponíveis:**
- **Diário:** Atendimentos realizados no dia
- **Semanal:** Estatísticas da semana
- **Mensal:** Relatório consolidado mensal
- **Por Médico:** Performance individual
- **Por Classificação:** Distribuição de riscos
- **Epidemiológico:** Principais diagnósticos

## Fluxos Especiais

### 6. 🚨 Atendimento de Emergência

#### 6.1 Emergência Médica
**Classificação:** Vermelho
**Tempo:** Imediato

```mermaid
flowchart LR
    A[🚨 Emergência Detectada] --> B[⚡ Bypass Triagem Normal]
    B --> C[👩‍⚕️ Atendimento Médico Imediato]
    C --> D[📝 Registro Paralelo de Dados]
    D --> E{🚑 Necessita SAMU?}
    E -->|Sim| F[📞 Comunicação SAMU]
    E -->|Não| G[🏥 Continua Atendimento]
    F --> H{🏥 Transferência Necessária?}
    G --> H
    H -->|UTI| I[🚨 UTI]
    H -->|Cirurgia| J[⚔️ Centro Cirúrgico]
    H -->|Estabilizado| K[📋 Conclusão Atendimento]
    I --> K
    J --> K
```

**Fluxo Acelerado:**
1. Paciente bypassa triagem normal
2. Atendimento médico imediato
3. Registro paralelo dos dados
4. Comunicação com SAMU se necessário
5. Possível transferência para UTI/cirurgia

#### 6.2 Óbito
**Módulo:** Consultas > Registro de Óbito

**Procedimentos:**
1. Registro da hora do óbito
2. Preenchimento de declaração de óbito
3. Comunicação com família
4. Liberação do corpo
5. Notificação aos órgãos competentes

### 7. 👥 Gestão de Usuários

#### 7.1 Perfis de Acesso

```mermaid
graph TD
    A[🔐 Sistema de Autenticação] --> B[👑 Administrador]
    A --> C[👩‍⚕️ Médico]
    A --> D[👩‍⚕️ Enfermeiro]
    A --> E[👩‍💼 Recepcionista]
    A --> F[💊 Farmacêutico]
    A --> G[📊 Gestor]
    
    B --> B1[✅ Acesso Total]
    B1 --> B2[Configurações do Sistema]
    B1 --> B3[Gestão de Usuários]
    B1 --> B4[Todos os Módulos]
    
    C --> C1[🩺 Consultas]
    C --> C2[💊 Prescrições]
    C --> C3[📋 Prontuários]
    C --> C4[🔬 Exames]
    
    D --> D1[🏥 Triagem]
    D --> D2[💉 Medicamentos]
    D --> D3[🩹 Procedimentos]
    D --> D4[📊 Sinais Vitais]
    
    E --> E1[👤 Cadastro Pacientes]
    E --> E2[📅 Agendamentos]
    E --> E3[📞 Atendimento]
    
    F --> F1[💊 Dispensação]
    F --> F2[📦 Controle Estoque]
    F --> F3[⚠️ Interações]
    
    G --> G1[📊 Relatórios]
    G --> G2[📈 Dashboard]
    G --> G3[📋 Estatísticas]
```

- **Administrador:** Acesso total ao sistema
- **Médico:** Consultas, prescrições, prontuários
- **Enfermeiro:** Triagem, medicamentos, procedimentos
- **Recepcionista:** Cadastro de pacientes, agendamentos
- **Farmacêutico:** Medicamentos, dispensação
- **Gestor:** Relatórios, dashboard, estatísticas

#### 7.2 Controle de Acesso

```mermaid
sequenceDiagram
    participant U as 👤 Usuário
    participant S as 🔐 Sistema
    participant DB as 💾 Banco de Dados
    participant A as 📝 Auditoria
    
    U->>S: Login (CPF + Senha)
    S->>DB: Validar credenciais
    DB-->>S: Credenciais válidas
    S->>DB: Verificar perfil de acesso
    DB-->>S: Permissões do usuário
    S->>A: Log de login
    S-->>U: Acesso liberado
    
    Note over U,A: Sessão ativa com timeout
    
    U->>S: Ação no sistema
    S->>A: Log da ação
    S->>DB: Executar operação
    DB-->>S: Resultado
    S-->>U: Resposta
    
    Note over U,A: Timeout ou logout
    
    U->>S: Logout/Timeout
    S->>A: Log de logout
    S-->>U: Sessão encerrada
```

- Login com CPF e senha
- Sessão com timeout automático
- Log de auditoria de todas as ações
- Controle por módulos e funcionalidades

### 8. 📱 Funcionalidades Móveis

#### 8.1 Responsividade
- Interface adaptada para tablets
- Menu lateral colapsível
- Tabelas responsivas
- Formulários otimizados para toque

#### 8.2 Acessibilidade
- Contraste adequado para leitura
- Fontes legíveis
- Navegação por teclado
- Compatibilidade com leitores de tela

## Integrações Externas

```mermaid
graph TB
    subgraph "🏥 Sistema SisPAM"
        PA[Pronto Atendimento]
        DB[(💾 Banco de Dados)]
        API[🔌 APIs Internas]
    end
    
    subgraph "🌐 Sistemas Externos"
        DATASUS[🏛️ DATASUS]
        SAMU[🚑 SAMU]
        LAB[🧪 Laboratórios]
        FARM[💊 Farmácia Popular]
    end
    
    subgraph "📱 Comunicação"
        SMS[📱 SMS]
        EMAIL[📧 Email]
        WHATS[💬 WhatsApp]
        INTERNO[🔔 Sistema Interno]
    end
    
    PA <--> DATASUS
    PA <--> SAMU
    PA <--> LAB
    PA <--> FARM
    
    PA --> SMS
    PA --> EMAIL
    PA --> WHATS
    PA --> INTERNO
    
    PA <--> DB
    API <--> PA
```

### 9.1 Sistemas de Saúde
- **DATASUS:** Sincronização de dados SUS
- **SAMU:** Comunicação de emergências
- **Laboratórios:** Recebimento de resultados
- **Farmácia Popular:** Verificação de medicamentos

### 9.2 Notificações
- **SMS:** Confirmação de consultas
- **Email:** Relatórios para gestores
- **WhatsApp:** Comunicação com pacientes
- **Sistema Interno:** Alertas e avisos

## Indicadores de Performance

```mermaid
pie title Distribuição de Classificação de Risco
    "🔴 Vermelho (Emergência)" : 10
    "🟠 Laranja (Muito Urgente)" : 15
    "🟡 Amarelo (Urgente)" : 25
    "🟢 Verde (Pouco Urgente)" : 40
    "🔵 Azul (Não Urgente)" : 10
```

```mermaid
xychart-beta
    title "Tempo Médio de Atendimento por Classificação"
    x-axis [Vermelho, Laranja, Amarelo, Verde, Azul]
    y-axis "Tempo (minutos)" 0 --> 250
    bar [0, 10, 60, 120, 240]
```

### 10.1 KPIs Operacionais
- **Tempo médio de espera por classificação**
- **Taxa de ocupação do pronto atendimento**
- **Número de atendimentos por médico/hora**
- **Taxa de retorno em 72h**
- **Satisfação do paciente**

### 10.2 KPIs Clínicos
- **Distribuição de classificação de risco**
- **Taxa de internação**
- **Taxa de transferência**
- **Principais diagnósticos**
- **Tempo porta-medicina**

## Considerações de Segurança

### 11.1 Proteção de Dados
- Conformidade com LGPD
- Criptografia de dados sensíveis
- Backup automático diário
- Controle de acesso por níveis

### 11.2 Auditoria
- Log completo de todas as ações
- Rastreabilidade de alterações
- Relatórios de auditoria
- Monitoramento de acessos suspeitos

## 🗃️ Arquitetura do Sistema

```mermaid
graph TB
    subgraph "🖥️ Frontend"
        UI[Interface do Usuário]
        JS[JavaScript/jQuery]
        CSS[Bootstrap 5]
    end
    
    subgraph "⚙️ Backend - CodeIgniter 4"
        C[Controllers]
        M[Models]
        V[Views]
        F[Filters]
        H[Helpers]
    end
    
    subgraph "🔐 Autenticação"
        SHIELD[CodeIgniter Shield]
        AUTH[Auth System]
        PERMS[Permissions]
    end
    
    subgraph "💾 Banco de Dados"
        MYSQL[(MySQL/MariaDB)]
        TABLES[Tabelas pam_*]
        MIG[Migrations]
    end
    
    subgraph "📂 Estrutura MVC"
        direction TB
        CONT[📋 Controllers]
        CONT --> PAC[PacientesController]
        CONT --> ATD[AtendimentosController]
        CONT --> MED[MedicosController]
        
        MOD[🗃️ Models]
        MOD --> PACM[PacienteModel]
        MOD --> ATDM[AtendimentoModel]
        MOD --> MEDM[MedicoModel]
        
        VIEW[👁️ Views]
        VIEW --> PACV[pacientes/]
        VIEW --> ATDV[atendimentos/]
        VIEW --> MEDV[medicos/]
    end
    
    UI <--> C
    C <--> M
    M <--> MYSQL
    C --> V
    V --> UI
    
    C <--> SHIELD
    SHIELD <--> AUTH
    AUTH <--> PERMS
    
    MYSQL --> TABLES
    TABLES --> MIG
```

---

**Versão:** 2.1.0  
**Data de Criação:** 10 de Junho de 2025  
**Responsável:** Equipe de Desenvolvimento MedSystem  
**Aprovação:** Coordenação Médica e TI