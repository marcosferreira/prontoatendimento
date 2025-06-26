# Área Administrativa - Sistema de Pronto Atendimento

Esta documentação descreve a área administrativa do sistema, destinada exclusivamente aos superadministradores.

## Visão Geral

A área administrativa foi desenvolvida com uma interface moderna e responsiva, permitindo o gerenciamento completo do sistema de pronto atendimento municipal.

## Acesso

### URL de Acesso
```
/admin
```

### Requisitos de Acesso
- Usuário autenticado
- Grupo: **superadmin**

## Funcionalidades

### 1. Dashboard Principal (`/admin`)
- Estatísticas gerais do sistema
- Contadores de usuários (total, ativos, superadmins)
- Ações rápidas
- Lista de usuários recentes
- Informações do sistema

### 2. Gerenciamento de Usuários (`/admin/users`)
- **Listar Usuários** (`/admin/users`): Visualização completa de todos os usuários
- **Criar Usuário** (`/admin/users/create`): Formulário para criação de novos usuários
- **Editar Usuário** (`/admin/users/edit/{id}`): Edição de dados de usuários existentes
- **Deletar Usuário** (`/admin/users/delete/{id}`): Remoção de usuários (com confirmação)

#### Recursos de Gerenciamento de Usuários:
- Busca e filtros
- Visualização de grupos e permissões
- Status de usuários (ativo/inativo)
- Último login
- Proteção contra auto-exclusão

### 3. Configurações do Sistema (`/admin/settings`)
- Informações do sistema (CodeIgniter, PHP, servidor)
- Configurações de banco de dados
- Configurações de segurança
- Gerenciamento de cache
- Ferramentas do sistema (backup, teste de email, manutenção)

### 4. Logs do Sistema (`/admin/logs`)
- Visualização de logs em tempo real
- Filtros por nível (error, warning, info, debug)
- Navegação por diferentes arquivos de log
- Estatísticas de logs
- Ações de limpeza e exportação

### 5. Relatórios (`/admin/reports`)
- Estatísticas de usuários
- Gráficos interativos (Chart.js)
- Distribuição por grupos
- Geração de relatórios em PDF
- Linha do tempo de atividades

## Estrutura de Arquivos

### Controllers
```
app/Controllers/Admin.php - Controller principal da área administrativa
```

### Views
```
app/Views/admin/
├── layout/
│   └── base.php                 # Layout base do admin
├── components/
│   ├── sidebar.php              # Barra lateral
│   └── topbar.php               # Barra superior
├── dashboard.php                # Dashboard principal
├── users/
│   ├── index.php                # Lista de usuários
│   ├── create.php               # Criar usuário
│   └── edit.php                 # Editar usuário
├── settings/
│   └── index.php                # Configurações
├── logs/
│   └── index.php                # Logs do sistema
└── reports/
    └── index.php                # Relatórios
```

### Filters
```
app/Filters/AdminFilter.php - Filtro de segurança para área admin
```

### CSS
```
public/assets/css/admin.css - Estilos específicos da área administrativa
```

## Rotas Administrativas

```php
// Dashboard
/admin
/admin/dashboard

// Usuários
/admin/users                     # Listar
/admin/users/create             # Criar (GET)
/admin/users/store              # Criar (POST)
/admin/users/edit/{id}          # Editar (GET)
/admin/users/update/{id}        # Editar (POST)
/admin/users/delete/{id}        # Deletar

// Configurações
/admin/settings

// Logs
/admin/logs

// Relatórios
/admin/reports
```

## Segurança

### Filtro de Autenticação
O `AdminFilter` garante que:
1. O usuário esteja autenticado
2. O usuário pertença ao grupo `superadmin`
3. Redirecionamento para login se não autenticado
4. Erro 404 se não for superadmin

### Grupos e Permissões
Baseado no sistema Shield do CodeIgniter:
- **superadmin**: Acesso total ao sistema
- **admin**: Acesso limitado
- **developer**: Acesso técnico
- **user**: Usuário comum
- **beta**: Acesso a recursos beta

## Comandos CLI

### Criar Superadmin
```bash
php spark admin:create-superadmin
```

Opções:
```bash
php spark admin:create-superadmin --username=admin --email=admin@exemplo.com --password=senha123
```

## Design e Interface

### Características do Design:
- **Responsivo**: Compatível com dispositivos móveis
- **Moderno**: Interface limpa e profissional
- **Acessível**: Boa usabilidade e navegação
- **Consistente**: Padrões visuais unificados

### Componentes da Interface:
- Sidebar com navegação por categorias
- Topbar com informações do usuário e tempo
- Cards estatísticos
- Tabelas responsivas
- Formulários validados
- Modais de confirmação (SweetAlert2)
- Gráficos interativos (Chart.js)

### Paleta de Cores:
- **Primary**: #0d6efd (azul)
- **Success**: #28a745 (verde)
- **Warning**: #ffc107 (amarelo)
- **Danger**: #dc3545 (vermelho)
- **Info**: #17a2b8 (ciano)
- **Secondary**: #6c757d (cinza)

## Tecnologias Utilizadas

### Frontend:
- **Bootstrap 5**: Framework CSS
- **Bootstrap Icons**: Ícones
- **SweetAlert2**: Modais e alertas
- **Chart.js**: Gráficos
- **Inter Font**: Tipografia

### Backend:
- **CodeIgniter 4**: Framework PHP
- **Shield**: Sistema de autenticação
- **MySQLi**: Banco de dados

## Manutenção e Extensões

### Adicionando Novas Páginas:
1. Criar método no `Admin.php`
2. Criar view correspondente
3. Adicionar rota em `Routes.php`
4. Atualizar sidebar se necessário

### Personalizando Estilos:
Edite o arquivo `/assets/css/admin.css` para modificar a aparência.

### Adicionando Permissões:
Modifique o arquivo `app/Config/AuthGroups.php` para incluir novas permissões.

## Considerações de Performance

- Paginação automática em listas grandes
- Cache de consultas frequentes
- Otimização de imagens e assets
- Lazy loading para componentes pesados

## Suporte e Troubleshooting

### Problemas Comuns:

1. **Erro 404 ao acessar /admin**
   - Verificar se o usuário é superadmin
   - Verificar se as rotas estão configuradas

2. **Permissões negadas**
   - Confirmar grupo do usuário
   - Verificar configuração do Shield

3. **Layouts quebrados**
   - Verificar paths dos assets CSS/JS
   - Confirmar estrutura de diretórios

### Logs de Debug:
Verifique os logs em `writable/logs/` para troubleshooting detalhado.

---

**Desenvolvido para o Sistema de Pronto Atendimento Municipal**
*Versão: 1.0*
*Data: 26/06/2025*
