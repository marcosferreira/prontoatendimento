<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-clipboard-check"></i> Exames em Atendimentos</h1>
                <p class="subtitle">Gerenciamento de Exames Solicitados e Realizados</p>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-left m-4">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <div class="search-container position-relative">
                                <input type="text" id="searchExame" class="form-control search-input pe-5"
                                placeholder="Buscar por paciente ou exame..." value="<?= esc($search) ?>">
                                <i class="bi bi-search search-icon position-absolute top-50 end-0 translate-middle-y me-3"></i>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterExame" onchange="applyFilters()">
                                <option value="">Todos os Exames</option>
                                <?php foreach ($exames as $ex): ?>
                                    <option value="<?= $ex['id_exame'] ?>" 
                                            <?= $exame == $ex['id_exame'] ? 'selected' : '' ?>>
                                        <?= esc($ex['nome']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterStatus" onchange="applyFilters()">
                                <option value="">Todos os Status</option>
                                <?php foreach ($statusOptions as $statusOption): ?>
                                    <option value="<?= $statusOption ?>" 
                                            <?= $status == $statusOption ? 'selected' : '' ?>>
                                        <?= $statusOption ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                <i class="bi bi-x-circle"></i> Limpar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="action-right m-4">
                    <a href="/atendimento-exames/relatorio" class="btn btn-info me-2">
                        <i class="bi bi-bar-chart"></i> Relatórios
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row m-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= $stats['total'] ?></div>
                        <div class="stat-label">Total de Exames</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="stat-item bg-warning">
                        <div class="stat-number"><?= $stats['solicitados'] ?></div>
                        <div class="stat-label">Solicitados</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="stat-item bg-success">
                        <div class="stat-number"><?= $stats['realizados'] ?></div>
                        <div class="stat-label">Realizados</div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="stat-item bg-danger">
                        <div class="stat-number"><?= $stats['cancelados'] ?></div>
                        <div class="stat-label">Cancelados</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item bg-info">
                        <div class="stat-number"><?= $stats['hoje'] ?></div>
                        <div class="stat-label">Solicitados Hoje</div>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container m-4">
                <div class="table-responsive">
                    <table class="table table-hover" id="examesTable">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Exame</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th>Data Solicitação</th>
                                <th>Data Realização</th>
                                <th>Resultado</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($atendimentoExames)): ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2">Nenhum exame encontrado</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($atendimentoExames as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= esc($item['nome_paciente']) ?></div>
                                            <small class="text-muted">CPF: <?= esc($item['cpf']) ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?= esc($item['nome_exame']) ?></div>
                                            <?php if ($item['codigo_exame']): ?>
                                                <small class="text-muted"><?= esc($item['codigo_exame']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge 
                                                <?php 
                                                switch($item['tipo_exame']) {
                                                    case 'laboratorial': echo 'bg-primary'; break;
                                                    case 'imagem': echo 'bg-info'; break;
                                                    case 'funcional': echo 'bg-warning'; break;
                                                    default: echo 'bg-secondary';
                                                }
                                                ?>">
                                                <?= ucfirst($item['tipo_exame']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm status-select" 
                                                    data-id="<?= $item['id_atendimento_exame'] ?>"
                                                    data-original="<?= $item['status'] ?>">
                                                <option value="Solicitado" <?= $item['status'] === 'Solicitado' ? 'selected' : '' ?>>Solicitado</option>
                                                <option value="Realizado" <?= $item['status'] === 'Realizado' ? 'selected' : '' ?>>Realizado</option>
                                                <option value="Cancelado" <?= $item['status'] === 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div><?= date('d/m/Y', strtotime($item['data_solicitacao'])) ?></div>
                                            <small class="text-muted"><?= date('H:i', strtotime($item['data_solicitacao'])) ?></small>
                                        </td>
                                        <td>
                                            <?php if ($item['data_realizacao']): ?>
                                                <div><?= date('d/m/Y', strtotime($item['data_realizacao'])) ?></div>
                                                <small class="text-muted"><?= date('H:i', strtotime($item['data_realizacao'])) ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($item['resultado']): ?>
                                                <span title="<?= esc($item['resultado']) ?>" data-bs-toggle="tooltip">
                                                    <i class="bi bi-file-text text-success" style="font-size: 1.2rem;"></i>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="/atendimento-exames/show/<?= $item['id_atendimento_exame'] ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="/atendimentos/show/<?= $item['id_atendimento'] ?>" 
                                                   class="btn btn-sm btn-outline-info" title="Ver Atendimento">
                                                    <i class="bi bi-file-medical"></i>
                                                </a>
                                                <a href="/atendimento-exames/print/<?= $item['id_atendimento_exame'] ?>" 
                                                   class="btn btn-sm btn-outline-secondary" title="Imprimir Solicitação" target="_blank">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                                <a href="/atendimento-exames/edit/<?= $item['id_atendimento_exame'] ?>" 
                                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmarExclusao(<?= $item['id_atendimento_exame'] ?>)" title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if (isset($pager)): ?>
                    <div class="pagination-container">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
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
    // Busca
    const searchInput = document.getElementById('searchExame');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });
    }

    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

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

function applyFilters() {
    const search = document.getElementById('searchExame').value;
    const exame = document.getElementById('filterExame').value;
    const status = document.getElementById('filterStatus').value;
    
    let url = '/atendimento-exames?';
    const params = [];
    
    if (search) params.push('search=' + encodeURIComponent(search));
    if (exame) params.push('exame=' + exame);
    if (status) params.push('status=' + status);
    
    url += params.join('&');
    window.location.href = url;
}

function clearFilters() {
    window.location.href = '/atendimento-exames';
}

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
            
            // Mostrar notificação de sucesso
            showToast('Status atualizado com sucesso!', 'success');
            
            // Recarregar página após 1 segundo para atualizar estatísticas
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
    // Implementar sistema de notificações toast
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alert);
    
    // Remover após 5 segundos
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
        // Criar form para envio via POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/atendimento-exames/delete/' + deleteId;
        
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
