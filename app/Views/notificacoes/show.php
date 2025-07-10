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
                <h1>
                    <i class="bi bi-eye"></i> Detalhes da Notificação
                </h1>
                <p class="subtitle"><?= esc($notificacao['titulo']) ?></p>
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
                    <!-- Informações Principais -->
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title">
                                    <?php 
                                    $tipo = $notificacao['tipo'];
                                    include APPPATH . 'Views/notificacoes/partials/icon.php'; 
                                    ?>
                                    Informações da Notificação
                                </h5>
                                <span class="badge bg-<?php 
                                    $severidade = $notificacao['severidade'];
                                    include APPPATH . 'Views/notificacoes/partials/badge_color.php'; 
                                ?> fs-6">
                                    <?= ucfirst($notificacao['severidade']) ?>
                                </span>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Tipo:</strong><br>
                                        <?php
                                        $tipos = [
                                            'paciente_recorrente' => 'Paciente Recorrente',
                                            'surto_sintomas' => 'Surto de Sintomas',
                                            'alta_demanda' => 'Alta Demanda',
                                            'medicamento_critico' => 'Medicamento Crítico',
                                            'equipamento_falha' => 'Falha de Equipamento',
                                            'estatistica_anomala' => 'Estatística Anômala'
                                        ];
                                        echo $tipos[$notificacao['tipo']] ?? ucfirst(str_replace('_', ' ', $notificacao['tipo']));
                                        ?>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Módulo:</strong><br>
                                        <?= esc($notificacao['modulo']) ?>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <strong>Status:</strong><br>
                                        <span class="badge bg-<?= $notificacao['status'] === 'ativa' ? 'warning' : ($notificacao['status'] === 'resolvida' ? 'success' : 'secondary') ?>">
                                            <?= ucfirst($notificacao['status']) ?>
                                        </span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Acionada em:</strong><br>
                                        <?= date('d/m/Y H:i:s', strtotime($notificacao['acionada_em'])) ?>
                                    </div>
                                </div>

                                <?php if ($notificacao['data_vencimento']): ?>
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <strong>Vencimento:</strong><br>
                                            <?= date('d/m/Y H:i:s', strtotime($notificacao['data_vencimento'])) ?>
                                            <?php 
                                            $vencimento = new DateTime($notificacao['data_vencimento']);
                                            $agora = new DateTime();
                                            if ($vencimento < $agora): 
                                            ?>
                                                <span class="badge bg-danger ms-2">Vencida</span>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($notificacao['resolvida_em']): ?>
                                            <div class="col-md-6">
                                                <strong>Resolvida em:</strong><br>
                                                <?= date('d/m/Y H:i:s', strtotime($notificacao['resolvida_em'])) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <strong>Descrição:</strong><br>
                                    <div class="border rounded p-3 bg-light">
                                        <?= nl2br(esc($notificacao['descricao'])) ?>
                                    </div>
                                </div>

                                <!-- Parâmetros específicos -->
                                <?php if (!empty($notificacao['parametros'])): ?>
                                    <div class="mb-3">
                                        <strong>Detalhes Específicos:</strong><br>
                                        <div class="border rounded p-3 bg-light">
                                            <?php 
                                            $parametros = $notificacao['parametros'];
                                            $tipo = $notificacao['tipo'];
                                            include APPPATH . 'Views/notificacoes/partials/parametros.php'; 
                                            ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Metadata -->
                                <?php if (!empty($notificacao['metadata'])): ?>
                                    <div class="mb-3">
                                        <strong>Informações Adicionais:</strong><br>
                                        <div class="border rounded p-3 bg-light">
                                            <?php foreach ($notificacao['metadata'] as $chave => $valor): ?>
                                                <?php if (is_array($valor) || is_object($valor)) continue; ?>
                                                <div class="row mb-2">
                                                    <div class="col-md-4">
                                                        <strong><?= ucfirst(str_replace('_', ' ', $chave)) ?>:</strong>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <?= esc($valor) ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Dados Complementares -->
                        <?php if (!empty($dados_complementares)): ?>
                            <div class="card mt-4">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="bi bi-info-circle"></i> Dados Complementares
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($dados_complementares as $secao => $dados): ?>
                                        <h6><?= ucfirst(str_replace('_', ' ', $secao)) ?></h6>
                                        <div class="table-responsive mb-3">
                                            <table class="table table-sm">
                                                <?php foreach ($dados as $item): ?>
                                                    <tr>
                                                        <?php foreach ($item as $chave => $valor): ?>
                                                            <td>
                                                                <strong><?= ucfirst(str_replace('_', ' ', $chave)) ?>:</strong>
                                                                <?= esc($valor) ?>
                                                            </td>
                                                        <?php endforeach; ?>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </table>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sidebar com Ações -->
                    <div class="col-lg-4">
                        <!-- Ações -->
                        <?php if ($notificacao['status'] === 'ativa'): ?>
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="bi bi-tools"></i> Ações
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <button type="button" 
                                            class="btn btn-success w-100 mb-2"
                                            onclick="resolverNotificacao(<?= $notificacao['id'] ?>)">
                                        <i class="bi bi-check-circle"></i> Marcar como Resolvida
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-danger w-100"
                                            onclick="cancelarNotificacao(<?= $notificacao['id'] ?>)">
                                        <i class="bi bi-x-circle"></i> Cancelar Notificação
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Ações Sugeridas -->
                        <?php if (!empty($acoes_sugeridas)): ?>
                            <div class="card mt-3">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="bi bi-lightbulb"></i> Ações Sugeridas
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($acoes_sugeridas as $acao): ?>
                                        <div class="alert alert-info">
                                            <strong><?= esc($acao['titulo']) ?></strong><br>
                                            <small><?= esc($acao['descricao']) ?></small>
                                            <?php if (isset($acao['link'])): ?>
                                                <br><a href="<?= $acao['link'] ?>" class="btn btn-sm btn-primary mt-2">
                                                    <?= $acao['texto_link'] ?? 'Executar' ?>
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Informações Técnicas -->
                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="bi bi-info-square"></i> Informações Técnicas
                                </h5>
                            </div>
                            <div class="card-body">
                                <small class="text-muted">
                                    <strong>ID:</strong> <?= $notificacao['id'] ?><br>
                                    <strong>Criada:</strong> <?= date('d/m/Y H:i:s', strtotime($notificacao['created_at'])) ?><br>
                                    <strong>Última atualização:</strong> <?= date('d/m/Y H:i:s', strtotime($notificacao['updated_at'])) ?><br>
                                    <?php if ($notificacao['usuario_responsavel']): ?>
                                        <strong>Responsável:</strong> <?= esc($notificacao['usuario_responsavel']) ?><br>
                                    <?php endif; ?>
                                </small>
                            </div>
                        </div>
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
                <div class="mb-3">
                    <label for="observacaoResolucao" class="form-label">Observações (opcional)</label>
                    <textarea class="form-control" id="observacaoResolucao" rows="3" 
                              placeholder="Descreva as ações tomadas ou observações sobre a resolução..."></textarea>
                </div>
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
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    Esta ação cancelará a notificação permanentemente.
                </div>
                <div class="mb-3">
                    <label for="motivoCancelamento" class="form-label">Motivo do cancelamento <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="motivoCancelamento" rows="3" 
                              placeholder="Explique o motivo do cancelamento..." required></textarea>
                </div>
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
<script>
    let notificacaoAtual = <?= $notificacao['id'] ?>;

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

    document.getElementById('btnConfirmarResolver').addEventListener('click', function() {
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
                    window.location.href = '<?= base_url("notificacoes") ?>';
                }, 2000);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('error', 'Erro ao resolver notificação');
        });

        bootstrap.Modal.getInstance(document.getElementById('modalResolver')).hide();
    });

    document.getElementById('btnConfirmarCancelar').addEventListener('click', function() {
        const motivo = document.getElementById('motivoCancelamento').value;

        if (!motivo.trim()) {
            showAlert('error', 'Informe o motivo do cancelamento');
            return;
        }

        fetch(`<?= base_url("notificacoes/cancelar") ?>/${notificacaoAtual}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                motivo: motivo
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                setTimeout(() => {
                    window.location.href = '<?= base_url("notificacoes") ?>';
                }, 2000);
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showAlert('error', 'Erro ao cancelar notificação');
        });

        bootstrap.Modal.getInstance(document.getElementById('modalCancelar')).hide();
    });

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

        // Auto-remove após 5 segundos
        setTimeout(() => {
            const alert = document.querySelector('.alert.position-fixed');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    }
</script>
<?= $this->endSection() ?>
