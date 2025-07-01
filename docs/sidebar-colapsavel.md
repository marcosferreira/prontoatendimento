# Sistema de Sidebar Colapsável

## Visão Geral

O sistema de sidebar foi atualizado para permitir que o botão de toggle funcione em **todas as resoluções de tela**, não apenas em dispositivos móveis. Agora os usuários podem colapsar a sidebar tanto em desktop quanto em mobile para ter mais espaço na tela.

## Funcionalidades Implementadas

### 1. **Toggle Universal**
- Botão de toggle visível em todas as resoluções
- Comportamento diferente entre mobile e desktop
- Estado persistente usando localStorage

### 2. **Modo Colapsado (Desktop)**
- Sidebar reduzida para apenas ícones (40px de largura)
- Tooltips aparecem ao passar o mouse sobre os ícones
- Transições suaves entre estados expandido/colapsado
- Ajuste automático do conteúdo principal

### 3. **Modo Mobile**
- Comportamento overlay tradicional
- Fecha automaticamente ao clicar fora da sidebar
- Não interfere com o estado desktop

### 4. **Persistência de Estado**
- Estado da sidebar salvo no localStorage
- Restaurado automaticamente ao carregar a página
- Estados separados para painel admin e usuário

## Arquivos Modificados

### CSS
- `public/assets/css/main.css` - Estilos para sidebar principal
- `public/assets/css/admin.css` - Estilos para painel admin

### PHP Views
- `app/Views/layout/base.php` - JavaScript principal
- `app/Views/admin/layout/base.php` - JavaScript do admin
- `app/Views/components/sidebar.php` - Sidebar principal
- `app/Views/admin/components/sidebar.php` - Sidebar do admin
- `app/Views/components/topbar.php` - Topbar principal
- `app/Views/admin/components/topbar.php` - Topbar do admin

### JavaScript
- `public/assets/js/sidebar.js` - Classe avançada (opcional)

## Uso

### Botão de Toggle
O botão de hambúrguer (☰) agora está sempre visível no canto superior esquerdo:
- **Desktop**: Colapsa/expande a sidebar
- **Mobile**: Abre/fecha a sidebar em overlay

### Atalhos de Teclado
- `Ctrl/Cmd + B`: Toggle da sidebar
- `ESC`: Fecha sidebar no mobile

### Estados da Sidebar

#### Expandida (Padrão)
```css
.sidebar {
    width: 280px;
}
```

#### Colapsada (Desktop)
```css
.sidebar.sidebar-mini {
    width: 40px;
}
```

#### Mobile Aberta
```css
.sidebar.mobile-open {
    transform: translateX(0);
}
```

## Classes CSS Principais

### Sidebar Principal
- `.sidebar` - Container da sidebar
- `.sidebar-mini` - Estado colapsado
- `.sidebar-collapsed` - Aplicado ao body quando colapsada
- `.mobile-open` - Estado mobile aberto

### Sidebar Admin
- `.admin-sidebar` - Container da sidebar admin
- `.admin-sidebar-mini` - Estado colapsado
- `.admin-sidebar-collapsed` - Aplicado ao body quando colapsada

## Customização

### Alterar Largura da Sidebar Colapsada
```css
.sidebar.sidebar-mini {
    width: 60px; /* Altere conforme necessário */
}
```

### Modificar Breakpoint Mobile
```javascript
// No arquivo sidebar.js
const SidebarConfig = {
    breakpoint: 1024, // Altere para tablet também
    // ...
};
```

### Desabilitar Tooltips
```css
.sidebar.sidebar-mini .nav-item:hover::after,
.sidebar.sidebar-mini .nav-item:hover::before {
    display: none;
}
```

## API JavaScript

Se estiver usando o arquivo `sidebar.js`, você tem acesso a:

```javascript
// Expandir sidebar
window.sidebarManager.expand();

// Colapsar sidebar
window.sidebarManager.collapse();

// Verificar estado
const state = window.sidebarManager.getState();
console.log(state.collapsed); // true/false

// Escutar eventos
document.addEventListener('sidebarToggle', (e) => {
    console.log('Sidebar toggled:', e.detail.collapsed);
});
```

## Compatibilidade

- ✅ Chrome 60+
- ✅ Firefox 55+
- ✅ Safari 12+
- ✅ Edge 79+
- ✅ Mobile browsers

## Melhorias Futuras

1. **Gestos Touch**: Swipe para abrir/fechar no mobile
2. **Auto-collapse**: Colapsar automaticamente em telas pequenas
3. **Temas**: Diferentes estilos de sidebar colapsada
4. **Favoritos**: Sistema de marcação de itens frequentes
5. **Busca**: Campo de busca na sidebar colapsada

## Troubleshooting

### Sidebar não colapsa
- Verifique se o JavaScript está carregando
- Confirme que as classes CSS foram aplicadas
- Verifique o console para erros

### Estado não persiste
- Verifique se localStorage está habilitado
- Confirme as chaves de storage no JavaScript

### Tooltips não aparecem
- Verifique se os atributos `data-tooltip` estão definidos
- Confirme se o CSS dos tooltips foi aplicado

### Layout quebrado
- Verifique as transições CSS
- Confirme os valores de margin-left nos breakpoints
