# Sistema de Backup - SisPAM

## Funcionalidades Implementadas

### 1. Backup Manual
- **Backup Completo**: Inclui estrutura e dados do banco
- **Backup de Dados**: Apenas os dados, sem estrutura
- Interface web integrada para criação de backups
- Download automático ou armazenamento local

### 2. Backup Automático
- Comando CLI para execução via CRON
- Configurável através da interface web
- Limpeza automática de backups antigos
- Logs de auditoria para todos os backups

### 3. Restauração de Backup
- Upload de arquivos de backup (.sql, .backup, .zip)
- Validação de arquivos
- Execução segura da restauração
- Apenas superadmins podem restaurar

### 4. Histórico e Monitoramento
- Histórico completo de backups
- Estatísticas de sucesso/erro
- Informações de tamanho e data
- Status em tempo real

## Configuração do CRON

Para ativar o backup automático, configure o CRON no servidor:

### 1. Acesse o crontab
```bash
sudo crontab -e
```

### 2. Adicione a entrada para backup diário às 2h
```bash
# Backup automático SisPAM - Todos os dias às 2h
0 2 * * * cd /home/marcos/pmdonaines/saude/prontoatendimento && php spark backup:automatico
```

### 3. Para backup semanal (domingos às 2h)
```bash
# Backup automático SisPAM - Domingos às 2h
0 2 * * 0 cd /home/marcos/pmdonaines/saude/prontoatendimento && php spark backup:automatico
```

### 4. Para backup mensal (dia 1 às 2h)
```bash
# Backup automático SisPAM - Todo dia 1 às 2h
0 2 1 * * cd /home/marcos/pmdonaines/saude/prontoatendimento && php spark backup:automatico
```

## Pré-requisitos

### 1. MySQL Client
O sistema utiliza `mysqldump` e `mysql` para backup/restauração:

```bash
# Ubuntu/Debian
sudo apt-get install mysql-client

# CentOS/RHEL
sudo yum install mysql

# Verificar instalação
mysqldump --version
```

### 2. Permissões de Diretório
Certifique-se que o diretório de backup tem permissões corretas:

```bash
sudo mkdir -p /path/to/writable/backups
sudo chown www-data:www-data /path/to/writable/backups
sudo chmod 755 /path/to/writable/backups
```

### 3. Configurações do Banco
Verifique se as credenciais do banco estão corretas no arquivo `.env`:

```env
database.default.username = seu_usuario
database.default.password = sua_senha
database.default.hostname = localhost
database.default.port = 3306
database.default.database = nome_do_banco
```

## Configurações Disponíveis

### Interface Web
Acesse **Configurações > Backup e Segurança** para:

- ✅ Ativar/desativar backup automático
- ⏰ Definir horário (padrão: 02:00)
- 📅 Escolher frequência (diário/semanal/mensal)
- 🗂️ Configurar retenção (padrão: 30 dias)

### Configurações no Banco
As configurações ficam na tabela `configuracoes_sistema`:

| Chave | Valor Padrão | Descrição |
|-------|--------------|-----------|
| `backup_automatico_ativo` | `0` | 0=desativado, 1=ativado |
| `backup_frequencia` | `diario` | diario/semanal/mensal |
| `backup_horario` | `02:00` | Formato HH:MM |
| `backup_retencao_dias` | `30` | Dias para manter backups |

## Monitoramento

### 1. Logs do Sistema
Os backups são registrados nos logs do CodeIgniter:
```bash
tail -f writable/logs/log-*.log | grep -i backup
```

### 2. Auditoria
Todos os backups ficam registrados na tabela `auditoria`:
- Criação de backup
- Restauração de backup
- Limpeza de backups antigos

### 3. Tabela de Backups
A tabela `backups` mantém histórico completo:
```sql
SELECT * FROM backups ORDER BY created_at DESC LIMIT 10;
```

## Solução de Problemas

### 1. Erro "mysqldump not found"
```bash
# Instalar MySQL client
sudo apt-get install mysql-client-core-8.0

# Verificar PATH
which mysqldump
```

### 2. Erro de permissões
```bash
# Verificar permissões do diretório
ls -la writable/backups/

# Corrigir se necessário
sudo chown -R www-data:www-data writable/backups/
sudo chmod -R 755 writable/backups/
```

### 3. Erro de conexão com banco
- Verificar credenciais no `.env`
- Testar conexão manual:
```bash
mysql -h localhost -u usuario -p nome_do_banco
```

### 4. Backup muito grande
Para bancos grandes, considere:
- Aumentar `memory_limit` no PHP
- Usar backup incremental
- Configurar compressão

## Comandos Úteis

### Teste manual do comando
```bash
cd /home/marcos/pmdonaines/saude/prontoatendimento
php spark backup:automatico
```

### Verificar status do CRON
```bash
sudo systemctl status cron
sudo tail -f /var/log/cron.log
```

### Listar backups existentes
```bash
ls -la writable/backups/
```

### Restaurar backup manualmente
```bash
mysql -h localhost -u usuario -p nome_do_banco < backup_arquivo.sql
```

## Segurança

### 1. Backup Files
- Backups são salvos em `writable/backups/`
- Apenas acessível pelo sistema
- Limpeza automática por retenção

### 2. Restauração
- Apenas superadmins podem restaurar
- Confirmação obrigatória na interface
- Log de auditoria completo

### 3. Credenciais
- Nunca versionar arquivos `.env`
- Usar credenciais específicas para backup
- Considerar read-only user para backup

## Próximos Passos

### Melhorias Sugeridas
- [ ] Backup para cloud (AWS S3, Google Drive)
- [ ] Compressão de arquivos de backup
- [ ] Notificações por email em caso de erro
- [ ] Backup incremental inteligente
- [ ] Interface para download de backups
- [ ] Verificação de integridade dos backups
