<div class="row">
    <div class="col-md-4">
        <div class="text-center mb-3">
            <div class="patient-avatar-large">
                <?= substr($paciente['nome'], 0, 2) ?>
            </div>
            <h5 class="mt-2"><?= esc($paciente['nome']) ?></h5>
            <p class="text-muted"><?= esc($paciente['idade']) ?> anos</p>
        </div>
        
        <div class="info-list">
            <div class="info-item">
                <strong>CPF:</strong>
                <span><?= esc($paciente['cpf']) ?></span>
            </div>
            <div class="info-item">
                <strong>SUS:</strong>
                <span><?= esc($paciente['sus'] ?? 'Não informado') ?></span>
            </div>
            <div class="info-item">
                <strong>Data de Nascimento:</strong>
                <span><?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?></span>
            </div>
            <div class="info-item">
                <strong>Endereço:</strong>
                <span>
                    <?php if (!empty($paciente['nome_logradouro'])): ?>
                        <?= esc($paciente['tipo_logradouro'] ?? '') ?> <?= esc($paciente['nome_logradouro']) ?>
                        <?php if (!empty($paciente['numero'])): ?>
                            , <?= esc($paciente['numero']) ?>
                        <?php endif; ?>
                        <?php if (!empty($paciente['complemento'])): ?>
                            - <?= esc($paciente['complemento']) ?>
                        <?php endif; ?>
                        <?php if (!empty($paciente['nome_bairro'])): ?>
                            - <?= esc($paciente['nome_bairro']) ?>
                        <?php endif; ?>
                    <?php else: ?>
                        Não informado
                    <?php endif; ?>
                </span>
            </div>
            <div class="info-item">
                <strong>Cadastrado em:</strong>
                <span><?= date('d/m/Y \à\s H:i', strtotime($paciente['created_at'])) ?></span>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <h6><i class="bi bi-clock-history"></i> Últimos Atendimentos</h6>
        
        <?php if (isset($atendimentos_recentes) && !empty($atendimentos_recentes)): ?>
            <div class="timeline-simple">
                <?php foreach ($atendimentos_recentes as $atendimento): ?>
                    <div class="timeline-item-simple">
                        <div class="timeline-date">
                            <?= date('d/m/Y', strtotime($atendimento['data_atendimento'])) ?>
                        </div>
                        <div class="timeline-content-simple">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>Dr. <?= esc($atendimento['nome_medico']) ?></strong>
                                    <?php if (!empty($atendimento['hipotese_diagnostico'])): ?>
                                        <p class="mb-0 text-muted small">
                                            <?= esc(substr($atendimento['hipotese_diagnostico'], 0, 80)) ?>...
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-<?= getClassificacaoRiscoCor($atendimento['classificacao_risco']) ?>">
                                    <?= esc($atendimento['classificacao_risco']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <?php if (count($atendimentos_recentes) >= 5): ?>
                <div class="text-center mt-3">
                    <a href="<?= base_url('pacientes/' . $paciente['id_paciente']) ?>" class="btn btn-outline-primary btn-sm">
                        Ver Histórico Completo
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state-small text-center py-3">
                <i class="bi bi-journal-medical text-muted" style="font-size: 2rem;"></i>
                <p class="text-muted mt-2 mb-0">Nenhum atendimento registrado</p>
            </div>
        <?php endif; ?>
        
        <hr>
        
        <div class="d-flex justify-content-between">
            <a href="<?= base_url('pacientes/' . $paciente['id_paciente']) ?>" class="btn btn-outline-primary">
                <i class="bi bi-eye"></i> Ver Detalhes
            </a>
            <div>
                <a href="<?= base_url('pacientes/' . $paciente['id_paciente'] . '/edit') ?>" class="btn btn-outline-warning me-2">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <a href="<?= base_url('atendimentos/create?paciente=' . $paciente['id_paciente']) ?>" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Novo Atendimento
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.patient-avatar-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    font-weight: bold;
    margin: 0 auto;
}

.info-list {
    text-align: left;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--medium-gray);
}

.info-item:last-child {
    border-bottom: none;
}

.timeline-simple {
    max-height: 300px;
    overflow-y: auto;
}

.timeline-item-simple {
    display: flex;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid var(--medium-gray);
}

.timeline-item-simple:last-child {
    border-bottom: none;
}

.timeline-date {
    min-width: 80px;
    font-size: 0.85rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.timeline-content-simple {
    flex: 1;
    margin-left: 1rem;
}

.empty-state-small i {
    opacity: 0.5;
}
</style>

<?php
// Helper function
function getClassificacaoRiscoCor($classificacao) {
    switch (strtolower($classificacao)) {
        case 'vermelho': return 'danger';
        case 'amarelo': return 'warning';
        case 'verde': return 'success';
        case 'azul': return 'info';
        default: return 'secondary';
    }
}
?>
