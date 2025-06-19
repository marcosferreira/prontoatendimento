# Diagrama de Fluxo de Processos - MedSystem

## Fluxo Principal de Atendimento

```mermaid
flowchart TD
    A[Paciente chega ao PA] --> B{Já possui cadastro?}
    B -->|Não| C[Cadastro na Recepção]
    B -->|Sim| D[Verificar/Atualizar dados]
    C --> E[Triagem de Enfermagem]
    D --> E
    
    E --> F{Classificação de Risco}
    F -->|Vermelho| G[Atendimento IMEDIATO]
    F -->|Amarelo| H[Fila Urgência - 10min]
    F -->|Verde| I[Fila Pouco Urgente - 60min]
    F -->|Azul| J[Fila Não Urgente - 120min]
    
    G --> K[Consulta Médica]
    H --> K
    I --> K
    J --> K
    
    K --> L{Necessita Exames?}
    L -->|Sim| M[Solicitar Exames]
    L -->|Não| N{Necessita Procedimentos?}
    M --> O[Aguardar Resultados]
    O --> N
    
    N -->|Sim| P[Realizar Procedimentos]
    N -->|Não| Q{Prescrição Médica?}
    P --> Q
    
    Q -->|Sim| R[Gerar Prescrição]
    Q -->|Não| S[Definir Encaminhamento]
    R --> T[Dispensação Farmácia]
    T --> S
    
    S --> U{Tipo de Alta}
    U -->|Alta para Casa| V[Documentos + Orientações]
    U -->|Internação| W[Transferir para Leito]
    U -->|Transferência| X[Transferir outro Hospital]
    U -->|Óbito| Y[Procedimentos de Óbito]
    
    V --> Z[Fim do Atendimento]
    W --> Z
    X --> Z
    Y --> Z
```

## Fluxo de Usuários do Sistema

```mermaid
flowchart LR
    subgraph "RECEPÇÃO"
        A1[Recepcionista]
        A2[Cadastro Pacientes]
        A3[Agendamentos]
    end
    
    subgraph "ENFERMAGEM"
        B1[Enfermeiro]
        B2[Triagem]
        B3[Sinais Vitais]
        B4[Medicações]
    end
    
    subgraph "MÉDICO"
        C1[Médico]
        C2[Consultas]
        C3[Prescrições]
        C4[Prontuários]
    end
    
    subgraph "FARMÁCIA"
        D1[Farmacêutico]
        D2[Dispensação]
        D3[Controle Estoque]
    end
    
    subgraph "GESTÃO"
        E1[Gestor]
        E2[Relatórios]
        E3[Dashboard]
        E4[Estatísticas]
    end
    
    A1 --> A2
    A1 --> A3
    B1 --> B2
    B1 --> B3
    B1 --> B4
    C1 --> C2
    C1 --> C3
    C1 --> C4
    D1 --> D2
    D1 --> D3
    E1 --> E2
    E1 --> E3
    E1 --> E4
    
    A2 --> B2
    B2 --> C2
    C3 --> D2
```

## Integração com Interface MedSystem

### Mapeamento dos Módulos da Sidebar

| Módulo da Interface | Funcionalidade | Usuários Principais |
|-------------------|----------------|---------------------|
| **Dashboard** | Visão geral em tempo real | Todos os usuários |
| **Pacientes** | Cadastro e busca de pacientes | Recepcionista, Enfermeiro |
| **Agendamentos** | Controle de consultas agendadas | Recepcionista |
| **Consultas** | Registro de atendimentos médicos | Médico, Enfermeiro |
| **Prontuários** | Histórico completo do paciente | Médico |
| **Medicamentos** | Prescrição e dispensação | Médico, Farmacêutico |
| **Estatísticas** | KPIs e métricas operacionais | Gestor, Coordenação |
| **Relatórios** | Relatórios gerenciais | Gestor, Administração |
| **Configurações** | Parametrização do sistema | Administrador |
| **Ajuda** | Documentação e suporte | Todos os usuários |

### Estados dos Componentes da Interface

#### Cards de Estatísticas (Dashboard)
- **Pacientes Ativos**: Contagem em tempo real
- **Atendimentos Hoje**: Contador incremental
- **Idade Média**: Cálculo automático
- **Casos Prioritários**: Filtro por classificação vermelha/amarela

#### Tabela de Pacientes
- **Status em Tempo Real**: 
  - `Em Observação` (amarelo)
  - `Estável` (verde)
  - `Crítico` (vermelho)
  - `Alta` (azul)

#### Indicadores de Prioridade
- **Ponto Vermelho**: Emergência
- **Ponto Amarelo**: Urgência  
- **Ponto Verde**: Não urgente

#### Alertas do Sistema
- **Alertas Médicos**: Pacientes que requerem atenção
- **Alertas de Sistema**: Notificações operacionais
- **Alertas de Medicamento**: Interações e contraindicações

## Fluxo de Dados Entre Módulos

```mermaid
sequenceDiagram
    participant R as Recepção
    participant E as Enfermagem
    participant M as Médico
    participant F as Farmácia
    participant S as Sistema
    
    R->>S: Cadastra/Atualiza Paciente
    S->>E: Notifica Novo Paciente
    E->>S: Registra Triagem + Classificação
    S->>M: Paciente na Fila (por prioridade)
    M->>S: Registra Consulta + Diagnóstico
    
    alt Prescrição Necessária
        M->>S: Gera Prescrição
        S->>F: Notifica Nova Prescrição
        F->>S: Confirma Dispensação
    end
    
    alt Exames Necessários
        M->>S: Solicita Exames
        S->>M: Retorna Resultados
    end
    
    M->>S: Define Alta/Encaminhamento
    S->>R: Atualiza Status Final
```

## Cronologia de um Atendimento Típico

| Tempo | Ação | Responsável | Módulo Sistema |
|-------|------|-------------|----------------|
| 08:00 | Chegada do paciente | - | - |
| 08:02 | Cadastro/Verificação | Recepcionista | Pacientes |
| 08:05 | Triagem (classificação verde) | Enfermeiro | Consultas > Triagem |
| 08:07 | Paciente na fila de espera | Sistema | Dashboard |
| 09:05 | Chamada para consulta | Médico | Consultas |
| 09:30 | Consulta finalizada | Médico | Prontuários |
| 09:32 | Prescrição gerada | Médico | Medicamentos |
| 09:35 | Medicamento dispensado | Farmácia | Medicamentos |
| 09:40 | Alta hospitalar | Médico | Consultas |
| 09:42 | Atendimento finalizado | Sistema | Dashboard |

**Tempo total:** 1h42min (dentro do esperado para classificação verde)

---

**Documento complementar ao Fluxo de Uso Principal**  
**Versão:** 2.1.0  
**Data:** 10 de Junho de 2025
