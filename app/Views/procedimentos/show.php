<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-clipboard-check"></i> <?= esc($procedimento['nome']) ?></h1>
                <p class="subtitle">Detalhes do procedimento médico</p>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-left m-4">
                    <a href="<?= base_url('procedimentos') ?>" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
                <div class="action-right m-4">
                    <a href="<?= base_url('procedimentos/' . $procedimento['id_procedimento'] . '/edit') ?>"
                        class="btn btn-warning me-2">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <div class="btn-group">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots"></i> Ações
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="printProcedimento()">
                                <i class="bi bi-printer"></i> Imprimir
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="confirmDelete()">
                                <i class="bi bi-trash"></i> Excluir
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="content-wrapper">
                <div class="row">
                    <!-- Informações do Procedimento -->
                    <div class="col-lg-8">
                        <div class="section-card">
                            <div class="section-header">
                                <h3 class="section-title">
                                    <i class="bi bi-info-circle"></i>
                                    Informações do Procedimento
                                </h3>
                            </div>

                            <div class="procedure-info">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="info-group">
                                            <label>Nome do Procedimento:</label>
                                            <h4 class="text-primary"><?= esc($procedimento['nome']) ?></h4>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="info-group">
                                            <label>Código:</label>
                                            <?php if (!empty($procedimento['codigo'])): ?>
                                                <p class="h5">
                                                    <span class="badge bg-secondary"><?= esc($procedimento['codigo']) ?></span>
                                                </p>
                                            <?php else: ?>
                                                <p class="text-muted">Não informado</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if (!empty($procedimento['descricao'])): ?>
                                    <div class="info-group">
                                        <label>Descrição:</label>
                                        <div class="description-box">
                                            <?= nl2br(esc($procedimento['descricao'])) ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <label>Cadastrado em:</label>
                                            <p><?= date('d/m/Y \à\s H:i', strtotime($procedimento['created_at'])) ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="info-group">
                                            <label>Última atualização:</label>
                                            <p><?= date('d/m/Y \à\s H:i', strtotime($procedimento['updated_at'])) ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Estatísticas de Uso -->
                    <div class="col-lg-4">
                        <div class="section-card">
                            <div class="section-header">
                                <h4 class="section-title">
                                    <i class="bi bi-graph-up"></i>
                                    Estatísticas de Uso
                                </h4>
                            </div>

                            <div class="stats-grid">
                                <div class="stat-item-small">
                                    <div class="stat-number"><?= $stats['total_usos'] ?? 0 ?></div>
                                    <div class="stat-label">Total de Usos</div>
                                </div>
                                <div class="stat-item-small">
                                    <div class="stat-number"><?= $stats['usos_hoje'] ?? 0 ?></div>
                                    <div class="stat-label">Usos Hoje</div>
                                </div>
                                <div class="stat-item-small">
                                    <div class="stat-number"><?= $stats['usos_mes'] ?? 0 ?></div>
                                    <div class="stat-label">Usos este Mês</div>
                                </div>
                                <div class="stat-item-small">
                                    <div class="stat-number">
                                        <?php
                                        $totalUsos = $stats['total_usos'] ?? 0;
                                        if ($totalUsos > 0) {
                                            $diasCadastrado = (time() - strtotime($procedimento['created_at'])) / (60 * 60 * 24);
                                            $media = $diasCadastrado > 0 ? round($totalUsos / max(1, $diasCadastrado), 1) : $totalUsos;
                                            echo $media;
                                        } else {
                                            echo '0';
                                        }
                                        ?>
                                    </div>
                                    <div class="stat-label">Média por Dia</div>
                                </div>
                            </div>
                        </div>

                        <!-- Ações Rápidas -->
                        <div class="section-card">
                            <div class="section-header">
                                <h4 class="section-title">
                                    <i class="bi bi-lightning"></i>
                                    Ações Rápidas
                                </h4>
                            </div>

                            <div class="quick-actions">
                                <a href="<?= base_url('atendimentos/create?procedimento=' . $procedimento['id_procedimento']) ?>"
                                    class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bi bi-plus-circle"></i> Usar em Atendimento
                                </a>
                                <a href="<?= base_url('procedimentos/' . $procedimento['id_procedimento'] . '/edit') ?>"
                                    class="btn btn-outline-warning w-100 mb-2">
                                    <i class="bi bi-pencil"></i> Editar Dados
                                </a>
                                <a href="<?= base_url('procedimentos/create?duplicate=' . $procedimento['id_procedimento']) ?>"
                                    class="btn btn-outline-success w-100">
                                    <i class="bi bi-copy"></i> Duplicar Procedimento
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?= $this->include('components/footer') ?>
        </div>
    </main>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Função para imprimir procedimento
    function printProcedimento() {
        window.print();
    }

    // Função para confirmar exclusão
    function confirmDelete() {
        if (confirm('Tem certeza que deseja excluir este procedimento?\n\nEsta ação não pode ser desfeita.')) {
            // Fazer requisição AJAX para excluir
            fetch('<?= base_url('procedimentos/delete/' . $procedimento['id_procedimento']) ?>', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Procedimento excluído com sucesso!');
                    window.location.href = '<?= base_url('procedimentos') ?>';
                } else {
                    alert('Erro: ' + (data.error || 'Não foi possível excluir o procedimento'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Erro ao excluir procedimento');
            });
        }
    }
</script>

<style>
    .procedure-info {
        padding: 1rem 0;
    }

    .info-group {
        margin-bottom: 1.5rem;
    }

    .info-group label {
        font-weight: 600;
        color: var(--text-secondary);
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
        display: block;
    }

    .info-group p, .info-group h4 {
        margin: 0;
        font-weight: 500;
    }

    .description-box {
        background: var(--light-gray);
        border-radius: var(--border-radius);
        padding: 1rem;
        border-left: 4px solid var(--primary-color);
        line-height: 1.6;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .stat-item-small {
        text-align: center;
        padding: 1rem;
        background: var(--light-gray);
        border-radius: var(--border-radius);
    }

    .stat-item-small .stat-number {
        font-size: 1.5rem;
        font-weight: bold;
        color: var(--primary-color);
    }

    .stat-item-small .stat-label {
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-top: 0.25rem;
    }

    .quick-actions {
        padding: 0.5rem 0;
    }

    @media print {
        .action-bar,
        .quick-actions,
        .btn {
            display: none !important;
        }
        
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>

<?= $this->endSection() ?>
