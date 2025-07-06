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
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="<?= base_url('notificacoes') ?>">
                                <i class="bi bi-bell"></i> Notificações
                            </a>
                        </li>
                        <li class="breadcrumb-item active">Configurações</li>
                    </ol>
                </nav>
                <h1>
                    <i class="bi bi-gear"></i> Configurações de Notificações BI
                </h1>
                <p class="subtitle">Gerenciar configurações do sistema de monitoramento inteligente</p>
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

                <div class="row">
                    <!-- Configurações Gerais -->
                    <div class="col-lg-8">
                        <form id="formConfiguracoes">
                            <!-- Análise Automática -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="bi bi-cpu"></i> Análise Automática
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="analiseAutomatica" 
                                                       <?= $configuracoes['analise_automatica'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="analiseAutomatica">
                                                    <strong>Habilitar Análise Automática</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">
                                                Quando habilitada, o sistema executará análises automaticamente em intervalos definidos
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="intervaloAnalise" class="form-label">Intervalo de Análise</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="intervaloAnalise" 
                                                       value="<?= $configuracoes['intervalo_analise'] ?>" min="5" max="1440">
                                                <span class="input-group-text">minutos</span>
                                            </div>
                                            <small class="text-muted">Intervalo entre execuções automáticas (5-1440 min)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Parâmetros de Detecção -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="bi bi-search"></i> Parâmetros de Detecção
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Paciente Recorrente</h6>
                                            <div class="mb-3">
                                                <label for="limiteAtendimentosPaciente" class="form-label">Limite de Atendimentos</label>
                                                <input type="number" class="form-control" id="limiteAtendimentosPaciente" 
                                                       value="3" min="2" max="10">
                                                <small class="text-muted">Número de atendimentos para gerar alerta</small>
                                            </div>
                                            <div class="mb-3">
                                                <label for="periodoRecorrencia" class="form-label">Período de Análise</label>
                                                <select class="form-select" id="periodoRecorrencia">
                                                    <option value="7">Últimos 7 dias</option>
                                                    <option value="15" selected>Últimos 15 dias</option>
                                                    <option value="30">Últimos 30 dias</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Surto de Sintomas</h6>
                                            <div class="mb-3">
                                                <label for="limiteCasosSurto" class="form-label">Limite de Casos</label>
                                                <input type="number" class="form-control" id="limiteCasosSurto" 
                                                       value="5" min="3" max="20">
                                                <small class="text-muted">Casos com mesmo sintoma no bairro</small>
                                            </div>
                                            <div class="mb-3">
                                                <label for="periodoSurto" class="form-label">Período de Análise</label>
                                                <select class="form-select" id="periodoSurto">
                                                    <option value="1">Último dia</option>
                                                    <option value="3" selected>Últimos 3 dias</option>
                                                    <option value="7">Últimos 7 dias</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6>Alta Demanda</h6>
                                            <div class="mb-3">
                                                <label for="percentualAumento" class="form-label">Percentual de Aumento</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="percentualAumento" 
                                                           value="150" min="100" max="500">
                                                    <span class="input-group-text">%</span>
                                                </div>
                                                <small class="text-muted">Aumento em relação à média histórica</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h6>Equipamento Crítico</h6>
                                            <div class="mb-3">
                                                <label for="tempoInatividade" class="form-label">Tempo de Inatividade</label>
                                                <div class="input-group">
                                                    <input type="number" class="form-control" id="tempoInatividade" 
                                                           value="2" min="1" max="24">
                                                    <span class="input-group-text">horas</span>
                                                </div>
                                                <small class="text-muted">Tempo sem uso para gerar alerta</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notificações por Email -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="bi bi-envelope"></i> Notificações por Email
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check form-switch mb-3">
                                                <input class="form-check-input" type="checkbox" id="notificarEmail" 
                                                       <?= $configuracoes['notificar_email'] ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="notificarEmail">
                                                    <strong>Habilitar Notificações por Email</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">
                                                Enviar emails automáticos para responsáveis quando notificações forem geradas
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="severidadeEmail" class="form-label">Severidade Mínima</label>
                                            <select class="form-select" id="severidadeEmail">
                                                <option value="baixa" <?= $configuracoes['severidade_email'] === 'baixa' ? 'selected' : '' ?>>Baixa</option>
                                                <option value="media" <?= $configuracoes['severidade_email'] === 'media' ? 'selected' : '' ?>>Média</option>
                                                <option value="alta" <?= $configuracoes['severidade_email'] === 'alta' ? 'selected' : '' ?>>Alta</option>
                                                <option value="critica" <?= $configuracoes['severidade_email'] === 'critica' ? 'selected' : '' ?>>Apenas Crítica</option>
                                            </select>
                                            <small class="text-muted">Severidade mínima para envio de email</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <label for="emailsDestino" class="form-label">Emails de Destino</label>
                                        <textarea class="form-control" id="emailsDestino" rows="3" 
                                                  placeholder="admin@municipio.gov.br&#10;coordenador@saude.gov.br&#10;gestor@prontoatendimento.gov.br">admin@municipio.gov.br
coordenador@saude.gov.br</textarea>
                                        <small class="text-muted">Um email por linha</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Retenção de Dados -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="bi bi-database"></i> Retenção de Dados
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="retencaoDados" class="form-label">Período de Retenção</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="retencaoDados" 
                                                       value="<?= $configuracoes['retencao_dados'] ?>" min="30" max="365">
                                                <span class="input-group-text">dias</span>
                                            </div>
                                            <small class="text-muted">Notificações resolvidas serão mantidas por este período</small>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-end h-100">
                                                <button type="button" class="btn btn-outline-warning" id="btnLimparDados">
                                                    <i class="bi bi-trash"></i> Limpar Dados Antigos
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <button type="button" class="btn btn-outline-danger" id="btnRestaurarPadrao">
                                                <i class="bi bi-arrow-clockwise"></i> Restaurar Padrão
                                            </button>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-secondary me-2" onclick="window.history.back()">
                                                <i class="bi bi-x-circle"></i> Cancelar
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check-circle"></i> Salvar Configurações
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Sidebar com Informações -->
                    <div class="col-lg-4">
                        <!-- Status do Sistema -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="bi bi-activity"></i> Status do Sistema
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="status-indicator bg-success me-2"></div>
                                    <strong>Sistema Ativo</strong>
                                </div>
                                <small class="text-muted">
                                    <strong>Última análise:</strong><br>
                                    <?= date('d/m/Y H:i:s') ?><br><br>
                                    <strong>Próxima análise:</strong><br>
                                    <?= date('d/m/Y H:i:s', strtotime('+' . $configuracoes['intervalo_analise'] . ' minutes')) ?>
                                </small>
                            </div>
                        </div>

                        <!-- Ações Rápidas -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="bi bi-lightning"></i> Ações Rápidas
                                </h5>
                            </div>
                            <div class="card-body">
                                <button type="button" class="btn btn-outline-primary w-100 mb-2" id="btnExecutarAnalise">
                                    <i class="bi bi-cpu"></i> Executar Análise Manual
                                </button>
                                <button type="button" class="btn btn-outline-info w-100 mb-2" id="btnTestarEmail">
                                    <i class="bi bi-envelope-check"></i> Testar Email
                                </button>
                                <a href="<?= base_url('notificacoes/relatorio') ?>" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-file-earmark-text"></i> Ver Relatório
                                </a>
                            </div>
                        </div>

                        <!-- Informações de Ajuda -->
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="bi bi-question-circle"></i> Ajuda
                                </h5>
                            </div>
                            <div class="card-body">
                                <h6>Como funciona?</h6>
                                <p class="small">
                                    O sistema de notificações BI analisa continuamente os dados do pronto atendimento 
                                    para identificar padrões anômalos e situações que requerem atenção.
                                </p>
                                
                                <h6>Tipos de Análise:</h6>
                                <ul class="small">
                                    <li><strong>Pacientes Recorrentes:</strong> Identifica pacientes com múltiplos atendimentos</li>
                                    <li><strong>Surtos:</strong> Detecta concentrações de sintomas por localização</li>
                                    <li><strong>Alta Demanda:</strong> Alerta sobre picos de atendimento</li>
                                    <li><strong>Equipamentos:</strong> Monitora uso e disponibilidade</li>
                                </ul>
                                
                                <h6>Severidades:</h6>
                                <ul class="small">
                                    <li><span class="badge bg-danger">Crítica</span> - Ação imediata necessária</li>
                                    <li><span class="badge bg-warning">Alta</span> - Atenção prioritária</li>
                                    <li><span class="badge bg-info">Média</span> - Monitoramento</li>
                                    <li><span class="badge bg-success">Baixa</span> - Informativo</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php echo $this->include('components/footer'); ?>
        </div>
    </main>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('formConfiguracoes');
        
        // Submissão do formulário
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            salvarConfiguracoes();
        });

        // Restaurar padrão
        document.getElementById('btnRestaurarPadrao').addEventListener('click', function() {
            if (confirm('Tem certeza que deseja restaurar as configurações padrão? Esta ação não pode ser desfeita.')) {
                restaurarPadrao();
            }
        });

        // Executar análise manual
        document.getElementById('btnExecutarAnalise').addEventListener('click', executarAnalise);

        // Testar email
        document.getElementById('btnTestarEmail').addEventListener('click', testarEmail);

        // Limpar dados antigos
        document.getElementById('btnLimparDados').addEventListener('click', function() {
            if (confirm('Tem certeza que deseja limpar os dados antigos? Esta ação não pode ser desfeita.')) {
                limparDadosAntigos();
            }
        });

        // Habilitar/desabilitar campos relacionados
        document.getElementById('analiseAutomatica').addEventListener('change', function() {
            document.getElementById('intervaloAnalise').disabled = !this.checked;
        });

        document.getElementById('notificarEmail').addEventListener('change', function() {
            document.getElementById('severidadeEmail').disabled = !this.checked;
            document.getElementById('emailsDestino').disabled = !this.checked;
        });

        // Inicializar estado dos campos
        document.getElementById('intervaloAnalise').disabled = !document.getElementById('analiseAutomatica').checked;
        document.getElementById('severidadeEmail').disabled = !document.getElementById('notificarEmail').checked;
        document.getElementById('emailsDestino').disabled = !document.getElementById('notificarEmail').checked;
    });

    function salvarConfiguracoes() {
        const dados = {
            analise_automatica: document.getElementById('analiseAutomatica').checked,
            intervalo_analise: document.getElementById('intervaloAnalise').value,
            limite_atendimentos_paciente: document.getElementById('limiteAtendimentosPaciente').value,
            periodo_recorrencia: document.getElementById('periodoRecorrencia').value,
            limite_casos_surto: document.getElementById('limiteCasosSurto').value,
            periodo_surto: document.getElementById('periodoSurto').value,
            percentual_aumento: document.getElementById('percentualAumento').value,
            tempo_inatividade: document.getElementById('tempoInatividade').value,
            notificar_email: document.getElementById('notificarEmail').checked,
            severidade_email: document.getElementById('severidadeEmail').value,
            emails_destino: document.getElementById('emailsDestino').value,
            retencao_dados: document.getElementById('retencaoDados').value
        };

        fetch('<?= base_url("notificacoes/salvarConfiguracoes") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(dados)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Configurações salvas com sucesso!');
            } else {
                showAlert('error', data.message || 'Erro ao salvar configurações');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('error', 'Erro ao salvar configurações');
        });
    }

    function restaurarPadrao() {
        fetch('<?= base_url("notificacoes/restaurarPadrao") ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Configurações restauradas!');
                setTimeout(() => window.location.reload(), 2000);
            } else {
                showAlert('error', data.message || 'Erro ao restaurar configurações');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('error', 'Erro ao restaurar configurações');
        });
    }

    function executarAnalise() {
        const btn = document.getElementById('btnExecutarAnalise');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Executando...';

        fetch('<?= base_url("notificacoes/executarAnalise") ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', `Análise concluída! ${data.notificacoes_geradas || 0} notificações geradas.`);
            } else {
                showAlert('error', data.message || 'Erro na análise');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('error', 'Erro ao executar análise');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    function testarEmail() {
        const btn = document.getElementById('btnTestarEmail');
        const originalText = btn.innerHTML;

        btn.disabled = true;
        btn.innerHTML = '<div class="spinner-border spinner-border-sm me-2"></div>Enviando...';

        fetch('<?= base_url("notificacoes/testarEmail") ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'Email de teste enviado com sucesso!');
            } else {
                showAlert('error', data.message || 'Erro ao enviar email');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('error', 'Erro ao enviar email de teste');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    function limparDadosAntigos() {
        fetch('<?= base_url("notificacoes/limparDadosAntigos") ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', `${data.registros_removidos || 0} registros antigos foram removidos.`);
            } else {
                showAlert('error', data.message || 'Erro ao limpar dados');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('error', 'Erro ao limpar dados antigos');
        });
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
                <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'}"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', alertHtml);

        setTimeout(() => {
            const alert = document.querySelector('.alert.position-fixed');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    }
</script>

<style>
.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    display: inline-block;
}
</style>
<?= $this->endSection() ?>
