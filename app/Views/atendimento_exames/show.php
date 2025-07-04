<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-clipboard-check"></i> Detalhes do Exame</h1>
                <p class="subtitle">Visualização completa da solicitação de exame</p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="m-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/atendimento-exames">Exames</a></li>
                    <li class="breadcrumb-item"><a href="/atendimentos/show/<?= $atendimentoExame['id_atendimento'] ?>">Atendimento #<?= $atendimentoExame['id_atendimento'] ?></a></li>
                    <li class="breadcrumb-item active">Exame #<?= $atendimentoExame['id_atendimento_exame'] ?></li>
                </ol>
            </nav>

            <!-- Actions -->
            <div class="action-bar">
                <div class="action-left m-4"></div>
                <div class="action-right m-4">
                    <a href="/atendimento-exames/print/<?= $atendimentoExame['id_atendimento_exame'] ?>" 
                       class="btn btn-info" target="_blank">
                        <i class="bi bi-printer"></i> Imprimir Solicitação
                    </a>
                    <a href="/atendimento-exames/edit/<?= $atendimentoExame['id_atendimento_exame'] ?>" 
                       class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="/atendimentos/show/<?= $atendimentoExame['id_atendimento'] ?>" 
                       class="btn btn-outline-info">
                        <i class="bi bi-file-medical"></i> Ver Atendimento
                    </a>
                    <button type="button" class="btn btn-danger" 
                            onclick="confirmarExclusao(<?= $atendimentoExame['id_atendimento_exame'] ?>)">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                </div>
            </div>

            <!-- Informações do Paciente -->
            <div class="card m-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-person"></i> Informações do Paciente</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nome:</strong> <?= esc($atendimentoExame['nome_paciente']) ?></p>
                            <p><strong>CPF:</strong> <?= esc($atendimentoExame['cpf']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Médico Solicitante:</strong> <?= esc($atendimentoExame['nome_medico']) ?></p>
                            <p><strong>Data do Atendimento:</strong> <?= date('d/m/Y H:i', strtotime($atendimentoExame['data_atendimento'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status do Exame -->
            <div class="card m-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-clock"></i> Status do Exame</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center gap-3">
                                <div>
                                    <h6 class="mb-1">Status Atual:</h6>
                                    <span class="badge 
                                        <?php 
                                        switch($atendimentoExame['status']) {
                                            case 'Solicitado': echo 'bg-warning'; break;
                                            case 'Realizado': echo 'bg-success'; break;
                                            case 'Cancelado': echo 'bg-danger'; break;
                                            default: echo 'bg-secondary';
                                        }
                                        ?> fs-6">
                                        <?= esc($atendimentoExame['status']) ?>
                                    </span>
                                </div>
                                <div class="vr"></div>
                                <div>
                                    <h6 class="mb-1">Data de Solicitação:</h6>
                                    <span><?= date('d/m/Y H:i', strtotime($atendimentoExame['data_solicitacao'])) ?></span>
                                </div>
                                <?php if ($atendimentoExame['data_realizacao']): ?>
                                    <div class="vr"></div>
                                    <div>
                                        <h6 class="mb-1">Data de Realização:</h6>
                                        <span><?= date('d/m/Y H:i', strtotime($atendimentoExame['data_realizacao'])) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <select class="form-select status-select" 
                                    data-id="<?= $atendimentoExame['id_atendimento_exame'] ?>"
                                    data-original="<?= $atendimentoExame['status'] ?>">
                                <option value="Solicitado" <?= $atendimentoExame['status'] === 'Solicitado' ? 'selected' : '' ?>>Solicitado</option>
                                <option value="Realizado" <?= $atendimentoExame['status'] === 'Realizado' ? 'selected' : '' ?>>Realizado</option>
                                <option value="Cancelado" <?= $atendimentoExame['status'] === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                            </select>
                            <small class="text-muted">Alterar status</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações do Exame -->
            <div class="card m-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-clipboard-check"></i> Informações do Exame</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-primary"><?= esc($atendimentoExame['nome_exame']) ?></h6>
                            <div class="d-flex gap-3 mb-3">
                                <div>
                                    <strong>Tipo:</strong> 
                                    <span class="badge 
                                        <?php 
                                        switch($atendimentoExame['tipo_exame']) {
                                            case 'laboratorial': echo 'bg-primary'; break;
                                            case 'imagem': echo 'bg-info'; break;
                                            case 'funcional': echo 'bg-warning'; break;
                                            default: echo 'bg-secondary';
                                        }
                                        ?>">
                                        <?= ucfirst($atendimentoExame['tipo_exame']) ?>
                                    </span>
                                </div>
                                <?php if ($atendimentoExame['codigo_exame']): ?>
                                    <div>
                                        <strong>Código:</strong> 
                                        <span class="badge bg-secondary"><?= esc($atendimentoExame['codigo_exame']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($atendimentoExame['descricao_exame']): ?>
                                <p><strong>Descrição:</strong></p>
                                <div class="alert alert-light">
                                    <?= nl2br(esc($atendimentoExame['descricao_exame'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($atendimentoExame['observacao']): ?>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6><i class="bi bi-journal-text"></i> Observações da Solicitação</h6>
                                <div class="alert alert-info">
                                    <?= nl2br(esc($atendimentoExame['observacao'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Resultado do Exame -->
            <?php if ($atendimentoExame['resultado'] || $atendimentoExame['status'] === 'Realizado'): ?>
                <div class="card m-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="bi bi-file-text"></i> Resultado do Exame</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($atendimentoExame['resultado']): ?>
                            <div class="alert alert-success">
                                <h6><i class="bi bi-check-circle"></i> Resultado</h6>
                                <?= nl2br(esc($atendimentoExame['resultado'])) ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle"></i> 
                                Exame marcado como realizado mas resultado ainda não foi informado.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Informações do Sistema -->
            <div class="card m-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Informações do Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Data de Criação:</strong> 
                                <?= date('d/m/Y H:i:s', strtotime($atendimentoExame['created_at'])) ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <?php if ($atendimentoExame['updated_at']): ?>
                                <p><strong>Última Atualização:</strong> 
                                    <?= date('d/m/Y H:i:s', strtotime($atendimentoExame['updated_at'])) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Footer -->
            <div class="form-actions m-4">
                <a href="/atendimento-exames" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar à Lista
                </a>
                <a href="/atendimentos/show/<?= $atendimentoExame['id_atendimento'] ?>" class="btn btn-outline-info">
                    <i class="bi bi-file-medical"></i> Ver Atendimento Completo
                </a>
                <a href="/atendimento-exames/print/<?= $atendimentoExame['id_atendimento_exame'] ?>" 
                   class="btn btn-outline-primary" target="_blank">
                    <i class="bi bi-printer"></i> Imprimir Solicitação
                </a>
            </div>
        </div>
    </main>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja remover este exame do atendimento?</p>
                <div class="alert alert-warning">
                    <strong>Exame:</strong> <?= esc($atendimentoExame['nome_exame']) ?><br>
                    <strong>Status:</strong> <?= esc($atendimentoExame['status']) ?>
                </div>
                <p class="text-muted small">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Excluir</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status change
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function() {
            const id = this.dataset.id;
            const newStatus = this.value;
            const originalStatus = this.dataset.original;
            
            if (newStatus !== originalStatus) {
                updateStatus(id, newStatus, this);
            }
        });
    });
});

function updateStatus(id, status, selectElement) {
    const originalValue = selectElement.dataset.original;
    
    // Desabilitar select temporariamente
    selectElement.disabled = true;
    
    fetch('/atendimento-exames/updateStatus', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            id: id,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            selectElement.dataset.original = status;
            showToast('Status atualizado com sucesso!', 'success');
            
            // Recarregar página após 1 segundo
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            // Reverter valor
            selectElement.value = originalValue;
            showToast('Erro ao atualizar status: ' + (data.message || 'Erro desconhecido'), 'error');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        selectElement.value = originalValue;
        showToast('Erro de comunicação com o servidor', 'error');
    })
    .finally(() => {
        selectElement.disabled = false;
    });
}

function showToast(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    setTimeout(() => {
        if (alert.parentNode) {
            alert.parentNode.removeChild(alert);
        }
    }, 5000);
}

let deleteId = null;

function confirmarExclusao(id) {
    deleteId = id;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (deleteId) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/atendimento-exames/delete/' + deleteId;
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            const tokenInput = document.createElement('input');
            tokenInput.type = 'hidden';
            tokenInput.name = '_token';
            tokenInput.value = csrfToken.getAttribute('content');
            form.appendChild(tokenInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
});
</script>

<?= $this->endSection() ?>
