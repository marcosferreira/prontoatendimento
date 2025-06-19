# Consultas SQL de Exemplo - Pronto Atendimento

## Consultas Básicas

### 1. Listar todos os atendimentos do dia
```sql
SELECT 
    a.id_atendimento,
    p.nome AS paciente,
    m.nome AS medico,
    a.data_atendimento,
    a.classificacao_risco,
    a.hipotese_diagnostico
FROM atendimento a
JOIN paciente p ON a.id_paciente = p.id_paciente
LEFT JOIN medico m ON a.id_medico = m.id_medico
WHERE DATE(a.data_atendimento) = CURRENT_DATE
ORDER BY a.data_atendimento;
```

### 2. Buscar paciente por nome ou CPF
```sql
SELECT 
    p.id_paciente,
    p.nome,
    p.cpf,
    p.sus,
    p.endereco,
    b.nome_bairro,
    p.data_nascimento,
    p.idade
FROM paciente p
LEFT JOIN bairro b ON p.id_bairro = b.id_bairro
WHERE p.nome ILIKE '%$nome%' 
   OR p.cpf = '$cpf'
ORDER BY p.nome;
```

### 3. Histórico de atendimentos de um paciente
```sql
SELECT 
    a.data_atendimento,
    m.nome AS medico,
    a.classificacao_risco,
    a.hipotese_diagnostico,
    a.pressao_arterial,
    a.hgt_glicemia,
    a.encaminhamento,
    a.observacao
FROM atendimento a
LEFT JOIN medico m ON a.id_medico = m.id_medico
WHERE a.id_paciente = $id_paciente
ORDER BY a.data_atendimento DESC;
```

## Consultas de Relatórios

### 4. Atendimentos por classificação de risco (período)
```sql
SELECT 
    a.classificacao_risco,
    COUNT(*) as total_atendimentos,
    ROUND(COUNT(*) * 100.0 / SUM(COUNT(*)) OVER(), 2) as percentual
FROM atendimento a
WHERE a.data_atendimento BETWEEN '$data_inicio' AND '$data_fim'
GROUP BY a.classificacao_risco
ORDER BY 
    CASE a.classificacao_risco 
        WHEN 'VERMELHO' THEN 1
        WHEN 'AMARELO' THEN 2
        WHEN 'VERDE' THEN 3
        WHEN 'AZUL' THEN 4
    END;
```

### 5. Atendimentos por bairro
```sql
SELECT 
    b.nome_bairro,
    b.area,
    COUNT(a.id_atendimento) as total_atendimentos
FROM bairro b
LEFT JOIN paciente p ON b.id_bairro = p.id_bairro
LEFT JOIN atendimento a ON p.id_paciente = a.id_paciente
WHERE a.data_atendimento BETWEEN '$data_inicio' AND '$data_fim'
GROUP BY b.id_bairro, b.nome_bairro, b.area
ORDER BY total_atendimentos DESC;
```

### 6. Produtividade médica
```sql
SELECT 
    m.nome AS medico,
    m.crm,
    COUNT(a.id_atendimento) as total_atendimentos,
    COUNT(CASE WHEN a.classificacao_risco = 'VERMELHO' THEN 1 END) as urgentes,
    COUNT(CASE WHEN a.obito = true THEN 1 END) as obitos
FROM medico m
LEFT JOIN atendimento a ON m.id_medico = a.id_medico
WHERE a.data_atendimento BETWEEN '$data_inicio' AND '$data_fim'
GROUP BY m.id_medico, m.nome, m.crm
ORDER BY total_atendimentos DESC;
```

## Consultas de Procedimentos e Exames

### 7. Procedimentos mais realizados
```sql
SELECT 
    pr.nome AS procedimento,
    pr.codigo,
    COUNT(ap.id_atendimento_procedimento) as total_realizados,
    SUM(ap.quantidade) as quantidade_total
FROM procedimento pr
JOIN atendimento_procedimento ap ON pr.id_procedimento = ap.id_procedimento
JOIN atendimento a ON ap.id_atendimento = a.id_atendimento
WHERE a.data_atendimento BETWEEN '$data_inicio' AND '$data_fim'
GROUP BY pr.id_procedimento, pr.nome, pr.codigo
ORDER BY total_realizados DESC;
```

### 8. Exames solicitados e realizados
```sql
SELECT 
    e.nome AS exame,
    e.tipo,
    COUNT(ae.id_atendimento_exame) as total_solicitados,
    COUNT(CASE WHEN ae.status = 'REALIZADO' THEN 1 END) as total_realizados,
    COUNT(CASE WHEN ae.status = 'CANCELADO' THEN 1 END) as total_cancelados,
    ROUND(
        COUNT(CASE WHEN ae.status = 'REALIZADO' THEN 1 END) * 100.0 / 
        COUNT(ae.id_atendimento_exame), 2
    ) as percentual_realizacao
FROM exame e
JOIN atendimento_exame ae ON e.id_exame = ae.id_exame
JOIN atendimento a ON ae.id_atendimento = a.id_atendimento
WHERE a.data_atendimento BETWEEN '$data_inicio' AND '$data_fim'
GROUP BY e.id_exame, e.nome, e.tipo
ORDER BY total_solicitados DESC;
```

## Consultas de Análise de Dados

### 9. Faixa etária dos pacientes atendidos
```sql
SELECT 
    CASE 
        WHEN p.idade < 18 THEN 'Menor de 18'
        WHEN p.idade BETWEEN 18 AND 30 THEN '18-30 anos'
        WHEN p.idade BETWEEN 31 AND 50 THEN '31-50 anos'
        WHEN p.idade BETWEEN 51 AND 70 THEN '51-70 anos'
        WHEN p.idade > 70 THEN 'Maior de 70'
        ELSE 'Não informado'
    END as faixa_etaria,
    COUNT(a.id_atendimento) as total_atendimentos
FROM paciente p
JOIN atendimento a ON p.id_paciente = a.id_paciente
WHERE a.data_atendimento BETWEEN '$data_inicio' AND '$data_fim'
GROUP BY 
    CASE 
        WHEN p.idade < 18 THEN 'Menor de 18'
        WHEN p.idade BETWEEN 18 AND 30 THEN '18-30 anos'
        WHEN p.idade BETWEEN 31 AND 50 THEN '31-50 anos'
        WHEN p.idade BETWEEN 51 AND 70 THEN '51-70 anos'
        WHEN p.idade > 70 THEN 'Maior de 70'
        ELSE 'Não informado'
    END
ORDER BY total_atendimentos DESC;
```

### 10. Atendimentos por horário
```sql
SELECT 
    EXTRACT(HOUR FROM a.data_atendimento) as hora,
    COUNT(a.id_atendimento) as total_atendimentos,
    COUNT(CASE WHEN a.classificacao_risco = 'VERMELHO' THEN 1 END) as urgentes
FROM atendimento a
WHERE DATE(a.data_atendimento) = CURRENT_DATE
GROUP BY EXTRACT(HOUR FROM a.data_atendimento)
ORDER BY hora;
```

## Consultas de Monitoramento

### 11. Pacientes com múltiplos atendimentos (retornos)
```sql
SELECT 
    p.nome,
    p.cpf,
    COUNT(a.id_atendimento) as total_atendimentos,
    MIN(a.data_atendimento) as primeiro_atendimento,
    MAX(a.data_atendimento) as ultimo_atendimento
FROM paciente p
JOIN atendimento a ON p.id_paciente = a.id_paciente
WHERE a.data_atendimento >= CURRENT_DATE - INTERVAL '30 days'
GROUP BY p.id_paciente, p.nome, p.cpf
HAVING COUNT(a.id_atendimento) > 1
ORDER BY total_atendimentos DESC;
```

### 12. Tempo médio de atendimento por classificação
```sql
-- Esta consulta assume que existe um campo data_fim_atendimento
-- ou que será adicionado posteriormente
SELECT 
    a.classificacao_risco,
    COUNT(*) as total_atendimentos,
    AVG(EXTRACT(EPOCH FROM (a.data_fim_atendimento - a.data_atendimento))/60) as tempo_medio_minutos
FROM atendimento a
WHERE a.data_atendimento BETWEEN '$data_inicio' AND '$data_fim'
  AND a.data_fim_atendimento IS NOT NULL
GROUP BY a.classificacao_risco
ORDER BY 
    CASE a.classificacao_risco 
        WHEN 'VERMELHO' THEN 1
        WHEN 'AMARELO' THEN 2
        WHEN 'VERDE' THEN 3
        WHEN 'AZUL' THEN 4
    END;
```
