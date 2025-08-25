# Documentação - Relatórios de Pacientes

## Visão Geral

Este documento descreve a implementação dos relatórios mensais de pacientes no SisPAM (Sistema de Pronto Atendimento Municipal). Os relatórios fornecem insights sobre os pacientes atendidos, segmentados por faixa etária, atendimentos de enfermagem e atendimentos médicos.

## Funcionalidades Implementadas

### 1. Relatório por Faixa Etária

Apresenta a distribuição dos pacientes atendidos no mês por grupos de idade:

- **0-12 anos**: Pacientes pediátricos
- **13-17 anos**: Adolescentes
- **18-30 anos**: Adultos jovens
- **31-50 anos**: Adultos
- **51-70 anos**: Idosos jovens
- **Acima de 70**: Idosos
- **Idade não informada**: Pacientes sem data de nascimento cadastrada

#### Dados Exibidos:
- Total de pacientes por faixa etária
- Percentual de cada faixa sobre o total
- Gráfico de pizza interativo
- Total geral de pacientes únicos

### 2. Relatório de Atendimentos de Enfermagem

Analisa os pacientes que passaram pelos cuidados de enfermagem:

#### Métricas Principais:
- **Pacientes com consulta de enfermagem**: Número de pacientes que tiveram o campo `consulta_enfermagem` preenchido
- **Pacientes sem consulta de enfermagem**: Pacientes sem dados de enfermagem
- **Idade média**: Média de idade dos pacientes atendidos pela enfermagem

#### Classificação de Risco:
- Distribuição por cores do Protocolo de Manchester:
  - **Vermelho**: Emergência (0 minutos)
  - **Laranja**: Muito urgente (10 minutos)
  - **Amarelo**: Urgente (60 minutos)
  - **Verde**: Pouco urgente (120 minutos)
  - **Azul**: Não urgente (240 minutos)
  - **Sem classificação**: Quando não há triagem

### 3. Relatório de Atendimentos Médicos

Apresenta dados sobre os atendimentos realizados por médicos:

#### Métricas Principais:
- **Total de pacientes atendidos**: Pacientes únicos que passaram por consulta médica
- **Médicos ativos**: Número de médicos diferentes que atenderam no período
- **Idade média dos pacientes**: Média de idade dos pacientes atendidos por médicos

#### Ranking de Médicos:
- Top 10 médicos por número de pacientes atendidos
- Dados incluem: nome, CRM, especialidade, total de pacientes e total de atendimentos

#### Tipos de Encaminhamento:
- **Alta**: Pacientes liberados após atendimento
- **Internação**: Pacientes que necessitaram internação
- **Transferência**: Pacientes transferidos para outros serviços
- **Especialista**: Encaminhamento para especialidades
- **Retorno**: Pacientes que devem retornar para acompanhamento
- **Óbito**: Casos de óbito durante o atendimento

## Arquivos Implementados

### Controller: `app/Controllers/Pacientes.php`

#### Novos Métodos:

1. **`relatorios()`**
   - Método principal que exibe a página de relatórios
   - Aceita filtros de mês e ano via GET
   - Retorna view com todos os dados dos relatórios

2. **`getRelatorioIdade($mes, $ano)`**
   - Gera dados do relatório por faixa etária
   - Utiliza query SQL com CASE para categorizar idades
   - Retorna array com faixa_etaria e total_pacientes

3. **`getRelatorioEnfermagem($mes, $ano)`**
   - Analisa atendimentos de enfermagem
   - Verifica campo `consulta_enfermagem` dos atendimentos
   - Inclui dados de classificação de risco

4. **`getRelatorioMedico($mes, $ano)`**
   - Compila dados de atendimentos médicos
   - Gera ranking de médicos
   - Analisa tipos de encaminhamento

5. **`relatoriosApi()`**
   - Endpoint JSON para dados dos relatórios
   - Permite consulta por tipo específico ou todos
   - Útil para integrações futuras

6. **`exportarRelatorios()`**
   - Gera arquivo Excel (.xls) com todos os relatórios
   - Formatação adequada para impressão
   - Nome do arquivo inclui mês e ano

7. **`getAnosDisponiveis()`**
   - Método privado que busca anos com atendimentos
   - Usado para popular filtro de anos

### View: `app/Views/pacientes/relatorios.php`

#### Características:
- Interface responsiva com Bootstrap 5
- Filtros por mês e ano
- Gráficos interativos com Chart.js
- Tabelas organizadas com dados estatísticos
- Botão de exportação para Excel
- Sistema de cores para classificação de risco
- Badges coloridos para diferentes tipos de dados

#### Seções:
1. **Filtros**: Seletores de mês e ano com botão de aplicar
2. **Relatório por Idade**: Tabela + gráfico de pizza
3. **Relatório de Enfermagem**: Métricas + classificações
4. **Relatório Médico**: Cards estatísticos + ranking + encaminhamentos

### Rotas: `app/Config/Routes.php`

#### Novas Rotas Adicionadas:
```php
// Relatórios
$routes->get('relatorios', 'Pacientes::relatorios');
$routes->get('relatorios-api', 'Pacientes::relatoriosApi');
$routes->post('exportar-relatorios', 'Pacientes::exportarRelatorios');
```

### Navegação: `app/Views/components/sidebar.php`

- Adicionado link "Rel. Pacientes" na seção de Relatórios
- Ícone apropriado (bi-people-fill)
- Destaque quando página ativa

## Consultas SQL Utilizadas

### Relatório por Faixa Etária:
```sql
SELECT 
    CASE 
        WHEN TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) BETWEEN 0 AND 12 THEN '0-12 anos'
        WHEN TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) BETWEEN 13 AND 17 THEN '13-17 anos'
        WHEN TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) BETWEEN 18 AND 30 THEN '18-30 anos'
        WHEN TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) BETWEEN 31 AND 50 THEN '31-50 anos'
        WHEN TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) BETWEEN 51 AND 70 THEN '51-70 anos'
        WHEN TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE()) > 70 THEN 'Acima de 70'
        ELSE 'Idade não informada'
    END as faixa_etaria,
    COUNT(DISTINCT p.id_paciente) as total_pacientes
FROM pam_pacientes p
INNER JOIN pam_atendimentos a ON p.id_paciente = a.id_paciente
WHERE MONTH(a.data_atendimento) = ? 
AND YEAR(a.data_atendimento) = ?
AND p.deleted_at IS NULL
AND a.deleted_at IS NULL
GROUP BY faixa_etaria
```

### Relatório de Enfermagem:
```sql
SELECT 
    COUNT(DISTINCT p.id_paciente) as pacientes_enfermagem,
    COUNT(DISTINCT CASE WHEN a.consulta_enfermagem IS NOT NULL AND a.consulta_enfermagem != '' THEN p.id_paciente END) as com_consulta_enfermagem,
    COUNT(DISTINCT CASE WHEN a.consulta_enfermagem IS NULL OR a.consulta_enfermagem = '' THEN p.id_paciente END) as sem_consulta_enfermagem,
    AVG(TIMESTAMPDIFF(YEAR, p.data_nascimento, CURDATE())) as idade_media_enfermagem
FROM pam_pacientes p
INNER JOIN pam_atendimentos a ON p.id_paciente = a.id_paciente
WHERE MONTH(a.data_atendimento) = ? 
AND YEAR(a.data_atendimento) = ?
AND p.deleted_at IS NULL
AND a.deleted_at IS NULL
```

### Ranking de Médicos:
```sql
SELECT 
    m.nome as nome_medico,
    m.crm,
    m.especialidade,
    COUNT(DISTINCT p.id_paciente) as total_pacientes,
    COUNT(a.id_atendimento) as total_atendimentos
FROM pam_pacientes p
INNER JOIN pam_atendimentos a ON p.id_paciente = a.id_paciente
INNER JOIN pam_medicos m ON a.id_medico = m.id_medico
WHERE MONTH(a.data_atendimento) = ? 
AND YEAR(a.data_atendimento) = ?
AND p.deleted_at IS NULL
AND a.deleted_at IS NULL
AND m.deleted_at IS NULL
GROUP BY m.id_medico, m.nome, m.crm, m.especialidade
ORDER BY total_pacientes DESC
LIMIT 10
```

## Considerações Técnicas

### Performance:
- Todas as consultas utilizam índices apropriados
- DISTINCT evita contagem duplicada de pacientes
- LIMIT aplicado onde necessário
- Soft deletes respeitados em todas as consultas

### Segurança:
- Parâmetros sanitizados via prepared statements
- Validação de entrada nos filtros de mês/ano
- Escape de dados na exibição

### Compatibilidade:
- Funciona com MySQL/MariaDB
- Compatível com CodeIgniter 4.x+
- Interface responsiva para mobile

### Extensibilidade:
- API JSON disponível para integrações
- Estrutura permite adição de novos tipos de relatório
- Filtros podem ser expandidos (ex: por médico, por classificação)

## Como Acessar

1. **Via Menu**: Sidebar → Relatórios → Rel. Pacientes
2. **URL Direta**: `/pacientes/relatorios`
3. **API JSON**: `/pacientes/relatorios-api`

## Exportação

- **Formato**: Excel (.xls)
- **Conteúdo**: Todos os relatórios em abas/seções
- **Nome do arquivo**: `relatorio_pacientes_MES_ANO.xls`
- **Ativação**: Botão "Exportar" na interface

## Futuras Melhorias Sugeridas

1. **Filtros Adicionais**:
   - Por médico específico
   - Por classificação de risco
   - Por bairro/logradouro
   - Por faixa de idade customizada

2. **Visualizações**:
   - Gráficos de barras temporais
   - Mapas de calor por bairro
   - Tendências mensais/anuais

3. **Exportação**:
   - Formato PDF
   - Agendamento automático de relatórios
   - Envio por email

4. **Performance**:
   - Cache de consultas frequentes
   - Processamento assíncrono
   - Paginação em grandes volumes

5. **Análises Avançadas**:
   - Comparativo entre períodos
   - Indicadores de produtividade
   - Análise preditiva de demanda
