<?php echo $this->extend('layout/base'); ?>

<?php echo $this->section('content'); ?>
<div class="app-container">
    <!-- Sidebar -->
    <?php echo $this->include('components/sidebar'); ?>

    <!-- Topbar -->
    <?php echo $this->include('components/topbar'); ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-activity"></i> Painel de Controle</h1>
                <p class="subtitle">Monitoramento em Tempo Real | Pronto Atendimento Municipal</p>
            </div>

            <!-- Content -->
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-person-badge"></i>
                                Últimos Atendimentos (5 mais recentes)
                            </h2>
                            <div class="table-responsive">
                                
                                <?php if (!empty($ultimosAtendimentos)): ?>
                                    <table class="table modern-table">
                                        <thead>
                                            <tr>
                                                <th scope="col">ID</th>
                                                <th scope="col">Paciente</th>
                                                <th scope="col">Médico</th>
                                                <th scope="col">Data/Hora</th>
                                                <th scope="col">Classificação</th>
                                                <th scope="col">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($ultimosAtendimentos as $atendimento): ?>
                                                <tr>
                                                    <td data-label="ID"><strong>AT-<?= str_pad($atendimento['id_atendimento'], 3, '0', STR_PAD_LEFT) ?></strong></td>
                                                    <td data-label="Paciente"><?= esc($atendimento['paciente_nome']) ?></td>
                                                    <td data-label="Médico"><?= esc($atendimento['medico_nome']) ?></td>
                                                    <td data-label="Data/Hora"><?= date('d/m H:i', strtotime($atendimento['data_atendimento'])) ?></td>
                                                    <td data-label="Classificação">
                                                        <span class="priority-indicator priority-<?= getClassificacaoRiscoClass($atendimento['classificacao_risco']) ?>"></span>
                                                        <?= esc($atendimento['classificacao_risco']) ?>
                                                    </td>
                                                    <td data-label="Status">
                                                        <span class="status-badge status-<?= getStatusClass($atendimento['status']) ?>">
                                                            <?= esc($atendimento['status']) ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                <?php else: ?>
                                    <div class="empty-state text-center py-4">
                                        <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-2">Nenhum atendimento registrado recentemente</p>
                                        <a href="<?= base_url('atendimentos/create') ?>" class="btn btn-primary btn-sm">
                                            <i class="bi bi-plus-circle"></i> Novo Atendimento
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-people"></i>
                                Médicos Ativos Hoje
                            </h2>
                            <?php if (!empty($medicosAtivos)): ?>
                                <ul class="recent-list">
                                    <?php foreach ($medicosAtivos as $medico): ?>
                                        <li class="recent-item">
                                            <strong><?= esc($medico['nome']) ?></strong> - <?= esc($medico['especialidade'] ?? 'Clínico Geral') ?><br>
                                            <small>
                                                <i class="bi bi-clipboard-check"></i> <?= $medico['atendimentos_hoje'] ?> atendimento<?= $medico['atendimentos_hoje'] != 1 ? 's' : '' ?> hoje
                                                | <i class="bi bi-clock"></i> Ativo
                                            </small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <div class="empty-state text-center py-3">
                                    <i class="bi bi-person-x text-muted" style="font-size: 2rem;"></i>
                                    <p class="text-muted mt-2">Nenhum médico realizou atendimentos hoje</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-bar-chart"></i>
                                Indicadores Operacionais
                            </h2>
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-number"><?= $stats['total_pacientes'] ?></div>
                                    <div class="stat-label">Total de Pacientes</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?= $stats['atendimentos_hoje'] ?></div>
                                    <div class="stat-label">Atendimentos Hoje</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?= $stats['idade_media'] ?></div>
                                    <div class="stat-label">Idade Média</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?= $stats['casos_vermelhos_hoje'] ?></div>
                                    <div class="stat-label">Casos Prioritários</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?= $stats['atendimentos_em_andamento'] ?></div>
                                    <div class="stat-label">Em Andamento</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?= $stats['medicos_ativos'] ?></div>
                                    <div class="stat-label">Médicos Ativos</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Métricas Resumidas por Classificação -->
                <div class="row">
                    <div class="col-12">
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-pie-chart"></i>
                                Classificação de Risco - Hoje (Protocolo Manchester)
                            </h2>
                            <div class="stats-grid">
                                <div class="stat-item stat-danger">
                                    <div class="stat-number"><?= $stats['casos_vermelhos_hoje'] ?></div>
                                    <div class="stat-label">Vermelho (Emergência - 0min)</div>
                                </div>
                                <div class="stat-item stat-orange">
                                    <div class="stat-number"><?= $stats['casos_laranjas_hoje'] ?></div>
                                    <div class="stat-label">Laranja (Muito Urgente - 10min)</div>
                                </div>
                                <div class="stat-item stat-warning">
                                    <div class="stat-number"><?= $stats['casos_amarelos_hoje'] ?></div>
                                    <div class="stat-label">Amarelo (Urgente - 60min)</div>
                                </div>
                                <div class="stat-item stat-success">
                                    <div class="stat-number"><?= $stats['casos_verdes_hoje'] ?></div>
                                    <div class="stat-label">Verde (Pouco Urgente - 120min)</div>
                                </div>
                                <div class="stat-item stat-info">
                                    <div class="stat-number"><?= $stats['casos_azuis_hoje'] ?></div>
                                    <div class="stat-label">Azul (Não Urgente - 240min)</div>
                                </div>
                                <div class="stat-item stat-secondary">
                                    <div class="stat-number"><?= $stats['casos_sem_classificacao_hoje'] ?? 0 ?></div>
                                    <div class="stat-label">Sem Classificação</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Links Rápidos para as principais funcionalidades -->
                <div class="row">
                    <div class="col-12">
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-lightning"></i>
                                Ações Rápidas
                            </h2>
                            <div class="quick-actions">
                                <a href="<?= base_url('atendimentos/create') ?>" class="quick-action-card">
                                    <i class="bi bi-plus-circle"></i>
                                    <span>Novo Atendimento</span>
                                </a>
                                <a href="<?= base_url('pacientes/create') ?>" class="quick-action-card">
                                    <i class="bi bi-person-plus"></i>
                                    <span>Cadastrar Paciente</span>
                                </a>
                                <a href="<?= base_url('atendimentos') ?>" class="quick-action-card">
                                    <i class="bi bi-list-ul"></i>
                                    <span>Lista de Atendimentos</span>
                                </a>
                                <a href="<?= base_url('pacientes') ?>" class="quick-action-card">
                                    <i class="bi bi-people"></i>
                                    <span>Lista de Pacientes</span>
                                </a>
                                <a href="<?= base_url('atendimentos/relatorio') ?>" class="quick-action-card">
                                    <i class="bi bi-bar-chart"></i>
                                    <span>Relatórios</span>
                                </a>
                                <a href="<?= base_url('notificacoes') ?>" class="quick-action-card">
                                    <i class="bi bi-bell"></i>
                                    <span>Notificações BI</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-shield-exclamation"></i>
                                Notificações do Sistema
                            </h2>
                            <?php if (!empty($notificacoes)): ?>
                                <?php foreach ($notificacoes as $notificacao): ?>
                                    <div class="alert-modern my-2 alert-<?= $notificacao['severidade'] ?>" role="alert">
                                        <i class="bi bi-<?= getSeveridadeIcone($notificacao['severidade']) ?>"></i>
                                        <div>
                                            <strong><?= esc($notificacao['titulo']) ?>:</strong> 
                                            <?= esc($notificacao['descricao']) ?>
                                            <br><small class="text-muted">
                                                <i class="bi bi-clock"></i> <?= date('d/m/Y H:i', strtotime($notificacao['created_at'])) ?>
                                                | Módulo: <?= esc($notificacao['modulo']) ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert-modern alert-success" role="alert">
                                    <i class="bi bi-check-circle-fill"></i>
                                    <div>
                                        <strong>Sistema Operacional:</strong> Nenhum alerta ativo no momento. Todos os indicadores estão dentro do esperado.
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Resumo Mensal -->
                <div class="row">
                    <div class="col-12">
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-calendar-month"></i>
                                Resumo do Mês Atual
                            </h2>
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-number"><?= $stats['pacientes_mes'] ?></div>
                                    <div class="stat-label">Pacientes Cadastrados</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?= $stats['atendimentos_mes'] ?></div>
                                    <div class="stat-label">Atendimentos Realizados</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?= $stats['total_bairros'] ?></div>
                                    <div class="stat-label">Bairros Atendidos</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?= $stats['notificacoes_ativas'] ?></div>
                                    <div class="stat-label">Notificações Ativas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <?php echo $this->include('components/footer'); ?>
        </div>
    </main>
</div>
<?php echo $this->endSection('content'); ?>