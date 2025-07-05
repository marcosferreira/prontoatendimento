# Sistema de Configurações - SisPAM

## Visão Geral

O sistema de configurações do SisPAM (Sistema de Pronto Atendimento Municipal) permite a administração centralizada de parâmetros do sistema, gestão de usuários, controle de auditoria e backup automático.

## Funcionalidades Implementadas

### 1. Gestão de Configurações
- **Informações da Unidade**: Nome, CNPJ, endereço e telefone
- **Parâmetros do Sistema**: Timeout de sessão, tempo de triagem, capacidade máxima, notificações
- **Aparência**: Tema do sistema e cores personalizáveis
- **Backup**: Configurações de backup automático e retenção

### 2. Gestão de Usuários
- Criação, edição e exclusão de usuários
- Perfis de acesso: Admin, Médico, Enfermeiro, Farmacêutico, Recepcionista, Gestor
- Reset de senhas e controle de status
- Forçar alteração de senha no primeiro login

### 3. Sistema de Auditoria
- Log automático de todas as ações do sistema
- Filtros por usuário, ação, módulo e data
- Histórico completo de modificações
- Rastreabilidade de dados anteriores e novos

### 4. Sistema de Backup
- Backup automático configurável (diário, semanal, mensal)
- Backup manual (completo ou apenas dados)
- Histórico de backups realizados
- Configuração de retenção de arquivos

## Estrutura de Arquivos

### Controllers
- `app/Controllers/Configuracoes.php` - Controller principal das configurações

### Models
- `app/Models/ConfiguracaoModel.php` - Modelo para gerenciar configurações
- `app/Models/AuditoriaModel.php` - Modelo para logs de auditoria

### Views
- `app/Views/configuracoes/index.php` - Página principal
- `app/Views/configuracoes/tabs/` - Tabs individuais (users, system, backup, audit)
- `app/Views/configuracoes/modals/` - Modals para ações (novo usuário, editar usuário)

### Assets
- `public/assets/js/configuracoes.js` - JavaScript para funcionalidades interativas

### Database
- `app/Database/Migrations/` - Migrations para tabelas configuracoes e auditoria
- `app/Database/Seeds/ConfiguracaoSeeder.php` - Seeder para configurações padrão

### Helpers
- `app/Helpers/configuracoes_helper.php` - Funções auxiliares para configurações

### Commands
- `app/Commands/InitConfiguracoes.php` - Comando CLI para inicializar configurações

### Filters
- `app/Filters/AuditoriaFilter.php` - Filtro para auditoria automática

## Instalação e Configuração

### 1. Executar Migrations
```bash
php spark migrate
```

### 2. Executar Seeder (Opcional)
```bash
php spark db:seed ConfiguracaoSeeder
```

### 3. Inicializar Configurações via CLI
```bash
php spark init:configuracoes
```

### 4. Configurar Filtros (Opcional)
Adicionar o filtro de auditoria em `app/Config/Filters.php`:

```php
public array $globals = [
    'before' => [
        // outros filtros...
    ],
    'after' => [
        'auditoria' => \App\Filters\AuditoriaFilter::class,
        // outros filtros...
    ],
];
```

## Permissões de Acesso

O sistema de configurações requer permissões específicas:

- **Superadmin**: Acesso completo a todas as funcionalidades
- **Admin**: Acesso a configurações gerais e gestão de usuários (exceto criação de superadmins)
- **Outros perfis**: Sem acesso às configurações

## Rotas Implementadas

```php
// Rotas principais
GET  /configuracoes                    - Página principal
POST /configuracoes/salvarConfiguracoes - Salvar configurações do sistema

// Gestão de usuários
GET  /configuracoes/usuarios           - Listar usuários (AJAX)
POST /configuracoes/criarUsuario       - Criar novo usuário
POST /configuracoes/editarUsuario/{id} - Editar usuário
POST /configuracoes/resetarSenha/{id}  - Resetar senha

// Auditoria
GET  /configuracoes/auditoria          - Buscar logs de auditoria

// Backup
POST /configuracoes/criarBackup        - Criar backup manual
```

## Funcionalidades JavaScript

O arquivo `configuracoes.js` implementa:

- Gestão de tabs com carregamento dinâmico
- Formulários de usuário com validação
- Filtros de auditoria com paginação
- Masks para CPF, CNPJ e telefone
- Gerador de senhas automático
- Alerts flutuantes para feedback
- Integração AJAX com o backend

## Tabelas do Banco de Dados

### Tabela `configuracoes`
```sql
- id (INT, AUTO_INCREMENT, PRIMARY KEY)
- chave (VARCHAR(100), UNIQUE)
- valor (TEXT)
- descricao (VARCHAR(255))
- tipo (ENUM: string, integer, boolean, float, json)
- categoria (VARCHAR(50))
- editavel (BOOLEAN)
- created_at, updated_at (DATETIME)
```

### Tabela `auditoria`
```sql
- id (BIGINT, AUTO_INCREMENT, PRIMARY KEY)
- usuario_id (INT)
- usuario_nome (VARCHAR(255))
- acao (VARCHAR(100))
- modulo (VARCHAR(100))
- detalhes (VARCHAR(500))
- ip_address (VARCHAR(45))
- user_agent (VARCHAR(500))
- dados_anteriores (JSON)
- dados_novos (JSON)
- created_at, updated_at (DATETIME)
```

### Campos adicionais em `users`
```sql
- nome (VARCHAR(255))
- cpf (VARCHAR(14))
- last_active (DATETIME)
- force_pass_reset (BOOLEAN)
```

## Helper Functions

O sistema disponibiliza funções auxiliares:

```php
// Buscar configuração
$valor = config_value('unidade_nome', 'Padrão');

// Buscar por categoria
$configs = config_categoria('sistema');

// Definir configuração
set_config_value('unidade_nome', 'Novo Nome');

// Registrar auditoria
audit_log('Ação', 'Módulo', 'Detalhes', $dadosAnteriores, $dadosNovos);

// Informações do sistema
$info = sistema_info();

// Status do backup
$status = backup_status();
```

## Segurança

- Validação de permissões em todas as rotas
- Sanitização de dados de entrada
- Logs de auditoria para rastreabilidade
- Senhas temporárias com força de alteração
- Proteção contra CSRF (tokens automáticos do CodeIgniter)

## Considerações Técnicas

1. **Performance**: Caching automático de configurações via static variables
2. **Escalabilidade**: Paginação automática para logs de auditoria
3. **Manutenibilidade**: Separação clara entre Models, Views e Controllers
4. **Flexibilidade**: Sistema de tipos para configurações (string, integer, boolean, etc.)
5. **Backup**: Estrutura preparada para implementação real de backup

## Próximos Passos

1. Implementar backup real do banco de dados
2. Adicionar configurações de email SMTP
3. Implementar notificações por email
4. Adicionar mais filtros de auditoria
5. Implementar cleanup automático de logs antigos
6. Adicionar exportação de logs de auditoria

## Suporte

Para suporte técnico ou dúvidas sobre implementação, consulte a documentação do CodeIgniter 4 ou entre em contato com a equipe de desenvolvimento.
