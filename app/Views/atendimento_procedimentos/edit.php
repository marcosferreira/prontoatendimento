<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-pencil"></i> Editar Procedimento do Atendimento</h1>
                <p class="subtitle">Modificar informações do procedimento realizado</p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="m-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/atendimento-procedimentos">Procedimentos</a></li>
                    <li class="breadcrumb-item"><a href="/atendimentos/show/<?= $atendimento['id_atendimento'] ?>">Atendimento #<?= $atendimento['id_atendimento'] ?></a></li>
                    <li class="breadcrumb-item"><a href="/atendimento-procedimentos/show/<?= $atendimentoProcedimento['id_atendimento_procedimento'] ?>">Procedimento #<?= $atendimentoProcedimento['id_atendimento_procedimento'] ?></a></li>
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
                <form id="procedimentoForm" action="/atendimento-procedimentos/update/<?= $atendimentoProcedimento['id_atendimento_procedimento'] ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="id_procedimento" class="form-label">Procedimento *</label>
                                <select class="form-select" id="id_procedimento" name="id_procedimento" required>
                                    <option value="">Selecione um procedimento...</option>
                                    <?php foreach ($procedimentos as $procedimento): ?>
                                        <option value="<?= $procedimento['id_procedimento'] ?>" 
                                                <?= $procedimento['id_procedimento'] == $atendimentoProcedimento['id_procedimento'] ? 'selected' : '' ?>
                                                data-codigo="<?= esc($procedimento['codigo']) ?>"
                                                data-descricao="<?= esc($procedimento['descricao']) ?>">
                                            <?= esc($procedimento['nome']) ?>
                                            <?php if ($procedimento['codigo']): ?>
                                                (<?= esc($procedimento['codigo']) ?>)
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="quantidade" class="form-label">Quantidade *</label>
                                <input type="number" class="form-control" id="quantidade" name="quantidade" 
                                       value="<?= $atendimentoProcedimento['quantidade'] ?>" min="1" max="99999" required>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações do Procedimento Selecionado -->
                    <div id="procedimentoInfo" class="alert alert-info" style="display: none;">
                        <h6><i class="bi bi-info-circle"></i> Informações do Procedimento</h6>
                        <p><strong>Código:</strong> <span id="infoCodigo">-</span></p>
                        <p class="mb-0"><strong>Descrição:</strong> <span id="infoDescricao">-</span></p>
                    </div>

                    <div class="mb-3">
                        <label for="observacao" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacao" name="observacao" rows="3"
                                  placeholder="Observações sobre o procedimento realizado..."><?= esc($atendimentoProcedimento['observacao']) ?></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar Alterações
                        </button>
                        <a href="/atendimento-procedimentos/show/<?= $atendimentoProcedimento['id_atendimento_procedimento'] ?>" class="btn btn-secondary">
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
                        <div class="col-md-6">
                            <p><strong>Criado em:</strong> <?= date('d/m/Y H:i:s', strtotime($atendimentoProcedimento['created_at'])) ?></p>
                        </div>
                        <div class="col-md-6">
                            <?php if ($atendimentoProcedimento['updated_at']): ?>
                                <p><strong>Última atualização:</strong> <?= date('d/m/Y H:i:s', strtotime($atendimentoProcedimento['updated_at'])) ?></p>
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
    const procedimentoSelect = document.getElementById('id_procedimento');
    const procedimentoInfo = document.getElementById('procedimentoInfo');
    const infoCodigo = document.getElementById('infoCodigo');
    const infoDescricao = document.getElementById('infoDescricao');

    // Mostrar informações do procedimento selecionado inicialmente
    updateProcedimentoInfo();

    // Mostrar informações do procedimento selecionado
    procedimentoSelect.addEventListener('change', updateProcedimentoInfo);

    function updateProcedimentoInfo() {
        const selectedOption = procedimentoSelect.options[procedimentoSelect.selectedIndex];
        
        if (selectedOption.value) {
            const codigo = selectedOption.dataset.codigo || '-';
            const descricao = selectedOption.dataset.descricao || '-';
            
            infoCodigo.textContent = codigo;
            infoDescricao.textContent = descricao;
            procedimentoInfo.style.display = 'block';
        } else {
            procedimentoInfo.style.display = 'none';
        }
    }

    // Validação do formulário
    const form = document.getElementById('procedimentoForm');
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Limpar validações anteriores
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        
        let isValid = true;
        
        // Validar procedimento
        if (!procedimentoSelect.value) {
            procedimentoSelect.classList.add('is-invalid');
            procedimentoSelect.nextElementSibling.textContent = 'Selecione um procedimento';
            isValid = false;
        }
        
        // Validar quantidade
        const quantidade = document.getElementById('quantidade');
        if (!quantidade.value || quantidade.value < 1 || quantidade.value > 99999) {
            quantidade.classList.add('is-invalid');
            quantidade.nextElementSibling.textContent = 'Quantidade deve estar entre 1 e 99999';
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

    // Máscara para quantidade
    const quantidadeInput = document.getElementById('quantidade');
    quantidadeInput.addEventListener('input', function() {
        let value = parseInt(this.value);
        if (value < 1) this.value = 1;
        if (value > 99999) this.value = 99999;
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
