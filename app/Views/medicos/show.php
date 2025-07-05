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
                <h1><i class="bi bi-person-badge"></i> Detalhes do Médico</h1>
                <p class="subtitle"><?= esc($medico['nome']) ?></p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('medicos') ?>">Médicos</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= esc($medico['nome']) ?></li>
                </ol>
            </nav>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-end mb-3">
                <div class="btn-group">
                    <a href="<?= base_url('medicos/edit/' . $medico['id_medico']) ?>" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="<?= base_url('medicos') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <div class="row">
                <!-- Informações do Médico -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-person-badge"></i> Informações Pessoais
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label>ID do Médico:</label>
                                        <span class="badge bg-info">#<?= $medico['id_medico'] ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label>Status:</label>
                                        <?php if ($medico['status'] === 'Ativo'): ?>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle"></i> Ativo
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">
                                                <i class="bi bi-pause-circle"></i> Inativo
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-md-8">
                                    <div class="info-item">
                                        <label>Nome Completo:</label>
                                        <p><?= esc($medico['nome']) ?></p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <label>CRM:</label>
                                        <p><strong><?= esc($medico['crm']) ?></strong></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="info-item">
                                        <label>Especialidade:</label>
                                        <p><?= esc($medico['especialidade'] ?? 'Não informada') ?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label>Cadastrado em:</label>
                                        <p><?= date('d/m/Y H:i:s', strtotime($medico['created_at'])) ?></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <label>Última atualização:</label>
                                        <p><?= date('d/m/Y H:i:s', strtotime($medico['updated_at'])) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-graph-up"></i> Estatísticas de Atendimentos
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="stat-item">
                                <div class="stat-icon bg-primary">
                                    <i class="bi bi-clipboard-data"></i>
                                </div>
                                <div class="stat-details">
                                    <h4><?= number_format($stats['total_atendimentos']) ?></h4>
                                    <p>Total de Atendimentos</p>
                                </div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-icon bg-success">
                                    <i class="bi bi-calendar-today"></i>
                                </div>
                                <div class="stat-details">
                                    <h4><?= number_format($stats['atendimentos_hoje']) ?></h4>
                                    <p>Atendimentos Hoje</p>
                                </div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-icon bg-info">
                                    <i class="bi bi-calendar-month"></i>
                                </div>
                                <div class="stat-details">
                                    <h4><?= number_format($stats['atendimentos_mes']) ?></h4>
                                    <p>Atendimentos este Mês</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ações Rápidas -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-lightning"></i> Ações Rápidas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="<?= base_url('atendimentos/create?medico=' . $medico['id_medico']) ?>" 
                                   class="btn btn-success">
                                    <i class="bi bi-plus-circle"></i> Novo Atendimento
                                </a>
                                <a href="<?= base_url('atendimentos?medico=' . $medico['id_medico']) ?>" 
                                   class="btn btn-outline-primary">
                                    <i class="bi bi-list"></i> Ver Atendimentos
                                </a>
                                <a href="<?= base_url('medicos/edit/' . $medico['id_medico']) ?>" 
                                   class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil"></i> Editar Dados
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            <?= $this->include('components/footer') ?>
        </div>
    </main>
</div>

<style>
.info-item {
    margin-bottom: 1rem;
}

.info-item label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
    display: block;
    margin-bottom: 0.25rem;
}

.info-item p {
    margin: 0;
    font-size: 1rem;
    color: #212529;
}

.stat-item {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 0.375rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 1rem;
}

.stat-icon i {
    font-size: 1.25rem;
    color: white;
}

.stat-details h4 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: #212529;
}

.stat-details p {
    margin: 0;
    font-size: 0.875rem;
    color: #6c757d;
}
</style>

<?= $this->endSection() ?>
