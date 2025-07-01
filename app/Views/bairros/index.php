<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-geo-alt"></i> Bairros</h1>
                <p class="subtitle">Gerenciamento de Bairros Cadastrados</p>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-left m-4">
                    <div class="search-container position-relative">
                        <input type="text" id="searchBairro" class="form-control search-input pe-5"
                        placeholder="Buscar por nome ou área...">
                        <i class="bi bi-search search-icon position-absolute top-50 end-0 translate-middle-y me-3"></i>
                    </div>
                </div>
                <div class="action-right m-4">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoBairroModal">
                        <i class="bi bi-plus-circle"></i> Novo Bairro
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?= base_url('bairros/export') ?>">
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
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <div class="stat-info">
                        <h3><?= $stats['total'] ?></h3>
                        <p>Total de Bairros</p>
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
                                <th>Nome do Bairro</th>
                                <th>Área</th>
                                <th>Pacientes</th>
                                <th>Cadastrado em</th>
                                <th width="180">Ações</th>
                            </tr>
                        </thead>
                        <tbody id="bairrosTableBody">
                            <?php if (!empty($bairros)): ?>
                                <?php foreach ($bairros as $bairro): ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-secondary"><?= $bairro['id_bairro'] ?></span>
                                        </td>
                                        <td>
                                            <strong><?= esc($bairro['nome_bairro']) ?></strong>
                                        </td>
                                        <td>
                                            <?= $bairro['area'] ? esc($bairro['area']) : '<span class="text-muted">Não informado</span>' ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= $bairro['total_pacientes'] ?? 0 ?> pacientes
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?= date('d/m/Y \à\s H:i', strtotime($bairro['created_at'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-outline-info btn-sm"
                                                    onclick="viewBairro(<?= $bairro['id_bairro'] ?>)"
                                                    title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-warning btn-sm"
                                                    onclick="editBairro(<?= $bairro['id_bairro'] ?>)"
                                                    title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="deleteBairro(<?= $bairro['id_bairro'] ?>, '<?= esc($bairro['nome_bairro']) ?>')"
                                                    title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="empty-state">
                                            <i class="bi bi-geo-alt-fill text-muted" style="font-size: 3rem;"></i>
                                            <h5 class="text-muted mt-2">Nenhum bairro encontrado</h5>
                                            <p class="text-muted">Clique em "Novo Bairro" para adicionar o primeiro bairro.</p>
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

<!-- Modal Novo Bairro -->
<div class="modal fade" id="novoBairroModal" tabindex="-1" aria-labelledby="novoBairroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('bairros') ?>" method="POST" id="novoBairroForm">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="novoBairroModalLabel">
                        <i class="bi bi-plus-circle"></i> Novo Bairro
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="nome_bairro" class="form-label">Nome do Bairro *</label>
                                <input type="text" class="form-control" id="nome_bairro" name="nome_bairro" 
                                       placeholder="Digite o nome do bairro" required maxlength="100">
                                <div class="invalid-feedback" id="nome_bairro_feedback"></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="area" class="form-label">Área/Região</label>
                                <input type="text" class="form-control" id="area" name="area" 
                                       placeholder="Ex: Centro, Zona Norte, etc." maxlength="100">
                                <small class="form-text text-muted">Campo opcional para agrupar bairros por região.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Salvar Bairro
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Visualizar Bairro -->
<div class="modal fade" id="viewBairroModal" tabindex="-1" aria-labelledby="viewBairroModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewBairroModalLabel">
                    <i class="bi bi-eye"></i> Detalhes do Bairro
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="viewBairroContent">
                <!-- Conteúdo carregado via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Busca em tempo real
    document.getElementById('searchBairro').addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#bairrosTableBody tr');
        
        rows.forEach(function(row) {
            const cells = row.querySelectorAll('td');
            let found = false;
            
            // Verifica se alguma célula contém o texto pesquisado (exceto a coluna de ações)
            for (let j = 0; j < cells.length - 1; j++) {
                if (cells[j].textContent.toLowerCase().includes(filter)) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? '' : 'none';
        });
    });

    // Validação do nome do bairro
    document.getElementById('nome_bairro').addEventListener('blur', function() {
        const nome = this.value.trim();
        const feedback = document.getElementById('nome_bairro_feedback');
        
        if (nome.length < 3) {
            this.classList.add('is-invalid');
            feedback.textContent = 'O nome deve ter pelo menos 3 caracteres';
            return;
        }

        // Verificar se já existe via AJAX
        fetch('<?= base_url('bairros/validateNome') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'nome_bairro=' + encodeURIComponent(nome) + '&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (!data.valid) {
                this.classList.add('is-invalid');
                feedback.textContent = data.message;
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        })
        .catch(error => {
            console.error('Erro na validação:', error);
        });
    });
});

// Funções para ações da tabela
function viewBairro(id) {
    fetch(`<?= base_url('bairros') ?>/${id}/modal`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('viewBairroContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('viewBairroModal')).show();
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao carregar dados do bairro');
        });
}

function editBairro(id) {
    window.location.href = `<?= base_url('bairros') ?>/${id}/edit`;
}

function deleteBairro(id, nome) {
    if (confirm(`Tem certeza que deseja excluir o bairro "${nome}"?\n\nEsta ação não pode ser desfeita.`)) {
        // Criar formulário para enviar DELETE
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `<?= base_url('bairros') ?>/${id}`;
        
        // Adicionar token CSRF
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '<?= csrf_token() ?>';
        csrfInput.value = '<?= csrf_hash() ?>';
        form.appendChild(csrfInput);
        
        // Adicionar método DELETE
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function showAll() {
    window.location.href = '<?= base_url('bairros') ?>';
}
</script>

<?= $this->endSection() ?>
