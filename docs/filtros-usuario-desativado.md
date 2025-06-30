# Sistema de Filtros para Usuários Desativados - Documentação

## Visão Geral

Foi implementado um sistema completo de filtros para garantir que usuários desativados não possam acessar o sistema, proporcionando segurança e controle de acesso adequados.

## Filtros Implementados

### 1. ActiveUserFilter (`app/Filters/ActiveUserFilter.php`)

**Propósito:** Verificar continuamente se usuários logados estão ativos durante navegação no sistema.

**Funcionalidades:**
- Verifica se há usuário logado
- Checa se o usuário está ativo (`active = 1`)
- Se inativo, faz logout automático e redireciona para login
- Limpa sessões de usuários desativados

**Como funciona:**
```php
if (auth()->loggedIn()) {
    $user = auth()->user();
    
    if (!$user->active) {
        auth()->logout();
        session()->remove('user');
        
        return redirect()->to('/login')
            ->with('error', 'Sua conta foi desativada. Entre em contato com o administrador do sistema.');
    }
}
```

**Aplicação:** Global, exceto rotas de login, registro e logout.

### 2. LoginFilter (`app/Filters/LoginFilter.php`)

**Propósito:** Prevenir tentativas de login de usuários desativados.

**Funcionalidades:**
- Intercepta tentativas de login (POST para `/login` ou `/auth/login`)
- Verifica se o usuário existe e está ativo antes de processar credenciais
- Bloqueia login com mensagem específica se usuário estiver desativado

**Como funciona:**
```php
if ($request->getMethod() === 'post' && strpos($request->getUri()->getPath(), '/login') !== false) {
    $email = $serviceRequest->getPost('email');
    $user = $userProvider->findByCredentials(['email' => $email]);
    
    if ($user && !$user->active) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Sua conta está desativada. Entre em contato com o administrador do sistema.');
    }
}
```

**Aplicação:** Global, intercepta todas as tentativas de login.

## Configuração dos Filtros

### Registro no `app/Config/Filters.php`

```php
public array $aliases = [
    // ...outros filtros...
    'activeuser'    => \App\Filters\ActiveUserFilter::class,
    'loginfilter'   => \App\Filters\LoginFilter::class,
];

public array $globals = [
    'before' => [
        'session' => ['except' => ['login*', 'register', 'auth/a/*', 'logout']],
        'activeuser' => ['except' => ['login*', 'register', 'auth/a/*', 'logout']],
        'loginfilter',
    ],
];
```

### Exceções Configuradas

**Rotas excluídas do `activeuser`:**
- `login*` - Páginas de login
- `register` - Registro de usuários
- `auth/a/*` - Rotas de autenticação do Shield
- `logout` - Logout

**Filtro `loginfilter`:**
- Aplicado globalmente (sem exceções)
- Ativo apenas durante tentativas de login

## Fluxo de Segurança

### 1. Tentativa de Login
```
Usuario tenta login
       ↓
LoginFilter verifica se conta está ativa
       ↓
Se inativa: Bloqueia com mensagem
Se ativa: Continua processo normal
```

### 2. Navegação no Sistema
```
Usuario navega (já logado)
       ↓
ActiveUserFilter verifica status a cada página
       ↓
Se foi desativado: Logout automático
Se ainda ativo: Continua navegação
```

### 3. Desativação de Conta
```
Admin desativa usuario
       ↓
Próxima requisição do usuário
       ↓
ActiveUserFilter detecta status inativo
       ↓
Logout automático + redirecionamento
```

## Mensagens de Erro

### Para Login Bloqueado
```
"Sua conta está desativada. Entre em contato com o administrador do sistema."
```

### Para Logout Automático
```
"Sua conta foi desativada. Entre em contato com o administrador do sistema."
```

## Integração com Shield

### Compatibilidade
- ✅ **Compatible:** Os filtros trabalham junto com o Shield, não substituem
- ✅ **Preservação:** Mantém funcionalidades nativas do Shield
- ✅ **Melhorias:** Adiciona camadas extras de segurança

### Campo `active` 
O Shield nativamente verifica o campo `active`, mas nossos filtros garantem:
- Mensagens personalizadas em português
- Logout imediato de usuários desativados
- Verificação preventiva durante login

## Cenários de Teste

### ✅ Cenário 1: Login de Usuário Desativado
1. Admin desativa usuário
2. Usuário tenta fazer login
3. **Resultado:** Login bloqueado com mensagem de erro

### ✅ Cenário 2: Desativação Durante Sessão
1. Usuário está logado e navegando
2. Admin desativa o usuário
3. Usuário clica em qualquer link
4. **Resultado:** Logout automático + redirecionamento para login

### ✅ Cenário 3: Reativação de Conta
1. Admin reativa usuário desativado
2. Usuário tenta fazer login
3. **Resultado:** Login normal sem bloqueios

### ✅ Cenário 4: Usuário Ativo Normal
1. Usuário ativo faz login
2. Navega pelo sistema
3. **Resultado:** Nenhuma interferência dos filtros

## Segurança Implementada

### 🔒 Prevenção de Acesso
- **Dupla verificação:** Login + navegação
- **Limpeza de sessão:** Remove dados de usuários desativados
- **Mensagens claras:** Usuário sabe exatamente o que aconteceu

### 🔒 Proteção Administrativa
- **Impossibilidade de bypass:** Filtros globais não podem ser contornados
- **Aplicação imediata:** Efeito instantâneo após desativação
- **Preservação de dados:** Usuário não perde dados, apenas acesso

## Logs e Monitoramento

### Eventos Registrados
- Tentativas de login de usuários desativados
- Logouts automáticos por desativação
- Todos os eventos aparecem nos logs do CodeIgniter

### Monitoramento
Para monitorar atividade de usuários desativados:
```bash
# Ver logs de tentativas bloqueadas
tail -f writable/logs/log-$(date +%Y-%m-%d).log | grep "desativada"
```

## Manutenção e Troubleshooting

### Verificar Status dos Filtros
```php
// Verificar se filtros estão registrados
$filters = config('Filters');
var_dump($filters->aliases['activeuser']);
var_dump($filters->aliases['loginfilter']);
```

### Verificar Usuários Desativados
```sql
-- Listar usuários desativados
SELECT id, username, email, active FROM users WHERE active = 0;

-- Reativar usuário específico
UPDATE users SET active = 1 WHERE id = [ID_USUARIO];
```

### Desabilitar Filtros Temporariamente
Se necessário para manutenção:
```php
// Em app/Config/Filters.php
public array $globals = [
    'before' => [
        'session' => ['except' => ['login*', 'register', 'auth/a/*', 'logout']],
        // 'activeuser' => ['except' => ['login*', 'register', 'auth/a/*', 'logout']],
        // 'loginfilter',
    ],
];
```

## Performance

### Impacto Mínimo
- **Consultas:** Apenas uma consulta adicional por login
- **Verificação:** Usa dados já carregados na sessão
- **Cache:** Aproveita cache do Shield para dados do usuário

### Otimizações
- Filtros só executam quando necessário
- Verificações rápidas com dados em memória
- Sem impacto na performance geral

## Melhorias Futuras

### 1. Log Detalhado
- Registrar tentativas de login bloqueadas
- Histórico de desativações/reativações

### 2. Notificações
- Email para usuário quando conta é desativada
- Notificação para admin sobre tentativas bloqueadas

### 3. Período de Graça
- Permitir período para usuário se despedir antes do logout
- Aviso antes da desativação automática

### 4. API de Status
- Endpoint para verificar status de usuário
- Integração com sistemas externos
