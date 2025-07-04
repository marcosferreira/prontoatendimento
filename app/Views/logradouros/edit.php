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
                <h1><i class="bi bi-pencil"></i> Editar Logradouro</h1>
                <p class="subtitle">Atualizar Dados do Logradouro</p>
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
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('logradouros/' . $logradouro['id_logradouro']) ?>">
                            <?= esc($logradouro['tipo_logradouro'] . ' ' . $logradouro['nome_logradouro']) ?>
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Editar</li>
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
                            <form action="<?= base_url('logradouros/' . $logradouro['id_logradouro'] . '/update') ?>" method="POST" id="editLogradouroForm">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="PUT">
                                
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
                                                        <option value="<?= $value ?>" 
                                                                <?= (old('tipo_logradouro', $logradouro['tipo_logradouro']) == $value) ? 'selected' : '' ?>>
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
                                                       value="<?= old('nome_logradouro', $logradouro['nome_logradouro']) ?>"
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
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="cep" class="form-label">
                                                    CEP
                                                </label>
                                                <input type="text" 
                                                       class="form-control <?= session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('cep') ? 'is-invalid' : '' ?>"
                                                       id="cep" 
                                                       name="cep" 
                                                       value="<?= old('cep', $logradouro['cep']) ?>"
                                                       placeholder="00000-000"
                                                       maxlength="9">
                                                <div class="invalid-feedback">
                                                    <?= session()->getFlashdata('validation') ? session()->getFlashdata('validation')->getError('cep') : '' ?>
                                                </div>
                                                <small class="form-text text-muted">
                                                    CEP do logradouro (opcional)
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="cidade" class="form-label">
                                                    Cidade
                                                </label>
                                                <input type="text" 
                                                       class="form-control <?= session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('cidade') ? 'is-invalid' : '' ?>"
                                                       id="cidade" 
                                                       name="cidade" 
                                                       value="<?= old('cidade', $logradouro['cidade'] ?? 'Dona Inês') ?>"
                                                       placeholder="Nome da cidade"
                                                       maxlength="100">
                                                <div class="invalid-feedback">
                                                    <?= session()->getFlashdata('validation') ? session()->getFlashdata('validation')->getError('cidade') : '' ?>
                                                </div>
                                                <small class="form-text text-muted">
                                                    Cidade onde o logradouro está localizado
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="mb-3">
                                                <label for="estado" class="form-label">
                                                    Estado *
                                                </label>
                                                <select class="form-select <?= session()->getFlashdata('validation') && session()->getFlashdata('validation')->hasError('estado') ? 'is-invalid' : '' ?>"
                                                        id="estado" 
                                                        name="estado" 
                                                        required>
                                                    <option value="">UF</option>
                                                    <?php foreach ($estados as $sigla => $nome): ?>
                                                        <option value="<?= $sigla ?>" <?= old('estado', $logradouro['estado'] ?? 'PB') == $sigla ? 'selected' : '' ?>>
                                                            <?= $sigla ?> - <?= $nome ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="invalid-feedback">
                                                    <?= session()->getFlashdata('validation') ? session()->getFlashdata('validation')->getError('estado') : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
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
                                                                <?= (old('id_bairro', $logradouro['id_bairro']) == $bairro['id_bairro']) ? 'selected' : '' ?>
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
                                                          placeholder="Informações adicionais sobre o logradouro..."><?= old('observacoes', $logradouro['observacoes']) ?></textarea>
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
                                        <span id="previewText">Carregando prévia...</span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="d-flex justify-content-between">
                                    <a href="<?= base_url('logradouros/' . $logradouro['id_logradouro']) ?>" class="btn btn-secondary">
                                        <i class="bi bi-arrow-left"></i> Voltar
                                    </a>
                                    <div>
                                        <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                            <i class="bi bi-arrow-clockwise"></i> Restaurar
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle"></i> Atualizar Logradouro
                                        </button>
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
                            <h6 class="card-title mb-0">
                                <i class="bi bi-info-circle"></i> Informações do Logradouro
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td><?= $logradouro['id_logradouro'] ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Cadastrado:</strong></td>
                                    <td><?= date('d/m/Y \à\s H:i', strtotime($logradouro['created_at'])) ?></td>
                                </tr>
                                <?php if ($logradouro['updated_at'] && $logradouro['updated_at'] != $logradouro['created_at']): ?>
                                <tr>
                                    <td><strong>Atualizado:</strong></td>
                                    <td><?= date('d/m/Y \à\s H:i', strtotime($logradouro['updated_at'])) ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <!-- Help Card -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="bi bi-question-circle"></i> Ajuda
                            </h6>
                        </div>
                        <div class="card-body">
                            <h6>Dicas para edição:</h6>
                            <ul class="small text-muted">
                                <li><strong>Tipo:</strong> Selecione o tipo correto (Rua, Avenida, etc.)</li>
                                <li><strong>Nome:</strong> Digite apenas o nome, sem o tipo</li>
                                <li><strong>CEP:</strong> Formato: 00000-000 (opcional)</li>
                                <li><strong>Bairro:</strong> Você pode alterar o bairro se necessário</li>
                            </ul>
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
                                <a href="<?= base_url('logradouros/' . $logradouro['id_logradouro']) ?>" class="btn btn-outline-info btn-sm">
                                    <i class="bi bi-eye"></i> Visualizar
                                </a>
                                <a href="<?= base_url('logradouros') ?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-list"></i> Todos os Logradouros
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete()">
                                    <i class="bi bi-trash"></i> Excluir
                                </button>
                            </div>
                        </div>
                    </div>
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
                <p>Tem certeza que deseja excluir este logradouro?</p>
                <p><strong><?= esc($logradouro['tipo_logradouro'] . ' ' . $logradouro['nome_logradouro']) ?></strong></p>
                <p class="text-muted small">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="<?= base_url('logradouros/' . $logradouro['id_logradouro']) ?>" method="POST" style="display: inline;">
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
        const cidade = document.getElementById('cidade').value;
        const estado = document.getElementById('estado');
        const selectedEstado = estado.options[estado.selectedIndex];
        
        if (tipo && nome) {
            let preview = `${tipo} ${nome}`;
            if (selectedBairro && selectedBairro.value) {
                preview += `, ${selectedBairro.text.split(' - ')[0]}`;
            }
            if (cidade) {
                preview += `, ${cidade}`;
            }
            if (selectedEstado && selectedEstado.value) {
                preview += ` - ${selectedEstado.value}`;
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
    document.getElementById('cidade').addEventListener('input', updatePreview);
    document.getElementById('estado').addEventListener('change', updatePreview);

    // Initial preview
    updatePreview();

    // Form validation
    document.getElementById('editLogradouroForm').addEventListener('submit', function(e) {
        const tipo = document.getElementById('tipo_logradouro').value;
        const nome = document.getElementById('nome_logradouro').value.trim();
        const bairro = document.getElementById('id_bairro').value;

        if (!tipo || !nome || !bairro) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos obrigatórios.');
            return false;
        }
    });
});

// Store original values for reset
const originalValues = {
    tipo_logradouro: '<?= $logradouro['tipo_logradouro'] ?>',
    nome_logradouro: '<?= $logradouro['nome_logradouro'] ?>',
    cep: '<?= $logradouro['cep'] ?>',
    cidade: '<?= $logradouro['cidade'] ?? '' ?>',
    estado: '<?= $logradouro['estado'] ?? 'PB' ?>',
    id_bairro: '<?= $logradouro['id_bairro'] ?>',
    observacoes: '<?= $logradouro['observacoes'] ?>'
};

function resetForm() {
    document.getElementById('tipo_logradouro').value = originalValues.tipo_logradouro;
    document.getElementById('nome_logradouro').value = originalValues.nome_logradouro;
    document.getElementById('cep').value = originalValues.cep;
    document.getElementById('cidade').value = originalValues.cidade;
    document.getElementById('estado').value = originalValues.estado;
    document.getElementById('id_bairro').value = originalValues.id_bairro;
    document.getElementById('observacoes').value = originalValues.observacoes;
    
    // Update preview
    const event = new Event('change', { bubbles: true });
    document.getElementById('tipo_logradouro').dispatchEvent(event);
}

function confirmDelete() {
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?= $this->endSection() ?>
