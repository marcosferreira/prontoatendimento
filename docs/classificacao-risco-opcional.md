# Alteração: Classificação de Risco com Valor Padrão

## Descrição
Implementação de valor padrão "Azul - NÃO URGENTE" para a classificação de risco nos atendimentos, permitindo que atendimentos sejam criados rapidamente com classificação automática que pode ser alterada posteriormente.

## Alterações Realizadas

### 1. Database Migration
**Arquivo**: `app/Database/Migrations/2025-08-08-193313_AlterClassificacaoRiscoOptional.php`
- Modificou a coluna `classificacao_risco` da tabela `atendimentos` para permitir valores NULL
- Adicionou método `down()` para reverter a alteração se necessário

### 2. Model AtendimentoModel
**Arquivo**: `app/Models/AtendimentoModel.php`
- Alterou a regra de validação de `'required|in_list[...]'` para `'permit_empty|in_list[...]'`
- Atualizou a mensagem de validação para indicar que o campo é opcional

### 3. Controller Atendimentos
**Arquivo**: `app/Controllers/Atendimentos.php`
- **Nova alteração**: Modificou regra de validação para `permit_empty`
- **Nova alteração**: Adicionou lógica para definir "Azul" como valor padrão quando classificação estiver vazia
- **Nova alteração**: Removeu mensagem de obrigatoriedade da validação

### 4. View Create (Criação de Atendimento)
**Arquivo**: `app/Views/atendimentos/create.php`
- **Nova alteração**: Removeu opção vazia "Selecione a classificação"
- **Nova alteração**: "Azul" agora é pré-selecionado como padrão usando `old('classificacao_risco', 'Azul')`
- **Nova alteração**: Reorganizou a ordem das opções priorizando "Azul" como primeira opção
- **Nova alteração**: Adicionou feedback visual e texto explicativo sobre o padrão
- **Nova alteração**: Implementou função JavaScript para destacar visualmente o padrão

### 5. View Edit (Edição de Atendimento)
**Arquivo**: `app/Views/atendimentos/edit.php`
- Removeu o asterisco (*) do label indicando obrigatoriedade
- Removeu o atributo `required` do select
- Alterou o texto da opção padrão para "Selecione a classificação (opcional)"
- Atualizou a mensagem de feedback para "A classificação de risco é opcional"

### 6. View Show (Visualização de Atendimento)
**Arquivo**: `app/Views/atendimentos/show.php`
- Adicionou verificação se a classificação está vazia
- Exibe "Não classificado" com badge secundário quando não há classificação

### 7. View Index (Listagem de Atendimentos)
**Arquivo**: `app/Views/atendimentos/index.php`
- Adicionou verificação se a classificação está vazia
- Exibe "Não classificado" com badge secundário quando não há classificação

## Comportamento Atual

### Valor Padrão
- **Classificação Padrão**: "Azul - NÃO URGENTE (240 min)"
- **Aplicação**: Automaticamente definido quando nenhuma classificação é selecionada
- **Flexibilidade**: Pode ser alterado pelo usuário antes ou depois de salvar o atendimento

### Interface
- Campo pré-selecionado com "Azul"
- Feedback visual destacando que é o valor padrão
- Texto explicativo informando sobre a classificação padrão
- Ordem das opções priorizando níveis de menor para maior urgência

## Impacto das Alterações

### Positivos
1. **Agilidade**: Permite criar atendimentos rapidamente com classificação sensata por padrão
2. **Segurança**: Evita atendimentos sem classificação (sempre terão pelo menos "Azul")
3. **Workflow**: Profissionais podem focar na urgência real e alterar apenas quando necessário
4. **Consistência**: Todos os atendimentos terão classificação para relatórios e estatísticas

### Considerações
1. **Padrão Conservador**: "Azul" é o nível menos urgente, evitando priorização inadequada
2. **Flexibilidade**: Classificação pode ser alterada a qualquer momento
3. **Relatórios**: Todos os atendimentos terão classificação válida para análises
2. **Estatísticas**: Contadores por classificação não incluirão atendimentos não classificados
3. **Protocolo**: Deve ser estabelecido protocolo para classificação posterior

## Validação
- ✅ Migration executada com sucesso
- ✅ Model atualizado com validação opcional
- ✅ Views atualizadas para refletir opcionalidade
- ✅ Tratamento de valores NULL nas exibições

## Instruções para Uso
1. Ao criar um atendimento, a classificação de risco agora é opcional
2. O campo pode ser deixado em branco e preenchido posteriormente
3. Atendimentos sem classificação aparecem como "Não classificado"
4. A edição permite adicionar classificação a qualquer momento

## Reversão (se necessário)
Para reverter essas alterações:
```bash
php spark migrate:rollback
```
E desfazer as alterações nos arquivos do Model e Views manualmente.
