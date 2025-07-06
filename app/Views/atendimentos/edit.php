<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-pencil"></i> Editar Atendimento</h1>
                <p class="subtitle">Editar dados do atendimento médico #<?= $atendimento['id_atendimento'] ?></p>
            </div>

            <div class="content-wrapper">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="my-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('atendimentos') ?>">Atendimentos</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('atendimentos/show/' . $atendimento['id_atendimento']) ?>">Atendimento #<?= $atendimento['id_atendimento'] ?></a></li>
                        <li class="breadcrumb-item active">Editar</li>
                    </ol>
                </nav>

                <!-- Form -->
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

                        <?= form_open('atendimentos/update/' . $atendimento['id_atendimento'], ['id' => 'formAtendimento', 'class' => 'needs-validation', 'novalidate' => '']) ?>

                        <div class="row">
                            <!-- Paciente -->
                            <div class="col-md-6 mb-3">
                                <label for="id_paciente" class="form-label">
                                    <i class="bi bi-person"></i> Paciente *
                                </label>
                                <select class="form-select <?= session('validation') && session('validation')->hasError('id_paciente') ? 'is-invalid' : '' ?>" 
                                        id="id_paciente" name="id_paciente" required>
                                    <option value="">Selecione um paciente</option>
                                    <?php if (isset($pacientes)): ?>
                                        <?php foreach ($pacientes as $paciente): ?>
                                            <option value="<?= $paciente['id_paciente'] ?>"
                                                <?= ($atendimento['id_paciente'] == $paciente['id_paciente'] || old('id_paciente') == $paciente['id_paciente']) ? 'selected' : '' ?>>
                                                <?= esc($paciente['nome']) ?> - CPF: <?= $paciente['cpf'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= session('validation') && session('validation')->hasError('id_paciente') ? session('validation')->getError('id_paciente') : 'Por favor, selecione um paciente.' ?>
                                </div>
                            </div>

                            <!-- Médico -->
                            <div class="col-md-6 mb-3">
                                <label for="id_medico" class="form-label">
                                    <i class="bi bi-person-badge"></i> Médico *
                                </label>
                                <select class="form-select <?= session('validation') && session('validation')->hasError('id_medico') ? 'is-invalid' : '' ?>" 
                                        id="id_medico" name="id_medico" required>
                                    <option value="">Selecione um médico</option>
                                    <?php if (isset($medicos)): ?>
                                        <?php foreach ($medicos as $medico): ?>
                                            <option value="<?= $medico['id_medico'] ?>"
                                                <?= ($atendimento['id_medico'] == $medico['id_medico'] || old('id_medico') == $medico['id_medico']) ? 'selected' : '' ?>>
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
                                <input type="datetime-local" class="form-control <?= session('validation') && session('validation')->hasError('data_atendimento') ? 'is-invalid' : '' ?>" 
                                       id="data_atendimento" name="data_atendimento"
                                       value="<?= old('data_atendimento', date('Y-m-d\TH:i', strtotime($atendimento['data_atendimento']))) ?>" required>
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
                                            <option value="<?= $opcao ?>" <?= ($atendimento['classificacao_risco'] == $opcao || old('classificacao_risco') == $opcao) ? 'selected' : '' ?>>
                                                <?= $descricoes[$opcao] ?? $opcao ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="Verde" <?= ($atendimento['classificacao_risco'] == 'Verde' || old('classificacao_risco') == 'Verde') ? 'selected' : '' ?>>Verde - Pouco Urgente</option>
                                        <option value="Amarelo" <?= ($atendimento['classificacao_risco'] == 'Amarelo' || old('classificacao_risco') == 'Amarelo') ? 'selected' : '' ?>>Amarelo - Urgente</option>
                                        <option value="Vermelho" <?= ($atendimento['classificacao_risco'] == 'Vermelho' || old('classificacao_risco') == 'Vermelho') ? 'selected' : '' ?>>Vermelho - Muito Urgente</option>
                                        <option value="Azul" <?= ($atendimento['classificacao_risco'] == 'Azul' || old('classificacao_risco') == 'Azul') ? 'selected' : '' ?>>Azul - Não Urgente</option>
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
                                    step="0.01" min="0" max="999.99"
                                    value="<?= old('hgt_glicemia', $atendimento['hgt_glicemia']) ?>">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="pressao_arterial" class="form-label">
                                    <i class="bi bi-heart-pulse"></i> Pressão Arterial
                                </label>
                                <input type="text" class="form-control" id="pressao_arterial" name="pressao_arterial"
                                    placeholder="Ex: 120x80"
                                    value="<?= old('pressao_arterial', $atendimento['pressao_arterial']) ?>">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="temperatura" class="form-label">
                                    <i class="bi bi-thermometer"></i> Temperatura (°C)
                                </label>
                                <input type="number" class="form-control" id="temperatura" name="temperatura"
                                    step="0.1" min="30" max="45"
                                    value="<?= old('temperatura', isset($atendimento['temperatura']) ? $atendimento['temperatura'] : '') ?>">
                            </div>
                        </div>

                        <!-- Consulta de Enfermagem -->
                        <div class="mb-3">
                            <label for="consulta_enfermagem" class="form-label">
                                <i class="bi bi-clipboard-pulse"></i> Consulta de Enfermagem
                            </label>
                            <textarea class="form-control" id="consulta_enfermagem" name="consulta_enfermagem"
                                rows="3" placeholder="Observações da consulta de enfermagem..."><?= old('consulta_enfermagem', $atendimento['consulta_enfermagem']) ?></textarea>
                        </div>

                        <!-- Hipótese Diagnóstica -->
                        <div class="mb-3">
                            <label for="hipotese_diagnostico" class="form-label">
                                <i class="bi bi-clipboard-check"></i> Hipótese Diagnóstica
                            </label>
                            <textarea class="form-control" id="hipotese_diagnostico" name="hipotese_diagnostico"
                                rows="3" placeholder="Hipótese diagnóstica..."><?= old('hipotese_diagnostico', $atendimento['hipotese_diagnostico']) ?></textarea>
                        </div>

                        <!-- Observações -->
                        <div class="mb-3">
                            <label for="observacao" class="form-label">
                                <i class="bi bi-chat-text"></i> Observações
                            </label>
                            <textarea class="form-control" id="observacao" name="observacao"
                                rows="3" placeholder="Observações gerais..."><?= old('observacao', $atendimento['observacao']) ?></textarea>
                        </div>

                        <!-- Status do Atendimento e Encaminhamento -->
                        <div class="row">
                            <!-- Status do Atendimento -->
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">
                                    <i class="bi bi-activity"></i> Status do Atendimento *
                                </label>
                                <select class="form-select <?= session('validation') && session('validation')->hasError('status') ? 'is-invalid' : '' ?>" 
                                        id="status" name="status" required>
                                    <?php if (isset($status_opcoes)): ?>
                                        <?php foreach ($status_opcoes as $opcao): ?>
                                            <option value="<?= $opcao ?>" <?= ($atendimento['status'] == $opcao || old('status') == $opcao) ? 'selected' : '' ?>>
                                                <?= $opcao ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="Em Andamento" <?= ($atendimento['status'] == 'Em Andamento' || old('status') == 'Em Andamento') ? 'selected' : '' ?>>Em Andamento</option>
                                        <option value="Finalizado" <?= ($atendimento['status'] == 'Finalizado' || old('status') == 'Finalizado') ? 'selected' : '' ?>>Finalizado</option>
                                        <option value="Cancelado" <?= ($atendimento['status'] == 'Cancelado' || old('status') == 'Cancelado') ? 'selected' : '' ?>>Cancelado</option>
                                        <option value="Aguardando" <?= ($atendimento['status'] == 'Aguardando' || old('status') == 'Aguardando') ? 'selected' : '' ?>>Aguardando</option>
                                        <option value="Suspenso" <?= ($atendimento['status'] == 'Suspenso' || old('status') == 'Suspenso') ? 'selected' : '' ?>>Suspenso</option>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback">
                                    <?= session('validation') && session('validation')->hasError('status') ? session('validation')->getError('status') : 'Por favor, selecione o status do atendimento.' ?>
                                </div>
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
                                            <option value="<?= $opcao ?>" <?= ($atendimento['encaminhamento'] == $opcao || old('encaminhamento') == $opcao) ? 'selected' : '' ?>>
                                                <?= $opcao ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="Alta" <?= ($atendimento['encaminhamento'] == 'Alta' || old('encaminhamento') == 'Alta') ? 'selected' : '' ?>>Alta</option>
                                        <option value="Internação" <?= ($atendimento['encaminhamento'] == 'Internação' || old('encaminhamento') == 'Internação') ? 'selected' : '' ?>>Internação</option>
                                        <option value="Transferência" <?= ($atendimento['encaminhamento'] == 'Transferência' || old('encaminhamento') == 'Transferência') ? 'selected' : '' ?>>Transferência</option>
                                        <option value="Especialista" <?= ($atendimento['encaminhamento'] == 'Especialista' || old('encaminhamento') == 'Especialista') ? 'selected' : '' ?>>Especialista</option>
                                        <option value="Retorno" <?= ($atendimento['encaminhamento'] == 'Retorno' || old('encaminhamento') == 'Retorno') ? 'selected' : '' ?>>Retorno</option>
                                        <option value="Óbito" <?= ($atendimento['encaminhamento'] == 'Óbito' || old('encaminhamento') == 'Óbito') ? 'selected' : '' ?>>Óbito</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Checkbox Óbito -->
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="obito" name="obito" value="1"
                                    <?= ($atendimento['obito'] || old('obito')) ? 'checked' : '' ?>>
                                <label class="form-check-label text-danger" for="obito">
                                    <i class="bi bi-exclamation-triangle"></i> Marcar como óbito
                                </label>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('atendimentos/show/' . $atendimento['id_atendimento']) ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <div>
                                <a href="<?= base_url('atendimentos') ?>" class="btn btn-outline-secondary me-2">
                                    <i class="bi bi-list"></i> Lista
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Salvar Alterações
                                </button>
                            </div>
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
    $(document).ready(function() {
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Auto-check óbito when encaminhamento is Óbito
        $('#encaminhamento').on('change', function() {
            if ($(this).val() === 'Óbito') {
                $('#obito').prop('checked', true);
            } else if ($('#obito').is(':checked') && $(this).val() !== 'Óbito') {
                // Only uncheck if user is not manually setting óbito
                if (!$('#obito').data('manual-set')) {
                    $('#obito').prop('checked', false);
                }
            }
        });

        // Auto-set encaminhamento when óbito is checked
        $('#obito').on('change', function() {
            if ($(this).is(':checked')) {
                $('#encaminhamento').val('Óbito');
                $(this).data('manual-set', true);
            } else {
                if ($('#encaminhamento').val() === 'Óbito') {
                    $('#encaminhamento').val('');
                }
                $(this).data('manual-set', false);
            }
        });

        // Select2 for better dropdowns
        if (typeof $.fn.select2 !== 'undefined') {
            $('#id_paciente, #id_medico').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: function() {
                    return $(this).data('placeholder');
                }
            });
        }

        // Confirm before leaving if form has changes
        let formChanged = false;
        $('#formAtendimento input, #formAtendimento select, #formAtendimento textarea').on('change input', function() {
            formChanged = true;
        });

        $(window).on('beforeunload', function() {
            if (formChanged) {
                return 'Você tem alterações não salvas. Tem certeza que deseja sair?';
            }
        });

        $('#formAtendimento').on('submit', function() {
            formChanged = false;
        });
    });
</script>
<?= $this->endSection() ?>