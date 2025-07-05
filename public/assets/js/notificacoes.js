/**
 * Sistema de Notificações BI - JavaScript
 * Gerencia interface interativa para monitoramento inteligente
 */
class NotificacoesManager {
    constructor() {
        this.baseUrl = window.location.origin;
        this.refreshInterval = null;
        this.charts = {};
        this.init();
    }

    init() {
        console.log('🔔 Sistema de Notificações BI inicializado');
        this.setupEventListeners();
        this.initializeCharts();
        this.startAutoRefresh();
        this.loadInitialData();
    }

    setupEventListeners() {
        // Filtros
        document.getElementById('btnFiltrar')?.addEventListener('click', () => this.aplicarFiltros());
        document.getElementById('btnLimparFiltros')?.addEventListener('click', () => this.limparFiltros());
        
        // Controles
        document.getElementById('btnAtualizarDados')?.addEventListener('click', () => this.atualizarDados());
        document.getElementById('btnExecutarAnalise')?.addEventListener('click', () => this.executarAnalise());
        
        // Modais
        document.getElementById('btnConfirmarResolver')?.addEventListener('click', () => this.confirmarResolucao());
        document.getElementById('btnConfirmarCancelar')?.addEventListener('click', () => this.confirmarCancelamento());

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => this.handleKeyboardShortcuts(e));

        // Notificação desktop
        this.requestNotificationPermission();
    }

    initializeCharts() {
        // Chart.js configuration
        Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#64748b';

        this.initSeveridadeChart();
        this.initTiposChart();
        this.initTendenciaChart();
    }

    initSeveridadeChart() {
        const canvas = document.getElementById('graficoSeveridade');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        this.charts.severidade = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Crítica', 'Alta', 'Média', 'Baixa'],
                datasets: [{
                    data: [0, 0, 0, 0],
                    backgroundColor: [
                        '#dc3545', // Crítica - Vermelho
                        '#fd7e14', // Alta - Laranja
                        '#ffc107', // Média - Amarelo
                        '#198754'  // Baixa - Verde
                    ],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                size: 11,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#374151',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                return `${context.label}: ${context.parsed} (${percentage}%)`;
                            }
                        }
                    }
                },
                animation: {
                    animateRotate: true,
                    duration: 1000
                }
            }
        });
    }

    initTiposChart() {
        const canvas = document.getElementById('graficoTipos');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        this.charts.tipos = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'Notificações',
                    data: [],
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(156, 163, 175, 0.2)'
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            font: {
                                size: 10
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#374151',
                        borderWidth: 1,
                        cornerRadius: 8
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    initTendenciaChart() {
        const canvas = document.getElementById('graficoTendencia');
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        this.charts.tendencia = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Notificações por Dia',
                    data: [],
                    borderColor: 'rgba(239, 68, 68, 1)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(239, 68, 68, 1)',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(156, 163, 175, 0.2)'
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 11
                            }
                        },
                        grid: {
                            color: 'rgba(156, 163, 175, 0.2)'
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(17, 24, 39, 0.95)',
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        borderColor: '#374151',
                        borderWidth: 1,
                        cornerRadius: 8
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeOutQuart'
                }
            }
        });
    }

    async loadInitialData() {
        try {
            const response = await fetch(`${this.baseUrl}/notificacoes/estatisticas`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success) {
                    this.updateCharts(data.graficos);
                    this.updateStats(data.estatisticas);
                }
            }
        } catch (error) {
            console.error('Erro ao carregar dados iniciais:', error);
        }
    }

    updateCharts(dadosGraficos) {
        // Atualiza gráfico de severidade
        if (this.charts.severidade && dadosGraficos.severidade) {
            this.charts.severidade.data.datasets[0].data = dadosGraficos.severidade.data;
            this.charts.severidade.update('active');
        }

        // Atualiza gráfico de tipos
        if (this.charts.tipos && dadosGraficos.tipos) {
            this.charts.tipos.data.labels = dadosGraficos.tipos.labels.map(label => 
                this.formatTipoLabel(label)
            );
            this.charts.tipos.data.datasets[0].data = dadosGraficos.tipos.data;
            this.charts.tipos.update('active');
        }

        // Atualiza gráfico de tendência
        if (this.charts.tendencia && dadosGraficos.tendencia) {
            this.charts.tendencia.data.labels = dadosGraficos.tendencia.labels.map(data => 
                new Date(data).toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit' })
            );
            this.charts.tendencia.data.datasets[0].data = dadosGraficos.tendencia.data;
            this.charts.tendencia.update('active');
        }
    }

    updateStats(estatisticas) {
        // Atualiza cards de estatísticas
        const elements = {
            total: document.querySelector('[data-stat="total"] .stat-number'),
            criticas: document.querySelector('[data-stat="criticas"] .stat-number'),
            altas: document.querySelector('[data-stat="altas"] .stat-number'),
            dias: document.querySelector('[data-stat="dias"] .stat-number')
        };

        if (elements.total) elements.total.textContent = estatisticas.total_ativas;
        if (elements.criticas) elements.criticas.textContent = estatisticas.criticas;
        if (elements.altas) elements.altas.textContent = estatisticas.altas;
        if (elements.dias) elements.dias.textContent = estatisticas.tendencia_7_dias?.length || 0;
    }

    formatTipoLabel(label) {
        return label
            .replace(/_/g, ' ')
            .replace(/\b\w/g, l => l.toUpperCase())
            .replace(/Bi/g, 'BI');
    }

    async aplicarFiltros() {
        const severidade = document.getElementById('filtroSeveridade')?.value;
        const tipo = document.getElementById('filtroTipo')?.value;

        const params = new URLSearchParams();
        if (severidade) params.append('severidade', severidade);
        if (tipo) params.append('tipo', tipo);

        this.mostrarLoading(true);

        try {
            const response = await fetch(`${this.baseUrl}/notificacoes/api?${params}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.atualizarListaNotificacoes(data.data);
                this.showToast('success', 'Filtros aplicados com sucesso');
            } else {
                this.showToast('error', 'Erro ao aplicar filtros');
            }
        } catch (error) {
            console.error('Erro ao aplicar filtros:', error);
            this.showToast('error', 'Erro de comunicação com o servidor');
        } finally {
            this.mostrarLoading(false);
        }
    }

    limparFiltros() {
        document.getElementById('filtroSeveridade').value = '';
        document.getElementById('filtroTipo').value = '';
        this.aplicarFiltros();
    }

    async atualizarDados() {
        const btn = document.getElementById('btnAtualizarDados');
        const originalContent = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Atualizando...';

        try {
            const [notificacoes, estatisticas] = await Promise.all([
                fetch(`${this.baseUrl}/notificacoes/api`),
                fetch(`${this.baseUrl}/notificacoes/estatisticas`)
            ]);

            const [notificacoesData, estatisticasData] = await Promise.all([
                notificacoes.json(),
                estatisticas.json()
            ]);

            if (notificacoesData.success && estatisticasData.success) {
                this.atualizarListaNotificacoes(notificacoesData.data);
                this.updateCharts(estatisticasData.graficos);
                this.updateStats(estatisticasData.estatisticas);
                this.showToast('success', 'Dados atualizados com sucesso');
                
                // Anima os cards
                this.animateStats();
            }
        } catch (error) {
            console.error('Erro ao atualizar dados:', error);
            this.showToast('error', 'Erro ao atualizar dados');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }
    }

    async executarAnalise() {
        const btn = event.target.closest('button');
        const originalContent = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Executando análise...';

        try {
            const response = await fetch(`${this.baseUrl}/notificacoes/executarAnalise`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('success', `Análise concluída! ${data.notificacoes_criadas} notificações criadas.`);
                
                // Recarrega a página após um delay para mostrar o resultado
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                this.showToast('error', data.error || 'Erro ao executar análise');
            }
        } catch (error) {
            console.error('Erro ao executar análise:', error);
            this.showToast('error', 'Erro de comunicação com o servidor');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalContent;
        }
    }

    resolverNotificacao(id) {
        this.notificacaoAtual = id;
        const modal = new bootstrap.Modal(document.getElementById('modalResolver'));
        modal.show();
    }

    cancelarNotificacao(id) {
        this.notificacaoAtual = id;
        const modal = new bootstrap.Modal(document.getElementById('modalCancelar'));
        modal.show();
    }

    async confirmarResolucao() {
        const observacao = document.getElementById('observacaoResolucao').value;
        
        try {
            const response = await fetch(`${this.baseUrl}/notificacoes/resolver/${this.notificacaoAtual}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `observacao=${encodeURIComponent(observacao)}`
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('success', data.message);
                this.removerNotificacao(this.notificacaoAtual);
                bootstrap.Modal.getInstance(document.getElementById('modalResolver')).hide();
                
                // Atualiza estatísticas
                this.atualizarDados();
            } else {
                this.showToast('error', data.message);
            }
        } catch (error) {
            console.error('Erro ao resolver notificação:', error);
            this.showToast('error', 'Erro de comunicação com o servidor');
        }
    }

    async confirmarCancelamento() {
        const motivo = document.getElementById('motivoCancelamento').value;
        
        if (!motivo) {
            this.showToast('warning', 'Selecione o motivo do cancelamento');
            return;
        }
        
        try {
            const response = await fetch(`${this.baseUrl}/notificacoes/cancelar/${this.notificacaoAtual}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `motivo=${encodeURIComponent(motivo)}`
            });

            const data = await response.json();

            if (data.success) {
                this.showToast('success', data.message);
                this.removerNotificacao(this.notificacaoAtual);
                bootstrap.Modal.getInstance(document.getElementById('modalCancelar')).hide();
                
                // Atualiza estatísticas
                this.atualizarDados();
            } else {
                this.showToast('error', data.message);
            }
        } catch (error) {
            console.error('Erro ao cancelar notificação:', error);
            this.showToast('error', 'Erro de comunicação com o servidor');
        }
    }

    removerNotificacao(id) {
        const elemento = document.querySelector(`[data-id="${id}"]`);
        if (elemento) {
            elemento.style.transition = 'all 0.3s ease';
            elemento.style.opacity = '0';
            elemento.style.transform = 'translateX(100%)';
            
            setTimeout(() => {
                elemento.remove();
                
                // Verifica se não há mais notificações
                const lista = document.getElementById('listaNotificacoes');
                if (lista && lista.children.length === 0) {
                    this.mostrarMensagemVazia();
                }
            }, 300);
        }
    }

    mostrarMensagemVazia() {
        const lista = document.getElementById('listaNotificacoes');
        lista.innerHTML = `
            <div class="text-center py-5">
                <i class="bi bi-check-circle-fill text-success display-1"></i>
                <h4 class="mt-3">Nenhuma notificação ativa</h4>
                <p class="text-muted">O sistema está funcionando normalmente. Execute uma análise para verificar novos alertas.</p>
                <button class="btn btn-primary" onclick="notificacoesManager.executarAnalise()">
                    <i class="bi bi-play-circle"></i> Executar Análise BI
                </button>
            </div>
        `;
    }

    atualizarListaNotificacoes(notificacoes) {
        // Implementação simplificada - recarrega página
        // Em produção, seria melhor atualizar dinamicamente
        if (notificacoes.length !== document.querySelectorAll('.notification-item').length) {
            setTimeout(() => window.location.reload(), 1000);
        }
    }

    mostrarLoading(show) {
        const loading = document.getElementById('loadingNotificacoes');
        const lista = document.getElementById('listaNotificacoes');
        
        if (loading && lista) {
            if (show) {
                loading.style.display = 'block';
                lista.style.opacity = '0.5';
            } else {
                loading.style.display = 'none';
                lista.style.opacity = '1';
            }
        }
    }

    animateStats() {
        const cards = document.querySelectorAll('.stat-card');
        cards.forEach((card, index) => {
            setTimeout(() => {
                card.style.transform = 'scale(1.05)';
                setTimeout(() => {
                    card.style.transform = 'scale(1)';
                }, 200);
            }, index * 100);
        });
    }

    startAutoRefresh() {
        // Auto-refresh a cada 2 minutos
        this.refreshInterval = setInterval(() => {
            this.atualizarDados();
        }, 120000);
    }

    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
            this.refreshInterval = null;
        }
    }

    handleKeyboardShortcuts(e) {
        // Ctrl + R: Atualizar dados
        if (e.ctrlKey && e.key === 'r') {
            e.preventDefault();
            this.atualizarDados();
        }
        
        // Ctrl + Shift + A: Executar análise
        if (e.ctrlKey && e.shiftKey && e.key === 'A') {
            e.preventDefault();
            this.executarAnalise();
        }
        
        // Escape: Fechar modais
        if (e.key === 'Escape') {
            const modals = document.querySelectorAll('.modal.show');
            modals.forEach(modal => {
                bootstrap.Modal.getInstance(modal)?.hide();
            });
        }
    }

    requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    }

    showDesktopNotification(title, body, icon = null) {
        if ('Notification' in window && Notification.permission === 'granted') {
            new Notification(title, {
                body,
                icon: icon || '/favicon.ico',
                badge: '/favicon.ico',
                tag: 'notificacao-bi'
            });
        }
    }

    showToast(type, message, title = null) {
        const toastContainer = this.getToastContainer();
        const toastId = `toast-${Date.now()}`;
        
        const iconMap = {
            success: 'check-circle-fill',
            error: 'exclamation-triangle-fill',
            warning: 'exclamation-triangle-fill',
            info: 'info-circle-fill'
        };

        const bgMap = {
            success: 'bg-success',
            error: 'bg-danger',
            warning: 'bg-warning',
            info: 'bg-info'
        };

        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-white ${bgMap[type]} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <i class="bi bi-${iconMap[type]} me-2"></i>
                    ${title ? `<strong>${title}</strong><br>` : ''}
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast, {
            autohide: true,
            delay: type === 'error' ? 8000 : 5000
        });
        
        bsToast.show();

        // Remove element after hide
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }

    getToastContainer() {
        let container = document.getElementById('toastContainer');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }
        return container;
    }

    destroy() {
        this.stopAutoRefresh();
        
        // Destroy charts
        Object.values(this.charts).forEach(chart => {
            if (chart && typeof chart.destroy === 'function') {
                chart.destroy();
            }
        });
        
        console.log('🔔 Sistema de Notificações BI finalizado');
    }
}

// Inicialização global
let notificacoesManager;

document.addEventListener('DOMContentLoaded', function() {
    // Inicializa apenas se estivermos na página de notificações
    if (document.getElementById('graficoSeveridade') || document.querySelector('.notification-item')) {
        notificacoesManager = new NotificacoesManager();
    }
});

// Funções globais para compatibilidade com HTML
function resolverNotificacao(id) {
    if (notificacoesManager) {
        notificacoesManager.resolverNotificacao(id);
    }
}

function cancelarNotificacao(id) {
    if (notificacoesManager) {
        notificacoesManager.cancelarNotificacao(id);
    }
}

// Cleanup ao sair da página
window.addEventListener('beforeunload', function() {
    if (notificacoesManager) {
        notificacoesManager.destroy();
    }
});
