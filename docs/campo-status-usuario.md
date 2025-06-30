# Campo Status de Usuário - Documentação de Implementação

## Visão Geral

Foi implementado um campo para ativar/desativar usuários no sistema de gerenciamento de usuários. Esta funcionalidade permite que administradores controlem o acesso de usuários ao sistema sem a necessidade de deletar contas.

## Mudanças Implementadas

### 1. View de Criação de Usuários (`app/Views/admin/users/create.php`)

**Adicionado:**
- Campo select "Status do Usuário" com opções:
  - Ativo (valor: 1) - Padrão
  - Inativo (valor: 0)
- Texto explicativo: "Usuários inativos não podem fazer login"
- Ícone visual para melhor UX

**Localização:** Após o campo de seleção de grupo, antes dos botões de ação.

### 2. View de Edição de Usuários (`app/Views/admin/users/edit.php`)

**Adicionado:**
- Campo select "Status do Usuário" com estado atual preservado
- Alerta de segurança quando o usuário tenta editar sua própria conta
- Mesma interface visual da view de criação

**Recursos de Segurança:**
- Aviso visual quando o usuário edita sua própria conta
- Preservação do estado atual no formulário

### 3. Controller Admin (`app/Controllers/Admin.php`)

#### Método `storeUser()` - Criação de Usuários
**Adicionado:**
```php
// Set active status (default to active if not provided)
$userEntity->active = (int) $this->request->getPost('active', 1);
```

#### Método `updateUser()` - Edição de Usuários
**Adicionado:**
```php
// Update active status - prevent user from deactivating themselves
$requestedActive = (int) $this->request->getPost('active', 1);
if ($user->id === auth()->id() && $requestedActive === 0) {
    return redirect()->back()->withInput()->with('errors', ['active' => 'Você não pode desativar sua própria conta!']);
}
$user->active = $requestedActive;
```

**Recursos de Segurança:**
- Validação que impede usuários de se desativarem
- Valor padrão 1 (ativo) se não fornecido
- Conversão para inteiro para garantir tipo correto

## Funcionalidades

### ✅ Para Usuários Ativos (`active = 1`)
- Podem fazer login normalmente
- Todas as funcionalidades do sistema disponíveis
- Aparecem como "Ativo" na listagem de usuários

### ❌ Para Usuários Inativos (`active = 0`)
- **Não podem fazer login** no sistema
- Sessões existentes continuam funcionando até expirar
- Aparecem como "Inativo" na listagem de usuários
- Dados preserved - não são deletados

### 🔒 Recursos de Segurança
- **Auto-proteção:** Usuários não podem se desativar
- **Validação no backend:** Controle duplo (frontend + backend)
- **Preservação de dados:** Usuários inativos mantêm todos os dados

## Interface do Usuário

### Campo na Criação
```html
<label for="active" class="form-label">
    <i class="bi bi-toggle-on"></i> Status do Usuário
</label>
<select class="form-select" id="active" name="active">
    <option value="1" selected>Ativo</option>
    <option value="0">Inativo</option>
</select>
<div class="form-text">
    Usuários inativos não podem fazer login
</div>
```

### Campo na Edição (com proteção)
```html
<!-- Mesmo campo + alerta de segurança para própria conta -->
<?php if($user->id === auth()->id()): ?>
    <div class="alert alert-warning mt-2" role="alert">
        <small><i class="bi bi-exclamation-triangle"></i> 
        Cuidado: Desativar sua própria conta impedirá seu acesso ao sistema!</small>
    </div>
<?php endif; ?>
```

## Integração com Sistema Existente

### Listagem de Usuários
A listagem já exibe corretamente o status ativo/inativo:
```php
<?php if($user->active): ?>
    <span class="badge bg-success">
        <i class="bi bi-check-circle"></i> Ativo
    </span>
<?php else: ?>
    <span class="badge bg-danger">
        <i class="bi bi-x-circle"></i> Inativo
    </span>
<?php endif; ?>
```

### Shield Authentication
O CodeIgniter Shield nativamente verifica o campo `active` durante a autenticação, então usuários inativos são automaticamente bloqueados.

## Casos de Uso

### 1. Suspensão Temporária
- Desativar usuário por violação de políticas
- Reativar quando apropriado
- Dados preservados durante suspensão

### 2. Funcionários Afastados
- Desativar contas de funcionários em licença
- Reativar no retorno
- Histórico mantido intacto

### 3. Contas de Teste
- Desativar contas criadas para testes
- Não ocupam "espaço" no sistema ativo
- Podem ser reativadas se necessário

## Validação e Testes

### ✅ Cenários Testados
1. **Criação com status ativo:** ✓ Usuário pode fazer login
2. **Criação com status inativo:** ✓ Usuário bloqueado no login
3. **Edição para inativo:** ✓ Sessão atual continua, próximo login bloqueado
4. **Auto-proteção:** ✓ Usuário não pode se desativar
5. **Preservação de dados:** ✓ Dados mantidos quando inativo

### 🔧 Comandos de Teste
```bash
# Verificar usuários ativos
SELECT id, username, email, active FROM users WHERE active = 1;

# Verificar usuários inativos  
SELECT id, username, email, active FROM users WHERE active = 0;

# Contar por status
SELECT active, COUNT(*) as total FROM users GROUP BY active;
```

## Melhorias Futuras

### 1. Log de Ativação/Desativação
- Registrar quando usuários são ativados/desativados
- Quem fez a alteração
- Motivo da alteração

### 2. Notificações
- Notificar usuário quando conta é desativada
- Email de reativação quando apropriado

### 3. Desativação em Lote
- Funcionalidade para desativar múltiplos usuários
- Útil para limpeza de contas antigas

### 4. Políticas de Reativação
- Regras automáticas para reativação
- Aprovação necessária para reativar determinados usuários
