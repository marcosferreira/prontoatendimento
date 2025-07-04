<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-clipboard-check"></i> Procedimentos</h1>
                <p class="subtitle">Gerenciamento de Procedimentos Médicos</p>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-left m-4">
                    <div class="search-container position-relative">
                        <input type="text" id="searchProcedimento" class="form-control search-input pe-5"
                        placeholder="Buscar por nome, código ou descrição...">
                        <i class="bi bi-search search-icon position-absolute top-50 end-0 translate-middle-y me-3"></i>
                    </div>
                </div>
                <div class="action-right m-4">
                    <a href="<?= base_url('procedimentos/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Novo Procedimento
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('procedimentos/export') ?>">
                                <i class="bi bi-download"></i> Exportar CSV
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="#" onclick="showAll()">
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
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['total']) ?></h3>
                        <p>Total de Procedimentos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-info">
                        <i class="bi bi-calendar-today"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['hoje']) ?></h3>
                        <p>Cadastrados Hoje</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-secondary">
                        <i class="bi bi-calendar-month"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['mes']) ?></h3>
                        <p>Cadastrados este Mês</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-dark">
                        <i class="bi bi-calendar-year"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['ano']) ?></h3>
                        <p>Cadastrados este Ano</p>
                    </div>
                </div>
            </div>

            <!-- Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Table -->
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover" id="procedimentosTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Código</th>
                                <th>Descrição</th>
                                <th>Cadastrado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($procedimentos)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="bi bi-clipboard-check"></i>
                                            <p>Nenhum procedimento encontrado</p>
                                            <a href="<?= base_url('procedimentos/create') ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Procedimento
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($procedimentos as $procedimento): ?>
                                    <tr>
                                        <td><?= $procedimento['id_procedimento'] ?></td>
                                        <td>
                                            <div class="procedure-info">
                                                <div class="procedure-icon">
                                                    <i class="bi bi-clipboard-check"></i>
                                                </div>
                                                <div class="procedure-details">
                                                    <span class="procedure-name"><?= esc($procedimento['nome']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($procedimento['codigo']): ?>
                                                <span class="badge bg-info"><?= esc($procedimento['codigo']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">Não informado</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($procedimento['descricao']): ?>
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                                      title="<?= esc($procedimento['descricao']) ?>">
                                                    <?= esc(substr($procedimento['descricao'], 0, 50)) ?>
                                                    <?= strlen($procedimento['descricao']) > 50 ? '...' : '' ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">Sem descrição</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($procedimento['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('procedimentos/show/' . $procedimento['id_procedimento']) ?>" 
                                                   class="btn btn-sm btn-outline-info" title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('procedimentos/edit/' . $procedimento['id_procedimento']) ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteProcedimento(<?= $procedimento['id_procedimento'] ?>, '<?= esc($procedimento['nome']) ?>')" 
                                                        title="Excluir">
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
            </div>
        </div>
    </main>
</div>

<style>
.procedure-info {
    display: flex;
    align-items: center;
}

.procedure-icon {
    margin-right: 0.75rem;
    color: #6c757d;
}

.procedure-name {
    font-weight: 500;
}
</style>

<script>
// Busca de procedimentos
document.getElementById('searchProcedimento').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('procedimentosTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        // Buscar em nome, código e descrição
        for (let j = 1; j <= 3; j++) {
            if (cells[j] && cells[j].textContent.toLowerCase().includes(searchTerm)) {
                found = true;
                break;
            }
        }

        row.style.display = found ? '' : 'none';
    }
});

// Função para deletar procedimento
function deleteProcedimento(id, nome) {
    if (confirm(`Tem certeza que deseja excluir o procedimento "${nome}"?`)) {
        fetch(`<?= base_url('procedimentos/delete/') ?>${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.error || 'Erro ao excluir procedimento');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao excluir procedimento');
        });
    }
}

// Função para mostrar todos
function showAll() {
    const searchInput = document.getElementById('searchProcedimento');
    searchInput.value = '';
    searchInput.dispatchEvent(new Event('input'));
}
</script>

<?= $this->endSection() ?>
