<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-plus-circle"></i> Novo Atendimento</h1>
                <p class="subtitle">Cadastrar novo atendimento médico</p>
            </div>

            <div class="content-wrapper">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="my-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('atendimentos') ?>">Atendimentos</a></li>
                        <li class="breadcrumb-item active">Novo Atendimento</li>
                    </ol>
                </nav>

                <!-- Form -->
                <!-- 
                Parâmetros URL suportados para pré-seleção:
                - ?paciente=ID - Pré-seleciona o paciente
                - ?medico=ID - Pré-seleciona o médico  
                - ?classificacao=VALOR - Pré-seleciona a classificação de risco (Verde|Amarelo|Vermelho|Azul)
                - ?data=YYYY-MM-DDTHH:MM - Pré-define a data/hora do atendimento
                
                Exemplo: /atendimentos/create?paciente=5&medico=2&classificacao=Amarelo
            -->
                <div class="card my-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Dados do Atendimento</h5>
                    </div>
                    <div class="card-body">
                        <!-- Exibir mensagens de validação -->
                        <?php if (session()->has('validation')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <h6><i class="bi bi-exclamation-triangle"></i> Erro de Validação</h6>
                                <ul class="mb-0">
                                    <?php foreach (session('validation')->getErrors() as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Exibir mensagens de erro -->
                        <?php if (session()->has('error')): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-x-circle"></i> <?= session('error') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Exibir mensagens de sucesso -->
                        <?php if (session()->has('success')): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle"></i> <?= session('success') ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>

                        <?= form_open('atendimentos/store', ['id' => 'formAtendimento', 'class' => 'needs-validation', 'novalidate' => '']) ?>

                        <div class="row">
                            <!-- Paciente -->
                            <div class="col-md-6 mb-3">
                                <label for="id_paciente" class="form-label">
                                    <i class="bi bi-person"></i> Paciente *
                                </label>
                                <select class="form-select <?= session('validation') && session('validation')->hasError('id_paciente') ? 'is-invalid' : '' ?>" 
                                        id="id_paciente" name="id_paciente" required data-placeholder="Selecione um paciente">
                                    <option value="">Selecione um paciente</option>
                                    <?php if (isset($pacientes)): ?>
                                        <?php foreach ($pacientes as $paciente): ?>
                                            <option value="<?= $paciente['id_paciente'] ?>" <?= old('id_paciente') == $paciente['id_paciente'] ? 'selected' : '' ?>>
                                                <?= esc($paciente['nome']) ?> - CPF: <?= $paciente['cpf'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= session('validation') && session('validation')->hasError('id_paciente') ? session('validation')->getError('id_paciente') : 'Por favor, selecione um paciente.' ?>
                                </div>
                                <!-- Button to unlock patient selection (only shown when locked) -->
                                <button type="button" id="unlock_paciente" class="btn btn-sm btn-outline-warning mt-1" style="display: none;" title="Permitir alteração do paciente">
                                    <i class="bi bi-unlock"></i> Alterar Paciente
                                </button>
                            </div>

                            <!-- Médico -->
                            <div class="col-md-6 mb-3">
                                <label for="id_medico" class="form-label">
                                    <i class="bi bi-person-badge"></i> Médico *
                                </label>
                                <select class="form-select <?= session('validation') && session('validation')->hasError('id_medico') ? 'is-invalid' : '' ?>" 
                                        id="id_medico" name="id_medico" required data-placeholder="Selecione um médico">
                                    <option value="">Selecione um médico</option>
                                    <?php if (isset($medicos)): ?>
                                        <?php foreach ($medicos as $medico): ?>
                                            <option value="<?= $medico['id_medico'] ?>" <?= old('id_medico') == $medico['id_medico'] ? 'selected' : '' ?>>
                                                <?= esc($medico['nome']) ?> - CRM: <?= $medico['crm'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= session('validation') && session('validation')->hasError('id_medico') ? session('validation')->getError('id_medico') : 'Por favor, selecione um médico.' ?>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Data/Hora do Atendimento -->
                            <div class="col-md-6 mb-3">
                                <label for="data_atendimento" class="form-label">
                                    <i class="bi bi-calendar"></i> Data/Hora do Atendimento *
                                </label>
                                <?php
                                // Tratar o formato da data para o input datetime-local
                                $dataAtendimento = old('data_atendimento', date('Y-m-d\TH:i'));
                                
                                // Se a data for um objeto Time, converter para string
                                if (is_object($dataAtendimento) && method_exists($dataAtendimento, 'format')) {
                                    $dataAtendimento = $dataAtendimento->format('Y-m-d\TH:i');
                                }
                                // Se a data vier no formato do banco (Y-m-d H:i:s), converter para datetime-local (Y-m-d\TH:i)
                                elseif ($dataAtendimento && !strpos($dataAtendimento, 'T')) {
                                    // Converter de "2025-07-05 19:59:00" para "2025-07-05T19:59"
                                    $dataAtendimento = date('Y-m-d\TH:i', strtotime($dataAtendimento));
                                }
                                ?>
                                <input type="datetime-local" class="form-control <?= session('validation') && session('validation')->hasError('data_atendimento') ? 'is-invalid' : '' ?>" 
                                       id="data_atendimento" name="data_atendimento"
                                       value="<?= esc($dataAtendimento) ?>" required>
                                <small class="form-text text-muted">
                                    <i class="bi bi-info-circle"></i> Use o seletor para escolher data e hora. Formato: DD/MM/AAAA HH:MM
                                </small>
                                <div class="invalid-feedback">
                                    <?= session('validation') && session('validation')->hasError('data_atendimento') ? session('validation')->getError('data_atendimento') : 'Por favor, informe a data e hora do atendimento.' ?>
                                </div>
                            </div>

                            <!-- Classificação de Risco -->
                            <div class="col-md-6 mb-3">
                                <label for="classificacao_risco" class="form-label">
                                    <i class="bi bi-exclamation-triangle"></i> Classificação de Risco *
                                </label>
                                <select class="form-select <?= session('validation') && session('validation')->hasError('classificacao_risco') ? 'is-invalid' : '' ?>" 
                                        id="classificacao_risco" name="classificacao_risco" required>
                                    <option value="">Selecione a classificação</option>
                                    <?php if (isset($classificacoes)): ?>
                                        <?php 
                                        $descricoes = [
                                            'Azul' => 'Azul - Não Urgente',
                                            'Verde' => 'Verde - Pouco Urgente', 
                                            'Amarelo' => 'Amarelo - Urgente',
                                            'Vermelho' => 'Vermelho - Muito Urgente'
                                        ];
                                        foreach ($classificacoes as $opcao): ?>
                                            <option value="<?= $opcao ?>" <?= old('classificacao_risco') == $opcao ? 'selected' : '' ?>>
                                                <?= $descricoes[$opcao] ?? $opcao ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="Verde" <?= old('classificacao_risco') == 'Verde' ? 'selected' : '' ?>>Verde - Pouco Urgente</option>
                                        <option value="Amarelo" <?= old('classificacao_risco') == 'Amarelo' ? 'selected' : '' ?>>Amarelo - Urgente</option>
                                        <option value="Vermelho" <?= old('classificacao_risco') == 'Vermelho' ? 'selected' : '' ?>>Vermelho - Muito Urgente</option>
                                        <option value="Azul" <?= old('classificacao_risco') == 'Azul' ? 'selected' : '' ?>>Azul - Não Urgente</option>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= session('validation') && session('validation')->hasError('classificacao_risco') ? session('validation')->getError('classificacao_risco') : 'Por favor, selecione a classificação de risco.' ?>
                                </div>
                            </div>
                        </div>

                        <!-- Dados Vitais -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="hgt_glicemia" class="form-label">
                                    <i class="bi bi-droplet"></i> HGT/Glicemia (mg/dL)
                                </label>
                                <input type="number" class="form-control" id="hgt_glicemia" name="hgt_glicemia"
                                    step="0.01" min="0" max="999.99" value="<?= old('hgt_glicemia') ?>">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="pressao_arterial" class="form-label">
                                    <i class="bi bi-heart-pulse"></i> Pressão Arterial
                                </label>
                                <input type="text" class="form-control" id="pressao_arterial" name="pressao_arterial"
                                    placeholder="Ex: 120x80" value="<?= old('pressao_arterial') ?>">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="temperatura" class="form-label">
                                    <i class="bi bi-thermometer"></i> Temperatura (°C)
                                </label>
                                <input type="number" class="form-control" id="temperatura" name="temperatura"
                                    step="0.1" min="30" max="45" value="<?= old('temperatura') ?>">
                            </div>
                        </div>

                        <!-- Consulta de Enfermagem -->
                        <div class="mb-3">
                            <label for="consulta_enfermagem" class="form-label">
                                <i class="bi bi-clipboard-pulse"></i> Consulta de Enfermagem
                            </label>
                            <textarea class="form-control" id="consulta_enfermagem" name="consulta_enfermagem"
                                rows="3" placeholder="Observações da consulta de enfermagem..."><?= old('consulta_enfermagem') ?></textarea>
                        </div>

                        <!-- Hipótese Diagnóstica -->
                        <div class="mb-3">
                            <label for="hipotese_diagnostico" class="form-label">
                                <i class="bi bi-clipboard-check"></i> Hipótese Diagnóstica
                            </label>
                            <textarea class="form-control" id="hipotese_diagnostico" name="hipotese_diagnostico"
                                rows="3" placeholder="Hipótese diagnóstica..."><?= old('hipotese_diagnostico') ?></textarea>
                        </div>

                        <!-- Observações -->
                        <div class="mb-3">
                            <label for="observacao" class="form-label">
                                <i class="bi bi-chat-text"></i> Observações
                            </label>
                            <textarea class="form-control" id="observacao" name="observacao"
                                rows="3" placeholder="Observações gerais..."><?= old('observacao') ?></textarea>
                        </div>

                        <!-- Status do Atendimento -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    <i class="bi bi-activity"></i> Status do Atendimento *
                                </label>
                                <select class="form-select <?= session('validation') && session('validation')->hasError('status') ? 'is-invalid' : '' ?>" 
                                        id="status" name="status" required>
                                    <?php if (isset($status_opcoes)): ?>
                                        <?php foreach ($status_opcoes as $opcao): ?>
                                            <option value="<?= $opcao ?>" <?= old('status', 'Em Andamento') == $opcao ? 'selected' : '' ?>>
                                                <?= $opcao ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="Em Andamento" <?= old('status', 'Em Andamento') == 'Em Andamento' ? 'selected' : '' ?>>Em Andamento</option>
                                        <option value="Finalizado" <?= old('status') == 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
                                        <option value="Cancelado" <?= old('status') == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                        <option value="Aguardando" <?= old('status') == 'Aguardando' ? 'selected' : '' ?>>Aguardando</option>
                                        <option value="Suspenso" <?= old('status') == 'Suspenso' ? 'selected' : '' ?>>Suspenso</option>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= session('validation') && session('validation')->hasError('status') ? session('validation')->getError('status') : 'Por favor, selecione o status do atendimento.' ?>
                                </div>
                                <small class="form-text text-muted">
                                    <i class="bi bi-info-circle"></i> Status padrão: Em Andamento
                                </small>
                            </div>

                            <!-- Encaminhamento -->
                            <div class="col-md-6 mb-3">
                                <label for="encaminhamento" class="form-label">
                                    <i class="bi bi-arrow-right-circle"></i> Encaminhamento
                                </label>
                                <select class="form-select" id="encaminhamento" name="encaminhamento">
                                    <option value="">Selecione o encaminhamento</option>
                                    <?php if (isset($encaminhamentos)): ?>
                                        <?php foreach ($encaminhamentos as $opcao): ?>
                                            <option value="<?= $opcao ?>" <?= old('encaminhamento') == $opcao ? 'selected' : '' ?>>
                                                <?= $opcao ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="Alta" <?= old('encaminhamento') == 'Alta' ? 'selected' : '' ?>>Alta</option>
                                        <option value="Internação" <?= old('encaminhamento') == 'Internação' ? 'selected' : '' ?>>Internação</option>
                                        <option value="Transferência" <?= old('encaminhamento') == 'Transferência' ? 'selected' : '' ?>>Transferência</option>
                                        <option value="Especialista" <?= old('encaminhamento') == 'Especialista' ? 'selected' : '' ?>>Especialista</option>
                                        <option value="Retorno" <?= old('encaminhamento') == 'Retorno' ? 'selected' : '' ?>>Retorno</option>
                                        <option value="Óbito" <?= old('encaminhamento') == 'Óbito' ? 'selected' : '' ?>>Óbito</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Checkbox Óbito -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="obito" name="obito" value="1"
                                    <?= old('obito') ? 'checked' : '' ?>>
                                <label class="form-check-label text-danger" for="obito">
                                    <i class="bi bi-exclamation-triangle"></i> Marcar como óbito
                                </label>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('atendimentos') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Salvar Atendimento
                            </button>
                        </div>

                        <?= form_close() ?>
                    </div>
                </div>
            </div>
            <?php echo $this->include('components/footer') ?>
        </div>
    </main>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Função auxiliar para garantir formato correto da data
        function ensureDateTimeLocalFormat(dateString) {
            if (!dateString) return '';
            
            // Se já está no formato correto (YYYY-MM-DDTHH:MM), retornar como está
            if (dateString.includes('T') && dateString.length >= 16) {
                return dateString.substring(0, 16);
            }
            
            // Se está no formato do banco (YYYY-MM-DD HH:MM:SS), converter
            if (dateString.includes(' ')) {
                return dateString.replace(' ', 'T').substring(0, 16);
            }
            
            return dateString;
        }

        // Verificar se o campo de data precisa de correção ao carregar a página
        const dataInput = document.getElementById('data_atendimento');
        if (dataInput && dataInput.value) {
            dataInput.value = ensureDateTimeLocalFormat(dataInput.value);
        }
        // Pre-select fields based on URL parameters
        function preSelectFromUrlParams() {
            const urlParams = new URLSearchParams(window.location.search);

            // Pre-select patient if paciente parameter exists
            const pacienteId = urlParams.get('paciente');
            if (pacienteId) {
                const pacienteSelect = document.getElementById('id_paciente');
                const unlockButton = document.getElementById('unlock_paciente');

                // Set the value
                pacienteSelect.value = pacienteId;

                // Disable the patient select to prevent manual changes
                pacienteSelect.disabled = true;

                // Add visual indication that field is locked
                const label = pacienteSelect.closest('.mb-3').querySelector('label');
                const badge = document.createElement('span');
                badge.className = 'badge bg-info text-white ms-1';
                badge.innerHTML = '<i class="bi bi-lock"></i> Pré-selecionado';
                label.appendChild(badge);

                // Show unlock button
                unlockButton.style.display = 'inline-block';
            } else {
                // If no paciente parameter, ensure field is enabled
                const pacienteSelect = document.getElementById('id_paciente');
                pacienteSelect.disabled = false;
            }

            // Pre-select doctor if medico parameter exists
            const medicoId = urlParams.get('medico');
            if (medicoId) {
                document.getElementById('id_medico').value = medicoId;
            }

            // Pre-select risk classification if classificacao parameter exists
            const classificacao = urlParams.get('classificacao');
            if (classificacao) {
                document.getElementById('classificacao_risco').value = classificacao;
            }

            // Pre-set date/time if data parameter exists (format: YYYY-MM-DDTHH:MM)
            const dataAtendimento = urlParams.get('data');
            if (dataAtendimento) {
                const dataInput = document.getElementById('data_atendimento');
                dataInput.value = ensureDateTimeLocalFormat(dataAtendimento);
            }
        }

        // Form validation and submit handling
        (function() {
            'use strict';
            const forms = document.getElementsByClassName('needs-validation');
            Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    const pacienteSelect = document.getElementById('id_paciente');
                    const dataInput = document.getElementById('data_atendimento');
                    const urlParams = new URLSearchParams(window.location.search);
                    const pacienteFromUrl = urlParams.get('paciente');
                    
                    // Garantir que a data esteja no formato correto antes do envio
                    if (dataInput && dataInput.value) {
                        dataInput.value = ensureDateTimeLocalFormat(dataInput.value);
                    }
                    
                    // If patient was pre-selected and select is disabled, temporarily enable it for submission
                    if (pacienteSelect.disabled && pacienteFromUrl) {
                        pacienteSelect.disabled = false;
                        pacienteSelect.value = pacienteFromUrl;
                    }

                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                        
                        // Re-disable the select if validation fails and patient was pre-selected
                        if (pacienteFromUrl && !pacienteSelect.value) {
                            pacienteSelect.disabled = true;
                        }
                    } else {
                        // Form is valid, ensure the select stays enabled for submission
                        if (pacienteFromUrl) {
                            pacienteSelect.disabled = false;
                        }
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        })();

        // Auto-check óbito when encaminhamento is Óbito
        document.getElementById('encaminhamento').addEventListener('change', function() {
            const obitoCheckbox = document.getElementById('obito');
            if (this.value === 'Óbito') {
                obitoCheckbox.checked = true;
            } else {
                obitoCheckbox.checked = false;
            }
        });

        // Auto-set encaminhamento when óbito is checked
        document.getElementById('obito').addEventListener('change', function() {
            const encaminhamentoSelect = document.getElementById('encaminhamento');
            if (this.checked) {
                encaminhamentoSelect.value = 'Óbito';
            } else if (encaminhamentoSelect.value === 'Óbito') {
                encaminhamentoSelect.value = '';
            }
        });

        // Pre-select fields based on URL parameters
        preSelectFromUrlParams();

        // Add visual feedback for pre-selected fields
        function highlightPreSelectedFields() {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.get('paciente')) {
                const pacienteContainer = document.getElementById('id_paciente').closest('.mb-3');
                pacienteContainer.classList.add('border', 'border-primary', 'rounded', 'p-2', 'bg-light');
                setTimeout(() => {
                    pacienteContainer.classList.remove('border', 'border-primary', 'rounded', 'p-2', 'bg-light');
                }, 3000);
            }

            if (urlParams.get('medico')) {
                const medicoContainer = document.getElementById('id_medico').closest('.mb-3');
                medicoContainer.classList.add('border', 'border-primary', 'rounded', 'p-2', 'bg-light');
                setTimeout(() => {
                    medicoContainer.classList.remove('border', 'border-primary', 'rounded', 'p-2', 'bg-light');
                }, 3000);
            }

            if (urlParams.get('classificacao')) {
                const classificacaoContainer = document.getElementById('classificacao_risco').closest('.mb-3');
                classificacaoContainer.classList.add('border', 'border-success', 'rounded', 'p-2', 'bg-light');
                setTimeout(() => {
                    classificacaoContainer.classList.remove('border', 'border-success', 'rounded', 'p-2', 'bg-light');
                }, 3000);
            }
        }

        // Call highlight function
        highlightPreSelectedFields();

        // Handle unlock patient button
        document.getElementById('unlock_paciente').addEventListener('click', function() {
            const pacienteSelect = document.getElementById('id_paciente');
            const pacienteContainer = pacienteSelect.closest('.mb-3');

            // Enable the patient select
            pacienteSelect.disabled = false;

            // Remove the locked badge
            const badge = pacienteContainer.querySelector('.badge');
            if (badge) {
                badge.remove();
            }

            // Hide the unlock button
            this.style.display = 'none';

            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show mt-2';
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = `
            <i class="bi bi-check-circle"></i> Agora você pode alterar o paciente selecionado.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
            pacienteContainer.appendChild(alertDiv);

            // Auto-dismiss the alert after 3 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.style.opacity = '0';
                    setTimeout(() => {
                        if (alertDiv.parentNode) {
                            alertDiv.remove();
                        }
                    }, 300);
                }
            }, 3000);
        });
    });
</script>
<?= $this->endSection() ?>