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
                <h1><i class="bi bi-plus-circle"></i> Novo Bairro</h1>
                <p class="subtitle">Cadastrar Novo Bairro no Sistema</p>
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
                    <li class="breadcrumb-item active">Novo Bairro</li>
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
                            <form action="<?= base_url('bairros') ?>" method="POST" id="createBairroForm">
                                <?= csrf_field() ?>
                                
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
                                                       value="<?= old('nome_bairro') ?>"
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
                                                       value="<?= old('area') ?>"
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
                                        <div class="col-md-6">
                                            <a href="<?= base_url('bairros') ?>" class="btn btn-secondary">
                                                <i class="bi bi-arrow-left"></i> Voltar
                                            </a>
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                                <i class="bi bi-check-circle"></i> Salvar Bairro
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
                                <i class="bi bi-info-circle"></i> Informações
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-section">
                                <h6><i class="bi bi-check-circle text-success"></i> Campos Obrigatórios</h6>
                                <ul class="info-list">
                                    <li><strong>Nome do Bairro:</strong> Nome oficial do bairro</li>
                                </ul>
                            </div>
                            
                            <div class="info-section">
                                <h6><i class="bi bi-info-circle text-info"></i> Campos Opcionais</h6>
                                <ul class="info-list">
                                    <li><strong>Área/Região:</strong> Para agrupar bairros por região geográfica</li>
                                </ul>
                            </div>

                            <div class="info-section">
                                <h6><i class="bi bi-lightbulb text-warning"></i> Dicas</h6>
                                <ul class="info-list">
                                    <li>Use nomes oficiais dos bairros</li>
                                    <li>Evite abreviações desnecessárias</li>
                                    <li>A área pode ser usada para relatórios regionais</li>
                                </ul>
                            </div>
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
                                <span id="preview_nome">-</span>
                            </div>
                            <div class="preview-item">
                                <strong>Área:</strong>
                                <span id="preview_area">-</span>
                            </div>
                            <div class="preview-item">
                                <strong>Data de Cadastro:</strong>
                                <span><?= date('d/m/Y H:i') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const nomeBairroInput = document.getElementById('nome_bairro');
    const areaInput = document.getElementById('area');
    const previewNome = document.getElementById('preview_nome');
    const previewArea = document.getElementById('preview_area');
    const feedback = document.getElementById('nome_bairro_feedback');

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
                feedback.textContent = '';
            }
        })
        .catch(error => {
            console.error('Erro na validação:', error);
        });
    });

    // Validação do formulário
    document.getElementById('createBairroForm').addEventListener('submit', function(e) {
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
