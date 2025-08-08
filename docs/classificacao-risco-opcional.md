# Alteração: Classificação de Risco Opcional

## Descrição
Remoção da obrigatoriedade da classificação de risco nos atendimentos, permitindo que atendimentos sejam criados sem especificar a classificação.

## Alterações Realizadas

### 1. Database Migration
**Arquivo**: `app/Database/Migrations/2025-08-08-193313_AlterClassificacaoRiscoOptional.php`
- Modificou a coluna `classificacao_risco` da tabela `atendimentos` para permitir valores NULL
- Adicionou método `down()` para reverter a alteração se necessário

### 2. Model AtendimentoModel
**Arquivo**: `app/Models/AtendimentoModel.php`
- Alterou a regra de validação de `'required|in_list[...]'` para `'permit_empty|in_list[...]'`
- Atualizou a mensagem de validação para indicar que o campo é opcional

### 3. View Create (Criação de Atendimento)
**Arquivo**: `app/Views/atendimentos/create.php`
- Removeu o asterisco (*) do label indicando obrigatoriedade
- Removeu o atributo `required` do select
- Alterou o texto da opção padrão para "Selecione a classificação (opcional)"
- Atualizou a mensagem de feedback para "A classificação de risco é opcional"

### 4. View Edit (Edição de Atendimento)
**Arquivo**: `app/Views/atendimentos/edit.php`
- Removeu o asterisco (*) do label indicando obrigatoriedade
- Removeu o atributo `required` do select
- Alterou o texto da opção padrão para "Selecione a classificação (opcional)"
- Atualizou a mensagem de feedback para "A classificação de risco é opcional"

### 5. View Show (Visualização de Atendimento)
**Arquivo**: `app/Views/atendimentos/show.php`
- Adicionou verificação se a classificação está vazia
- Exibe "Não classificado" com badge secundário quando não há classificação

### 6. View Index (Listagem de Atendimentos)
**Arquivo**: `app/Views/atendimentos/index.php`
- Adicionou verificação se a classificação está vazia
- Exibe "Não classificado" com badge secundário quando não há classificação

## Impacto das Alterações

### Positivos
1. **Flexibilidade**: Permite criar atendimentos urgentes sem perder tempo com classificação
2. **Workflow**: Classificação pode ser feita posteriormente por profissional especializado
3. **Usabilidade**: Remove barreira desnecessária no cadastro rápido de atendimentos

### Considerações
1. **Relatórios**: Atendimentos sem classificação aparecerão como "Não classificado"
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
