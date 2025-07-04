<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-person-badge"></i> Médicos</h1>
                <p class="subtitle">Gerenciamento de Médicos Cadastrados</p>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-left m-4">
                    <div class="search-container position-relative">
                        <input type="text" id="searchMedico" class="form-control search-input pe-5"
                        placeholder="Buscar por nome, CRM ou especialidade...">
                        <i class="bi bi-search search-icon position-absolute top-50 end-0 translate-middle-y me-3"></i>
                    </div>
                </div>
                <div class="action-right m-4">
                    <a href="<?= base_url('medicos/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Novo Médico
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('medicos/export') ?>">
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
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['total']) ?></h3>
                        <p>Total de Médicos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-success">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['ativos']) ?></h3>
                        <p>Médicos Ativos</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon bg-warning">
                        <i class="bi bi-pause-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= number_format($stats['inativos']) ?></h3>
                        <p>Médicos Inativos</p>
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
                    <table class="table table-hover" id="medicosTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>CRM</th>
                                <th>Especialidade</th>
                                <th>Status</th>
                                <th>Cadastrado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($medicos)): ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="bi bi-person-badge"></i>
                                            <p>Nenhum médico encontrado</p>
                                            <a href="<?= base_url('medicos/create') ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Médico
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($medicos as $medico): ?>
                                    <tr>
                                        <td><?= $medico['id_medico'] ?></td>
                                        <td>
                                            <div class="user-info">
                                                <div class="user-avatar">
                                                    <i class="bi bi-person-badge"></i>
                                                </div>
                                                <div class="user-details">
                                                    <span class="user-name"><?= esc($medico['nome']) ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?= esc($medico['crm']) ?></span>
                                        </td>
                                        <td><?= esc($medico['especialidade'] ?? 'Não informada') ?></td>
                                        <td>
                                            <?php if ($medico['status'] === 'Ativo'): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle"></i> Ativo
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-pause-circle"></i> Inativo
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y H:i', strtotime($medico['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('medicos/show/' . $medico['id_medico']) ?>" 
                                                   class="btn btn-sm btn-outline-info" title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('medicos/edit/' . $medico['id_medico']) ?>" 
                                                   class="btn btn-sm btn-outline-primary" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        onclick="deleteMedico(<?= $medico['id_medico'] ?>, '<?= esc($medico['nome']) ?>')" 
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

<script>
// Busca de médicos
document.getElementById('searchMedico').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('medicosTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;

        // Buscar em nome, CRM e especialidade
        for (let j = 1; j <= 3; j++) {
            if (cells[j] && cells[j].textContent.toLowerCase().includes(searchTerm)) {
                found = true;
                break;
            }
        }

        row.style.display = found ? '' : 'none';
    }
});

// Função para deletar médico
function deleteMedico(id, nome) {
    if (confirm(`Tem certeza que deseja excluir o médico "${nome}"?`)) {
        fetch(`<?= base_url('medicos/delete/') ?>${id}`, {
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
                alert(data.error || 'Erro ao excluir médico');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao excluir médico');
        });
    }
}

// Função para mostrar todos
function showAll() {
    const searchInput = document.getElementById('searchMedico');
    searchInput.value = '';
    searchInput.dispatchEvent(new Event('input'));
}
</script>

<?= $this->endSection() ?>
