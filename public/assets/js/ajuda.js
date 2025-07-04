/**
 * JavaScript avançado para Central de Ajuda
 * Funcionalidades interativas e melhorias de UX
 */

class AjudaManager {
    constructor() {
        this.searchHistory = this.getSearchHistory();
        this.init();
    }

    init() {
        this.setupSearchAutocomplete();
        this.setupKeyboardShortcuts();
        this.setupAnalytics();
        this.setupPrintFunctionality();
        this.setupBookmarks();
    }

    // Configurar autocomplete na busca
    setupSearchAutocomplete() {
        const searchInput = document.getElementById('helpSearch');
        if (!searchInput) return;

        const suggestions = [
            'como fazer login',
            'cadastrar paciente',
            'esqueci minha senha',
            'sistema lento',
            'prescrever medicamento',
            'gerar relatório',
            'backup dados',
            'triagem paciente',
            'controle estoque',
            'imprimir receita',
            'consulta médica',
            'prontuário eletrônico'
        ];

        // Criar container para sugestões
        const suggestionsContainer = document.createElement('div');
        suggestionsContainer.className = 'search-suggestions';
        suggestionsContainer.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        `;

        searchInput.parentElement.style.position = 'relative';
        searchInput.parentElement.appendChild(suggestionsContainer);

        searchInput.addEventListener('input', (e) => {
            const value = e.target.value.toLowerCase();
            if (value.length < 2) {
                suggestionsContainer.style.display = 'none';
                return;
            }

            const filtered = suggestions.filter(item => 
                item.toLowerCase().includes(value)
            );

            if (filtered.length === 0) {
                suggestionsContainer.style.display = 'none';
                return;
            }

            suggestionsContainer.innerHTML = filtered.map(item => 
                `<div class="suggestion-item" style="padding: 10px; cursor: pointer; border-bottom: 1px solid #eee;">
                    <i class="bi bi-search me-2"></i>${item}
                </div>`
            ).join('');

            // Adicionar eventos de clique
            suggestionsContainer.querySelectorAll('.suggestion-item').forEach(item => {
                item.addEventListener('click', () => {
                    searchInput.value = item.textContent.trim();
                    suggestionsContainer.style.display = 'none';
                    searchHelp();
                });
            });

            suggestionsContainer.style.display = 'block';
        });

        // Esconder sugestões ao clicar fora
        document.addEventListener('click', (e) => {
            if (!searchInput.parentElement.contains(e.target)) {
                suggestionsContainer.style.display = 'none';
            }
        });
    }

    // Configurar atalhos de teclado
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Ctrl+K ou Cmd+K para focar na busca
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.getElementById('helpSearch');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }

            // Esc para fechar modais e sugestões
            if (e.key === 'Escape') {
                const suggestions = document.querySelector('.search-suggestions');
                if (suggestions) {
                    suggestions.style.display = 'none';
                }
            }
        });
    }

    // Analytics simples para rastrear uso
    setupAnalytics() {
        // Rastrear categorias mais acessadas
        document.querySelectorAll('.help-category-card').forEach(card => {
            card.addEventListener('click', () => {
                const category = card.querySelector('h5').textContent;
                this.trackEvent('category_click', category);
            });
        });

        // Rastrear artigos mais acessados
        document.querySelectorAll('.help-article').forEach(article => {
            article.addEventListener('click', () => {
                const title = article.querySelector('h5').textContent;
                this.trackEvent('article_click', title);
            });
        });

        // Rastrear buscas
        const searchInput = document.getElementById('helpSearch');
        if (searchInput) {
            searchInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    this.trackEvent('search', e.target.value);
                    this.addToSearchHistory(e.target.value);
                }
            });
        }
    }

    trackEvent(action, label) {
        // Armazenar no localStorage para analytics simples
        const analytics = JSON.parse(localStorage.getItem('ajuda_analytics') || '{}');
        const key = `${action}_${label}`;
        analytics[key] = (analytics[key] || 0) + 1;
        localStorage.setItem('ajuda_analytics', JSON.stringify(analytics));
    }

    getSearchHistory() {
        return JSON.parse(localStorage.getItem('ajuda_search_history') || '[]');
    }

    addToSearchHistory(query) {
        if (!query.trim()) return;
        
        let history = this.getSearchHistory();
        history = history.filter(item => item !== query); // Remove duplicatas
        history.unshift(query); // Adiciona no início
        history = history.slice(0, 10); // Mantém apenas 10 itens
        
        localStorage.setItem('ajuda_search_history', JSON.stringify(history));
        this.searchHistory = history;
    }

    // Configurar funcionalidade de impressão
    setupPrintFunctionality() {
        // Adicionar botão de impressão nos artigos
        const articleContent = document.querySelector('.article-content');
        if (articleContent) {
            const printBtn = document.createElement('button');
            printBtn.className = 'btn btn-outline-secondary btn-sm';
            printBtn.innerHTML = '<i class="bi bi-printer"></i> Imprimir Artigo';
            printBtn.onclick = () => this.printArticle();
            
            const articleHeader = document.querySelector('.header');
            if (articleHeader) {
                articleHeader.appendChild(printBtn);
            }
        }
    }

    printArticle() {
        const title = document.querySelector('.header h1').textContent;
        const content = document.querySelector('.article-content').innerHTML;
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>${title} - SisPAM Ajuda</title>
                <style>
                    body { font-family: Arial, sans-serif; padding: 20px; }
                    h1, h2, h3 { color: #333; }
                    .alert { padding: 15px; margin: 10px 0; border-radius: 4px; }
                    .alert-info { background-color: #d1ecf1; border: 1px solid #bee5eb; }
                    .alert-warning { background-color: #fff3cd; border: 1px solid #ffeaa7; }
                    .alert-danger { background-color: #f8d7da; border: 1px solid #f5c6cb; }
                    ol, ul { padding-left: 20px; }
                    li { margin-bottom: 5px; }
                </style>
            </head>
            <body>
                <h1>${title}</h1>
                ${content}
                <hr>
                <p><small>Impresso em ${new Date().toLocaleString()} - SisPAM Central de Ajuda</small></p>
            </body>
            </html>
        `);
        
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();
    }

    // Sistema de favoritos/bookmarks
    setupBookmarks() {
        document.querySelectorAll('.help-article').forEach(article => {
            const bookmarkBtn = document.createElement('button');
            bookmarkBtn.className = 'btn btn-link btn-sm bookmark-btn';
            bookmarkBtn.innerHTML = '<i class="bi bi-bookmark"></i>';
            bookmarkBtn.style.cssText = 'position: absolute; top: 10px; right: 10px; z-index: 10;';
            bookmarkBtn.title = 'Adicionar aos favoritos';
            
            article.style.position = 'relative';
            article.appendChild(bookmarkBtn);
            
            // Verificar se já está nos favoritos
            const articleTitle = article.querySelector('h5').textContent;
            if (this.isBookmarked(articleTitle)) {
                bookmarkBtn.innerHTML = '<i class="bi bi-bookmark-fill"></i>';
                bookmarkBtn.classList.add('text-warning');
            }
            
            bookmarkBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleBookmark(articleTitle, bookmarkBtn);
            });
        });
    }

    isBookmarked(title) {
        const bookmarks = JSON.parse(localStorage.getItem('ajuda_bookmarks') || '[]');
        return bookmarks.includes(title);
    }

    toggleBookmark(title, button) {
        let bookmarks = JSON.parse(localStorage.getItem('ajuda_bookmarks') || '[]');
        
        if (this.isBookmarked(title)) {
            bookmarks = bookmarks.filter(item => item !== title);
            button.innerHTML = '<i class="bi bi-bookmark"></i>';
            button.classList.remove('text-warning');
            this.showToast('Removido dos favoritos', 'info');
        } else {
            bookmarks.push(title);
            button.innerHTML = '<i class="bi bi-bookmark-fill"></i>';
            button.classList.add('text-warning');
            this.showToast('Adicionado aos favoritos', 'success');
        }
        
        localStorage.setItem('ajuda_bookmarks', JSON.stringify(bookmarks));
    }

    showToast(message, type = 'info') {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'info'} border-0`;
        toast.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
        toast.setAttribute('role', 'alert');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    // Método para obter estatísticas de uso
    getUsageStats() {
        return JSON.parse(localStorage.getItem('ajuda_analytics') || '{}');
    }

    // Método para exportar dados do usuário
    exportUserData() {
        const data = {
            searchHistory: this.getSearchHistory(),
            bookmarks: JSON.parse(localStorage.getItem('ajuda_bookmarks') || '[]'),
            analytics: this.getUsageStats(),
            exportDate: new Date().toISOString()
        };
        
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'ajuda-sisupam-dados.json';
        a.click();
        URL.revokeObjectURL(url);
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.ajudaManager = new AjudaManager();
    
    // Adicionar indicador de atalho de teclado
    const searchInput = document.getElementById('helpSearch');
    if (searchInput) {
        searchInput.placeholder = 'Digite sua dúvida... (Ctrl+K)';
    }
    
    // Adicionar botão de exportar dados (para desenvolvimento/debug)
    if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
        const debugBtn = document.createElement('button');
        debugBtn.className = 'btn btn-secondary btn-sm position-fixed';
        debugBtn.style.cssText = 'bottom: 40px; right: 40px; z-index: 9999;';
        debugBtn.innerHTML = '<i class="bi bi-download"></i> Debug';
        debugBtn.onclick = () => window.ajudaManager.exportUserData();
        document.body.appendChild(debugBtn);
    }
});

// Exportar para uso global
window.AjudaManager = AjudaManager;
