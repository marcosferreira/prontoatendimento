<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<!-- CSS específico para bairros -->
<link rel="stylesheet" href="<?= base_url('assets/css/bairros.css') ?>">

<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-pencil"></i> Editar Bairro</h1>
                <p class="subtitle">Editar Dados do Bairro: <?= esc($bairro['nome_bairro']) ?></p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url() ?>">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('bairros') ?>">Bairros</a>
                    </li>
                    <li class="breadcrumb-item active">Editar Bairro</li>
                </ol>
            </nav>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('validation')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <strong>Erro de validação:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach (session()->getFlashdata('validation')->getErrors() as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Form Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-geo-alt"></i> Dados do Bairro
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('bairros/' . $bairro['id_bairro']) ?>" method="POST" id="editBairroForm">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="PUT">
                                
                                <div class="form-section mb-4">
                                    <h5 class="form-section-title">
                                        <i class="bi bi-info-circle"></i> Informações Básicas
                                    </h5>
                                    
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="nome_bairro" class="form-label">
                                                    Nome do Bairro *
                                                </label>
                                                <input type="text" 
                                                       class="form-control <?= session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('nome_bairro') ? 'is-invalid' : '' ?>"
                                                       id="nome_bairro" 
                                                       name="nome_bairro" 
                                                       value="<?= old('nome_bairro') ?: esc($bairro['nome_bairro']) ?>"
                                                       placeholder="Digite o nome do bairro"
                                                       maxlength="100"
                                                       required>
                                                <div class="invalid-feedback" id="nome_bairro_feedback">
                                                    <?= session()->getFlashdata('validation') ? session()->getFlashdata('validation')->getError('nome_bairro') : '' ?>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Nome oficial do bairro (máximo 100 caracteres)
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="area" class="form-label">
                                                    Área/Região
                                                </label>
                                                <input type="text" 
                                                       class="form-control <?= session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('area') ? 'is-invalid' : '' ?>"
                                                       id="area" 
                                                       name="area" 
                                                       value="<?= old('area') ?: esc($bairro['area'] ?? '') ?>"
                                                       placeholder="Ex: Centro, Zona Norte"
                                                       maxlength="100">
                                                <div class="invalid-feedback">
                                                    <?= session()->getFlashdata('validation') ? session()->getFlashdata('validation')->getError('area') : '' ?>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Campo opcional para agrupar bairros
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <a href="<?= base_url('bairros') ?>" class="btn btn-secondary">
                                                <i class="bi bi-arrow-left"></i> Voltar
                                            </a>
                                        </div>
                                        <div class="col-md-8 text-end">
                                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBairroModal">
                                                <i class="bi bi-trash"></i> Excluir
                                            </button>
                                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                                <i class="bi bi-check-circle"></i> Salvar Alterações
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Info Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle"></i> Informações do Bairro
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <strong>ID:</strong>
                                <span class="badge bg-secondary"><?= $bairro['id_bairro'] ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Cadastrado em:</strong>
                                <span><?= date('d/m/Y \à\s H:i', strtotime($bairro['created_at'])) ?></span>
                            </div>
                            <?php if (isset($bairro['updated_at']) && $bairro['updated_at'] != $bairro['created_at']): ?>
                            <div class="info-item">
                                <strong>Última atualização:</strong>
                                <span><?= date('d/m/Y \à\s H:i', strtotime($bairro['updated_at'])) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Preview Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-eye"></i> Pré-visualização
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="preview-item">
                                <strong>Nome:</strong>
                                <span id="preview_nome"><?= esc($bairro['nome_bairro']) ?></span>
                            </div>
                            <div class="preview-item">
                                <strong>Área:</strong>
                                <span id="preview_area"><?= esc($bairro['area'] ?? '-') ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tips Card -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-lightbulb"></i> Dicas
                            </h5>
                        </div>
                        <div class="card-body">
                            <ul class="info-list">
                                <li>Use nomes oficiais dos bairros</li>
                                <li>Evite abreviações desnecessárias</li>
                                <li>A área pode ser usada para relatórios regionais</li>
                                <li>Verifique se existem pacientes vinculados antes de excluir</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Confirmar Exclusão -->
<div class="modal fade" id="deleteBairroModal" tabindex="-1" aria-labelledby="deleteBairroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteBairroModalLabel">
                    <i class="bi bi-exclamation-triangle text-danger"></i> Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o bairro <strong><?= esc($bairro['nome_bairro']) ?></strong>?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Atenção:</strong> Esta ação não pode ser desfeita. Se existirem pacientes vinculados a este bairro, a exclusão será impedida.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="<?= base_url('bairros/' . $bairro['id_bairro']) ?>" method="POST" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Excluir Bairro
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nomeBairroInput = document.getElementById('nome_bairro');
    const areaInput = document.getElementById('area');
    const previewNome = document.getElementById('preview_nome');
    const previewArea = document.getElementById('preview_area');
    const feedback = document.getElementById('nome_bairro_feedback');
    const originalNome = '<?= esc($bairro['nome_bairro']) ?>';

    // Atualizar preview em tempo real
    nomeBairroInput.addEventListener('input', function() {
        previewNome.textContent = this.value || '-';
    });

    areaInput.addEventListener('input', function() {
        previewArea.textContent = this.value || '-';
    });

    // Validação do nome do bairro
    nomeBairroInput.addEventListener('blur', function() {
        const nome = this.value.trim();
        
        if (nome.length < 3) {
            this.classList.add('is-invalid');
            feedback.textContent = 'O nome deve ter pelo menos 3 caracteres';
            return;
        }

        // Só validar se o nome mudou
        if (nome === originalNome) {
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
            return;
        }

        // Verificar se já existe via AJAX
        fetch('<?= base_url('bairros/validateNome') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: 'nome_bairro=' + encodeURIComponent(nome) + '&id=<?= $bairro['id_bairro'] ?>&<?= csrf_token() ?>=' + '<?= csrf_hash() ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (!data.valid) {
                this.classList.add('is-invalid');
                feedback.textContent = data.message;
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
                feedback.textContent = '';
            }
        })
        .catch(error => {
            console.error('Erro na validação:', error);
        });
    });

    // Validação do formulário
    document.getElementById('editBairroForm').addEventListener('submit', function(e) {
        const nome = nomeBairroInput.value.trim();

        if (nome.length < 3) {
            e.preventDefault();
            nomeBairroInput.focus();
            feedback.textContent = 'O nome do bairro deve ter pelo menos 3 caracteres';
            nomeBairroInput.classList.add('is-invalid');
            return false;
        }

        // Desabilitar botão de submit para evitar envios duplos
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Salvando...';
    });
});
</script>

<?= $this->endSection() ?>
