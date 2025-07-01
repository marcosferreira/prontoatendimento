<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>
    
    <div class="main-wrapper">
        <?= $this->include('components/topbar') ?>
        
        <main class="main-content">
            <div class="main-container">
                <!-- Header -->
                <div class="header">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="<?= base_url('pacientes') ?>">
                                    <i class="bi bi-person-badge"></i> Pacientes
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Editar Paciente
                            </li>
                        </ol>
                    </nav>
                    <h1><i class="bi bi-pencil-square"></i> Editar Paciente</h1>
                    <p class="subtitle">Atualizar dados do paciente: <strong><?= esc($paciente['nome']) ?></strong></p>
                </div>

                <!-- Content -->
                <div class="content-wrapper">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="section-card">
                                <div class="section-header">
                                    <h3 class="section-title">
                                        <i class="bi bi-person-gear"></i>
                                        Dados do Paciente
                                    </h3>
                                </div>

                                <?php if (session()->getFlashdata('errors')): ?>
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                                <li><?= esc($error) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <?php if (session()->getFlashdata('success')): ?>
                                    <div class="alert alert-success">
                                        <?= session()->getFlashdata('success') ?>
                                    </div>
                                <?php endif; ?>

                                <form action="<?= base_url('pacientes/' . $paciente['id_paciente']) ?>" method="POST" id="editPacienteForm">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="PUT">
                                    
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="nome" class="form-label">Nome Completo *</label>
                                                <input type="text" class="form-control" id="nome" name="nome" 
                                                       value="<?= esc($paciente['nome']) ?>" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="data_nascimento" class="form-label">Data de Nascimento *</label>
                                                <input type="date" class="form-control" id="data_nascimento" name="data_nascimento" 
                                                       value="<?= esc($paciente['data_nascimento']) ?>" required>
                                                <div class="form-text">
                                                    Idade atual: <strong><?= esc($paciente['idade']) ?></strong> anos
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="cpf" class="form-label">CPF *</label>
                                                <input type="text" class="form-control" id="cpf" name="cpf" 
                                                       value="<?= esc($paciente['cpf']) ?>" placeholder="000.000.000-00" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="sus" class="form-label">Cartão SUS</label>
                                                <input type="text" class="form-control" id="sus" name="sus" 
                                                       value="<?= esc($paciente['sus'] ?? '') ?>" placeholder="Número do cartão SUS">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="endereco" class="form-label">Endereço</label>
                                                <input type="text" class="form-control" id="endereco" name="endereco" 
                                                       value="<?= esc($paciente['endereco'] ?? '') ?>" placeholder="Rua, número, complemento">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="id_bairro" class="form-label">Bairro</label>
                                                <select class="form-select" id="id_bairro" name="id_bairro">
                                                    <option value="">Selecione o bairro</option>
                                                    <?php if (isset($bairros)): ?>
                                                        <?php foreach ($bairros as $bairro): ?>
                                                            <option value="<?= $bairro['id_bairro'] ?>" 
                                                                    <?= ($paciente['id_bairro'] == $bairro['id_bairro']) ? 'selected' : '' ?>>
                                                                <?= esc($bairro['nome_bairro']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="alert alert-info">
                                                <i class="bi bi-info-circle"></i>
                                                <strong>Informações do Cadastro:</strong>
                                                <ul class="mb-0 mt-2">
                                                    <li>Cadastrado em: <?= date('d/m/Y \à\s H:i', strtotime($paciente['created_at'])) ?></li>
                                                    <?php if ($paciente['updated_at'] != $paciente['created_at']): ?>
                                                        <li>Última atualização: <?= date('d/m/Y \à\s H:i', strtotime($paciente['updated_at'])) ?></li>
                                                    <?php endif; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between">
                                        <a href="<?= base_url('pacientes') ?>" class="btn btn-secondary">
                                            <i class="bi bi-arrow-left"></i> Voltar
                                        </a>
                                        <div>
                                            <button type="button" class="btn btn-outline-danger me-2" onclick="confirmDelete()">
                                                <i class="bi bi-trash"></i> Excluir
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check-circle"></i> Salvar Alterações
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <!-- Histórico de Atendimentos -->
                            <div class="section-card">
                                <div class="section-header">
                                    <h4 class="section-title">
                                        <i class="bi bi-clock-history"></i>
                                        Histórico de Atendimentos
                                    </h4>
                                </div>

                                <?php if (isset($atendimentos) && !empty($atendimentos)): ?>
                                    <div class="timeline">
                                        <?php foreach ($atendimentos as $atendimento): ?>
                                            <div class="timeline-item">
                                                <div class="timeline-marker bg-<?= getClassificacaoRiscoCor($atendimento['classificacao_risco']) ?>"></div>
                                                <div class="timeline-content">
                                                    <h6><?= date('d/m/Y', strtotime($atendimento['data_atendimento'])) ?></h6>
                                                    <p class="mb-1">
                                                        <strong>Dr. <?= esc($atendimento['nome_medico']) ?></strong>
                                                    </p>
                                                    <p class="mb-1">
                                                        <span class="badge bg-<?= getClassificacaoRiscoCor($atendimento['classificacao_risco']) ?>">
                                                            <?= esc($atendimento['classificacao_risco']) ?>
                                                        </span>
                                                    </p>
                                                    <?php if (!empty($atendimento['hipotese_diagnostico'])): ?>
                                                        <p class="mb-0 text-muted small">
                                                            <?= esc(substr($atendimento['hipotese_diagnostico'], 0, 100)) ?>...
                                                        </p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    
                                    <div class="text-center mt-3">
                                        <a href="<?= base_url('pacientes/' . $paciente['id_paciente'] . '/atendimentos') ?>" 
                                           class="btn btn-outline-primary btn-sm">
                                            Ver Todos os Atendimentos
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state text-center py-3">
                                        <i class="bi bi-journal-medical text-muted" style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2 mb-0">Nenhum atendimento registrado</p>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Ações Rápidas -->
                            <div class="section-card">
                                <div class="section-header">
                                    <h4 class="section-title">
                                        <i class="bi bi-lightning"></i>
                                        Ações Rápidas
                                    </h4>
                                </div>

                                <div class="d-grid gap-2">
                                    <a href="<?= base_url('atendimentos/create?paciente=' . $paciente['id_paciente']) ?>" 
                                       class="btn btn-success">
                                        <i class="bi bi-plus-circle"></i> Novo Atendimento
                                    </a>
                                    <button type="button" class="btn btn-outline-primary" onclick="viewPacienteDetails()">
                                        <i class="bi bi-file-earmark-text"></i> Ver Prontuário Completo
                                    </button>
                                    <button type="button" class="btn btn-outline-info" onclick="printPacienteCard()">
                                        <i class="bi bi-printer"></i> Imprimir Ficha
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">
                    <i class="bi bi-exclamation-triangle text-danger"></i> Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o paciente <strong><?= esc($paciente['nome']) ?></strong>?</p>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Atenção:</strong> Esta ação não pode ser desfeita. Todos os dados relacionados serão perdidos.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form action="<?= base_url('pacientes/' . $paciente['id_paciente']) ?>" method="POST" class="d-inline">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Excluir Paciente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Máscara para CPF
document.getElementById('cpf').addEventListener('input', function() {
    let value = this.value.replace(/\D/g, '');
    value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
    this.value = value;
});

// Validação do formulário
document.getElementById('editPacienteForm').addEventListener('submit', function(e) {
    const cpf = document.getElementById('cpf').value.replace(/\D/g, '');
    
    if (cpf.length !== 11) {
        e.preventDefault();
        alert('CPF deve ter 11 dígitos');
        return;
    }
    
    // Validação básica de CPF
    if (!validarCPF(cpf)) {
        e.preventDefault();
        alert('CPF inválido');
        return;
    }
});

// Função para validar CPF
function validarCPF(cpf) {
    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) return false;
    
    let soma = 0;
    for (let i = 0; i < 9; i++) {
        soma += parseInt(cpf.charAt(i)) * (10 - i);
    }
    
    let resto = 11 - (soma % 11);
    let digito1 = resto === 10 || resto === 11 ? 0 : resto;
    
    if (digito1 !== parseInt(cpf.charAt(9))) return false;
    
    soma = 0;
    for (let i = 0; i < 10; i++) {
        soma += parseInt(cpf.charAt(i)) * (11 - i);
    }
    
    resto = 11 - (soma % 11);
    let digito2 = resto === 10 || resto === 11 ? 0 : resto;
    
    return digito2 === parseInt(cpf.charAt(10));
}

// Confirmação de exclusão
function confirmDelete() {
    new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
}

// Ações rápidas
function viewPacienteDetails() {
    window.location.href = '<?= base_url('pacientes/' . $paciente['id_paciente']) ?>';
}

function printPacienteCard() {
    window.open('<?= base_url('pacientes/' . $paciente['id_paciente'] . '/print') ?>', '_blank');
}
</script>

<?= $this->endSection() ?>

<?php
// Helper function for risk classification colors
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
