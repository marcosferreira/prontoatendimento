<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-pencil"></i> Editar Exame do Atendimento</h1>
                <p class="subtitle">Modificar informações da solicitação de exame</p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="m-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/atendimento-exames">Exames</a></li>
                    <li class="breadcrumb-item"><a href="/atendimentos/show/<?= $atendimento['id_atendimento'] ?>">Atendimento #<?= $atendimento['id_atendimento'] ?></a></li>
                    <li class="breadcrumb-item"><a href="/atendimento-exames/show/<?= $atendimentoExame['id_atendimento_exame'] ?>">Exame #<?= $atendimentoExame['id_atendimento_exame'] ?></a></li>
                    <li class="breadcrumb-item active">Editar</li>
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
                        </div>
                        <div class="col-md-6">
                            <p><strong>Data do Atendimento:</strong> <?= date('d/m/Y H:i', strtotime($atendimento['data_atendimento'])) ?></p>
                            <p><strong>Médico:</strong> <?= esc($atendimento['nome_medico']) ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulário -->
            <div class="form-container m-4">
                <form id="exameForm" action="/atendimento-exames/update/<?= $atendimentoExame['id_atendimento_exame'] ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-8">
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
                                    ?>
                                        <option value="<?= $exame['id_exame'] ?>" 
                                                <?= $exame['id_exame'] == $atendimentoExame['id_exame'] ? 'selected' : '' ?>
                                                data-codigo="<?= esc($exame['codigo']) ?>"
                                                data-tipo="<?= esc($exame['tipo']) ?>"
                                                data-descricao="<?= esc($exame['descricao']) ?>">
                                            <?= esc($exame['nome']) ?>
                                            <?php if ($exame['codigo']): ?>
                                                (<?= esc($exame['codigo']) ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <?php if ($currentType !== '') echo '</optgroup>'; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <?php foreach ($statusOptions as $statusOption): ?>
                                        <option value="<?= $statusOption ?>" 
                                                <?= $statusOption == $atendimentoExame['status'] ? 'selected' : '' ?>>
                                            <?= $statusOption ?>
                                        </option>
                                    <?php endforeach; ?>
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

                    <!-- Data de Realização -->
                    <div class="row" id="dataRealizacaoContainer" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_realizacao" class="form-label">Data de Realização</label>
                                <input type="datetime-local" class="form-control" id="data_realizacao" name="data_realizacao"
                                       value="<?= $atendimentoExame['data_realizacao'] ? date('Y-m-d\TH:i', strtotime($atendimentoExame['data_realizacao'])) : '' ?>">
                                <div class="form-text">Deixe em branco para usar a data/hora atual</div>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="resultado" class="form-label">Resultado do Exame</label>
                        <textarea class="form-control" id="resultado" name="resultado" rows="4"
                                  placeholder="Resultado do exame (quando realizado)..."><?= esc($atendimentoExame['resultado']) ?></textarea>
                        <div class="invalid-feedback"></div>
                        <div class="form-text">Preencha apenas quando o exame estiver realizado</div>
                    </div>

                    <div class="mb-3">
                        <label for="observacao" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacao" name="observacao" rows="3"
                                  placeholder="Observações sobre a solicitação ou realização do exame..."><?= esc($atendimentoExame['observacao']) ?></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar Alterações
                        </button>
                        <a href="/atendimento-exames/show/<?= $atendimentoExame['id_atendimento_exame'] ?>" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancelar
                        </a>
                        <a href="/atendimentos/show/<?= $atendimento['id_atendimento'] ?>" class="btn btn-outline-info">
                            <i class="bi bi-file-medical"></i> Ver Atendimento
                        </a>
                    </div>
                </form>
            </div>

            <!-- Histórico de Alterações -->
            <div class="card m-4">
                <div class="card-header">
                    <h5 class="card-title mb-0"><i class="bi bi-clock-history"></i> Histórico</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Solicitado em:</strong> <?= date('d/m/Y H:i:s', strtotime($atendimentoExame['data_solicitacao'])) ?></p>
                        </div>
                        <div class="col-md-4">
                            <?php if ($atendimentoExame['data_realizacao']): ?>
                                <p><strong>Realizado em:</strong> <?= date('d/m/Y H:i:s', strtotime($atendimentoExame['data_realizacao'])) ?></p>
                            <?php else: ?>
                                <p><strong>Realizado em:</strong> <span class="text-muted">Não realizado</span></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <?php if ($atendimentoExame['updated_at']): ?>
                                <p><strong>Última atualização:</strong> <?= date('d/m/Y H:i:s', strtotime($atendimentoExame['updated_at'])) ?></p>
                            <?php else: ?>
                                <p><strong>Última atualização:</strong> <span class="text-muted">Nunca</span></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const exameSelect = document.getElementById('id_exame');
    const statusSelect = document.getElementById('status');
    const exameInfo = document.getElementById('exameInfo');
    const infoTipo = document.getElementById('infoTipo');
    const infoCodigo = document.getElementById('infoCodigo');
    const infoDescricao = document.getElementById('infoDescricao');
    const dataRealizacaoContainer = document.getElementById('dataRealizacaoContainer');

    // Mostrar informações do exame selecionado inicialmente
    updateExameInfo();
    updateDataRealizacao();

    // Mostrar informações do exame selecionado
    exameSelect.addEventListener('change', updateExameInfo);
    statusSelect.addEventListener('change', updateDataRealizacao);

    function updateExameInfo() {
        const selectedOption = exameSelect.options[exameSelect.selectedIndex];
        
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
    }

    function updateDataRealizacao() {
        if (statusSelect.value === 'Realizado') {
            dataRealizacaoContainer.style.display = 'block';
        } else {
            dataRealizacaoContainer.style.display = 'none';
        }
    }

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

        // Validar status
        if (!statusSelect.value) {
            statusSelect.classList.add('is-invalid');
            statusSelect.nextElementSibling.textContent = 'Selecione um status';
            isValid = false;
        }
        
        if (isValid) {
            // Mostrar loading
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Salvando...';
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
