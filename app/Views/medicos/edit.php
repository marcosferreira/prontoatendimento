<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>

<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <div class="content-wrapper">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-pencil-square"></i> Editar Médico</h1>
                <p class="subtitle">Editar dados do médico: <?= esc($medico['nome']) ?></p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('medicos') ?>">Médicos</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('medicos/show/' . $medico['id_medico']) ?>"><?= esc($medico['nome']) ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar</li>
                </ol>
            </nav>

            <!-- Form -->
            <div class="form-container">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-person-badge"></i> Dados do Médico
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (session()->getFlashdata('validation')): ?>
                            <div class="alert alert-danger">
                                <h6><i class="bi bi-exclamation-triangle"></i> Erro de validação:</h6>
                                <ul class="mb-0">
                                    <?php foreach (session()->getFlashdata('validation')->getErrors() as $error): ?>
                                        <li><?= $error ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('error')): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('medicos/update/' . $medico['id_medico']) ?>" method="post">
                            <?= csrf_field() ?>
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="nome" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="nome" name="nome" 
                                               value="<?= old('nome', $medico['nome']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="crm" class="form-label">CRM <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="crm" name="crm" 
                                               value="<?= old('crm', $medico['crm']) ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="especialidade" class="form-label">Especialidade</label>
                                        <input type="text" class="form-control" id="especialidade" name="especialidade" 
                                               value="<?= old('especialidade', $medico['especialidade']) ?>" 
                                               placeholder="Ex: Clínico Geral, Cardiologia, etc.">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="">Selecione o status</option>
                                            <option value="Ativo" <?= old('status', $medico['status']) === 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                                            <option value="Inativo" <?= old('status', $medico['status']) === 'Inativo' ? 'selected' : '' ?>>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Informações de auditoria -->
                            <div class="card bg-light mt-4">
                                <div class="card-body">
                                    <h6 class="card-title">Informações de Auditoria</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <strong>Cadastrado em:</strong> 
                                                <?= date('d/m/Y H:i:s', strtotime($medico['created_at'])) ?>
                                            </small>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="text-muted">
                                                <strong>Última atualização:</strong> 
                                                <?= date('d/m/Y H:i:s', strtotime($medico['updated_at'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="<?= base_url('medicos/show/' . $medico['id_medico']) ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check"></i> Salvar Alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            </div>
            <?= $this->include('components/footer') ?>
        </div>
    </main>
</div>

<script>
// Máscara para CRM
document.getElementById('crm').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    e.target.value = value;
});

// Capitalizar nome
document.getElementById('nome').addEventListener('input', function(e) {
    const words = e.target.value.split(' ');
    const capitalizedWords = words.map(word => {
        if (word.length > 2) {
            return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
        }
        return word.toLowerCase();
    });
    e.target.value = capitalizedWords.join(' ');
});
</script>

<?= $this->endSection() ?>
