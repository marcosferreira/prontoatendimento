<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<div class="app-container">
    <?php echo $this->include('components/sidebar'); ?>

    <?php echo $this->include('components/topbar'); ?>
    

    <!-- Main Content -->
    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-bell-fill"></i> Central de Notificações BI</h1>
                <p class="subtitle">Monitoramento Inteligente e Alertas do Sistema de Pronto Atendimento</p>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Estatísticas em Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
                    <div class="stat-card stat-danger">
                        <div class="stat-content">
                            <div class="stat-number"><?= $estatisticas['total_ativas'] ?></div>
                            <div class="stat-label">Notificações Ativas</div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
                    <div class="stat-card stat-critical">
                        <div class="stat-content">
                            <div class="stat-number"><?= $estatisticas['criticas'] ?></div>
                            <div class="stat-label">Críticas</div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-shield-exclamation"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
                    <div class="stat-card stat-warning">
                        <div class="stat-content">
                            <div class="stat-number"><?= $estatisticas['altas'] ?></div>
                            <div class="stat-label">Alta Prioridade</div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-exclamation-circle-fill"></i>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
                    <div class="stat-card stat-info">
                        <div class="stat-content">
                            <div class="stat-number"><?= count($estatisticas['tendencia_7_dias']) ?></div>
                            <div class="stat-label">Dias com Alertas</div>
                        </div>
                        <div class="stat-icon">
                            <i class="bi bi-graph-up"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controles e Filtros -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="d-flex gap-3 align-items-center">
                                        <select id="filtroSeveridade" class="form-select form-select-sm" style="width: auto;">
                                            <option value="">Todas as severidades</option>
                                            <option value="critica">Crítica</option>
                                            <option value="alta">Alta</option>
                                            <option value="media">Média</option>
                                            <option value="baixa">Baixa</option>
                                        </select>
                                        
                                        <select id="filtroTipo" class="form-select form-select-sm" style="width: auto;">
                                            <option value="">Todos os tipos</option>
                                            <option value="paciente_recorrente">Paciente Recorrente</option>
                                            <option value="surto_sintomas">Surto de Sintomas</option>
                                            <option value="alta_demanda">Alta Demanda</option>
                                            <option value="estatistica_anomala">Anomalia Estatística</option>
                                        </select>
                                        
                                        <button id="btnFiltrar" class="btn btn-primary btn-sm">
                                            <i class="bi bi-funnel"></i> Filtrar
                                        </button>
                                        
                                        <button id="btnLimparFiltros" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-x-circle"></i> Limpar
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div class="btn-group">
                                        <button id="btnExecutarAnalise" class="btn btn-success btn-sm">
                                            <i class="bi bi-play-circle"></i> Executar Análise
                                        </button>
                                        <button id="btnAtualizarDados" class="btn btn-outline-primary btn-sm">
                                            <i class="bi bi-arrow-clockwise"></i> Atualizar
                                        </button>
                                        <a href="<?= base_url('notificacoes/relatorio') ?>" class="btn btn-outline-info btn-sm">
                                            <i class="bi bi-file-earmark-text"></i> Relatório
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="row mb-4">
                <div class="col-lg-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Distribuição por Severidade</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoSeveridade" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6 mb-3">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="bi bi-bar-chart"></i> Notificações por Tipo</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoTipos" height="300"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de Notificações -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Notificações Ativas</h5>
                </div>
                <div class="card-body">
                    <div id="loadingNotificacoes" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                    
                    <div id="listaNotificacoes">
                        <?php if (empty($notificacoes)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-check-circle-fill text-success display-1"></i>
                                <h4 class="mt-3">Nenhuma notificação ativa</h4>
                                <p class="text-muted">O sistema está funcionando normalmente. Execute uma análise para verificar novos alertas.</p>
                                <button id="btnExecutarAnaliseVazio" class="btn btn-primary">
                                    <i class="bi bi-play-circle"></i> Executar Análise BI
                                </button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notificacoes as $index => $notificacao): ?>
                                <!-- Debug temporário -->
                                <?php if ($index === 0): ?>
                                    <!-- DEBUG: Estrutura da primeira notificação -->
                                    <?php /* var_dump($notificacao); */ ?>
                                <?php endif; ?>
                                
                                <div class="notification-item notification-<?= $notificacao['severidade'] ?? 'baixa' ?> mb-3" data-id="<?= $notificacao['id'] ?? 0 ?>">
                                    <div class="notification-header">
                                        <div class="notification-title">
                                            <span class="notification-icon">
                                                <?= $this->include('notificacoes/partials/icon', ['tipo' => $notificacao['tipo'] ?? 'default']) ?>
                                            </span>
                                            <strong><?= esc($notificacao['titulo'] ?? 'Sem título') ?></strong>
                                            <span class="badge bg-<?= $this->include('notificacoes/partials/badge_color', ['severidade' => $notificacao['severidade'] ?? 'baixa']) ?>">
                                                <?= ucfirst($notificacao['severidade'] ?? 'baixa') ?>
                                            </span>
                                            <span class="badge bg-secondary"><?= ucfirst(str_replace('_', ' ', $notificacao['tipo'] ?? 'padrão')) ?></span>
                                        </div>
                                        <div class="notification-meta">
                                            <small class="text-muted">
                                                <i class="bi bi-clock"></i> Ativa há <?= $notificacao['tempo_ativa'] ?? '0 min' ?>
                                                <?php if (!empty($notificacao['data_vencimento'])): ?>
                                                    | <i class="bi bi-calendar-x"></i> Vence em <?= date('d/m/Y H:i', strtotime($notificacao['data_vencimento'])) ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="notification-content">
                                        <p class="mb-2"><?= esc($notificacao['descricao'] ?? 'Sem descrição') ?></p>
                                        
                                        <?php if (!empty($notificacao['parametros'])): ?>
                                            <div class="notification-details">
                                                <?= $this->include('notificacoes/partials/parametros', [
                                                    'parametros' => $notificacao['parametros'], 
                                                    'tipo' => $notificacao['tipo'] ?? 'default'
                                                ]) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="notification-actions">
                                        <a href="<?= base_url('notificacoes/show/' . ($notificacao['id'] ?? 0)) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Detalhes
                                        </a>
                                        <button class="btn btn-sm btn-success" onclick="resolverNotificacao(<?= $notificacao['id'] ?? 0 ?>)">
                                            <i class="bi bi-check-circle"></i> Resolver
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="cancelarNotificacao(<?= $notificacao['id'] ?? 0 ?>)">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Tendência dos Últimos 7 Dias -->
            <?php if (!empty($estatisticas['tendencia_7_dias'])): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-graph-up"></i> Tendência dos Últimos 7 Dias</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoTendencia" height="100"></canvas>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<!-- Modal para Resolver Notificação -->
<div class="modal fade" id="modalResolver" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Resolver Notificação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formResolver">
                    <div class="mb-3">
                        <label for="observacaoResolucao" class="form-label">Observações da Resolução</label>
                        <textarea class="form-control" id="observacaoResolucao" name="observacao" rows="3" 
                                  placeholder="Descreva as ações tomadas para resolver esta notificação..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnConfirmarResolver">
                    <i class="bi bi-check-circle"></i> Confirmar Resolução
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Cancelar Notificação -->
<div class="modal fade" id="modalCancelar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancelar Notificação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formCancelar">
                    <div class="mb-3">
                        <label for="motivoCancelamento" class="form-label">Motivo do Cancelamento</label>
                        <select class="form-select" id="motivoCancelamento" name="motivo" required>
                            <option value="">Selecione o motivo</option>
                            <option value="falso_positivo">Falso positivo</option>
                            <option value="duplicada">Notificação duplicada</option>
                            <option value="nao_aplicavel">Não aplicável</option>
                            <option value="resolvido_automaticamente">Resolvido automaticamente</option>
                            <option value="outro">Outro motivo</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarCancelar">
                    <i class="bi bi-x-circle"></i> Confirmar Cancelamento
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
/* Estilos específicos para notificações */
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.stat-content .stat-number {
    font-size: 2.5rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-content .stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 500;
}

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.7;
}

.stat-danger { border-left: 4px solid #dc3545; }
.stat-danger .stat-number { color: #dc3545; }
.stat-danger .stat-icon { color: #dc3545; }

.stat-critical { border-left: 4px solid #6f42c1; }
.stat-critical .stat-number { color: #6f42c1; }
.stat-critical .stat-icon { color: #6f42c1; }

.stat-warning { border-left: 4px solid #fd7e14; }
.stat-warning .stat-number { color: #fd7e14; }
.stat-warning .stat-icon { color: #fd7e14; }

.stat-info { border-left: 4px solid #0dcaf0; }
.stat-info .stat-number { color: #0dcaf0; }
.stat-info .stat-icon { color: #0dcaf0; }

/* Notificações */
.notification-item {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.25rem;
    background: white;
    transition: all 0.2s ease;
}

.notification-item:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transform: translateY(-1px);
}

.notification-critica {
    border-left: 4px solid #dc3545;
    background: linear-gradient(135deg, #fff5f5, #ffffff);
}

.notification-alta {
    border-left: 4px solid #fd7e14;
    background: linear-gradient(135deg, #fff8f0, #ffffff);
}

.notification-media {
    border-left: 4px solid #ffc107;
    background: linear-gradient(135deg, #fffdf0, #ffffff);
}

.notification-baixa {
    border-left: 4px solid #198754;
    background: linear-gradient(135deg, #f0fff4, #ffffff);
}

.notification-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.75rem;
}

.notification-title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    flex: 1;
}

.notification-icon {
    font-size: 1.25rem;
}

.notification-content {
    margin-bottom: 1rem;
}

.notification-details {
    background: #f8f9fa;
    border-radius: 6px;
    padding: 0.75rem;
    margin-top: 0.5rem;
}

.notification-actions {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.notification-meta {
    text-align: right;
    white-space: nowrap;
}

/* Cards responsivos */
@media (max-width: 768px) {
    .stat-card {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .notification-header {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .notification-meta {
        text-align: left;
    }
    
    .notification-actions {
        flex-wrap: wrap;
    }
}

/* Animações */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.notification-critica .notification-icon {
    animation: pulse 2s infinite;
    color: #dc3545;
}

.notification-alta .notification-icon {
    color: #fd7e14;
}

.notification-media .notification-icon {
    color: #ffc107;
}

.notification-baixa .notification-icon {
    color: #198754;
}

/* Controle de tamanho dos gráficos Chart.js */
canvas {
    max-height: 300px !important;
    height: 300px !important;
}

#graficoSeveridade, #graficoTipos {
    max-height: 300px !important;
    height: 300px !important;
    max-width: 100% !important;
}

#graficoTendencia {
    max-height: 100px !important;
    height: 100px !important;
    max-width: 100% !important;
}

/* Container dos gráficos */
.card-body canvas {
    max-height: 300px !important;
    height: 300px !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Dados dos gráficos vindos do backend - disponível globalmente
window.dadosGraficos = <?= json_encode($graficos) ?>;

document.addEventListener('DOMContentLoaded', function() {
    // Verifica se os dados existem e são válidos
    if (!window.dadosGraficos) {
        console.error('❌ Dados dos gráficos não encontrados');
        // Tentar mostrar uma mensagem para o usuário
        const graficos = document.querySelectorAll('canvas');
        graficos.forEach(canvas => {
            const container = canvas.parentElement;
            container.innerHTML = '<div class="alert alert-warning text-center"><i class="bi bi-exclamation-triangle"></i> Dados não disponíveis para este gráfico</div>';
        });
        return;
    }
    
    console.log('✅ Dados dos gráficos carregados:', window.dadosGraficos);
    
    // Inicializar gráficos
    inicializarGraficos();
    
    // Event listeners
    setupEventListeners();
    
    // Auto-refresh a cada 2 minutos
    setInterval(atualizarDados, 120000);
    
    console.log('🚀 Sistema de notificações inicializado com sucesso');
});

function setupEventListeners() {
    // Filtros
    const btnFiltrar = document.getElementById('btnFiltrar');
    if (btnFiltrar) btnFiltrar.addEventListener('click', filtrarNotificacoes);
    
    const btnLimparFiltros = document.getElementById('btnLimparFiltros');
    if (btnLimparFiltros) btnLimparFiltros.addEventListener('click', limparFiltros);
    
    // Controles
    const btnAtualizarDados = document.getElementById('btnAtualizarDados');
    if (btnAtualizarDados) btnAtualizarDados.addEventListener('click', atualizarDados);
    
    const btnExecutarAnalise = document.getElementById('btnExecutarAnalise');
    if (btnExecutarAnalise) btnExecutarAnalise.addEventListener('click', executarAnalise);
    
    // Event listener para botão de análise quando não há notificações
    const btnAnaliseVazio = document.getElementById('btnExecutarAnaliseVazio');
    if (btnAnaliseVazio) btnAnaliseVazio.addEventListener('click', executarAnalise);
    
    // Modais
    const btnConfirmarResolver = document.getElementById('btnConfirmarResolver');
    if (btnConfirmarResolver) btnConfirmarResolver.addEventListener('click', confirmarResolucao);
    
    const btnConfirmarCancelar = document.getElementById('btnConfirmarCancelar');
    if (btnConfirmarCancelar) btnConfirmarCancelar.addEventListener('click', confirmarCancelamento);
}

function inicializarGraficos() {
    const dados = window.dadosGraficos;
    
    if (!dados) {
        console.error('❌ Dados dos gráficos não disponíveis para inicialização');
        return;
    }
    
    console.log('📊 Inicializando gráficos com dados:', dados);
    
    // Gráfico de Severidade (Doughnut)
    try {
        const canvasSeveridade = document.getElementById('graficoSeveridade');
        if (canvasSeveridade && dados.severidade) {
            const ctxSeveridade = canvasSeveridade.getContext('2d');
            
            // Verificar se há dados válidos
            const hasDados = dados.severidade.data && dados.severidade.data.some(val => val > 0);
            
            if (!hasDados) {
                // Mostrar gráfico vazio com mensagem
                const container = canvasSeveridade.parentElement;
                container.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-pie-chart-fill display-4 mb-3"></i><p>Nenhum dado de severidade disponível</p></div>';
            } else {
                // Definir dimensões fixas para evitar crescimento descontrolado
                canvasSeveridade.style.maxHeight = '300px';
                canvasSeveridade.style.height = '300px';
                canvasSeveridade.width = canvasSeveridade.offsetWidth;
                canvasSeveridade.height = 300;
                
                new Chart(ctxSeveridade, {
                    type: 'doughnut',
                    data: {
                        labels: dados.severidade.labels || ['Crítica', 'Alta', 'Média', 'Baixa'],
                        datasets: [{
                            data: dados.severidade.data || [0, 0, 0, 0],
                            backgroundColor: dados.severidade.colors || ['#dc3545', '#fd7e14', '#ffc107', '#198754'],
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        aspectRatio: 1,
                        devicePixelRatio: 1,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                                        return `${context.label}: ${context.parsed} (${percentage}%)`;
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: {
                                top: 10,
                                bottom: 10,
                                left: 10,
                                right: 10
                            }
                        }
                    }
                });
                console.log('✅ Gráfico de severidade criado');
            }
        }
    } catch (error) {
        console.error('❌ Erro ao criar gráfico de severidade:', error);
        const canvas = document.getElementById('graficoSeveridade');
        if (canvas) {
            canvas.parentElement.innerHTML = '<div class="alert alert-danger text-center">Erro ao carregar gráfico de severidade</div>';
        }
    }
    
    // Gráfico de Tipos (Bar)
    try {
        const canvasTipos = document.getElementById('graficoTipos');
        if (canvasTipos && dados.tipos) {
            const ctxTipos = canvasTipos.getContext('2d');
            
            // Verificar se há dados válidos
            const hasDados = dados.tipos.data && dados.tipos.data.some(val => val > 0);
            
            if (!hasDados) {
                // Mostrar gráfico vazio com mensagem
                const container = canvasTipos.parentElement;
                container.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-bar-chart-fill display-4 mb-3"></i><p>Nenhum dado de tipos disponível</p></div>';
            } else {
                // Definir dimensões fixas para evitar crescimento descontrolado
                canvasTipos.style.maxHeight = '300px';
                canvasTipos.style.height = '300px';
                canvasTipos.width = canvasTipos.offsetWidth;
                canvasTipos.height = 300;
                
                new Chart(ctxTipos, {
                    type: 'bar',
                    data: {
                        labels: (dados.tipos.labels || []).map(label => 
                            label.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
                        ),
                        datasets: [{
                            label: 'Notificações',
                            data: dados.tipos.data || [],
                            backgroundColor: 'rgba(54, 162, 235, 0.8)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        aspectRatio: 1,
                        devicePixelRatio: 1,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return `${context.label}: ${context.parsed.y} notificações`;
                                    }
                                }
                            }
                        },
                        layout: {
                            padding: {
                                top: 10,
                                bottom: 10,
                                left: 10,
                                right: 10
                            }
                        }
                    }
                });
                console.log('✅ Gráfico de tipos criado');
            }
        }
    } catch (error) {
        console.error('❌ Erro ao criar gráfico de tipos:', error);
        const canvas = document.getElementById('graficoTipos');
        if (canvas) {
            canvas.parentElement.innerHTML = '<div class="alert alert-danger text-center">Erro ao carregar gráfico de tipos</div>';
        }
    }
    
    // Gráfico de Tendência (Line) - se houver dados
    try {
        const canvasTendencia = document.getElementById('graficoTendencia');
        if (canvasTendencia && dados.tendencia && dados.tendencia.labels && dados.tendencia.labels.length > 0) {
            const ctxTendencia = canvasTendencia.getContext('2d');
            
            // Definir dimensões fixas para evitar crescimento descontrolado
            canvasTendencia.style.maxHeight = '100px';
            canvasTendencia.style.height = '100px';
            canvasTendencia.width = canvasTendencia.offsetWidth;
            canvasTendencia.height = 100;
            
            new Chart(ctxTendencia, {
                type: 'line',
                data: {
                    labels: dados.tendencia.labels.map(data => 
                        new Date(data).toLocaleDateString('pt-BR')
                    ),
                    datasets: [{
                        label: 'Notificações por Dia',
                        data: dados.tendencia.data,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        tension: 0.1,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    aspectRatio: 1,
                    devicePixelRatio: 1,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.label}: ${context.parsed.y} notificações`;
                                }
                            }
                        }
                    },
                    layout: {
                        padding: {
                            top: 5,
                            bottom: 5,
                            left: 5,
                            right: 5
                        }
                    }
                }
            });
            console.log('✅ Gráfico de tendência criado');
        }
    } catch (error) {
        console.error('❌ Erro ao criar gráfico de tendência:', error);
        const canvas = document.getElementById('graficoTendencia');
        if (canvas) {
            canvas.parentElement.innerHTML = '<div class="alert alert-danger text-center">Erro ao carregar gráfico de tendência</div>';
        }
    }
    
    console.log('🎨 Inicialização de gráficos concluída');
}

function filtrarNotificacoes() {
    const severidade = document.getElementById('filtroSeveridade').value;
    const tipo = document.getElementById('filtroTipo').value;
    
    mostrarLoading(true);
    
    fetch('<?= base_url("notificacoes/api") ?>', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            atualizarListaNotificacoes(data.data);
        } else {
            showAlert('error', 'Erro ao filtrar notificações');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showAlert('error', 'Erro de comunicação com o servidor');
    })
    .finally(() => {
        mostrarLoading(false);
    });
}

function limparFiltros() {
    document.getElementById('filtroSeveridade').value = '';
    document.getElementById('filtroTipo').value = '';
    filtrarNotificacoes();
}

function atualizarDados() {
    mostrarLoading(true);
    
    Promise.all([
        fetch('<?= base_url("notificacoes/api") ?>'),
        fetch('<?= base_url("notificacoes/estatisticas") ?>')
    ])
    .then(responses => Promise.all(responses.map(r => r.json())))
    .then(([notificacoes, estatisticas]) => {
        if (notificacoes.success && estatisticas.success) {
            atualizarListaNotificacoes(notificacoes.data);
            atualizarEstatisticas(estatisticas.estatisticas);
            showAlert('success', 'Dados atualizados com sucesso');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showAlert('error', 'Erro ao atualizar dados');
    })
    .finally(() => {
        mostrarLoading(false);
    });
}

function executarAnalise() {
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Executando...';
    
    fetch('<?= base_url("notificacoes/executarAnalise") ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', `Análise concluída! ${data.notificacoes_criadas} notificações criadas.`);
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showAlert('error', data.error || 'Erro ao executar análise');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showAlert('error', 'Erro de comunicação com o servidor');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}

let notificacaoAtual = null;

function resolverNotificacao(id) {
    notificacaoAtual = id;
    const modal = new bootstrap.Modal(document.getElementById('modalResolver'));
    modal.show();
}

function cancelarNotificacao(id) {
    notificacaoAtual = id;
    const modal = new bootstrap.Modal(document.getElementById('modalCancelar'));
    modal.show();
}

function confirmarResolucao() {
    const observacao = document.getElementById('observacaoResolucao').value;
    
    fetch(`<?= base_url("notificacoes/resolver") ?>/${notificacaoAtual}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `observacao=${encodeURIComponent(observacao)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            document.querySelector(`[data-id="${notificacaoAtual}"]`).remove();
            bootstrap.Modal.getInstance(document.getElementById('modalResolver')).hide();
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showAlert('error', 'Erro de comunicação com o servidor');
    });
}

function confirmarCancelamento() {
    const motivo = document.getElementById('motivoCancelamento').value;
    
    if (!motivo) {
        showAlert('warning', 'Selecione o motivo do cancelamento');
        return;
    }
    
    fetch(`<?= base_url("notificacoes/cancelar") ?>/${notificacaoAtual}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `motivo=${encodeURIComponent(motivo)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', data.message);
            document.querySelector(`[data-id="${notificacaoAtual}"]`).remove();
            bootstrap.Modal.getInstance(document.getElementById('modalCancelar')).hide();
        } else {
            showAlert('error', data.message);
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showAlert('error', 'Erro de comunicação com o servidor');
    });
}

function mostrarLoading(show) {
    const loading = document.getElementById('loadingNotificacoes');
    const lista = document.getElementById('listaNotificacoes');
    
    if (show) {
        loading.style.display = 'block';
        lista.style.opacity = '0.5';
    } else {
        loading.style.display = 'none';
        lista.style.opacity = '1';
    }
}

function atualizarListaNotificacoes(notificacoes) {
    // Esta função seria implementada para atualizar dinamicamente a lista
    // Por simplicidade, vamos recarregar a página
    window.location.reload();
}

function atualizarEstatisticas(stats) {
    // Atualizar cards de estatísticas
    document.querySelector('.stat-card .stat-number').textContent = stats.total_ativas;
    // Atualizar outros valores conforme necessário
}

function showAlert(type, message) {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';

    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}-fill me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;

    document.body.insertAdjacentHTML('beforeend', alertHtml);

    // Auto-remove após 5 segundos
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.parentNode) {
                alert.remove();
            }
        });
    }, 5000);
}
</script>
<?= $this->endSection() ?>
