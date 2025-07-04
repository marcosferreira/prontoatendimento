<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>
    <?= $this->include('components/topbar') ?>
    <main class="main-content">
        <div class="main-container">
            <div class="header">
                <h1><i class="bi bi-clipboard2-data"></i> Detalhes do Exame</h1>
                <p class="subtitle">Informações do Exame: <strong><?= esc($exame['nome']) ?></strong></p>
            </div>
            <div class="section-card mt-4">
                <dl class="row">
                    <dt class="col-sm-3">Nome</dt>
                    <dd class="col-sm-9"><?= esc($exame['nome']) ?></dd>
                    <dt class="col-sm-3">Código</dt>
                    <dd class="col-sm-9"><?= esc($exame['codigo'] ?? '-') ?></dd>
                    <dt class="col-sm-3">Tipo</dt>
                    <dd class="col-sm-9"><?= ucfirst($exame['tipo']) ?></dd>
                    <dt class="col-sm-3">Descrição</dt>
                    <dd class="col-sm-9"><?= esc($exame['descricao'] ?? '-') ?></dd>
                    <dt class="col-sm-3">Criado em</dt>
                    <dd class="col-sm-9"><?= date('d/m/Y H:i', strtotime($exame['created_at'])) ?></dd>
                </dl>
                <hr>
                <h5 class="mb-3">Estatísticas de Solicitação</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number"><?= $stats['total_solicitacoes'] ?></div>
                            <div class="stat-label">Total de Solicitações</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number"><?= $stats['solicitacoes_hoje'] ?></div>
                            <div class="stat-label">Hoje</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number"><?= $stats['solicitacoes_mes'] ?></div>
                            <div class="stat-label">Este Mês</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-item">
                            <div class="stat-number"><?= $stats['realizados'] ?></div>
                            <div class="stat-label">Realizados</div>
                        </div>
                    </div>
                    <div class="col-md-3 mt-3">
                        <div class="stat-item">
                            <div class="stat-number"><?= $stats['pendentes'] ?></div>
                            <div class="stat-label">Pendentes</div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <a href="<?= base_url('exames') ?>" class="btn btn-secondary">Voltar</a>
                </div>
            </div>
        </div>
    </main>
</div>
<?= $this->endSection() ?>
