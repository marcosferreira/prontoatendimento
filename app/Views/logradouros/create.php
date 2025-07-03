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
                <h1><i class="bi bi-plus-circle"></i> Novo Logradouro</h1>
                <p class="subtitle">Cadastrar Novo Logradouro no Sistema</p>
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
                    <li class="breadcrumb-item active">Novo Logradouro</li>
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
                                <i class="bi bi-signpost"></i> Dados do Logradouro
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('logradouros') ?>" method="POST" id="createLogradouroForm">
                                <?= csrf_field() ?>
                                
                                <div class="form-section mb-4">
                                    <h5 class="form-section-title">
                                        <i class="bi bi-info-circle"></i> Informações Básicas
                                    </h5>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="tipo_logradouro" class="form-label">
                                                    Tipo de Logradouro *
                                                </label>
                                                <select class="form-select <?= session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('tipo_logradouro') ? 'is-invalid' : '' ?>"
                                                        id="tipo_logradouro" 
                                                        name="tipo_logradouro" 
                                                        required>
                                                    <option value="">Selecione...</option>
                                                    <?php foreach ($tipos as $value => $label): ?>
                                                        <option value="<?= $value ?>" <?= old('tipo_logradouro') == $value ? 'selected' : '' ?>>
                                                            <?= $label ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="invalid-feedback">
                                                    <?= session()->getFlashdata('validation') ? session()->getFlashdata('validation')->getError('tipo_logradouro') : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="nome_logradouro" class="form-label">
                                                    Nome do Logradouro *
                                                </label>
                                                <input type="text" 
                                                       class="form-control <?= session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('nome_logradouro') ? 'is-invalid' : '' ?>"
                                                       id="nome_logradouro" 
                                                       name="nome_logradouro" 
                                                       value="<?= old('nome_logradouro') ?>"
                                                       placeholder="Ex: das Flores, XV de Novembro"
                                                       maxlength="150"
                                                       required>
                                                <div class="invalid-feedback">
                                                    <?= session()->getFlashdata('validation') ? session()->getFlashdata('validation')->getError('nome_logradouro') : '' ?>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Nome do logradouro sem o tipo (máximo 150 caracteres)
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="cep" class="form-label">
                                                    CEP
                                                </label>
                                                <input type="text" 
                                                       class="form-control <?= session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('cep') ? 'is-invalid' : '' ?>"
                                                       id="cep" 
                                                       name="cep" 
                                                       value="<?= old('cep') ?>"
                                                       placeholder="00000-000"
                                                       maxlength="10">
                                                <div class="invalid-feedback">
                                                    <?= session()->getFlashdata('validation') ? session()->getFlashdata('validation')->getError('cep') : '' ?>
                                                </div>
                                                <small class="form-text text-muted">
                                                    CEP do logradouro (opcional)
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="id_bairro" class="form-label">
                                                    Bairro *
                                                </label>
                                                <select class="form-select <?= session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('id_bairro') ? 'is-invalid' : '' ?>"
                                                        id="id_bairro" 
                                                        name="id_bairro" 
                                                        required>
                                                    <option value="">Selecione o bairro...</option>
                                                    <?php foreach ($bairros as $bairro): ?>
                                                        <option value="<?= $bairro['id_bairro'] ?>" 
                                                                <?= (old('id_bairro', $bairro_selecionado ?? '') == $bairro['id_bairro']) ? 'selected' : '' ?>
                                                                data-area="<?= esc($bairro['area']) ?>">
                                                            <?= esc($bairro['nome_bairro']) ?>
                                                            <?php if ($bairro['area']): ?>
                                                                - <?= esc($bairro['area']) ?>
                                                            <?php endif; ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="invalid-feedback">
                                                    <?= session()->getFlashdata('validation') ? session()->getFlashdata('validation')->getError('id_bairro') : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="observacoes" class="form-label">
                                                    Observações
                                                </label>
                                                <textarea class="form-control" 
                                                          id="observacoes" 
                                                          name="observacoes" 
                                                          rows="3"
                                                          placeholder="Informações adicionais sobre o logradouro..."><?= old('observacoes') ?></textarea>
                                                <small class="form-text text-muted">
                                                    Informações complementares (opcional)
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Preview -->
                                <div class="form-section mb-4">
                                    <h5 class="form-section-title">
                                        <i class="bi bi-eye"></i> Visualização
                                    </h5>
                                    <div class="alert alert-info" id="preview">
                                        <i class="bi bi-info-circle"></i>
                                        <span id="previewText">Preencha os campos para ver a prévia do endereço</span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('logradouros') ?>" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Voltar
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-outline-secondary me-2">
                                            <i class="bi bi-arrow-clockwise"></i> Limpar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle"></i> Salvar Logradouro
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Help Card -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-question-circle"></i> Ajuda
                            </h6>
                        </div>
                        <div class="card-body">
                            <h6>Dicas para cadastro:</h6>
                            <ul class="small text-muted">
                                <li><strong>Tipo:</strong> Selecione o tipo correto (Rua, Avenida, etc.)</li>
                                <li><strong>Nome:</strong> Digite apenas o nome, sem o tipo</li>
                                <li><strong>CEP:</strong> Formato: 00000-000 (opcional)</li>
                                <li><strong>Bairro:</strong> Selecione o bairro onde o logradouro está localizado</li>
                            </ul>
                            
                            <hr>
                            
                            <h6>Exemplos:</h6>
                            <div class="small text-muted">
                                <strong>Tipo:</strong> Rua<br>
                                <strong>Nome:</strong> das Flores<br>
                                <strong>Resultado:</strong> Rua das Flores
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-lightning"></i> Ações Rápidas
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="<?= base_url('bairros/create') ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-plus"></i> Cadastrar Bairro
                                </a>
                                <a href="<?= base_url('logradouros') ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-list"></i> Ver Logradouros
                                </a>
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
    // CEP mask
    const cepInput = document.getElementById('cep');
    cepInput.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length >= 5) {
            value = value.substring(0, 5) + '-' + value.substring(5, 8);
        }
        this.value = value;
    });

    // Preview functionality
    function updatePreview() {
        const tipo = document.getElementById('tipo_logradouro').value;
        const nome = document.getElementById('nome_logradouro').value;
        const bairro = document.getElementById('id_bairro');
        const selectedBairro = bairro.options[bairro.selectedIndex];
        const cep = document.getElementById('cep').value;
        
        if (tipo && nome) {
            let preview = `${tipo} ${nome}`;
            if (selectedBairro && selectedBairro.value) {
                preview += `, ${selectedBairro.text.split(' - ')[0]}`;
            }
            if (cep) {
                preview += `, CEP: ${cep}`;
            }
            document.getElementById('previewText').textContent = preview;
        } else {
            document.getElementById('previewText').textContent = 'Preencha os campos para ver a prévia do endereço';
        }
    }

    // Event listeners for preview
    document.getElementById('tipo_logradouro').addEventListener('change', updatePreview);
    document.getElementById('nome_logradouro').addEventListener('input', updatePreview);
    document.getElementById('id_bairro').addEventListener('change', updatePreview);
    document.getElementById('cep').addEventListener('input', updatePreview);

    // Form validation
    document.getElementById('createLogradouroForm').addEventListener('submit', function(e) {
        const tipo = document.getElementById('tipo_logradouro').value;
        const nome = document.getElementById('nome_logradouro').value.trim();
        const bairro = document.getElementById('id_bairro').value;

        if (!tipo || !nome || !bairro) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos obrigatórios.');
            return false;
        }
    });

    // Auto-focus
    document.getElementById('tipo_logradouro').focus();
});
</script>

<?= $this->endSection() ?>
