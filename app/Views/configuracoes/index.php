<?= $this->extend('layout/base') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="app-container">
    <!-- Sidebar -->
    <?= $this->include('components/sidebar') ?>

    <!-- Topbar -->
    <?= $this->include('components/topbar') ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-gear"></i> <?= $title ?></h1>
                <p class="subtitle"><?= $description ?></p>
            </div>

            <!-- Content -->
            <div class="content-wrapper">
        <!-- Configuration Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="section-card">
                    <ul class="nav nav-tabs" id="configTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                                <i class="bi bi-people"></i> Usuários
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="system-tab" data-bs-toggle="tab" data-bs-target="#system" type="button" role="tab">
                                <i class="bi bi-gear"></i> Sistema
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="backup-tab" data-bs-toggle="tab" data-bs-target="#backup" type="button" role="tab">
                                <i class="bi bi-shield-check"></i> Backup
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="audit-tab" data-bs-toggle="tab" data-bs-target="#audit" type="button" role="tab">
                                <i class="bi bi-list-check"></i> Auditoria
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content mt-3" id="configTabsContent">
                        <!-- Users Tab -->
                        <div class="tab-pane fade show active" id="users" role="tabpanel">
                            <?= $this->include('configuracoes/tabs/users') ?>
                        </div>
                        
                        <!-- System Tab -->
                        <div class="tab-pane fade" id="system" role="tabpanel">
                            <?= $this->include('configuracoes/tabs/system') ?>
                        </div>
                        
                        <!-- Backup Tab -->
                        <div class="tab-pane fade" id="backup" role="tabpanel">
                            <?= $this->include('configuracoes/tabs/backup') ?>
                        </div>
                        
                        <!-- Audit Tab -->
                        <div class="tab-pane fade" id="audit" role="tabpanel">
                            <?= $this->include('configuracoes/tabs/audit') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>&copy; 2025 Sistema de Pronto Atendimento Municipal. Todos os direitos reservados. | Versão 2.1.0</p>
            </div>
        </div>
    </main>
</div>

<!-- Modals -->
<?= $this->include('configuracoes/modals/new_user') ?>
<?= $this->include('configuracoes/modals/edit_user') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/js/configuracoes.js') ?>"></script>
<?= $this->endSection() ?>
