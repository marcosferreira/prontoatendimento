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
                <h1><i class="bi bi-signpost"></i> Logradouros</h1>
                <p class="subtitle">Gerenciamento de Logradouros Cadastrados</p>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-left m-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="search-container position-relative">
                                <input type="text" id="searchLogradouro" class="form-control search-input pe-5"
                                placeholder="Buscar por nome, CEP ou bairro..." value="<?= esc($search ?? '') ?>">
                                <i class="bi bi-search search-icon position-absolute top-50 end-0 translate-middle-y me-3"></i>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <select class="form-select" id="filterBairro">
                                <option value="">Todos os bairros</option>
                                <?php foreach ($bairros as $bairro): ?>
                                    <option value="<?= $bairro['id_bairro'] ?>" <?= ($bairro_selecionado == $bairro['id_bairro']) ? 'selected' : '' ?>>
                                        <?= esc($bairro['nome_bairro']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="action-right m-4">
                    <a href="<?= base_url('logradouros/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Novo Logradouro
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('logradouros/export') ?>">
                                <i class="bi bi-download"></i> Exportar CSV
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="<?= base_url('logradouros') ?>">
                                <i class="bi bi-list"></i> Ver Todos
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon bg-primary">
                        <i class="bi bi-signpost"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['total'] ?></h3>
                        <p>Total de Logradouros</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-success">
                        <i class="bi bi-calendar-day"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['hoje'] ?></h3>
                        <p>Cadastrados Hoje</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-info">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['mes'] ?></h3>
                        <p>Este Mês</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-warning">
                        <i class="bi bi-calendar-year"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['ano'] ?></h3>
                        <p>Este Ano</p>
                    </div>
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

            <!-- Table -->
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo</th>
                                <th>Nome do Logradouro</th>
                                <th>CEP</th>
                                <th>Bairro</th>
                                <th>Área</th>
                                <th>Cadastrado em</th>
                                <th width="180">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="logradourosTableBody">
                            <?php if (!empty($logradouros)): ?>
                                <?php foreach ($logradouros as $logradouro): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary"><?= $logradouro['id_logradouro'] ?></span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= esc($logradouro['tipo_logradouro']) ?></span>
                                        </td>
                                        <td>
                                            <strong><?= esc($logradouro['nome_logradouro']) ?></strong>
                                            <?php if (!empty($logradouro['observacoes'])): ?>
                                                <br><small class="text-muted"><?= esc($logradouro['observacoes']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= $logradouro['cep'] ? '<code>' . esc($logradouro['cep']) . '</code>' : '<span class="text-muted">Não informado</span>' ?>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('bairros/' . $logradouro['id_bairro']) ?>" class="text-decoration-none">
                                                <?= esc($logradouro['nome_bairro']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= $logradouro['area'] ? esc($logradouro['area']) : 'Não informado' ?>
                                            </small>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y \à\s H:i', strtotime($logradouro['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('logradouros/' . $logradouro['id_logradouro']) ?>" 
                                                   class="btn btn-outline-info btn-sm" title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('logradouros/' . $logradouro['id_logradouro'] . '/edit') ?>" 
                                                   class="btn btn-outline-warning btn-sm" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="deleteLogradouro(<?= $logradouro['id_logradouro'] ?>, '<?= esc($logradouro['nome_logradouro']) ?>')"
                                                    title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="bi bi-signpost-2 text-muted" style="font-size: 3rem;"></i>
                                            <h5 class="text-muted mt-3">Nenhum logradouro encontrado</h5>
                                            <p class="text-muted">
                                                <?php if (!empty($search) || !empty($bairro_selecionado)): ?>
                                                    Tente ajustar os filtros de busca ou 
                                                    <a href="<?= base_url('logradouros') ?>" class="text-decoration-none">ver todos os logradouros</a>
                                                <?php else: ?>
                                                    Comece cadastrando um novo logradouro.
                                                <?php endif; ?>
                                            </p>
                                            <?php if (empty($search) && empty($bairro_selecionado)): ?>
                                                <a href="<?= base_url('logradouros/create') ?>" class="btn btn-primary">
                                                    <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Logradouro
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
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
                <p>Tem certeza que deseja excluir o logradouro <strong id="logradouroNome"></strong>?</p>
                <p class="text-muted small">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchLogradouro');
    const filterBairro = document.getElementById('filterBairro');
    
    function performSearch() {
        const search = searchInput.value;
        const bairro = filterBairro.value;
        
        const params = new URLSearchParams();
        if (search) params.append('search', search);
        if (bairro) params.append('bairro', bairro);
        
        const url = '<?= base_url('logradouros') ?>' + (params.toString() ? '?' + params.toString() : '');
        window.location.href = url;
    }
    
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
    
    filterBairro.addEventListener('change', performSearch);
    
    // Auto-search with delay
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(performSearch, 500);
    });
});

function deleteLogradouro(id, nome) {
    document.getElementById('logradouroNome').textContent = nome;
    document.getElementById('deleteForm').action = '<?= base_url('logradouros') ?>/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?= $this->endSection() ?>
