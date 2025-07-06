<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-plus-circle"></i> Adicionar Exame ao Atendimento</h1>
                <p class="subtitle">Solicitar novo exame ou registrar exame já realizado</p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="m-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/atendimentos">Atendimentos</a></li>
                    <li class="breadcrumb-item"><a href="/atendimentos/show/<?= $atendimento['id_atendimento'] ?>">Atendimento #<?= $atendimento['id_atendimento'] ?></a></li>
                    <li class="breadcrumb-item active">Adicionar Exame</li>
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
                            <p><strong>Paciente:</strong> <?= esc($atendimento['paciente_nome']) ?></p>
                            <p><strong>CPF:</strong> <?= esc($atendimento['cpf']) ?></p>
                            <p><strong>Idade:</strong> <?= $atendimento['idade'] ?? 'N/A' ?> anos</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Data do Atendimento:</strong> <?= date('d/m/Y H:i', strtotime($atendimento['data_atendimento'])) ?></p>
                            <p><strong>Médico:</strong> <?= esc($atendimento['medico_nome']) ?></p>
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

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status *</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="Solicitado" selected>Solicitado</option>
                                    <option value="Realizado">Realizado</option>
                                    <option value="Cancelado">Cancelado</option>
                                </select>
                                <div class="invalid-feedback"></div>
                                <div class="form-text">Se marcar como "Realizado", informe a data de realização</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="data_realizacao" class="form-label">Data de Realização</label>
                                <input type="datetime-local" class="form-control" id="data_realizacao" name="data_realizacao">
                                <div class="invalid-feedback"></div>
                                <div class="form-text">Obrigatório apenas se status for "Realizado". Se não informada, será assumida a data atual.</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3" id="resultadoContainer" style="display: none;">
                        <label for="resultado" class="form-label">Resultado do Exame</label>
                        <textarea class="form-control" id="resultado" name="resultado" rows="4"
                                  placeholder="Digite o resultado do exame..."></textarea>
                        <div class="invalid-feedback"></div>
                        <div class="form-text">Campo obrigatório quando status for "Realizado"</div>
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
                            <i class="bi bi-check-circle"></i> Salvar Exame
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
    const statusSelect = document.getElementById('status');
    const dataRealizacaoInput = document.getElementById('data_realizacao');
    const resultadoContainer = document.getElementById('resultadoContainer');
    const resultadoInput = document.getElementById('resultado');
    const exameInfo = document.getElementById('exameInfo');
    const infoTipo = document.getElementById('infoTipo');
    const infoCodigo = document.getElementById('infoCodigo');
    const infoDescricao = document.getElementById('infoDescricao');

    // Controlar visibilidade dos campos baseado no status
    statusSelect.addEventListener('change', function() {
        const isRealizado = this.value === 'Realizado';
        
        if (isRealizado) {
            // Mostrar campo de resultado para exame realizado
            resultadoContainer.style.display = 'block';
            
            // Se não há data definida, usar data/hora atual como sugestão
            if (!dataRealizacaoInput.value) {
                const now = new Date();
                now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                dataRealizacaoInput.value = now.toISOString().slice(0, 16);
            }
        } else {
            // Ocultar campo de resultado para exame não realizado
            resultadoContainer.style.display = 'none';
            resultadoInput.value = '';
            // Manter a data_realizacao visível mas limpar o valor se desejar
            // dataRealizacaoInput.value = '';
        }
    });

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
        
        // Validar status
        if (!statusSelect.value) {
            statusSelect.classList.add('is-invalid');
            statusSelect.nextElementSibling.textContent = 'Selecione um status';
            isValid = false;
        }
        
        // Validações específicas para status "Realizado"
        if (statusSelect.value === 'Realizado') {
            // Validar data de realização
            if (!dataRealizacaoInput.value) {
                dataRealizacaoInput.classList.add('is-invalid');
                dataRealizacaoInput.nextElementSibling.textContent = 'Data de realização é obrigatória quando status for "Realizado"';
                isValid = false;
            }
            
            // Validar resultado
            if (!resultadoInput.value.trim()) {
                resultadoInput.classList.add('is-invalid');
                resultadoInput.nextElementSibling.textContent = 'Resultado é obrigatório quando status for "Realizado"';
                isValid = false;
            }
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
    
    // Inicializar estado baseado no status padrão
    statusSelect.dispatchEvent(new Event('change'));
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
