<div class="row">
    <div class="col-md-6">
        <h6><i class="bi bi-info-circle"></i> Informações do Bairro</h6>
        <div class="info-grid">
            <div class="info-item">
                <strong>ID:</strong>
                <span class="badge bg-secondary"><?= $bairro['id_bairro'] ?></span>
            </div>
            <div class="info-item">
                <strong>Nome:</strong>
                <span><?= esc($bairro['nome_bairro']) ?></span>
            </div>
            <div class="info-item">
                <strong>Área/Região:</strong>
                <span><?= $bairro['area'] ? esc($bairro['area']) : '<em class="text-muted">Não informado</em>' ?></span>
            </div>
            <div class="info-item">
                <strong>Cadastrado em:</strong>
                <span><?= date('d/m/Y \à\s H:i', strtotime($bairro['created_at'])) ?></span>
            </div>
            <?php if (isset($bairro['updated_at']) && $bairro['updated_at'] != $bairro['created_at']): ?>
            <div class="info-item">
                <strong>Última atualização:</strong>
                <span><?= date('d/m/Y \à\s H:i', strtotime($bairro['updated_at'])) ?></span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="col-md-6">
        <h6><i class="bi bi-people"></i> Pacientes do Bairro</h6>
        
        <?php if (!empty($pacientes)): ?>
            <div class="stat-card mb-3">
                <div class="stat-icon bg-info">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stat-info">
                    <h4><?= count($pacientes) ?></h4>
                    <p>Pacientes Cadastrados</p>
                </div>
            </div>
            
            <?php if (count($pacientes) <= 5): ?>
                <div class="patient-list">
                    <?php foreach ($pacientes as $paciente): ?>
                        <div class="patient-item">
                            <div class="patient-info">
                                <strong><?= esc($paciente['nome']) ?></strong>
                                <br>
                                <small class="text-muted">
                                    CPF: <?= esc($paciente['cpf']) ?> | 
                                    <?= $paciente['idade'] ?> anos
                                </small>
                            </div>
                            <div class="patient-actions">
                                <a href="<?= base_url('pacientes/' . $paciente['id_paciente']) ?>" 
                                   class="btn btn-sm btn-outline-info"
                                   title="Ver detalhes">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="patient-summary">
                    <p>Este bairro possui <strong><?= count($pacientes) ?></strong> pacientes cadastrados.</p>
                    <a href="<?= base_url('pacientes?bairro=' . $bairro['id_bairro']) ?>" class="btn btn-sm btn-primary">
                        <i class="bi bi-list"></i> Ver Todos os Pacientes
                    </a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state-small">
                <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                <p class="text-muted mt-2">Nenhum paciente cadastrado neste bairro.</p>
                <a href="<?= base_url('pacientes/create?bairro=' . $bairro['id_bairro']) ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-circle"></i> Cadastrar Paciente
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal-actions mt-3">
    <div class="d-flex justify-content-between">
        <div>
            <a href="<?= base_url('bairros/' . $bairro['id_bairro']) ?>" class="btn btn-outline-primary">
                <i class="bi bi-eye"></i> Ver Detalhes Completos
            </a>
        </div>
        <div>
            <a href="<?= base_url('bairros/' . $bairro['id_bairro'] . '/edit') ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
        </div>
    </div>
</div>

<style>
.info-grid .info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-grid .info-item:last-child {
    border-bottom: none;
}

.patient-list {
    max-height: 200px;
    overflow-y: auto;
}

.patient-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.patient-item:last-child {
    border-bottom: none;
}

.stat-card {
    display: flex;
    align-items: center;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 15px;
}

.stat-info h4 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: bold;
}

.stat-info p {
    margin: 0;
    font-size: 0.9rem;
    color: #6c757d;
}

.empty-state-small {
    text-align: center;
    padding: 20px;
}
</style>
