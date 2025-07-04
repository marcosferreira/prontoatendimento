<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-plus-circle"></i> Solicitar Exame para Atendimento</h1>
                <p class="subtitle">Adicionar nova solicitação de exame</p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="m-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/atendimentos">Atendimentos</a></li>
                    <li class="breadcrumb-item"><a href="/atendimentos/show/<?= $atendimento['id_atendimento'] ?>">Atendimento #<?= $atendimento['id_atendimento'] ?></a></li>
                    <li class="breadcrumb-item active">Solicitar Exame</li>
                </ol>
            </nav>

            <!-- Informações do Atendimento -->
            <div class="card m-4 mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-file-medical"></i> Informações do Atendimento</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Paciente:</strong> <?= esc($atendimento['nome_paciente']) ?></p>
                            <p><strong>CPF:</strong> <?= esc($atendimento['cpf']) ?></p>
                            <p><strong>Idade:</strong> <?= $atendimento['idade'] ?? 'N/A' ?> anos</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Data do Atendimento:</strong> <?= date('d/m/Y H:i', strtotime($atendimento['data_atendimento'])) ?></p>
                            <p><strong>Médico:</strong> <?= esc($atendimento['nome_medico']) ?></p>
                            <p><strong>Classificação:</strong> 
                                <span class="badge 
                                    <?php 
                                    switch($atendimento['classificacao_risco']) {
                                        case 'Verde': echo 'bg-success'; break;
                                        case 'Amarelo': echo 'bg-warning'; break;
                                        case 'Vermelho': echo 'bg-danger'; break;
                                        case 'Azul': echo 'bg-info'; break;
                                        default: echo 'bg-secondary';
                                    }
                                    ?>">
                                    <?= esc($atendimento['classificacao_risco']) ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulário -->
            <div class="form-container m-4">
                <form id="exameForm" action="/atendimento-exames/store" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_atendimento" value="<?= $atendimento['id_atendimento'] ?>">

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="id_exame" class="form-label">Exame *</label>
                                <select class="form-select" id="id_exame" name="id_exame" required>
                                    <option value="">Selecione um exame...</option>
                                    <?php 
                                    $currentType = '';
                                    foreach ($exames as $exame): 
                                        if ($exame['tipo'] !== $currentType) {
                                            if ($currentType !== '') echo '</optgroup>';
                                            echo '<optgroup label="' . ucfirst($exame['tipo']) . '">';
                                            $currentType = $exame['tipo'];
                                        }
                                        
                                        if (!in_array($exame['id_exame'], $examesJaSolicitados)):
                                    ?>
                                            <option value="<?= $exame['id_exame'] ?>" 
                                                    data-codigo="<?= esc($exame['codigo']) ?>"
                                                    data-tipo="<?= esc($exame['tipo']) ?>"
                                                    data-descricao="<?= esc($exame['descricao']) ?>">
                                                <?= esc($exame['nome']) ?>
                                                <?php if ($exame['codigo']): ?>
                                                    (<?= esc($exame['codigo']) ?>)
                                                <?php endif; ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php if ($currentType !== '') echo '</optgroup>'; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações do Exame Selecionado -->
                    <div id="exameInfo" class="alert alert-info" style="display: none;">
                        <h6><i class="bi bi-info-circle"></i> Informações do Exame</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Tipo:</strong> <span id="infoTipo">-</span></p>
                                <p class="mb-0"><strong>Código:</strong> <span id="infoCodigo">-</span></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-0"><strong>Descrição:</strong></p>
                                <div id="infoDescricao" class="small">-</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="observacao" class="form-label">Observações da Solicitação</label>
                        <textarea class="form-control" id="observacao" name="observacao" rows="3"
                                  placeholder="Observações ou justificativas para a solicitação do exame..."></textarea>
                        <div class="invalid-feedback"></div>
                        <div class="form-text">Estas observações aparecerão na solicitação impressa</div>
                    </div>

                    <?php if (!empty($examesJaSolicitados)): ?>
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-exclamation-triangle"></i> Exames já solicitados</h6>
                            <p class="mb-0">Este atendimento já possui alguns exames solicitados. Apenas exames não solicitados estão disponíveis para seleção.</p>
                        </div>
                    <?php endif; ?>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Solicitar Exame
                        </button>
                        <a href="/atendimentos/show/<?= $atendimento['id_atendimento'] ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const exameSelect = document.getElementById('id_exame');
    const exameInfo = document.getElementById('exameInfo');
    const infoTipo = document.getElementById('infoTipo');
    const infoCodigo = document.getElementById('infoCodigo');
    const infoDescricao = document.getElementById('infoDescricao');

    // Mostrar informações do exame selecionado
    exameSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
            const tipo = selectedOption.dataset.tipo || '-';
            const codigo = selectedOption.dataset.codigo || '-';
            const descricao = selectedOption.dataset.descricao || '-';
            
            infoTipo.textContent = tipo.charAt(0).toUpperCase() + tipo.slice(1);
            infoCodigo.textContent = codigo;
            infoDescricao.textContent = descricao;
            exameInfo.style.display = 'block';
        } else {
            exameInfo.style.display = 'none';
        }
    });

    // Validação do formulário
    const form = document.getElementById('exameForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Limpar validações anteriores
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        let isValid = true;
        
        // Validar exame
        if (!exameSelect.value) {
            exameSelect.classList.add('is-invalid');
            exameSelect.nextElementSibling.textContent = 'Selecione um exame';
            isValid = false;
        }
        
        if (isValid) {
            // Mostrar loading
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Solicitando...';
            submitBtn.disabled = true;
            
            // Submeter formulário
            form.submit();
        }
    });
});

// Exibir erros de validação do servidor
<?php if (session()->has('errors')): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const errors = <?= json_encode(session('errors')) ?>;
        
        Object.keys(errors).forEach(field => {
            const input = document.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = errors[field];
                }
            }
        });
    });
<?php endif; ?>
</script>

<?= $this->endSection() ?>
