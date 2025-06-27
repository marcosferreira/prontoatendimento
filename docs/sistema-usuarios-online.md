# Sistema de Usuários Online - Documentação

## Visão Geral

O sistema de detecção de usuários online no sistema de pronto atendimento utiliza múltiplas abordagens para determinar quais usuários estão atualmente ativos/online no sistema.

## Abordagens Implementadas

### 1. Campo `last_active` (Abordagem Preferencial)

O CodeIgniter Shield pode ser configurado para atualizar automaticamente um campo `last_active` na tabela de usuários toda vez que um usuário autenticado faz uma requisição.

**Configuração:**
- `app/Config/Auth.php`: `$recordActiveDate = true`
- Filtro `session` deve estar aplicado às rotas protegidas
- O campo `last_active` deve existir na tabela `users`

**Como funciona:**
- Considera usuários online se `last_active >= (agora - 2 horas)`
- Atualização automática a cada requisição do usuário autenticado

### 2. Análise de Sessões Ativas (Fallback)

Quando o campo `last_active` não está disponível ou configurado, o sistema analisa os arquivos de sessão ativos.

**Como funciona:**
- Verifica arquivos em `writable/session/ci_session*`
- Considera apenas sessões modificadas nas últimas 2 horas
- Conta sessões que contêm dados de usuário logado (`user|a:1:{s:2:"id"`)

### 3. Usuários Ativos do Sistema (Fallback Final)

Como última opção, conta todos os usuários que têm o status `active = 1` no banco de dados.

**Como funciona:**
- Conta usuários com `active = 1`
- Não indica atividade real, apenas usuários habilitados

## Implementação no Código

### Dashboard (`app/Views/admin/dashboard.php`)

```php
// Usuários online: múltiplas abordagens para detectar usuários online
$onlineUsers = 0;

// Abordagem 1: Verificar last_active (se disponível)
$onlineThreshold = date('Y-m-d H:i:s', strtotime('-2 hours'));
try {
    $onlineUsers = auth()->getProvider()->where('last_active >=', $onlineThreshold)->countAllResults();
} catch (Exception $e) {
    $onlineUsers = 0;
}

// Abordagem 2: Se não houver last_active, verificar sessões ativas
if ($onlineUsers == 0) {
    $sessionPath = WRITEPATH . 'session/';
    $activeSessionCount = 0;
    
    if (is_dir($sessionPath)) {
        $sessionFiles = glob($sessionPath . 'ci_session*');
        $twoHoursAgo = time() - (2 * 3600);
        
        foreach ($sessionFiles as $sessionFile) {
            if (filemtime($sessionFile) > $twoHoursAgo) {
                $content = file_get_contents($sessionFile);
                if (strpos($content, 'user|a:1:{s:2:"id"') !== false) {
                    $activeSessionCount++;
                }
            }
        }
    }
    
    $onlineUsers = $activeSessionCount;
}

// Abordagem 3: Se ainda for 0, usar usuários ativos do sistema como fallback
if ($onlineUsers == 0) {
    $onlineUsers = auth()->getProvider()->where('active', 1)->countAllResults();
}
```

### Relatórios (`app/Views/admin/reports/index.php`)

A mesma lógica é aplicada na página de relatórios.

## Configuração Recomendada

Para o melhor funcionamento do sistema de usuários online:

### 1. Habilitar `recordActiveDate`

No arquivo `app/Config/Auth.php`:

```php
public bool $recordActiveDate = true;
```

### 2. Configurar Filtro de Sessão

No arquivo `app/Config/Filters.php`:

```php
public array $globals = [
    'before' => [
        'session' => ['except' => ['login*', 'register', 'auth/a/*', 'logout']],
    ],
];
```

### 3. Verificar Migração da Tabela Users

Certifique-se de que a tabela `users` tem o campo `last_active`:

```sql
ALTER TABLE users ADD COLUMN last_active DATETIME NULL;
```

## Verificação do Sistema

Para verificar se o sistema está funcionando corretamente:

1. **Verificar configurações:**
   - `recordActiveDate = true` em `Auth.php`
   - Filtro `session` aplicado às rotas administrativas

2. **Testar funcionamento:**
   - Fazer login no sistema
   - Navegar pelas páginas da área administrativa
   - Verificar se o campo `last_active` está sendo atualizado na tabela `users`

3. **Monitorar sessões:**
   - Verificar arquivos de sessão em `writable/session/`
   - Confirmar que sessões de usuários logados são criadas

## Troubleshooting

### Problema: Usuários online sempre mostra 0

**Possíveis causas e soluções:**

1. **Campo `last_active` não existe:**
   - Adicionar o campo à tabela `users`
   - Executar migração apropriada

2. **`recordActiveDate` desabilitado:**
   - Verificar `app/Config/Auth.php`
   - Definir `$recordActiveDate = true`

3. **Filtro `session` não aplicado:**
   - Verificar `app/Config/Filters.php`
   - Garantir que o filtro está aplicado às rotas administrativas

4. **Permissões de arquivo de sessão:**
   - Verificar permissões da pasta `writable/session/`
   - Garantir que o servidor web pode ler/escrever

### Problema: Usuários online muito alto

**Possível causa:**
- Sistema usando fallback (usuários ativos) em vez de detecção real
- Verificar se as abordagens 1 e 2 estão funcionando corretamente

## Melhorias Futuras

1. **Cache de contagem:**
   - Implementar cache para evitar recálculo a cada carregamento
   - Usar Redis ou Memcached para performance

2. **WebSockets:**
   - Implementar detecção em tempo real via WebSockets
   - Mostrar atividade de usuários em tempo real

3. **Detalhamento por usuário:**
   - Mostrar lista de usuários online
   - Exibir última atividade individual

4. **Configuração de timeout:**
   - Permitir configurar o tempo de "online" (atualmente 2 horas)
   - Diferentes timeouts para diferentes tipos de usuário
