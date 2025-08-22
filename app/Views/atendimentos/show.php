<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-clipboard-check"></i> Detalhes do Atendimento</h1>
                <p class="subtitle">Visualização completa do atendimento médico</p>
            </div>

            <div class="content-wrapper">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="my-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('atendimentos') ?>">Atendimentos</a></li>
                        <li class="breadcrumb-item active">Atendimento #<?= $atendimento['id_atendimento'] ?></li>
                    </ol>
                </nav>

                <!-- Action Buttons -->
                <div class="action-bar">
                    <div class="action-left m-4">
                        <a href="<?= base_url('atendimentos') ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                    <div class="action-right m-4">
                        <a href="<?= base_url('atendimentos/edit/' . $atendimento['id_atendimento']) ?>" class="btn btn-primary">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <button type="button" class="btn btn-outline-secondary" onclick="window.print()">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                        <button type="button" class="btn btn-danger" id="btnExcluir" data-atendimento-id="<?= $atendimento['id_atendimento'] ?>">
                            <i class="bi bi-trash"></i> Excluir
                        </button>
                    </div>
                </div>

                <div class="row my-4">
                    <!-- Informações Básicas -->
                    <div class="col-lg-8">
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-info-circle"></i> Informações do Atendimento
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <strong>ID do Atendimento:</strong>
                                            <span>#<?= $atendimento['id_atendimento'] ?></span>
                                        </div>
                                        <div class="info-item">
                                            <strong>Data/Hora:</strong>
                                            <span><?= date('d/m/Y H:i', strtotime($atendimento['data_atendimento'])) ?></span>
                                        </div>
                                        <div class="info-item">
                                            <strong>Classificação de Risco:</strong>
                                            <?php if (!empty($atendimento['classificacao_risco']) && $atendimento['classificacao_risco'] != 'Sem classificação'): ?>
                                                <span class="badge bg-<?=
                                                                        $atendimento['classificacao_risco'] == 'Vermelho' ? 'danger' : ($atendimento['classificacao_risco'] == 'Laranja' ? 'orange' : ($atendimento['classificacao_risco'] == 'Amarelo' ? 'warning' : ($atendimento['classificacao_risco'] == 'Verde' ? 'success' : 'info')))
                                                                        ?> fs-6">
                                                    <?= $atendimento['classificacao_risco'] ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary fs-6">
                                                    <i class="bi bi-question-circle"></i> Sem classificação
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <strong>Status:</strong>
                                            <?php if ($atendimento['obito']): ?>
                                                <span class="badge bg-dark fs-6">Óbito</span>
                                            <?php elseif ($atendimento['encaminhamento']): ?>
                                                <span class="badge bg-secondary fs-6"><?= $atendimento['encaminhamento'] ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-primary fs-6">Em Atendimento</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="info-item">
                                            <strong>Paciente em Observação:</strong>
                                            <?php if (isset($atendimento['paciente_observacao']) && $atendimento['paciente_observacao'] == 'Sim'): ?>
                                                <span class="badge bg-warning text-dark fs-6">
                                                    <i class="bi bi-eye"></i> Sim
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Não</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="info-item">
                                            <strong>Criado em:</strong>
                                            <span><?= date('d/m/Y H:i', strtotime($atendimento['created_at'])) ?></span>
                                        </div>
                                        <?php if ($atendimento['updated_at']): ?>
                                            <div class="info-item">
                                                <strong>Última atualização:</strong>
                                                <span><?= date('d/m/Y H:i', strtotime($atendimento['updated_at'])) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dados do Paciente -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-person"></i> Dados do Paciente
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <strong>Nome:</strong>
                                            <span><?= esc($atendimento['paciente_nome']) ?></span>
                                        </div>
                                        <div class="info-item">
                                            <strong>CPF:</strong>
                                            <span><?= $atendimento['cpf'] ?></span>
                                        </div>
                                        <?php if (isset($atendimento['data_nascimento'])): ?>
                                            <div class="info-item">
                                                <strong>Data de Nascimento:</strong>
                                                <span><?= date('d/m/Y', strtotime($atendimento['data_nascimento'])) ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if (isset($atendimento['sexo'])): ?>
                                            <div class="info-item">
                                                <strong>Sexo:</strong>
                                                <span><?= $atendimento['sexo'] == 'M' ? 'Masculino' : 'Feminino' ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="text-end">
                                            <a href="<?= base_url('pacientes/show/' . $atendimento['id_paciente']) ?>"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> Ver Paciente
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dados do Médico -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-person-badge"></i> Dados do Médico
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="info-item">
                                            <strong>Nome:</strong>
                                            <span><?= esc($atendimento['medico_nome']) ?></span>
                                        </div>
                                        <div class="info-item">
                                            <strong>CRM:</strong>
                                            <span><?= $atendimento['crm'] ?></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <?php if (isset($atendimento['especialidade'])): ?>
                                            <div class="info-item">
                                                <strong>Especialidade:</strong>
                                                <span><?= $atendimento['especialidade'] ?></span>
                                            </div>
                                        <?php endif; ?>
                                        <div class="text-end">
                                            <a href="<?= base_url('medicos/show/' . $atendimento['id_medico']) ?>"
                                                class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye"></i> Ver Médico
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar with additional info -->
                    <div class="col-lg-4">
                        <!-- Dados Vitais -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-heart-pulse"></i> Dados Vitais
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($atendimento['hgt_glicemia']): ?>
                                    <div class="info-item">
                                        <strong>Glicemia:</strong>
                                        <span><?= $atendimento['hgt_glicemia'] ?> mg/dL</span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($atendimento['pressao_arterial']): ?>
                                    <div class="info-item">
                                        <strong>Pressão Arterial:</strong>
                                        <span><?= $atendimento['pressao_arterial'] ?></span>
                                    </div>
                                <?php endif; ?>

                                <?php if (isset($atendimento['temperatura']) && $atendimento['temperatura']): ?>
                                    <div class="info-item">
                                        <strong>Temperatura:</strong>
                                        <span><?= $atendimento['temperatura'] ?>°C</span>
                                    </div>
                                <?php endif; ?>

                                <?php if (!$atendimento['hgt_glicemia'] && !$atendimento['pressao_arterial'] && (!isset($atendimento['temperatura']) || !$atendimento['temperatura'])): ?>
                                    <p class="text-muted">Nenhum dado vital registrado</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Procedimentos Realizados -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-list-check"></i> Procedimentos
                                </h5>
                                <a href="<?= base_url('atendimento-procedimentos/create?atendimento=' . $atendimento['id_atendimento']) ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus"></i> Adicionar
                                </a>
                            </div>
                            <div class="card-body">
                                <?php if (isset($procedimentos) && !empty($procedimentos)): ?>
                                    <?php foreach ($procedimentos as $proc): ?>
                                        <div class="procedure-item">
                                            <strong><?= esc($proc['procedimento_nome']) ?></strong>
                                            <?php if ($proc['quantidade'] > 1): ?>
                                                <span class="badge bg-secondary"><?= $proc['quantidade'] ?>x</span>
                                            <?php endif; ?>
                                            <?php if ($proc['observacao']): ?>
                                                <small class="d-block text-muted"><?= esc($proc['observacao']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">Nenhum procedimento realizado</p>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Exames Solicitados -->
                        <div class="card mb-4">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-clipboard2-data"></i> Exames
                                </h5>
                                <a href="<?= base_url('atendimento-exames/create?atendimento=' . $atendimento['id_atendimento']) ?>" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-plus"></i> Solicitar
                                </a>
                            </div>
                            <div class="card-body">
                                <?php if (isset($exames) && !empty($exames)): ?>
                                    <?php foreach ($exames as $exame): ?>
                                        <div class="exam-item">
                                            <strong><?= esc($exame['nome']) ?></strong>
                                            <span class="badge bg-<?=
                                                                    $exame['status'] == 'Realizado' ? 'success' : ($exame['status'] == 'Cancelado' ? 'danger' : 'warning')
                                                                    ?>"><?= $exame['status'] ?></span>
                                            <?php if (isset($exame['observacao']) && $exame['observacao']): ?>
                                                <small class="d-block text-muted"><?= esc($exame['observacao']) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p class="text-muted">Nenhum exame solicitado</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observações Clínicas -->
                <div class="row my-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">
                                    <i class="bi bi-clipboard-pulse"></i> Observações Clínicas
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($atendimento['consulta_enfermagem']): ?>
                                    <div class="clinical-note">
                                        <h6><i class="bi bi-clipboard-pulse"></i> Consulta de Enfermagem</h6>
                                        <p><?= nl2br(esc($atendimento['consulta_enfermagem'])) ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if ($atendimento['hipotese_diagnostico']): ?>
                                    <div class="clinical-note">
                                        <h6><i class="bi bi-clipboard-check"></i> Hipótese Diagnóstica</h6>
                                        <p><?= nl2br(esc($atendimento['hipotese_diagnostico'])) ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if ($atendimento['observacao']): ?>
                                    <div class="clinical-note">
                                        <h6><i class="bi bi-chat-text"></i> Observações Gerais</h6>
                                        <p><?= nl2br(esc($atendimento['observacao'])) ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if (!$atendimento['consulta_enfermagem'] && !$atendimento['hipotese_diagnostico'] && !$atendimento['observacao']): ?>
                                    <p class="text-muted">Nenhuma observação clínica registrada</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo $this->include('components/footer') ?>
        </div>
    </main>
</div>

<!-- Modal de confirmação de exclusão -->
<div class="modal fade" id="modalConfirmarExclusao" tabindex="-1" aria-labelledby="modalConfirmarExclusaoLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarExclusaoLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center">
                    <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    <h5 class="mt-3">Tem certeza?</h5>
                    <p>Esta ação não pode ser desfeita. O atendimento será excluído permanentemente.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarExclusaoBtn">Excluir Atendimento</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - iniciando script de exclusão');
    
    let atendimentoParaExcluir = null;

    function confirmarExclusao(id) {
        console.log('confirmarExclusao chamado com ID:', id);
        atendimentoParaExcluir = id;
        const modalElement = document.getElementById('modalConfirmarExclusao');
        console.log('Modal element found:', modalElement);
        
        if (modalElement) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            console.error('Modal não encontrado!');
        }
    }

    // Event listener para o botão de excluir
    const btnExcluir = document.getElementById('btnExcluir');
    console.log('Botão excluir encontrado:', btnExcluir);
    
    if (btnExcluir) {
        btnExcluir.addEventListener('click', function() {
            console.log('Botão excluir clicado');
            const atendimentoId = this.getAttribute('data-atendimento-id');
            console.log('ID do atendimento:', atendimentoId);
            confirmarExclusao(parseInt(atendimentoId));
        });
    } else {
        console.error('Botão excluir não encontrado!');
    }

    // Event listener para confirmar a exclusão
    const confirmarExclusaoBtn = document.getElementById('confirmarExclusaoBtn');
    console.log('Botão confirmar exclusão encontrado:', confirmarExclusaoBtn);
    
    if (confirmarExclusaoBtn) {
        confirmarExclusaoBtn.addEventListener('click', function() {
            console.log('Botão confirmar exclusão clicado, ID:', atendimentoParaExcluir);
            
            if (atendimentoParaExcluir) {
                fetch(`<?= base_url('atendimentos/delete/') ?>${atendimentoParaExcluir}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        showAlert('success', data.success || 'Atendimento excluído com sucesso!');
                        // Redirecionar para lista de atendimentos após 1.5 segundos
                        setTimeout(() => {
                            window.location.href = '<?= base_url('atendimentos') ?>';
                        }, 1500);
                    } else {
                        showAlert('error', data.error || 'Erro ao excluir atendimento');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'Erro ao excluir atendimento');
                })
                .finally(() => {
                    const modalElement = document.getElementById('modalConfirmarExclusao');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    if (modalInstance) {
                        modalInstance.hide();
                    }
                    atendimentoParaExcluir = null;
                });
            }
        });
    } else {
        console.error('Botão confirmar exclusão não encontrado!');
    }

    function showAlert(type, message) {
        const alertsContainer = document.querySelector('.alerts-container') || document.body;
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertsContainer.appendChild(alertDiv);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.remove();
            }
        }, 5000);
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .info-item {
        margin-bottom: 0.75rem;
    }

    .info-item strong {
        color: #495057;
        margin-right: 0.5rem;
    }

    .clinical-note {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background-color: #f8f9fa;
        border-left: 4px solid #007bff;
        border-radius: 0.375rem;
    }

    .clinical-note h6 {
        color: #007bff;
        margin-bottom: 0.5rem;
    }

    .procedure-item,
    .exam-item {
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #e9ecef;
    }

    .procedure-item:last-child,
    .exam-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .card-header .btn-sm {
        font-size: 0.8rem;
        padding: 0.25rem 0.5rem;
    }

    .card-header .btn-sm i {
        font-size: 0.75rem;
    }

    @media print {

        .action-bar,
        .sidebar,
        .topbar {
            display: none !important;
        }

        .main-content {
            margin-left: 0 !important;
            margin-top: 0 !important;
        }

        .card {
            border: 1px solid #dee2e6 !important;
            box-shadow: none !important;
        }
    }
</style>
<?= $this->endSection() ?>