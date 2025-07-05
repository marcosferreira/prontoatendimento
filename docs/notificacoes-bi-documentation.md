# Sistema de Notificações BI - SisPAM

## 📊 Visão Geral

O **Sistema de Notificações BI** é uma feature avançada de Business Intelligence integrada ao SisPAM que monitora continuamente os dados de atendimento para gerar alertas inteligentes e automáticos. O sistema analisa padrões nos atendimentos para identificar situações que requerem atenção médica ou administrativa.

## 🎯 Objetivos

### Monitoramento Inteligente
- **Pacientes Recorrentes**: Detecta pacientes com múltiplos atendimentos em curto período
- **Surtos Epidemiológicos**: Identifica agrupamentos de sintomas similares por região
- **Sobrecarga do Sistema**: Monitora períodos de alta demanda
- **Anomalias Estatísticas**: Detecta desvios em indicadores de qualidade

### Alertas Preventivos
- **Intervenção Precoce**: Permite ação antes que problemas se agravem
- **Gestão Proativa**: Facilita tomada de decisões baseada em dados
- **Qualidade Assistencial**: Melhora o cuidado ao paciente através de insights

## 🏗️ Arquitetura Técnica

### Componentes Principais

#### 1. **NotificacaoModel** (`app/Models/NotificacaoModel.php`)
- Model responsável pela persistência das notificações
- Suporte a soft delete para auditoria histórica
- Validações específicas para cada tipo de notificação
- Métodos para busca e filtragem avançada

#### 2. **NotificacaoAnalyzer** (`app/Libraries/NotificacaoAnalyzer.php`)
- Engine de análise BI que processa os dados
- Algoritmos específicos para cada tipo de detecção
- Configurações de limites e thresholds
- Sistema de prevenção de duplicatas

#### 3. **Notificacoes Controller** (`app/Controllers/Notificacoes.php`)
- Interface web para visualização e gestão
- APIs REST para integração e atualizações em tempo real
- Funcionalidades de exportação e relatórios
- Controle de permissões por perfil de usuário

#### 4. **Interface Responsiva** (`app/Views/notificacoes/`)
- Dashboard interativo com gráficos Chart.js
- Filtros dinâmicos e busca em tempo real
- Modais para ações rápidas
- Notificações desktop integradas

## 📈 Tipos de Análise

### 1. **Pacientes Recorrentes**
```php
Critério: ≥ 3 atendimentos em 30 dias + sintomas similares
Severidade: Média (3-4 atendimentos) | Alta (≥5 atendimentos)
Ação Sugerida: Acompanhamento especializado
```

### 2. **Surto de Sintomas**
```php
Critério: ≥ 5 casos do mesmo sintoma no mesmo bairro em 7 dias
Severidade: Alta (5-9 casos) | Crítica (≥10 casos)
Ação Sugerida: Investigação epidemiológica
```

### 3. **Alta Demanda**
```php
Critério: Atendimentos > 200% da média horária + ≥8 atendimentos
Severidade: Alta (casos normais) | Crítica (≥3 casos vermelhos)
Ação Sugerida: Protocolo de sobrecarga
```

### 4. **Anomalias Estatísticas**
```php
- Taxa de óbitos > 2x média histórica
- Tempo médio atendimento > 120 minutos
- Concentração casos vermelhos > 15%
```

## 🔧 Configuração e Instalação

### 1. **Migração do Banco**
```bash
php spark migrate
```

### 2. **Execução Manual**
```bash
# Análise completa
php spark notificacoes:analisar

# Análise forçada (ignora intervalo)
php spark notificacoes:analisar --force

# Modo silencioso
php spark notificacoes:analisar --quiet
```

### 3. **Configuração Automática (Cron)**
```bash
# Executa análise a cada 2 horas
0 */2 * * * cd /path/to/project && php spark notificacoes:analisar --quiet
```

## 🎨 Interface do Usuário

### Dashboard Principal
- **Cards de Estatísticas**: Visão geral em tempo real
- **Gráficos Interativos**: Distribuição por severidade e tipo
- **Lista de Notificações**: Ordenada por urgência e data
- **Filtros Avançados**: Por severidade, tipo e período

### Funcionalidades Interativas
- **Auto-refresh**: Atualização automática a cada 2 minutos
- **Ações Rápidas**: Resolver/cancelar direto da lista
- **Keyboard Shortcuts**: Ctrl+R (atualizar), Ctrl+Shift+A (análise)
- **Notificações Desktop**: Alertas críticos em tempo real

## 📊 Severidades e Cores

| Severidade | Cor | Descrição | Ação |
|------------|-----|-----------|------|
| **Crítica** | 🔴 Vermelho | Requer ação imediata | < 12 horas |
| **Alta** | 🟠 Laranja | Atenção urgente | < 24 horas |
| **Média** | 🟡 Amarelo | Monitoramento necessário | < 7 dias |
| **Baixa** | 🟢 Verde | Informativo | Sem prazo |

## 🔐 Controle de Acesso

### Permissões por Perfil
- **SuperAdmin**: Acesso total + configurações
- **Admin**: Visualização + resolução + análise manual
- **Médico**: Visualização de notificações relacionadas
- **Usuário**: Apenas notificações do seu setor

### Auditoria
- Todas as ações são registradas no `AuditoriaModel`
- Histórico completo de resoluções e cancelamentos
- Rastreabilidade de usuários responsáveis

## 📈 Métricas e KPIs

### Indicadores Principais
- **Taxa de Detecção**: % de problemas identificados antecipadamente
- **Tempo de Resposta**: Média entre alerta e resolução
- **Precisão dos Alertas**: % de verdadeiros positivos
- **Cobertura**: % de casos críticos detectados

### Relatórios Disponíveis
- **Relatório Mensal**: Análise de tendências e padrões
- **Exportação PDF**: Documentação para gestão
- **Dashboard em Tempo Real**: Monitoramento contínuo

## 🚀 Casos de Uso Práticos

### Cenário 1: Surto de Dengue
```
Detecção: 8 casos de "febre + cefaleia + mialgia" no Bairro Centro em 3 dias
Alerta: Severidade CRÍTICA - Possível surto
Ação: Notificação automática à vigilância epidemiológica
```

### Cenário 2: Paciente Crônico
```
Detecção: Maria Silva - 5 atendimentos em 20 dias (diabetes)
Alerta: Severidade ALTA - Paciente recorrente
Ação: Encaminhamento para endocrinologista + plano de cuidado
```

### Cenário 3: Sobrecarga do PA
```
Detecção: 15 atendimentos às 14h (média: 6)
Alerta: Severidade ALTA - Demanda excessiva
Ação: Ativação protocolo sobrecarga + recursos adicionais
```

## 🔧 Personalização e Extensão

### Novos Tipos de Análise
Para criar novos tipos de análise, adicione métodos ao `NotificacaoAnalyzer`:

```php
protected function analisarNovoTipo()
{
    // Sua lógica de análise
    $notificacao = [
        'tipo' => 'novo_tipo',
        'titulo' => 'Título da notificação',
        'descricao' => 'Descrição detalhada',
        'severidade' => 'alta',
        'modulo' => 'Módulo',
        'parametros' => $dados
    ];
    
    return $this->notificacaoModel->criarNotificacaoUnica($notificacao);
}
```

### Configuração de Limites
Modifique os thresholds no `NotificacaoAnalyzer` conforme necessário:

```php
// Pacientes recorrentes: 3+ atendimentos em 30 dias
$limiteAtendimentos = 3;
$periodoDias = 30;

// Surto: 5+ casos em 7 dias
$limiteCasos = 5;
$periodoSurto = 7;
```

## 🛠️ Troubleshooting

### Problemas Comuns

#### 1. **Análise não executa**
```bash
# Verificar permissões
php spark notificacoes:analisar --force

# Verificar logs
tail -f writable/logs/log-*.php
```

#### 2. **Notificações duplicadas**
```php
// O sistema previne duplicatas automaticamente
// Verifique o método existeNotificacaoSimilar()
```

#### 3. **Performance lenta**
```sql
-- Adicionar índices se necessário
CREATE INDEX idx_atendimentos_data_created ON atendimentos(created_at);
CREATE INDEX idx_notificacoes_status_severidade ON notificacoes(status, severidade);
```

## 📚 Referências Técnicas

### Dependências
- **CodeIgniter 4.3+**: Framework base
- **Chart.js 3.9+**: Visualização de gráficos
- **Bootstrap 5.2+**: Interface responsiva
- **MySQL 8.0+**: Banco de dados com suporte JSON

### APIs Utilizadas
- **Notificações Web API**: Para alertas desktop
- **Chart.js**: Gráficos interativos
- **Bootstrap Modal**: Interface de ações

### Padrões Seguidos
- **PSR-4**: Autoloading
- **RESTful API**: Endpoints padronizados
- **Responsive Design**: Mobile-first
- **Security Best Practices**: Validação e sanitização

## 🎯 Roadmap Futuro

### Próximas Versões
- [ ] **Machine Learning**: Predição de surtos com IA
- [ ] **Integração WhatsApp**: Alertas via mensagem
- [ ] **Geolocalização**: Mapas de calor de incidências
- [ ] **APIs Externas**: Integração com vigilância sanitária
- [ ] **Relatórios Avançados**: Dashboards executivos

### Melhorias Planejadas
- [ ] **Cache Redis**: Otimização de performance
- [ ] **Webhooks**: Notificações para sistemas externos
- [ ] **Multi-tenancy**: Suporte a múltiplas unidades
- [ ] **Análise Preditiva**: Alertas preventivos com IA

---

**Desenvolvido para o SisPAM - Sistema de Pronto Atendimento Municipal**  
Versão 1.0 - Julho 2025
