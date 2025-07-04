<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>
    <?= $this->include('components/topbar') ?>
    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-clipboard2-pulse"></i> Exames</h1>
                <p class="subtitle">Gerenciamento de Exames Cadastrados</p>
            </div>
            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-left m-4">
                    <div class="search-container position-relative">
                        <input type="text" id="searchExame" class="form-control search-input pe-5"
                        placeholder="Buscar por nome do exame...">
                        <i class="bi bi-search search-icon position-absolute top-50 end-0 translate-middle-y me-3"></i>
                    </div>
                </div>
                <div class="action-right m-4">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoExameModal">
                        <i class="bi bi-plus-circle"></i> Novo Exame
                    </button>
                </div>
            </div>
            <!-- Content -->
            <div class="content-wrapper">
                <div class="section-card">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="bi bi-list-ul"></i>
                            Lista de Exames
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table modern-table" id="examesTable">
                            <thead>
                                <tr>
                                    <th scope="col">Nome do Exame</th>
                                    <th scope="col">Descrição</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($exames) && !empty($exames)): ?>
                                    <?php foreach ($exames as $exame): ?>
                                        <tr>
                                            <td><?= esc($exame['nome']) ?></td>
                                            <td><?= esc($exame['descricao']) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewExame(<?= $exame['id_exame'] ?>)" title="Visualizar">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning btn-sm" onclick="editExame(<?= $exame['id_exame'] ?>)" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                                                <h5 class="text-muted mt-2">Nenhum exame encontrado</h5>
                                                <p class="text-muted">Clique em "Novo Exame" para adicionar o primeiro exame.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Pagination -->
                    <?php if (isset($pager)): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <?= $pager->links() ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Novo Exame -->
<div class="modal fade" id="novoExameModal" tabindex="-1" aria-labelledby="novoExameModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="novoExameModalLabel">
                    <i class="bi bi-clipboard-plus"></i> Cadastrar Novo Exame
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="novoExameForm" action="<?= base_url('exames/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modal_nome" class="form-label">Nome do Exame <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="modal_nome" name="nome" required>
                    </div>
                    <div class="mb-3">
                        <label for="modal_codigo" class="form-label">Código</label>
                        <input type="text" class="form-control" id="modal_codigo" name="codigo">
                    </div>
                    <div class="mb-3">
                        <label for="modal_tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select" id="modal_tipo" name="tipo" required>
                            <option value="">Selecione</option>
                            <option value="laboratorial">Laboratorial</option>
                            <option value="imagem">Imagem</option>
                            <option value="funcional">Funcional</option>
                            <option value="outros">Outros</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="modal_descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="modal_descricao" name="descricao" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Salvar Exame
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<script>
    // Busca de exames
    document.getElementById('searchExame').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const table = document.getElementById('examesTable');
        const rows = table.getElementsByTagName('tr');
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            for (let j = 0; j < cells.length - 1; j++) {
                if (cells[j].textContent.toLowerCase().includes(filter)) {
                    found = true;
                    break;
                }
            }
            row.style.display = found ? '' : 'none';
        }
    });
    function viewExame(id) {
        window.location.href = `<?= base_url('exames') ?>/${id}`;
    }
    function editExame(id) {
        window.location.href = `<?= base_url('exames') ?>/${id}/edit`;
    }
</script>

<style>
.form-section {
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    padding: 1rem;
    background: #f8f9fa;
    margin-bottom: 1rem;
}
.form-section-title {
    color: #0d6efd;
    font-weight: 600;
    margin-bottom: 0.75rem;
    padding-bottom: 0.25rem;
    border-bottom: 1px solid #0d6efd;
    font-size: 0.9rem;
}
.form-section-title i {
    margin-right: 0.5rem;
}
.modal-xl .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}
</style>

<?= $this->endSection() ?>
