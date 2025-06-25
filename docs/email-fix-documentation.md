# Correção do Erro de Email - Sistema Pronto Atendimento

## Problema
O sistema estava apresentando o erro: `Call to undefined function CodeIgniter\Email\mail()` ao tentar enviar emails.

## Causa
A função `mail()` nativa do PHP não estava disponível no ambiente de desenvolvimento, o que é comum em muitos setups locais.

## Solução Implementada

### 1. Configuração de Email Atualizada
- **Arquivo**: `app/Config/Email.php`
- **Mudanças**:
  - Mudou o protocolo padrão de `mail` para `smtp`
  - Adicionou constructor que lê configurações do arquivo `.env`
  - Implementou fallback para ambiente de desenvolvimento

### 2. Variáveis de Ambiente
- **Arquivo**: `.env`
- **Adicionadas**:
  ```
  email.fromEmail = 'suporte@pmdonaines.pb.gov.br'
  email.fromName = 'Suporte de TI'
  email.protocol = 'sendmail'
  email.SMTPHost = 'smtp.gmail.com'
  email.SMTPUser = ''
  email.SMTPPass = ''
  email.SMTPPort = 587
  email.SMTPCrypto = 'tls'
  ```

### 3. Biblioteca de Email Personalizada
- **Arquivo**: `app/Libraries/Email.php`
- **Funcionalidade**:
  - Estende a classe Email do CodeIgniter
  - Em desenvolvimento: loga emails em vez de enviá-los
  - Em produção: usa comportamento padrão com fallback
  - Salva logs detalhados em `writable/logs/emails/`

### 4. Serviço Personalizado
- **Arquivo**: `app/Config/Services.php`
- **Mudança**: Sobrescreve o serviço de email para usar nossa biblioteca personalizada

### 5. Rota de Teste
- **Arquivo**: `app/Config/Routes.php` e `app/Controllers/Home.php`
- **Funcionalidade**: Adicionada rota `/test-email` para testar o envio de emails

## Como Usar

### Para Desenvolvimento
1. Os emails serão logados automaticamente em `writable/logs/emails/`
2. Acesse `/test-email` para testar o sistema
3. Verifique os logs para confirmar que está funcionando

### Para Produção
1. Configure as credenciais SMTP no arquivo `.env`:
   ```
   email.SMTPUser = 'seu-email@gmail.com'
   email.SMTPPass = 'sua-senha-ou-app-password'
   ```
2. Mude o protocolo se necessário:
   ```
   email.protocol = 'smtp'
   ```

## Vantagens da Solução
- ✅ Funciona em desenvolvimento sem configuração complexa
- ✅ Facilmente configurável para produção
- ✅ Logs detalhados para debugging
- ✅ Fallback automático em caso de falha
- ✅ Compatível com diferentes provedores de email (Gmail, Outlook, etc.)

## Configurações para Diferentes Provedores

### Gmail
```
email.SMTPHost = 'smtp.gmail.com'
email.SMTPPort = 587
email.SMTPCrypto = 'tls'
```

### Outlook/Hotmail
```
email.SMTPHost = 'smtp-mail.outlook.com'
email.SMTPPort = 587
email.SMTPCrypto = 'tls'
```

### Servidor SMTP Personalizado
```
email.SMTPHost = 'seu-servidor-smtp.com'
email.SMTPPort = 587
email.SMTPCrypto = 'tls'
```

## Arquivo de Log de Exemplo
Os emails são salvos em `writable/logs/emails/email_YYYY-MM-DD.log` com formato:
```
2025-06-25 14:30:45 - Email simulado:
Para: teste@exemplo.com
Assunto: Teste de Email - Sistema Pronto Atendimento
Corpo:
Este é um email de teste para verificar se o sistema de email está funcionando corretamente.
--------------------------------------------------
```
