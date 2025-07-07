<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-gear"></i> Procedimentos em Atendimentos</h1>
                <p class="subtitle">Gerenciamento de Procedimentos Realizados</p>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-left m-4">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <div class="search-container position-relative">
                                <input type="text" id="searchProcedimento" class="form-control search-input pe-5"
                                placeholder="Buscar por paciente ou procedimento..." value="<?= esc($search) ?>">
                                <i class="bi bi-search search-icon position-absolute top-50 end-0 translate-middle-y me-3"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="filterProcedimento" onchange="applyFilters()">
                                <option value="">Todos os Procedimentos</option>
                                <?php foreach ($procedimentos as $proc): ?>
                                    <option value="<?= $proc['id_procedimento'] ?>" 
                                            <?= $procedimento == $proc['id_procedimento'] ? 'selected' : '' ?>>
                                        <?= esc($proc['nome']) ?>
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
                    <a href="<?= base_url('atendimento-procedimentos/relatorio') ?>" class="btn btn-info me-2">
                        <i class="bi bi-bar-chart"></i> Relatórios
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row m-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= $stats['total'] ?></div>
                        <div class="stat-label">Total de Procedimentos</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= $stats['hoje'] ?></div>
                        <div class="stat-label">Realizados Hoje</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= $stats['mes'] ?></div>
                        <div class="stat-label">Este Mês</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= $stats['ano'] ?></div>
                        <div class="stat-label">Este Ano</div>
                    </div>
                </div>
            </div>

            <!-- Table Container -->
            <div class="table-container m-4">
                <div class="table-responsive">
                    <table class="table table-hover" id="procedimentosTable">
                        <thead>
                            <tr>
                                <th>Paciente</th>
                                <th>Procedimento</th>
                                <th>Código</th>
                                <th>Data Atendimento</th>
                                <th>Quantidade</th>
                                <th>Observação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($atendimentoProcedimentos)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2">Nenhum procedimento encontrado</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($atendimentoProcedimentos as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-bold"><?= esc($item['nome_paciente']) ?></div>
                                            <small class="text-muted">CPF: <?= esc($item['cpf']) ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-bold"><?= esc($item['nome_procedimento']) ?></div>
                                        </td>
                                        <td>
                                            <?php if ($item['codigo_procedimento']): ?>
                                                <span class="badge bg-secondary"><?= esc($item['codigo_procedimento']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div><?= date('d/m/Y', strtotime($item['data_atendimento'])) ?></div>
                                            <small class="text-muted"><?= date('H:i', strtotime($item['data_atendimento'])) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary"><?= $item['quantidade'] ?></span>
                                        </td>
                                        <td>
                                            <?php if ($item['observacao']): ?>
                                                <span title="<?= esc($item['observacao']) ?>" data-bs-toggle="tooltip">
                                                    <?= strlen($item['observacao']) > 30 ? esc(substr($item['observacao'], 0, 30)) . '...' : esc($item['observacao']) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('atendimento-procedimentos/show/' . $item['id_atendimento_procedimento']) ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('atendimentos/show/' . $item['id_atendimento']) ?>" 
                                                   class="btn btn-sm btn-outline-info" title="Ver Atendimento">
                                                    <i class="bi bi-file-medical"></i>
                                                </a>
                                                <a href="<?= base_url('atendimento-procedimentos/edit/' . $item['id_atendimento_procedimento']) ?>" 
                                                   class="btn btn-sm btn-outline-warning" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmarExclusao(<?= $item['id_atendimento_procedimento'] ?>)" title="Excluir">
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
                <p>Tem certeza que deseja remover este procedimento do atendimento?</p>
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
    const searchInput = document.getElementById('searchProcedimento');
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
});

function applyFilters() {
    const search = document.getElementById('searchProcedimento').value;
    const procedimento = document.getElementById('filterProcedimento').value;
    
    let url = '<?= base_url('atendimento-procedimentos') ?>?';
    const params = [];
    
    if (search) params.push('search=' + encodeURIComponent(search));
    if (procedimento) params.push('procedimento=' + procedimento);
    
    url += params.join('&');
    window.location.href = url;
}

function clearFilters() {
    window.location.href = '<?= base_url('atendimento-procedimentos') ?>';
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
