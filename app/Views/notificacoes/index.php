<?= $this->extend('layout/base') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/notificacoes.css') ?>">
<?= $this->endSection() ?>

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

                <div class="header-actions">
                    <button type="button" class="btn btn-outline-primary" id="btnAtualizarDados">
                        <i class="bi bi-arrow-clockwise"></i> Atualizar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnExecutarAnalise">
                        <i class="bi bi-cpu"></i> Executar Análise
                    </button>
                    <a href="<?= base_url('notificacoes/relatorio') ?>" class="btn btn-secondary">
                        <i class="bi bi-file-earmark-text"></i> Relatório
                    </a>
                    <a href="<?= base_url('notificacoes/configuracoes') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-gear"></i> Configurações
                    </a>
                </div>
            </div>

            <div class="content-wrapper">
                <!-- Flash Messages -->
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i>
                        <?= session()->getFlashdata('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <?= session()->getFlashdata('error') ?>
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
                                <i class="bi bi-exclamation-diamond-fill"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
                        <div class="stat-card stat-warning">
                            <div class="stat-content">
                                <div class="stat-number"><?= $estatisticas['altas'] ?></div>
                                <div class="stat-label">Alta Severidade</div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-exclamation-circle-fill"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-lg-6 col-md-6 col-12 mb-3">
                        <div class="stat-card stat-info">
                            <div class="stat-content">
                                <div class="stat-number"><?= $estatisticas['medias'] + $estatisticas['baixas'] ?></div>
                                <div class="stat-label">Outras</div>
                            </div>
                            <div class="stat-icon">
                                <i class="bi bi-info-circle-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gráficos -->
                <div class="row mb-4">
                    <div class="col-lg-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Distribuição por Severidade</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="graficoSeveridade" width="300" height="300"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">Tipos de Notificações</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="graficoTipos" width="300" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tendência -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Tendência (7 dias)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="graficoTendencia" width="300" height="300"></canvas>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Filtros</h5>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="filtroSeveridade" class="form-label">Severidade</label>
                                <select class="form-select" id="filtroSeveridade">
                                    <option value="">Todas</option>
                                    <option value="critica">Crítica</option>
                                    <option value="alta">Alta</option>
                                    <option value="media">Média</option>
                                    <option value="baixa">Baixa</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="filtroTipo" class="form-label">Tipo</label>
                                <select class="form-select" id="filtroTipo">
                                    <option value="">Todos</option>
                                    <option value="paciente_recorrente">Paciente Recorrente</option>
                                    <option value="surto_sintomas">Surto de Sintomas</option>
                                    <option value="alta_demanda">Alta Demanda</option>
                                    <option value="medicamento_critico">Medicamento Crítico</option>
                                    <option value="equipamento_falha">Falha Equipamento</option>
                                    <option value="estatistica_anomala">Estatística Anômala</option>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-primary me-2" id="btnFiltrar">
                                    <i class="bi bi-funnel"></i> Filtrar
                                </button>
                                <button type="button" class="btn btn-outline-secondary" id="btnLimparFiltros">
                                    <i class="bi bi-x-circle"></i> Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Notificações -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title">Notificações Ativas</h5>
                        <div id="loadingNotificacoes" class="spinner-border spinner-border-sm d-none" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                    <div class="card-body" id="listaNotificacoes">
                        <?php if (empty($notificacoes)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-check-circle text-success" style="font-size: 4rem;"></i>
                                <h4 class="mt-3">Nenhuma Notificação Ativa</h4>
                                <p class="text-muted">Sistema funcionando normalmente. Não há alertas no momento.</p>
                                <button type="button" class="btn btn-outline-primary" id="btnExecutarAnaliseVazio">
                                    <i class="bi bi-cpu"></i> Executar Nova Análise
                                </button>
                            </div>
                        <?php else: ?>
                            <?php foreach ($notificacoes as $notificacao): ?>
                                <div class="notification-item border rounded mb-3 p-3">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-start">
                                                <div class="notification-icon me-3">
                                                    <?php
                                                    $tipo = $notificacao['tipo'];
                                                    include APPPATH . 'Views/notificacoes/partials/icon.php';
                                                    ?>
                                                </div>
                                                <div class="notification-content">
                                                    <h6 class="notification-title">
                                                        <?= esc($notificacao['titulo']) ?>
                                                        <span class="badge bg-<?php
                                                                                $severidade = $notificacao['severidade'];
                                                                                include APPPATH . 'Views/notificacoes/partials/badge_color.php';
                                                                                ?> ms-2">
                                                            <?= ucfirst($notificacao['severidade']) ?>
                                                        </span>
                                                    </h6>
                                                    <p class="notification-description text-muted mb-2">
                                                        <?= esc($notificacao['descricao']) ?>
                                                    </p>

                                                    <!-- Parâmetros específicos do tipo -->
                                                    <?php if (!empty($notificacao['parametros'])): ?>
                                                        <div class="notification-params">
                                                            <?php
                                                            $parametros = $notificacao['parametros'];
                                                            $tipo = $notificacao['tipo'];
                                                            include APPPATH . 'Views/notificacoes/partials/parametros.php';
                                                            ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="notification-meta h-100 d-flex flex-column align-items-end justify-content-between text-end">
                                                <div class="card-date-time">
                                                    <small class="text-muted d-block">
                                                        <i class="bi bi-clock"></i>
                                                        Ativa há <?= $notificacao['tempo_ativa'] ?>
                                                    </small>
                                                    <small class="text-muted d-block mb-2">
                                                        <i class="bi bi-calendar"></i>
                                                        <?= date('d/m/Y H:i', strtotime($notificacao['acionada_em'])) ?>
                                                    </small>
                                                    <span class="badge bg-<?= $notificacao['urgencia'] === 'maxima' ? 'danger' : ($notificacao['urgencia'] === 'alta' ? 'warning' : ($notificacao['urgencia'] === 'media' ? 'info' : 'success')) ?> mb-2">
                                                        Urgência: <?= ucfirst($notificacao['urgencia']) ?>
                                                    </span>
                                                </div>

                                                <div class="notification-actions mt-2">
                                                    <a href="<?= base_url('notificacoes/show/' . $notificacao['id']) ?>"
                                                        class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-eye"></i> Detalhes
                                                    </a>
                                                    <button type="button"
                                                        class="btn btn-sm btn-success"
                                                        onclick="resolverNotificacao(<?= $notificacao['id'] ?>)">
                                                        <i class="bi bi-check"></i> Resolver
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="cancelarNotificacao(<?= $notificacao['id'] ?>)">
                                                        <i class="bi bi-x"></i> Cancelar
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php echo $this->include('components/footer'); ?>
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
                        <select class="form-select mb-2" id="motivoCancelamento" name="motivo" required>
                            <option value="">Selecione o motivo</option>
                            <option value="Falso positivo">Falso positivo</option>
                            <option value="Notificação duplicada">Notificação duplicada</option>
                            <option value="Não aplicável">Não aplicável</option>
                            <option value="Resolvido automaticamente">Resolvido automaticamente</option>
                            <option value="Outro">Outro motivo</option>
                        </select>
                        <textarea class="form-control mt-2" id="motivoCancelamentoDetalhe" name="motivo_detalhe" rows="2"
                            placeholder="Detalhe o motivo do cancelamento (opcional)"></textarea>
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
                canvasTendencia.style.maxHeight = '300px';
                canvasTendencia.style.height = '300px';
                canvasTendencia.width = canvasTendencia.offsetWidth;
                canvasTendencia.height = 300;

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
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    observacao: observacao
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    bootstrap.Modal.getInstance(document.getElementById('modalResolver')).hide();
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showAlert('error', 'Erro ao resolver notificação');
            });
    }

    function confirmarCancelamento() {
        const motivoSelect = document.getElementById('motivoCancelamento').value;
        const motivoDetalhe = document.getElementById('motivoCancelamentoDetalhe').value;

        if (!motivoSelect.trim()) {
            showAlert('warning', 'Informe o motivo do cancelamento');
            return;
        }

        // Monta descrição detalhada
        let motivoFinal = motivoSelect;
        if (motivoDetalhe.trim()) {
            motivoFinal += ' - ' + motivoDetalhe.trim();
        }

        fetch(`<?= base_url("notificacoes/cancelar") ?>/${notificacaoAtual}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    motivo: motivoFinal
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                    bootstrap.Modal.getInstance(document.getElementById('modalCancelar')).hide();
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showAlert('error', 'Erro ao cancelar notificação');
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
        } [type] || 'alert-info';

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