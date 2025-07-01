# Views de Bairros - Sistema de Pronto Atendimento

## Visão Geral

As views de bairros implementam um sistema completo de gerenciamento de bairros, seguindo o mesmo padrão das views de pacientes, com funcionalidades CRUD (Create, Read, Update, Delete) e recursos avançados.

## Estrutura de Arquivos

```
app/Views/bairros/
├── index.php          # Lista de bairros
├── create.php         # Formulário de criação
├── edit.php           # Formulário de edição
├── show.php           # Detalhes do bairro
└── modal_view.php     # Visualização rápida (modal)
```

## Funcionalidades Implementadas

### 1. Listagem de Bairros (index.php)
- **Busca em tempo real** por nome ou área
- **Estatísticas** (total, cadastrados hoje, mês, ano)
- **Modal de criação rápida** diretamente na página
- **Visualização rápida** em modal (AJAX)
- **Exportação para CSV**
- **Responsividade** completa

**Componentes:**
- Cards de estatísticas
- Tabela com ações (visualizar, editar, excluir)
- Modal de criação
- Modal de visualização
- Sistema de busca

### 2. Criação de Bairros (create.php)
- **Formulário com validação** client-side e server-side
- **Pré-visualização em tempo real** dos dados
- **Validação AJAX** para nome duplicado
- **Cards informativos** com dicas
- **Breadcrumb** para navegação

**Campos:**
- Nome do Bairro (obrigatório)
- Área/Região (opcional)

### 3. Edição de Bairros (edit.php)
- **Formulário pré-preenchido** com dados atuais
- **Validação inteligente** (só valida se mudou)
- **Informações do registro** (datas, ID)
- **Botão de exclusão** integrado
- **Pré-visualização** atualizada

### 4. Visualização Detalhada (show.php)
- **Informações completas** do bairro
- **Lista de pacientes** vinculados
- **Estatísticas** (total de pacientes, idade média)
- **Controle de exclusão** baseado em dependências
- **Links de navegação** para pacientes

### 5. Modal de Visualização Rápida (modal_view.php)
- **Informações resumidas** do bairro
- **Lista compacta** de pacientes (máximo 5)
- **Links diretos** para ações
- **Design responsivo**

## Controller (Bairros.php)

### Métodos Implementados:

1. **index()** - Lista bairros com busca e estatísticas
2. **create()** - Exibe formulário de criação
3. **store()** - Salva novo bairro
4. **show($id)** - Detalhes de um bairro
5. **edit($id)** - Formulário de edição
6. **update($id)** - Atualiza bairro
7. **delete($id)** - Exclui bairro
8. **search()** - Busca AJAX
9. **modal($id)** - Modal de visualização
10. **validateNome()** - Validação AJAX de nome
11. **export()** - Exportação CSV

## Rotas Configuradas

```php
$routes->group('bairros', static function ($routes) {
    $routes->get('/', 'Bairros::index');
    $routes->get('create', 'Bairros::create');
    $routes->post('/', 'Bairros::store');
    $routes->get('(:num)', 'Bairros::show/$1');
    $routes->get('(:num)/edit', 'Bairros::edit/$1');
    $routes->put('(:num)', 'Bairros::update/$1');
    $routes->delete('(:num)', 'Bairros::delete/$1');
    
    // AJAX routes
    $routes->get('search', 'Bairros::search');
    $routes->get('(:num)/modal', 'Bairros::modal/$1');
    $routes->post('validateNome', 'Bairros::validateNome');
    
    // Export
    $routes->get('export', 'Bairros::export');
});
```

## Recursos JavaScript

### Funcionalidades:
- **Busca em tempo real** na tabela
- **Validação AJAX** de nomes duplicados
- **Pré-visualização** dinâmica nos formulários
- **Modais** para visualização e confirmação
- **Exportação** de dados
- **Prevenção de envios duplos**

### Principais Funções:
- `viewBairro(id)` - Abre modal de visualização
- `editBairro(id)` - Redireciona para edição
- `deleteBairro(id, nome)` - Confirma e exclui
- `showAll()` - Remove filtros de busca

## Estilos CSS

Arquivo específico: `/public/assets/css/bairros.css`

### Componentes Estilizados:
- **Stats Container** - Grid responsivo de estatísticas
- **Form Sections** - Seções organizadas do formulário
- **Info Items** - Itens de informação em lista
- **Empty States** - Estados vazios com ícones
- **Table Enhancements** - Melhorias na tabela
- **Modal Enhancements** - Estilos de modal
- **Responsive Design** - Adaptação móvel

## Integração com Pacientes

### Relacionamento:
- Bairros são **referenciados** em pacientes
- **Prevenção de exclusão** quando há pacientes vinculados
- **Listagem de pacientes** por bairro
- **Links diretos** para cadastro de pacientes

### Validações:
- Não permite excluir bairro com pacientes
- Mostra aviso no modal de exclusão
- Conta pacientes por bairro

## Navegação

### Sidebar:
Adicionado link "Bairros" na sidebar principal com ícone `bi-geo-alt`

### Breadcrumbs:
Implementado em todas as páginas para facilitar navegação

### Links Internos:
- Dashboard → Bairros
- Bairros → Detalhes
- Bairros → Edição
- Bairros → Pacientes

## Segurança

### Validações:
- **CSRF Protection** em todos os formulários
- **Validação server-side** completa
- **Sanitização** de dados de entrada
- **Verificação de dependências** antes da exclusão

### Controle de Acesso:
- Utiliza o mesmo sistema de autenticação
- Integrado com middleware de autenticação

## Responsividade

### Breakpoints:
- **Desktop** (>768px): Layout completo
- **Tablet** (≤768px): Ajustes de grid
- **Mobile** (≤576px): Layout em coluna única

### Adaptações Móveis:
- Grid de estatísticas em coluna única
- Action bar empilhada
- Tabelas com scroll horizontal
- Modais ajustados

## Uso

### Para Acessar:
1. Faça login no sistema
2. Clique em "Bairros" na sidebar
3. Use as funcionalidades disponíveis

### Para Testar:
1. Cadastre alguns bairros
2. Teste as buscas
3. Vincule pacientes aos bairros
4. Teste as validações de exclusão

## Manutenção

### Para Adicionar Campos:
1. Atualizar migration e model
2. Adicionar campos nos formulários
3. Atualizar validações
4. Ajustar views de exibição

### Para Personalizar Estilos:
1. Editar `/public/assets/css/bairros.css`
2. Seguir padrões existentes
3. Manter responsividade

## Observações Técnicas

- **Padrão MVC** seguido rigorosamente
- **Reusabilidade** de componentes
- **Código limpo** e documentado
- **Performance** otimizada com paginação e cache
- **UX/UI** consistente com o resto do sistema

As views de bairros estão prontas para uso em produção e seguem as melhores práticas do CodeIgniter 4.
