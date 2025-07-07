<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-gear"></i> Detalhes do Procedimento</h1>
                <p class="subtitle">Visualização completa do procedimento realizado</p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="m-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('atendimento-procedimentos') ?>">Procedimentos</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('atendimentos/show/' . $atendimentoProcedimento['id_atendimento']) ?>">Atendimento #<?= $atendimentoProcedimento['id_atendimento'] ?></a></li>
                    <li class="breadcrumb-item active">Procedimento #<?= $atendimentoProcedimento['id_atendimento_procedimento'] ?></li>
                </ol>
            </nav>

            <!-- Actions -->
            <div class="action-bar">
                <div class="action-left m-4"></div>
                <div class="action-right m-4">
                    <a href="<?= base_url('atendimento-procedimentos/edit/' . $atendimentoProcedimento['id_atendimento_procedimento']) ?>" 
                       class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="<?= base_url('atendimentos/show/' . $atendimentoProcedimento['id_atendimento']) ?>" 
                       class="btn btn-info">
                        <i class="bi bi-file-medical"></i> Ver Atendimento
                    </a>
                    <button type="button" class="btn btn-danger" 
                            onclick="confirmarExclusao(<?= $atendimentoProcedimento['id_atendimento_procedimento'] ?>)">
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
                            <p><strong>Nome:</strong> <?= esc($atendimentoProcedimento['nome_paciente']) ?></p>
                            <p><strong>CPF:</strong> <?= esc($atendimentoProcedimento['cpf']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Médico:</strong> <?= esc($atendimentoProcedimento['nome_medico']) ?></p>
                            <p><strong>Data do Atendimento:</strong> <?= date('d/m/Y H:i', strtotime($atendimentoProcedimento['data_atendimento'])) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informações do Procedimento -->
            <div class="card m-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-gear"></i> Informações do Procedimento</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h6 class="text-primary"><?= esc($atendimentoProcedimento['nome_procedimento']) ?></h6>
                            <?php if ($atendimentoProcedimento['codigo_procedimento']): ?>
                                <p><strong>Código:</strong> 
                                    <span class="badge bg-secondary"><?= esc($atendimentoProcedimento['codigo_procedimento']) ?></span>
                                </p>
                            <?php endif; ?>
                            <?php if ($atendimentoProcedimento['descricao_procedimento']): ?>
                                <p><strong>Descrição:</strong></p>
                                <div class="alert alert-light">
                                    <?= nl2br(esc($atendimentoProcedimento['descricao_procedimento'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex flex-column gap-3">
                                <div class="stat-item text-center">
                                    <div class="stat-number text-primary"><?= $atendimentoProcedimento['quantidade'] ?></div>
                                    <div class="stat-label">Quantidade Realizada</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($atendimentoProcedimento['observacao']): ?>
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6><i class="bi bi-journal-text"></i> Observações</h6>
                                <div class="alert alert-info">
                                    <?= nl2br(esc($atendimentoProcedimento['observacao'])) ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Informações do Sistema -->
            <div class="card m-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Informações do Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Data de Criação:</strong> 
                                <?= date('d/m/Y H:i:s', strtotime($atendimentoProcedimento['created_at'])) ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <?php if ($atendimentoProcedimento['updated_at']): ?>
                                <p><strong>Última Atualização:</strong> 
                                    <?= date('d/m/Y H:i:s', strtotime($atendimentoProcedimento['updated_at'])) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions Footer -->
            <div class="form-actions m-4">
                <a href="<?= base_url('atendimento-procedimentos') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar à Lista
                </a>
                <a href="<?= base_url('atendimentos/show/' . $atendimentoProcedimento['id_atendimento']) ?>" class="btn btn-outline-info">
                    <i class="bi bi-file-medical"></i> Ver Atendimento Completo
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
                <p>Tem certeza que deseja remover este procedimento do atendimento?</p>
                <div class="alert alert-warning">
                    <strong>Procedimento:</strong> <?= esc($atendimentoProcedimento['nome_procedimento']) ?><br>
                    <strong>Quantidade:</strong> <?= $atendimentoProcedimento['quantidade'] ?>
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
let deleteId = null;

function confirmarExclusao(id) {
    deleteId = id;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDelete').addEventListener('click', function() {
    if (deleteId) {
        // Criar form para envio via POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('atendimento-procedimentos/delete/') ?>' + deleteId;
        
        // Adicionar token CSRF se necessário
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
