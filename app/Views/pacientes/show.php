<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>
    
        <?= $this->include('components/topbar') ?>
        
        <main class="main-content">
            <div class="main-container">
                <!-- Header -->
                <div class="header">
                    <h1><i class="bi bi-person-circle"></i> <?= esc($paciente['nome']) ?></h1>
                    <p class="subtitle">Dados completos e histórico médico</p>
                </div>

                <!-- Action Bar -->
                <div class="action-bar">
                    <div class="action-left">
                        <a href="<?= base_url('pacientes') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                    <div class="action-right">
                        <a href="<?= base_url('atendimentos/create?paciente=' . $paciente['id_paciente']) ?>" 
                           class="btn btn-success me-2">
                            <i class="bi bi-plus-circle"></i> Novo Atendimento
                        </a>
                        <a href="<?= base_url('pacientes/' . $paciente['id_paciente'] . '/edit') ?>" 
                           class="btn btn-warning me-2">
                            <i class="bi bi-pencil"></i> Editar
                        </a>
                        <button type="button" class="btn btn-outline-primary" onclick="printPacienteCard()">
                            <i class="bi bi-printer"></i> Imprimir
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div class="content-wrapper">
                    <div class="row">
                        <!-- Informações Pessoais -->
                        <div class="col-lg-4">
                            <div class="section-card">
                                <div class="section-header">
                                    <h3 class="section-title">
                                        <i class="bi bi-person-vcard"></i>
                                        Informações Pessoais
                                    </h3>
                                </div>

                                <div class="patient-info">
                                    <div class="patient-avatar">
                                        <?= substr($paciente['nome'], 0, 2) ?>
                                    </div>
                                    
                                    <div class="info-group">
                                        <label>Nome Completo:</label>
                                        <p><?= esc($paciente['nome']) ?></p>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="info-group">
                                                <label>CPF:</label>
                                                <p><?= esc($paciente['cpf']) ?></p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="info-group">
                                                <label>Cartão SUS:</label>
                                                <p><?= esc($paciente['sus'] ?? 'Não informado') ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="info-group">
                                                <label>Data de Nascimento:</label>
                                                <p><?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?></p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="info-group">
                                                <label>Idade:</label>
                                                <p><strong><?= esc($paciente['idade']) ?> anos</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="info-group">
                                        <label>Endereço:</label>
                                        <p><?= esc($paciente['endereco'] ?? 'Não informado') ?></p>
                                    </div>
                                    
                                    <div class="info-group">
                                        <label>Bairro:</label>
                                        <p><?= esc($paciente['nome_bairro'] ?? 'Não informado') ?></p>
                                    </div>
                                    
                                    <div class="info-group">
                                        <label>Cadastrado em:</label>
                                        <p><?= date('d/m/Y \à\s H:i', strtotime($paciente['created_at'])) ?></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Estatísticas do Paciente -->
                            <div class="section-card">
                                <div class="section-header">
                                    <h4 class="section-title">
                                        <i class="bi bi-graph-up"></i>
                                        Estatísticas
                                    </h4>
                                </div>

                                <div class="stats-grid">
                                    <div class="stat-item-small">
                                        <div class="stat-number"><?= isset($stats['total_atendimentos']) ? $stats['total_atendimentos'] : '0' ?></div>
                                        <div class="stat-label">Atendimentos</div>
                                    </div>
                                    <div class="stat-item-small">
                                        <div class="stat-number"><?= isset($stats['exames_realizados']) ? $stats['exames_realizados'] : '0' ?></div>
                                        <div class="stat-label">Exames</div>
                                    </div>
                                    <div class="stat-item-small">
                                        <div class="stat-number"><?= isset($stats['procedimentos_realizados']) ? $stats['procedimentos_realizados'] : '0' ?></div>
                                        <div class="stat-label">Procedimentos</div>
                                    </div>
                                    <div class="stat-item-small">
                                        <div class="stat-number"><?= isset($stats['ultimo_atendimento']) ? $stats['ultimo_atendimento'] : 'Nunca' ?></div>
                                        <div class="stat-label">Último Atend.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Histórico Médico -->
                        <div class="col-lg-8">
                            <div class="section-card">
                                <div class="section-header">
                                    <h3 class="section-title">
                                        <i class="bi bi-journal-medical"></i>
                                        Histórico de Atendimentos
                                    </h3>
                                    <div class="section-actions">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary active" onclick="showTab('atendimentos')">
                                                Atendimentos
                                            </button>
                                            <button type="button" class="btn btn-outline-primary" onclick="showTab('exames')">
                                                Exames
                                            </button>
                                            <button type="button" class="btn btn-outline-primary" onclick="showTab('procedimentos')">
                                                Procedimentos
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tab Atendimentos -->
                                <div id="tab-atendimentos" class="tab-content active">
                                    <?php if (isset($atendimentos) && !empty($atendimentos)): ?>
                                        <div class="timeline">
                                            <?php foreach ($atendimentos as $atendimento): ?>
                                                <div class="timeline-item">
                                                    <div class="timeline-marker bg-<?= getClassificacaoRiscoCor($atendimento['classificacao_risco']) ?>"></div>
                                                    <div class="timeline-content">
                                                        <div class="d-flex justify-content-between align-items-start">
                                                            <div>
                                                                <h6><?= date('d/m/Y \à\s H:i', strtotime($atendimento['data_atendimento'])) ?></h6>
                                                                <p class="mb-1">
                                                                    <strong>Dr. <?= esc($atendimento['nome_medico']) ?></strong>
                                                                    <span class="text-muted">- <?= esc($atendimento['especialidade'] ?? 'Clínico Geral') ?></span>
                                                                </p>
                                                            </div>
                                                            <span class="badge bg-<?= getClassificacaoRiscoCor($atendimento['classificacao_risco']) ?>">
                                                                <?= esc($atendimento['classificacao_risco']) ?>
                                                            </span>
                                                        </div>
                                                        
                                                        <?php if (!empty($atendimento['consulta_enfermagem'])): ?>
                                                            <div class="mt-2">
                                                                <strong>Triagem:</strong>
                                                                <p class="text-muted small mb-1"><?= esc($atendimento['consulta_enfermagem']) ?></p>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <?php if (!empty($atendimento['hipotese_diagnostico'])): ?>
                                                            <div class="mt-2">
                                                                <strong>Diagnóstico:</strong>
                                                                <p class="mb-1"><?= esc($atendimento['hipotese_diagnostico']) ?></p>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <?php if (!empty($atendimento['encaminhamento'])): ?>
                                                            <div class="mt-2">
                                                                <span class="badge bg-secondary"><?= esc($atendimento['encaminhamento']) ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                        
                                                        <div class="mt-2">
                                                            <a href="<?= base_url('atendimentos/' . $atendimento['id_atendimento']) ?>" 
                                                               class="btn btn-outline-primary btn-sm">
                                                                Ver Detalhes
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-state text-center py-4">
                                            <i class="bi bi-journal-medical text-muted" style="font-size: 3rem;"></i>
                                            <h5 class="text-muted mt-2">Nenhum atendimento registrado</h5>
                                            <p class="text-muted">Este paciente ainda não possui histórico de atendimentos.</p>
                                            <a href="<?= base_url('atendimentos/create?paciente=' . $paciente['id_paciente']) ?>" 
                                               class="btn btn-success">
                                                <i class="bi bi-plus-circle"></i> Novo Atendimento
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Tab Exames -->
                                <div id="tab-exames" class="tab-content">
                                    <?php if (isset($exames) && !empty($exames)): ?>
                                        <div class="table-responsive">
                                            <table class="table modern-table">
                                                <thead>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Exame</th>
                                                        <th>Status</th>
                                                        <th>Resultado</th>
                                                        <th>Ações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($exames as $exame): ?>
                                                        <tr>
                                                            <td><?= date('d/m/Y', strtotime($exame['data_solicitacao'])) ?></td>
                                                            <td>
                                                                <strong><?= esc($exame['nome_exame']) ?></strong>
                                                                <br><small class="text-muted"><?= esc($exame['tipo']) ?></small>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-<?= getStatusExameCor($exame['status']) ?>">
                                                                    <?= esc($exame['status']) ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($exame['resultado'])): ?>
                                                                    <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                                                        <?= esc($exame['resultado']) ?>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-outline-primary btn-sm" 
                                                                        onclick="viewExame(<?= $exame['id_atendimento_exame'] ?>)">
                                                                    <i class="bi bi-eye"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-state text-center py-4">
                                            <i class="bi bi-clipboard-data text-muted" style="font-size: 3rem;"></i>
                                            <h5 class="text-muted mt-2">Nenhum exame registrado</h5>
                                            <p class="text-muted">Este paciente ainda não possui histórico de exames.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Tab Procedimentos -->
                                <div id="tab-procedimentos" class="tab-content">
                                    <?php if (isset($procedimentos) && !empty($procedimentos)): ?>
                                        <div class="table-responsive">
                                            <table class="table modern-table">
                                                <thead>
                                                    <tr>
                                                        <th>Data</th>
                                                        <th>Procedimento</th>
                                                        <th>Quantidade</th>
                                                        <th>Médico</th>
                                                        <th>Observações</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($procedimentos as $procedimento): ?>
                                                        <tr>
                                                            <td><?= date('d/m/Y', strtotime($procedimento['data_atendimento'])) ?></td>
                                                            <td>
                                                                <strong><?= esc($procedimento['nome_procedimento']) ?></strong>
                                                                <?php if (!empty($procedimento['codigo'])): ?>
                                                                    <br><small class="text-muted"><?= esc($procedimento['codigo']) ?></small>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= esc($procedimento['quantidade']) ?></td>
                                                            <td><?= esc($procedimento['nome_medico']) ?></td>
                                                            <td>
                                                                <?php if (!empty($procedimento['observacao'])): ?>
                                                                    <span class="text-truncate d-inline-block" style="max-width: 200px;">
                                                                        <?= esc($procedimento['observacao']) ?>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    <?php else: ?>
                                        <div class="empty-state text-center py-4">
                                            <i class="bi bi-gear text-muted" style="font-size: 3rem;"></i>
                                            <h5 class="text-muted mt-2">Nenhum procedimento registrado</h5>
                                            <p class="text-muted">Este paciente ainda não possui histórico de procedimentos.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
</div>

<script>
// Navegação entre tabs
function showTab(tabName) {
    // Remove active class from all tabs and buttons
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Add active class to selected tab and button
    document.getElementById(`tab-${tabName}`).classList.add('active');
    event.target.classList.add('active');
}

// Função para imprimir ficha do paciente
function printPacienteCard() {
    window.open('<?= base_url('pacientes/' . $paciente['id_paciente'] . '/print') ?>', '_blank');
}

// Função para visualizar exame
function viewExame(id) {
    // Implementar modal ou redirecionamento para detalhes do exame
    window.location.href = `<?= base_url('exames') ?>/${id}`;
}
</script>

<style>
.patient-info {
    text-align: center;
}

.patient-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0 auto 1rem;
}

.info-group {
    text-align: left;
    margin-bottom: 1rem;
}

.info-group label {
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 0.85rem;
    margin-bottom: 0.25rem;
    display: block;
}

.info-group p {
    margin: 0;
    font-weight: 500;
}

.stats-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
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

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--medium-gray);
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0.5rem;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content {
    background: white;
    border-radius: var(--border-radius);
    padding: 1rem;
    box-shadow: var(--shadow);
}
</style>

<?= $this->endSection() ?>

<?php
// Helper functions
function getClassificacaoRiscoCor($classificacao) {
    switch (strtolower($classificacao)) {
        case 'vermelho': return 'danger';
        case 'amarelo': return 'warning';
        case 'verde': return 'success';
        case 'azul': return 'info';
        default: return 'secondary';
    }
}

function getStatusExameCor($status) {
    switch (strtolower($status)) {
        case 'realizado': return 'success';
        case 'solicitado': return 'warning';
        case 'cancelado': return 'danger';
        default: return 'secondary';
    }
}
?>
