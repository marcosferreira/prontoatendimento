<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>
    <?= $this->include('components/topbar') ?>
    <main class="main-content">
        <div class="main-container">
            <div class="header">
                <h1><i class="bi bi-clipboard-plus"></i> Novo Exame</h1>
                <p class="subtitle">Cadastrar Novo Exame</p>
            </div>
            <div class="section-card mt-4">
                <form action="<?= base_url('exames/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Exame <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="nome" name="nome" value="<?= old('nome') ?>" required>
                        <?php if (session('validation') && session('validation')->hasError('nome')): ?>
                            <div class="text-danger small"><?= session('validation')->getError('nome') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="codigo" class="form-label">Código</label>
                        <input type="text" class="form-control" id="codigo" name="codigo" value="<?= old('codigo') ?>">
                        <?php if (session('validation') && session('validation')->hasError('codigo')): ?>
                            <div class="text-danger small"><?= session('validation')->getError('codigo') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select" id="tipo" name="tipo" required>
                            <option value="">Selecione</option>
                            <?php foreach ($tipos_exame as $tipo): ?>
                                <option value="<?= $tipo ?>" <?= old('tipo') == $tipo ? 'selected' : '' ?>><?= ucfirst($tipo) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (session('validation') && session('validation')->hasError('tipo')): ?>
                            <div class="text-danger small"><?= session('validation')->getError('tipo') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="2"><?= old('descricao') ?></textarea>
                        <?php if (session('validation') && session('validation')->hasError('descricao')): ?>
                            <div class="text-danger small"><?= session('validation')->getError('descricao') ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="d-flex justify-content-end">
                        <a href="<?= base_url('exames') ?>" class="btn btn-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar Exame
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>
<?= $this->endSection() ?>
