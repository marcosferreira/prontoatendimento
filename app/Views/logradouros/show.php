<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<!-- CSS específico para logradouros -->
<link rel="stylesheet" href="<?= base_url('assets/css/logradouros.css') ?>">

<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-signpost"></i> Detalhes do Logradouro</h1>
                <p class="subtitle"><?= esc($logradouro['tipo_logradouro'] . ' ' . $logradouro['nome_logradouro']) ?></p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url() ?>">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('logradouros') ?>">Logradouros</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <?= esc($logradouro['tipo_logradouro'] . ' ' . $logradouro['nome_logradouro']) ?>
                    </li>
                </ol>
            </nav>

            <!-- Action Bar -->
            <div class="action-bar mb-4">
                <div class="action-left">
                    <a href="<?= base_url('logradouros') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar para Lista
                    </a>
                </div>
                <div class="action-right">
                    <a href="<?= base_url('logradouros/' . $logradouro['id_logradouro'] . '/edit') ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                </div>
            </div>

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
                <div class="col-lg-8">
                    <!-- Dados Principais -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle"></i> Informações Principais
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item mb-3">
                                        <label class="info-label">ID do Logradouro:</label>
                                        <span class="badge bg-secondary fs-6"><?= $logradouro['id_logradouro'] ?></span>
                                    </div>

                                    <div class="info-item mb-3">
                                        <label class="info-label">Tipo:</label>
                                        <span class="badge bg-info fs-6"><?= esc($logradouro['tipo_logradouro']) ?></span>
                                    </div>

                                    <div class="info-item mb-3">
                                        <label class="info-label">Nome do Logradouro:</label>
                                        <div class="info-value">
                                            <strong><?= esc($logradouro['nome_logradouro']) ?></strong>
                                        </div>
                                    </div>

                                    <div class="info-item mb-3">
                                        <label class="info-label">Endereço Completo:</label>
                                        <div class="info-value">
                                            <h6 class="text-primary">
                                                <?= esc($logradouro['tipo_logradouro'] . ' ' . $logradouro['nome_logradouro']) ?>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item mb-3">
                                        <label class="info-label">CEP:</label>
                                        <div class="info-value">
                                            <?php if ($logradouro['cep']): ?>
                                                <code class="fs-6"><?= esc($logradouro['cep']) ?></code>
                                            <?php else: ?>
                                                <span class="text-muted">Não informado</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>

                                    <div class="info-item mb-3">
                                        <label class="info-label">Bairro:</label>
                                        <div class="info-value">
                                            <a href="<?= base_url('bairros/' . $logradouro['id_bairro']) ?>" 
                                               class="text-decoration-none">
                                                <strong><?= esc($logradouro['nome_bairro']) ?></strong>
                                            </a>
                                        </div>
                                    </div>

                                    <div class="info-item mb-3">
                                        <label class="info-label">Área/Região:</label>
                                        <div class="info-value">
                                            <?php if ($logradouro['area']): ?>
                                                <span class="badge bg-light text-dark border"><?= esc($logradouro['area']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Não informado</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if ($logradouro['observacoes']): ?>
                                <hr>
                                <div class="info-item">
                                    <label class="info-label">Observações:</label>
                                    <div class="info-value">
                                        <div class="alert alert-light">
                                            <i class="bi bi-chat-square-text"></i>
                                            <?= nl2br(esc($logradouro['observacoes'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Endereço Formatado -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-geo-alt"></i> Endereço para Correspondência
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="address-format p-3 bg-light rounded">
                                <div class="row">
                                    <div class="col-md-8">
                                        <strong><?= esc($logradouro['tipo_logradouro'] . ' ' . $logradouro['nome_logradouro']) ?></strong><br>
                                        <span class="text-muted"><?= esc($logradouro['nome_bairro']) ?></span>
                                        <?php if ($logradouro['area']): ?>
                                            <span class="text-muted"> - <?= esc($logradouro['area']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <?php if ($logradouro['cep']): ?>
                                            <strong>CEP: <?= esc($logradouro['cep']) ?></strong>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="copyAddress()">
                                    <i class="bi bi-clipboard"></i> Copiar Endereço
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Informações do Sistema -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-clock-history"></i> Informações do Sistema
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td><?= $logradouro['id_logradouro'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Cadastrado em:</strong></td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($logradouro['created_at'])) ?><br>
                                        <small class="text-muted">às <?= date('H:i', strtotime($logradouro['created_at'])) ?></small>
                                    </td>
                                </tr>
                                <?php if ($logradouro['updated_at'] && $logradouro['updated_at'] != $logradouro['created_at']): ?>
                                <tr>
                                    <td><strong>Última atualização:</strong></td>
                                    <td>
                                        <?= date('d/m/Y', strtotime($logradouro['updated_at'])) ?><br>
                                        <small class="text-muted">às <?= date('H:i', strtotime($logradouro['updated_at'])) ?></small>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <!-- Ações Rápidas -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-lightning"></i> Ações Rápidas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="<?= base_url('logradouros/' . $logradouro['id_logradouro'] . '/edit') ?>" 
                                   class="btn btn-outline-warning btn-sm">
                                    <i class="bi bi-pencil"></i> Editar Logradouro
                                </a>
                                <a href="<?= base_url('bairros/' . $logradouro['id_bairro']) ?>" 
                                   class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-geo-alt"></i> Ver Bairro
                                </a>
                                <a href="<?= base_url('logradouros/create') ?>" 
                                   class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-plus"></i> Novo Logradouro
                                </a>
                                <a href="<?= base_url('logradouros') ?>" 
                                   class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-list"></i> Todos os Logradouros
                                </a>
                                <hr>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete()">
                                    <i class="bi bi-trash"></i> Excluir Logradouro
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Logradouros do Mesmo Bairro -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-signpost-2"></i> Outros Logradouros do Bairro
                            </h6>
                        </div>
                        <div class="card-body">
                            <div id="outrosLogradouros">
                                <div class="text-center">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Carregando...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal de Confirmação para Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este logradouro?</p>
                <p><strong><?= esc($logradouro['tipo_logradouro'] . ' ' . $logradouro['nome_logradouro']) ?></strong></p>
                <p class="text-muted small">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="<?= base_url('logradouros/' . $logradouro['id_logradouro']) ?>" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.info-item {
    border-bottom: 1px solid #f0f0f0;
    padding-bottom: 0.5rem;
}

.info-label {
    display: block;
    font-weight: 600;
    color: #666;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 1rem;
}

.address-format {
    font-family: 'Courier New', monospace;
    border: 1px dashed #ddd;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Carregar outros logradouros do bairro
    carregarOutrosLogradouros();
});

function carregarOutrosLogradouros() {
    const bairroId = <?= $logradouro['id_bairro'] ?>;
    const logradouroAtualId = <?= $logradouro['id_logradouro'] ?>;
    
    fetch(`<?= base_url('logradouros/api/bairro') ?>/${bairroId}`)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('outrosLogradouros');
            
            if (data.success && data.data.length > 0) {
                // Filtrar o logradouro atual
                const outros = data.data.filter(l => l.id_logradouro != logradouroAtualId);
                
                if (outros.length > 0) {
                    let html = '<div class="list-group list-group-flush">';
                    outros.forEach(logradouro => {
                        html += `
                            <a href="<?= base_url('logradouros') ?>/${logradouro.id_logradouro}" 
                               class="list-group-item list-group-item-action">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>${logradouro.tipo_logradouro} ${logradouro.nome_logradouro}</strong>
                                        ${logradouro.cep ? `<br><small class="text-muted">CEP: ${logradouro.cep}</small>` : ''}
                                    </div>
                                    <i class="bi bi-arrow-right text-muted"></i>
                                </div>
                            </a>
                        `;
                    });
                    html += '</div>';
                    container.innerHTML = html;
                } else {
                    container.innerHTML = '<p class="text-muted small">Este é o único logradouro cadastrado neste bairro.</p>';
                }
            } else {
                container.innerHTML = '<p class="text-muted small">Nenhum outro logradouro encontrado neste bairro.</p>';
            }
        })
        .catch(error => {
            console.error('Erro ao carregar logradouros:', error);
            document.getElementById('outrosLogradouros').innerHTML = 
                '<p class="text-muted small">Erro ao carregar dados.</p>';
        });
}

function confirmDelete() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function copyAddress() {
    const address = `<?= esc($logradouro['tipo_logradouro'] . ' ' . $logradouro['nome_logradouro'] . ', ' . $logradouro['nome_bairro']) ?><?= $logradouro['cep'] ? ', CEP: ' . $logradouro['cep'] : '' ?>`;
    
    navigator.clipboard.writeText(address).then(function() {
        // Show success message
        const btn = event.target.closest('button');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i> Copiado!';
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');
        
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline-secondary');
        }, 2000);
    }).catch(function(err) {
        console.error('Erro ao copiar: ', err);
        alert('Erro ao copiar endereço. Tente selecionar e copiar manualmente.');
    });
}
</script>

<?= $this->endSection() ?>
