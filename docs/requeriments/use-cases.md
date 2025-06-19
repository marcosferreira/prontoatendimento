# Casos de Uso - Sistema MedSystem

## Casos de Uso Detalhados

### CU001 - Cadastro de Novo Paciente

**Ator Principal:** Recepcionista  
**Módulo:** Pacientes  
**Pré-condições:** Sistema logado, paciente presente  

**Fluxo Principal:**
1. Recepcionista acessa "Menu Lateral > Pacientes"
2. Clica em "Novo Paciente"
3. Preenche formulário:
   - Nome completo (obrigatório)
   - CPF (obrigatório, validação automática)
   - Cartão SUS (opcional)
   - Data de nascimento (obrigatório)
   - Endereço completo
   - Bairro (seleção em dropdown)
   - Telefone de contato
4. Sistema valida dados únicos (CPF)
5. Clica em "Salvar"
6. Sistema gera ID único do paciente
7. Paciente direcionado para triagem

**Fluxos Alternativos:**
- **3a.** CPF já cadastrado: Sistema exibe dados existentes
- **4a.** CPF inválido: Sistema exibe erro e solicita correção
- **5a.** Campos obrigatórios em branco: Sistema destaca campos

**Pós-condições:** Paciente cadastrado no sistema

---

### CU002 - Triagem e Classificação de Risco

**Ator Principal:** Enfermeiro  
**Módulo:** Consultas > Triagem  
**Pré-condições:** Paciente cadastrado no sistema  

**Fluxo Principal:**
1. Enfermeiro acessa fila de pacientes para triagem
2. Seleciona próximo paciente da lista
3. Realiza avaliação inicial:
   - Verifica sinais vitais
   - Anota queixa principal
   - Mede pressão arterial
   - Verifica glicemia se necessário
4. Aplica Protocolo de Manchester
5. Define classificação de risco:
   - Vermelho (emergência)
   - Amarelo (urgência)
   - Verde (pouco urgente)
   - Azul (não urgente)
6. Registra observações de enfermagem
7. Confirma classificação
8. Sistema atualiza fila médica por prioridade

**Fluxos Alternativos:**
- **4a.** Emergência identificada: Paciente direcionado imediatamente
- **5a.** Dúvida na classificação: Consulta médico supervisor

**Pós-condições:** Paciente classificado e na fila médica

---

### CU003 - Consulta Médica

**Ator Principal:** Médico  
**Módulo:** Consultas > Nova Consulta  
**Pré-condições:** Paciente triado e na fila  

**Fluxo Principal:**
1. Médico acessa Dashboard e visualiza fila
2. Seleciona próximo paciente por prioridade
3. Acessa histórico no módulo Prontuários
4. Chama paciente para consulta
5. Realiza anamnese e exame físico
6. Registra no sistema:
   - História da doença atual
   - Exame físico
   - Hipótese diagnóstica
   - Conduta médica
7. Define necessidade de exames/procedimentos
8. Prescreve medicamentos se necessário
9. Define tipo de alta/encaminhamento
10. Finaliza consulta

**Fluxos Alternativos:**
- **7a.** Exames necessários: Segue para CU004
- **8a.** Medicação necessária: Segue para CU005
- **9a.** Internação necessária: Segue para CU007

**Pós-condições:** Consulta registrada, paciente com conduta definida

---

### CU004 - Solicitação de Exames

**Ator Principal:** Médico  
**Módulo:** Consultas > Exames  
**Pré-condições:** Consulta médica em andamento  

**Fluxo Principal:**
1. Médico acessa módulo de exames na consulta
2. Seleciona tipos de exames necessários:
   - Laboratoriais (hemograma, bioquímica, etc.)
   - Imagem (RX, US, TC)
   - ECG
   - Outros específicos
3. Preenche justificativa clínica
4. Sistema gera código único da solicitação
5. Imprime guia de exames
6. Paciente direcionado para coleta/realização
7. Aguarda resultados no sistema
8. Resultados integrados ao prontuário
9. Médico reavalia com base nos resultados

**Fluxos Alternativos:**
- **6a.** Exame não disponível: Sistema sugere alternativas
- **8a.** Resultado crítico: Sistema gera alerta automático

**Pós-condições:** Exames solicitados e resultados disponíveis

---

### CU005 - Prescrição Médica

**Ator Principal:** Médico  
**Módulo:** Medicamentos > Nova Prescrição  
**Pré-condições:** Consulta médica em andamento  

**Fluxo Principal:**
1. Médico acessa módulo de prescrição
2. Busca medicamento por nome/princípio ativo
3. Seleciona medicamento da lista
4. Define posologia:
   - Dosagem
   - Via de administração
   - Frequência
   - Duração do tratamento
5. Adiciona orientações especiais
6. Sistema verifica interações medicamentosas
7. Confirma prescrição
8. Sistema gera receita digital
9. Notifica farmácia para dispensação

**Fluxos Alternativos:**
- **6a.** Interação detectada: Sistema exibe alerta e sugere alternativas
- **8a.** Medicamento controlado: Solicita justificativa adicional

**Pós-condições:** Prescrição gerada e enviada para farmácia

---

### CU006 - Dispensação de Medicamentos

**Ator Principal:** Farmacêutico  
**Módulo:** Medicamentos > Dispensação  
**Pré-condições:** Prescrição médica válida  

**Fluxo Principal:**
1. Farmacêutico recebe notificação de nova prescrição
2. Acessa módulo de dispensação
3. Verifica prescrição digital
4. Confere disponibilidade em estoque
5. Separa medicamentos prescritos
6. Registra dispensação no sistema
7. Orienta paciente sobre uso correto
8. Atualiza estoque automaticamente
9. Entrega medicamentos ao paciente

**Fluxos Alternativos:**
- **4a.** Medicamento em falta: Notifica médico para substituição
- **5a.** Medicamento controlado: Exige documentação adicional

**Pós-condições:** Medicamentos dispensados e estoque atualizado

---

### CU007 - Alta Hospitalar

**Ator Principal:** Médico  
**Módulo:** Consultas > Finalizar Atendimento  
**Pré-condições:** Consulta finalizada, conduta definida  

**Fluxo Principal:**
1. Médico acessa finalização do atendimento
2. Seleciona tipo de alta:
   - Alta para casa
   - Internação
   - Transferência
   - Óbito
3. Preenche documentos necessários:
   - Receita médica
   - Atestado médico
   - Relatório de atendimento
4. Define orientações pós-alta
5. Agenda retorno se necessário
6. Finaliza atendimento no sistema
7. Gera documentos para impressão
8. Orienta paciente/acompanhante

**Fluxos Alternativos:**
- **2a.** Internação: Sistema verifica disponibilidade de leitos
- **2b.** Transferência: Gera guia de encaminhamento
- **2c.** Óbito: Segue procedimentos específicos

**Pós-condições:** Atendimento finalizado, documentação gerada

---

### CU008 - Geração de Relatórios

**Ator Principal:** Gestor  
**Módulo:** Relatórios  
**Pré-condições:** Sistema com dados de atendimento  

**Fluxo Principal:**
1. Gestor acessa módulo de relatórios
2. Seleciona tipo de relatório:
   - Diário
   - Semanal
   - Mensal
   - Por médico
   - Por classificação
3. Define período de análise
4. Seleciona filtros adicionais
5. Sistema processa dados
6. Exibe relatório na tela
7. Opção de exportar (PDF, Excel)
8. Salva relatório no histórico

**Fluxos Alternativos:**
- **5a.** Período muito amplo: Sistema sugere otimização
- **6a.** Sem dados no período: Exibe mensagem informativa

**Pós-condições:** Relatório gerado e disponível

---

### CU009 - Atendimento de Emergência

**Ator Principal:** Médico  
**Módulo:** Dashboard (Acesso direto)  
**Pré-condições:** Paciente em estado crítico  

**Fluxo Principal:**
1. Paciente chega em estado crítico
2. Bypassa triagem normal
3. Atendimento médico imediato
4. Registro paralelo de dados básicos
5. Estabilização do paciente
6. Registro completo quando estabilizado
7. Definição de conduta (UTI, cirurgia, etc.)
8. Comunicação com família
9. Transferência/internação conforme necessário

**Fluxos Alternativos:**
- **5a.** Óbito: Segue protocolos específicos
- **7a.** Transferência externa: Contata SAMU

**Pós-condições:** Paciente estabilizado e encaminhado

---

### CU010 - Controle de Acesso

**Ator Principal:** Administrador  
**Módulo:** Configurações > Usuários  
**Pré-condições:** Privilégios administrativos  

**Fluxo Principal:**
1. Administrador acessa gestão de usuários
2. Cria novo usuário no sistema
3. Define perfil de acesso:
   - Médico
   - Enfermeiro
   - Recepcionista
   - Farmacêutico
   - Gestor
4. Configura permissões por módulo
5. Gera credenciais de acesso
6. Notifica usuário por email
7. Usuário altera senha no primeiro login
8. Sistema registra log de acessos

**Fluxos Alternativos:**
- **2a.** CPF já cadastrado: Sistema impede duplicação
- **7a.** Senha não alterada: Sistema bloqueia após período

**Pós-condições:** Usuário criado com acesso configurado

---

## Métricas de Sucesso

### Tempos Máximos por Caso de Uso
- **CU001 (Cadastro):** 3 minutos
- **CU002 (Triagem):** 5 minutos  
- **CU003 (Consulta):** 15-30 minutos
- **CU004 (Exames):** 2 minutos para solicitação
- **CU005 (Prescrição):** 3 minutos
- **CU006 (Dispensação):** 5 minutos
- **CU007 (Alta):** 5 minutos
- **CU008 (Relatórios):** 2 minutos
- **CU009 (Emergência):** Imediato
- **CU010 (Usuários):** 5 minutos

### Critérios de Qualidade
- **Usabilidade:** Interface intuitiva, máximo 3 cliques
- **Performance:** Resposta em menos de 2 segundos
- **Confiabilidade:** 99.9% de disponibilidade
- **Segurança:** Conformidade LGPD, auditoria completa

---

**Versão:** 2.1.0  
**Data:** 10 de Junho de 2025  
**Responsável:** Equipe de Análise de Sistemas
