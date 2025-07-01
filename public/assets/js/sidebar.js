/**
 * Sistema de Sidebar Responsiva
 * Funcionalidades avançadas para controle da sidebar
 */

// Configurações globais
const SidebarConfig = {
    breakpoint: 768,
    storageKey: 'sidebar-collapsed',
    adminStorageKey: 'admin-sidebar-collapsed',
    animationDuration: 300
};

// Classe principal para gerenciar a sidebar
class SidebarManager {
    constructor(options = {}) {
        this.config = {...SidebarConfig, ...options};
        this.isAdmin = document.querySelector('.admin-sidebar') !== null;
        this.sidebar = this.isAdmin 
            ? document.querySelector('.admin-sidebar')
            : document.querySelector('.sidebar');
        this.toggle = document.querySelector('.mobile-menu-toggle');
        this.body = document.body;
        
        this.init();
    }
    
    init() {
        if (!this.sidebar || !this.toggle) return;
        
        this.bindEvents();
        this.restoreState();
        this.handleResize();
    }
    
    bindEvents() {
        // Toggle button click
        this.toggle.addEventListener('click', () => this.toggleSidebar());
        
        // Window resize
        window.addEventListener('resize', () => this.handleResize());
        
        // Outside click (mobile only)
        document.addEventListener('click', (e) => this.handleOutsideClick(e));
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyboard(e));
    }
    
    toggleSidebar() {
        if (this.isMobile()) {
            this.toggleMobile();
        } else {
            this.toggleDesktop();
        }
    }
    
    toggleMobile() {
        this.sidebar.classList.toggle('mobile-open');
    }
    
    toggleDesktop() {
        const isCollapsed = this.isCollapsed();
        
        if (this.isAdmin) {
            this.sidebar.classList.toggle('admin-sidebar-mini');
            this.body.classList.toggle('admin-sidebar-collapsed');
        } else {
            this.sidebar.classList.toggle('sidebar-mini');
            this.body.classList.toggle('sidebar-collapsed');
        }
        
        // Save state
        this.saveState(!isCollapsed);
        
        // Trigger custom event
        this.triggerEvent('sidebarToggle', { collapsed: !isCollapsed });
    }
    
    isCollapsed() {
        if (this.isAdmin) {
            return this.body.classList.contains('admin-sidebar-collapsed');
        }
        return this.body.classList.contains('sidebar-collapsed');
    }
    
    isMobile() {
        return window.innerWidth <= this.config.breakpoint;
    }
    
    restoreState() {
        if (this.isMobile()) return;
        
        const storageKey = this.isAdmin ? this.config.adminStorageKey : this.config.storageKey;
        const isCollapsed = localStorage.getItem(storageKey) === 'true';
        
        if (isCollapsed) {
            if (this.isAdmin) {
                this.sidebar.classList.add('admin-sidebar-mini');
                this.body.classList.add('admin-sidebar-collapsed');
            } else {
                this.sidebar.classList.add('sidebar-mini');
                this.body.classList.add('sidebar-collapsed');
            }
        }
    }
    
    saveState(collapsed) {
        const storageKey = this.isAdmin ? this.config.adminStorageKey : this.config.storageKey;
        
        if (collapsed) {
            localStorage.setItem(storageKey, 'true');
        } else {
            localStorage.removeItem(storageKey);
        }
    }
    
    handleResize() {
        if (this.isMobile()) {
            // Remove desktop classes
            if (this.isAdmin) {
                this.sidebar.classList.remove('admin-sidebar-mini');
                this.body.classList.remove('admin-sidebar-collapsed');
            } else {
                this.sidebar.classList.remove('sidebar-mini');
                this.body.classList.remove('sidebar-collapsed');
            }
        } else {
            // Remove mobile classes and restore saved state
            this.sidebar.classList.remove('mobile-open');
            this.restoreState();
        }
        
        this.triggerEvent('sidebarResize', { isMobile: this.isMobile() });
    }
    
    handleOutsideClick(e) {
        if (!this.isMobile()) return;
        
        if (!this.sidebar.contains(e.target) && !this.toggle.contains(e.target)) {
            this.sidebar.classList.remove('mobile-open');
        }
    }
    
    handleKeyboard(e) {
        // Ctrl/Cmd + B para toggle da sidebar
        if ((e.ctrlKey || e.metaKey) && e.key === 'b') {
            e.preventDefault();
            this.toggleSidebar();
        }
        
        // ESC para fechar sidebar mobile
        if (e.key === 'Escape' && this.isMobile()) {
            this.sidebar.classList.remove('mobile-open');
        }
    }
    
    triggerEvent(eventName, detail = {}) {
        const event = new CustomEvent(eventName, { detail });
        document.dispatchEvent(event);
    }
    
    // API pública
    expand() {
        if (this.isCollapsed()) {
            this.toggleDesktop();
        }
    }
    
    collapse() {
        if (!this.isCollapsed()) {
            this.toggleDesktop();
        }
    }
    
    getState() {
        return {
            collapsed: this.isCollapsed(),
            mobile: this.isMobile(),
            admin: this.isAdmin
        };
    }
}

// Auto-inicialização quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    // Verificar se já existe uma instância
    if (window.sidebarManager) return;
    
    // Criar instância global
    window.sidebarManager = new SidebarManager();
    
    // Eventos personalizados disponíveis:
    // - sidebarToggle: Disparado quando a sidebar é expandida/colapsada
    // - sidebarResize: Disparado quando a janela é redimensionada
    
    // Exemplo de uso:
    // document.addEventListener('sidebarToggle', (e) => {
    //     console.log('Sidebar toggled:', e.detail.collapsed);
    // });
});

// Exportar para uso em módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = SidebarManager;
}
