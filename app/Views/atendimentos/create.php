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
                                <div class="input-group">
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
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#novoPacienteModal" title="Cadastrar novo paciente">
                                        <i class="bi bi-person-plus"></i>
                                    </button>
                                </div>
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
                                <div class="input-group">
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
                                    <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#novoMedicoModal" title="Cadastrar novo médico">
                                        <i class="bi bi-person-plus"></i>
                                    </button>
                                </div>
                                <div class="invalid-feedback">
                                    <?= session('validation') && session('validation')->hasError('id_medico') ? session('validation')->getError('id_medico') : 'Por favor, selecione um médico.' ?>
                                </div>
                                <!-- Button to unlock medico selection (only shown when locked) -->
                                <button type="button" id="unlock_medico" class="btn btn-sm btn-outline-warning mt-1" style="display: none;" title="Permitir alteração do médico">
                                    <i class="bi bi-unlock"></i> Alterar Médico
                                </button>
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
                                rows="3" placeholder="Digite 'SIM' ou 'NÃO' para informar. Caso necessário adicione observações da consulta de enfermagem..."><?= old('consulta_enfermagem') ?></textarea>
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

<!-- Modal Novo Paciente -->
<div class="modal fade" id="novoPacienteModal" tabindex="-1" aria-labelledby="novoPacienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="novoPacienteModalLabel">
                    <i class="bi bi-person-plus"></i> Novo Paciente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNovoPacienteModal" action="<?= base_url('pacientes/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="return_to_atendimento" value="1">
                    
                    <!-- Dados Pessoais -->
                    <div class="form-section mb-4">
                        <h5 class="form-section-title">
                            <i class="bi bi-person"></i> Dados Pessoais
                        </h5>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="modal_nome" class="form-label">
                                        Nome Completo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control"
                                        id="modal_nome"
                                        name="nome"
                                        required
                                        placeholder="Digite o nome completo">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_data_nascimento" class="form-label">
                                        Data de Nascimento <span class="text-danger">*</span>
                                    </label>
                                    <input type="date"
                                        class="form-control"
                                        id="modal_data_nascimento"
                                        name="data_nascimento"
                                        required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_cpf" class="form-label">
                                        CPF <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control"
                                        id="modal_cpf"
                                        name="cpf"
                                        required
                                        placeholder="000.000.000-00">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_rg" class="form-label">RG</label>
                                    <input type="text"
                                        class="form-control"
                                        id="modal_rg"
                                        name="rg"
                                        placeholder="Digite o RG">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_sexo" class="form-label">
                                        Sexo <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="modal_sexo" name="sexo" required>
                                        <option value="">Selecione</option>
                                        <option value="M">Masculino</option>
                                        <option value="F">Feminino</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modal_telefone" class="form-label">Telefone</label>
                                    <input type="text"
                                        class="form-control"
                                        id="modal_telefone"
                                        name="telefone"
                                        placeholder="(00) 0000-0000">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modal_celular" class="form-label">Celular</label>
                                    <input type="text"
                                        class="form-control"
                                        id="modal_celular"
                                        name="celular"
                                        placeholder="(00) 00000-0000">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="modal_email" class="form-label">E-mail</label>
                                    <input type="email"
                                        class="form-control"
                                        id="modal_email"
                                        name="email"
                                        placeholder="exemplo@email.com">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_numero_sus" class="form-label">Número do SUS</label>
                                    <input type="text"
                                        class="form-control"
                                        id="modal_numero_sus"
                                        name="numero_sus"
                                        placeholder="000000000000000"
                                        maxlength="15">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="form-section mb-4">
                        <h5 class="form-section-title">
                            <i class="bi bi-geo-alt"></i> Endereço
                        </h5>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_id_bairro" class="form-label">Bairro</label>
                                    <select class="form-select" id="modal_id_bairro" name="id_bairro">
                                        <option value="">Selecione o bairro</option>
                                        <?php if (isset($bairros) && !empty($bairros)): ?>
                                            <?php foreach ($bairros as $bairro): ?>
                                                <option value="<?= $bairro['id_bairro'] ?>">
                                                    <?= esc($bairro['nome_bairro']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modal_id_logradouro" class="form-label">Logradouro</label>
                                    <select class="form-select" id="modal_id_logradouro" name="id_logradouro">
                                        <option value="">Selecione primeiro o bairro</option>
                                        <?php if (isset($logradouros) && !empty($logradouros)): ?>
                                            <?php foreach ($logradouros as $logradouro): ?>
                                                <option value="<?= $logradouro['id_logradouro'] ?>" 
                                                        data-bairro="<?= $logradouro['id_bairro'] ?>" 
                                                        style="display: none;">
                                                    <?= esc($logradouro['tipo_logradouro'] . ' ' . $logradouro['nome_logradouro']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="modal_numero" class="form-label">Número</label>
                                    <input type="text"
                                        class="form-control"
                                        id="modal_numero"
                                        name="numero"
                                        placeholder="123">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="modal_complemento" class="form-label">Complemento</label>
                                    <input type="text"
                                        class="form-control"
                                        id="modal_complemento"
                                        name="complemento"
                                        placeholder="Apto, Bloco, Casa, etc.">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações Adicionais -->
                    <div class="form-section mb-4">
                        <h5 class="form-section-title">
                            <i class="bi bi-info-circle"></i> Informações Adicionais
                        </h5>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="modal_nome_responsavel" class="form-label">Nome do Responsável</label>
                                    <input type="text"
                                        class="form-control"
                                        id="modal_nome_responsavel"
                                        name="nome_responsavel"
                                        placeholder="Digite o nome do responsável (obrigatório para menores de 18 anos)">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="modal_observacoes" class="form-label">Observações</label>
                                    <textarea class="form-control"
                                        id="modal_observacoes"
                                        name="observacoes"
                                        rows="3"
                                        placeholder="Observações gerais sobre o paciente..."
                                        maxlength="1000"></textarea>
                                    <div class="form-text">Máximo 1000 caracteres</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="submit" form="formNovoPacienteModal" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Salvar Paciente
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Médico -->
<div class="modal fade" id="novoMedicoModal" tabindex="-1" aria-labelledby="novoMedicoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="novoMedicoModalLabel">
                    <i class="bi bi-person-badge"></i> Novo Médico
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formNovoMedicoModal" action="<?= base_url('medicos/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="return_to_atendimento" value="1">
                    
                    <!-- Dados Profissionais -->
                    <div class="form-section mb-4">
                        <h5 class="form-section-title">
                            <i class="bi bi-person-badge"></i> Dados Profissionais
                        </h5>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="medico_nome" class="form-label">
                                        Nome Completo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control"
                                        id="medico_nome"
                                        name="nome"
                                        required
                                        placeholder="Digite o nome completo do médico">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="medico_crm" class="form-label">
                                        CRM <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control"
                                        id="medico_crm"
                                        name="crm"
                                        required
                                        placeholder="000000">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="medico_especialidade" class="form-label">
                                        Especialidade <span class="text-danger">*</span>
                                    </label>
                                    <input type="text"
                                        class="form-control"
                                        id="medico_especialidade"
                                        name="especialidade"
                                        required
                                        placeholder="Ex: Clínica Médica, Cardiologia, etc.">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="medico_telefone" class="form-label">Telefone</label>
                                    <input type="text"
                                        class="form-control"
                                        id="medico_telefone"
                                        name="telefone"
                                        placeholder="(00) 0000-0000">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="medico_email" class="form-label">E-mail</label>
                                    <input type="email"
                                        class="form-control"
                                        id="medico_email"
                                        name="email"
                                        placeholder="medico@exemplo.com">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-section mb-4">
                        <h5 class="form-section-title">
                            <i class="bi bi-activity"></i> Status
                        </h5>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="medico_status" class="form-label">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" id="medico_status" name="status" required>
                                        <option value="Ativo" selected>Ativo</option>
                                        <option value="Inativo">Inativo</option>
                                        <option value="Suspenso">Suspenso</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="submit" form="formNovoMedicoModal" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Salvar Médico
                </button>
            </div>
        </div>
    </div>
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
                const medicoSelect = document.getElementById('id_medico');
                const unlockButton = document.getElementById('unlock_medico');

                // Set the value
                medicoSelect.value = medicoId;

                // Disable the medico select to prevent manual changes
                medicoSelect.disabled = true;

                // Add visual indication that field is locked
                const label = medicoSelect.closest('.mb-3').querySelector('label');
                const badge = document.createElement('span');
                badge.className = 'badge bg-info text-white ms-1';
                badge.innerHTML = '<i class="bi bi-lock"></i> Pré-selecionado';
                label.appendChild(badge);

                // Show unlock button
                unlockButton.style.display = 'inline-block';
            } else {
                // If no medico parameter, ensure field is enabled
                const medicoSelect = document.getElementById('id_medico');
                medicoSelect.disabled = false;
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
                    const medicoSelect = document.getElementById('id_medico');
                    const dataInput = document.getElementById('data_atendimento');
                    const urlParams = new URLSearchParams(window.location.search);
                    const pacienteFromUrl = urlParams.get('paciente');
                    const medicoFromUrl = urlParams.get('medico');
                    
                    // Garantir que a data esteja no formato correto antes do envio
                    if (dataInput && dataInput.value) {
                        dataInput.value = ensureDateTimeLocalFormat(dataInput.value);
                    }
                    
                    // If patient was pre-selected and select is disabled, temporarily enable it for submission
                    if (pacienteSelect.disabled && pacienteFromUrl) {
                        pacienteSelect.disabled = false;
                        pacienteSelect.value = pacienteFromUrl;
                    }

                    // If medico was pre-selected and select is disabled, temporarily enable it for submission
                    if (medicoSelect.disabled && medicoFromUrl) {
                        medicoSelect.disabled = false;
                        medicoSelect.value = medicoFromUrl;
                    }

                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                        
                        // Re-disable the selects if validation fails and they were pre-selected
                        if (pacienteFromUrl && !pacienteSelect.value) {
                            pacienteSelect.disabled = true;
                        }
                        if (medicoFromUrl && !medicoSelect.value) {
                            medicoSelect.disabled = true;
                        }
                    } else {
                        // Form is valid, ensure the selects stay enabled for submission
                        if (pacienteFromUrl) {
                            pacienteSelect.disabled = false;
                        }
                        if (medicoFromUrl) {
                            medicoSelect.disabled = false;
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

        // Handle unlock medico button
        document.getElementById('unlock_medico').addEventListener('click', function() {
            const medicoSelect = document.getElementById('id_medico');
            const medicoContainer = medicoSelect.closest('.mb-3');

            // Enable the medico select
            medicoSelect.disabled = false;

            // Remove the locked badge
            const badge = medicoContainer.querySelector('.badge');
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
            <i class="bi bi-check-circle"></i> Agora você pode alterar o médico selecionado.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
            medicoContainer.appendChild(alertDiv);

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

        // Modal Novo Paciente Functions
        const novoPacienteModal = document.getElementById('novoPacienteModal');
        const formNovoPacienteModal = document.getElementById('formNovoPacienteModal');

        // Adicionar event listener ao botão de submit da modal
        const modalSubmitBtn = document.querySelector('button[form="formNovoPacienteModal"][type="submit"]');
        if (modalSubmitBtn) {
            modalSubmitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Botão de submit clicado!');
                
                // Disparar o evento de submit no formulário
                const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
                formNovoPacienteModal.dispatchEvent(submitEvent);
            });
        }

        // Configurar máscaras e validações quando o modal for aberto
        novoPacienteModal.addEventListener('shown.bs.modal', function() {
            console.log('Modal aberta!');
            
            // Verificar se todos os elementos necessários existem
            const elementos = {
                'modal_nome': document.getElementById('modal_nome'),
                'modal_cpf': document.getElementById('modal_cpf'),
                'modal_data_nascimento': document.getElementById('modal_data_nascimento'),
                'modal_sexo': document.getElementById('modal_sexo'),
                'formNovoPacienteModal': document.getElementById('formNovoPacienteModal'),
                'submitBtn': document.querySelector('button[form="formNovoPacienteModal"][type="submit"]')
            };
            
            console.log('Elementos encontrados:', elementos);
            
            // Focar no primeiro campo
            if (elementos.modal_nome) {
                elementos.modal_nome.focus();
            }

            // Aplicar máscaras
            applyMask('modal_cpf', '000.000.000-00');
            applyMask('modal_telefone', '(00) 0000-0000');
            applyMask('modal_celular', '(00) 00000-0000');

            // Filtrar logradouros por bairro
            document.getElementById('modal_id_bairro').addEventListener('change', function() {
                const bairroId = this.value;
                const logradouroSelect = document.getElementById('modal_id_logradouro');
                
                // Limpar e resetar logradouros
                logradouroSelect.value = '';
                const options = logradouroSelect.querySelectorAll('option');
                
                options.forEach(option => {
                    if (option.value === '') {
                        option.style.display = 'block';
                        option.textContent = bairroId ? 'Selecione o logradouro' : 'Selecione primeiro o bairro';
                    } else {
                        const optionBairro = option.getAttribute('data-bairro');
                        option.style.display = (bairroId && optionBairro === bairroId) ? 'block' : 'none';
                    }
                });
            });

            // Validar idade e responsável
            document.getElementById('modal_data_nascimento').addEventListener('change', function() {
                const nascimento = new Date(this.value);
                const hoje = new Date();
                let idade = hoje.getFullYear() - nascimento.getFullYear();
                const mes = hoje.getMonth() - nascimento.getMonth();

                if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
                    idade--;
                }

                const responsavelField = document.getElementById('modal_nome_responsavel');
                const responsavelLabel = responsavelField.closest('.mb-3').querySelector('label');

                if (idade < 18) {
                    responsavelField.required = true;
                    responsavelLabel.innerHTML = 'Nome do Responsável <span class="text-danger">*</span>';
                    responsavelField.placeholder = 'Obrigatório para menores de 18 anos';
                } else {
                    responsavelField.required = false;
                    responsavelLabel.innerHTML = 'Nome do Responsável';
                    responsavelField.placeholder = 'Digite o nome do responsável (obrigatório para menores de 18 anos)';
                }
            });
        });

        // Limpar formulário quando modal for fechada
        novoPacienteModal.addEventListener('hidden.bs.modal', function() {
            formNovoPacienteModal.reset();
            formNovoPacienteModal.classList.remove('was-validated');
            
            // Resetar logradouros
            const logradouroSelect = document.getElementById('modal_id_logradouro');
            logradouroSelect.innerHTML = '<option value="">Selecione primeiro o bairro</option>';
            
            // Adicionar todas as opções de logradouro novamente
            <?php if (isset($logradouros) && !empty($logradouros)): ?>
                <?php foreach ($logradouros as $logradouro): ?>
                    const option_<?= $logradouro['id_logradouro'] ?> = document.createElement('option');
                    option_<?= $logradouro['id_logradouro'] ?>.value = '<?= $logradouro['id_logradouro'] ?>';
                    option_<?= $logradouro['id_logradouro'] ?>.setAttribute('data-bairro', '<?= $logradouro['id_bairro'] ?>');
                    option_<?= $logradouro['id_logradouro'] ?>.style.display = 'none';
                    option_<?= $logradouro['id_logradouro'] ?>.textContent = '<?= esc($logradouro['tipo_logradouro'] . ' ' . $logradouro['nome_logradouro']) ?>';
                    logradouroSelect.appendChild(option_<?= $logradouro['id_logradouro'] ?>);
                <?php endforeach; ?>
            <?php endif; ?>
        });

        // Submeter formulário do modal
        formNovoPacienteModal.addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('Submit do formulário da modal disparado!');
            
            // Validar campos obrigatórios primeiro
            const nome = document.getElementById('modal_nome').value.trim();
            const cpf = document.getElementById('modal_cpf').value.replace(/\D/g, '');
            const dataNascimento = document.getElementById('modal_data_nascimento').value;
            const sexo = document.getElementById('modal_sexo').value;
            
            // Verificar campos obrigatórios
            if (!nome) {
                alert('Nome é obrigatório.');
                document.getElementById('modal_nome').focus();
                return;
            }
            
            if (!dataNascimento) {
                alert('Data de nascimento é obrigatória.');
                document.getElementById('modal_data_nascimento').focus();
                return;
            }
            
            if (!sexo) {
                alert('Sexo é obrigatório.');
                document.getElementById('modal_sexo').focus();
                return;
            }
            
            // Validar CPF
            if (cpf.length !== 11 || !validarCPF(cpf)) {
                alert('CPF inválido. Por favor, verifique.');
                document.getElementById('modal_cpf').focus();
                return;
            }

            console.log('Validações passaram, enviando requisição...');

            // Mostrar loading - buscar o botão que está no modal-footer
            const submitBtn = document.querySelector('button[form="formNovoPacienteModal"][type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Salvando...';
            submitBtn.disabled = true;

            // Submeter via AJAX
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Adicionar novo paciente ao select
                    const pacienteSelect = document.getElementById('id_paciente');
                    const newOption = document.createElement('option');
                    newOption.value = data.paciente.id_paciente;
                    newOption.textContent = `${data.paciente.nome} - CPF: ${data.paciente.cpf}`;
                    newOption.selected = true;
                    pacienteSelect.appendChild(newOption);
                    
                    // Fechar modal
                    const modal = bootstrap.Modal.getInstance(novoPacienteModal);
                    modal.hide();
                    
                    // Mostrar mensagem de sucesso
                    showAlert('success', 'Paciente cadastrado com sucesso e selecionado!');
                } else {
                    showAlert('danger', data.message || 'Erro ao cadastrar paciente');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showAlert('danger', 'Erro de comunicação com o servidor');
            })
            .finally(() => {
                // Restaurar botão
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

        // Modal Novo Médico Functions
        const novoMedicoModal = document.getElementById('novoMedicoModal');
        const formNovoMedicoModal = document.getElementById('formNovoMedicoModal');

        // Adicionar event listener ao botão de submit da modal médico
        const modalMedicoSubmitBtn = document.querySelector('button[form="formNovoMedicoModal"][type="submit"]');
        if (modalMedicoSubmitBtn) {
            modalMedicoSubmitBtn.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('Botão de submit do médico clicado!');
                
                // Disparar o evento de submit no formulário
                const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
                formNovoMedicoModal.dispatchEvent(submitEvent);
            });
        }

        // Configurar máscaras e validações quando o modal médico for aberto
        novoMedicoModal.addEventListener('shown.bs.modal', function() {
            console.log('Modal médico aberta!');
            
            // Verificar se todos os elementos necessários existem
            const elementos = {
                'medico_nome': document.getElementById('medico_nome'),
                'medico_crm': document.getElementById('medico_crm'),
                'medico_especialidade': document.getElementById('medico_especialidade'),
                'formNovoMedicoModal': document.getElementById('formNovoMedicoModal'),
                'submitBtn': document.querySelector('button[form="formNovoMedicoModal"][type="submit"]')
            };
            
            console.log('Elementos médico encontrados:', elementos);
            
            // Focar no primeiro campo
            if (elementos.medico_nome) {
                elementos.medico_nome.focus();
            }

            // Aplicar máscara no telefone
            applyMask('medico_telefone', '(00) 0000-0000');
        });

        // Limpar formulário quando modal médico for fechada
        novoMedicoModal.addEventListener('hidden.bs.modal', function() {
            formNovoMedicoModal.reset();
            formNovoMedicoModal.classList.remove('was-validated');
        });

        // Submeter formulário da modal médico
        formNovoMedicoModal.addEventListener('submit', function(e) {
            e.preventDefault();
            
            console.log('Submit do formulário da modal médico disparado!');
            
            // Validar campos obrigatórios primeiro
            const nome = document.getElementById('medico_nome').value.trim();
            const crm = document.getElementById('medico_crm').value.trim();
            const especialidade = document.getElementById('medico_especialidade').value.trim();
            
            // Verificar campos obrigatórios
            if (!nome) {
                alert('Nome é obrigatório.');
                document.getElementById('medico_nome').focus();
                return;
            }
            
            if (!crm) {
                alert('CRM é obrigatório.');
                document.getElementById('medico_crm').focus();
                return;
            }
            
            if (!especialidade) {
                alert('Especialidade é obrigatória.');
                document.getElementById('medico_especialidade').focus();
                return;
            }

            console.log('Validações do médico passaram, enviando requisição...');

            // Mostrar loading - buscar o botão que está no modal-footer
            const submitBtn = document.querySelector('button[form="formNovoMedicoModal"][type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Salvando...';
            submitBtn.disabled = true;

            // Submeter via AJAX
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Adicionar novo médico ao select
                    const medicoSelect = document.getElementById('id_medico');
                    const newOption = document.createElement('option');
                    newOption.value = data.medico.id_medico;
                    newOption.textContent = `${data.medico.nome} - CRM: ${data.medico.crm}`;
                    newOption.selected = true;
                    medicoSelect.appendChild(newOption);
                    
                    // Fechar modal
                    const modal = bootstrap.Modal.getInstance(novoMedicoModal);
                    modal.hide();
                    
                    // Mostrar mensagem de sucesso
                    showAlert('success', 'Médico cadastrado com sucesso e selecionado!');
                } else {
                    showAlert('danger', data.message || 'Erro ao cadastrar médico');
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                showAlert('danger', 'Erro de comunicação com o servidor');
            })
            .finally(() => {
                // Restaurar botão
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

        // Função para aplicar máscara
        function applyMask(elementId, mask) {
            const element = document.getElementById(elementId);
            if (!element) return;

            element.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');
                let maskedValue = '';
                let maskIndex = 0;
                let valueIndex = 0;

                while (maskIndex < mask.length && valueIndex < value.length) {
                    if (mask[maskIndex] === '0') {
                        maskedValue += value[valueIndex];
                        valueIndex++;
                    } else {
                        maskedValue += mask[maskIndex];
                    }
                    maskIndex++;
                }

                this.value = maskedValue;
            });
        }

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

        // Função para mostrar alertas
        function showAlert(type, message) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
            alertDiv.setAttribute('role', 'alert');
            alertDiv.innerHTML = `
                <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Se estamos na modal, inserir na modal, senão no formulário principal
            const modalBody = document.querySelector('#novoPacienteModal .modal-body');
            const form = document.getElementById('formAtendimento');
            
            if (modalBody && novoPacienteModal.classList.contains('show')) {
                // Modal está aberta, inserir alerta na modal
                modalBody.insertBefore(alertDiv, modalBody.firstChild);
            } else if (form) {
                // Inserir no formulário principal
                form.insertBefore(alertDiv, form.firstChild);
            }
            
            // Auto-remover após 5 segundos
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 5000);
        }
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    /* Estilos para a modal de novo paciente */
    .modal-xl .modal-body {
        max-height: 70vh;
        overflow-y: auto;
    }

    .form-section {
        border: 1px solid #e9ecef;
        border-radius: 0.375rem;
        padding: 1rem;
        background: #f8f9fa;
        margin-bottom: 1rem;
    }

    .form-section-title {
        color: #0d6efd;
        font-weight: 600;
        margin-bottom: 0.75rem;
        padding-bottom: 0.25rem;
        border-bottom: 1px solid #0d6efd;
        font-size: 0.9rem;
    }

    .form-section-title i {
        margin-right: 0.5rem;
    }

    /* Melhorar aparência do input-group com select e botão */
    .input-group .form-select {
        border-right: 0;
    }

    .input-group .btn-outline-success {
        border-left: 0;
        border-color: #ced4da;
    }

    .input-group .form-select:focus {
        box-shadow: none;
        border-color: #86b7fe;
    }

    .input-group .form-select:focus + .btn-outline-success {
        border-color: #86b7fe;
    }

    /* Animação para campos pré-selecionados */
    .highlight-field {
        animation: highlightPulse 2s ease-in-out;
    }

    @keyframes highlightPulse {
        0% {
            background-color: #fff3cd;
            border-color: #ffc107;
        }
        50% {
            background-color: #d1ecf1;
            border-color: #bee5eb;
        }
        100% {
            background-color: #ffffff;
            border-color: #ced4da;
        }
    }

    /* Melhorar visual dos alertas */
    .alert {
        border-left: 4px solid;
        border-radius: 0.375rem;
    }

    .alert-success {
        border-left-color: #198754;
        background-color: #d1e7dd;
    }

    .alert-danger {
        border-left-color: #dc3545;
        background-color: #f8d7da;
    }

    .alert-info {
        border-left-color: #0dcaf0;
        background-color: #d1ecf1;
    }
</style>
<?= $this->endSection() ?>