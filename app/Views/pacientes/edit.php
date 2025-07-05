<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
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

                                <!-- Dados Pessoais -->
                                <div class="form-section mb-4">
                                    <h5 class="form-section-title">
                                        <i class="bi bi-person"></i> Dados Pessoais
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="nome" class="form-label">
                                                    Nome Completo <span class="text-danger">*</span>
                                                </label>
                                                <input type="text"
                                                    class="form-control"
                                                    id="nome"
                                                    name="nome"
                                                    value="<?= esc($paciente['nome']) ?>"
                                                    required
                                                    placeholder="Digite o nome completo">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="data_nascimento" class="form-label">
                                                    Data de Nascimento <span class="text-danger">*</span>
                                                </label>
                                                <input type="date"
                                                    class="form-control"
                                                    id="data_nascimento"
                                                    name="data_nascimento"
                                                    value="<?= esc($paciente['data_nascimento']) ?>"
                                                    required>
                                                <div class="form-text">
                                                    Idade atual: <strong><?= esc($paciente['idade']) ?></strong> anos
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="cpf" class="form-label">
                                                    CPF <span class="text-danger">*</span>
                                                </label>
                                                <input type="text"
                                                    class="form-control"
                                                    id="cpf"
                                                    name="cpf"
                                                    value="<?= esc($paciente['cpf']) ?>"
                                                    required
                                                    placeholder="000.000.000-00"
                                                    data-mask="000.000.000-00">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="rg" class="form-label">RG</label>
                                                <input type="text"
                                                    class="form-control"
                                                    id="rg"
                                                    name="rg"
                                                    value="<?= esc($paciente['rg'] ?? '') ?>"
                                                    placeholder="Digite o RG">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="sexo" class="form-label">
                                                    Sexo <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="sexo" name="sexo" required>
                                                    <option value="">Selecione</option>
                                                    <option value="M" <?= ($paciente['sexo'] ?? '') == 'M' ? 'selected' : '' ?>>Masculino</option>
                                                    <option value="F" <?= ($paciente['sexo'] ?? '') == 'F' ? 'selected' : '' ?>>Feminino</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="telefone" class="form-label">Telefone</label>
                                                <input type="text"
                                                    class="form-control"
                                                    id="telefone"
                                                    name="telefone"
                                                    value="<?= esc($paciente['telefone'] ?? '') ?>"
                                                    placeholder="(00) 0000-0000"
                                                    data-mask="(00) 0000-0000">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="celular" class="form-label">Celular</label>
                                                <input type="text"
                                                    class="form-control"
                                                    id="celular"
                                                    name="celular"
                                                    value="<?= esc($paciente['celular'] ?? '') ?>"
                                                    placeholder="(00) 00000-0000"
                                                    data-mask="(00) 00000-0000">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="email" class="form-label">E-mail</label>
                                                <input type="email"
                                                    class="form-control"
                                                    id="email"
                                                    name="email"
                                                    value="<?= esc($paciente['email'] ?? '') ?>"
                                                    placeholder="exemplo@email.com">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="numero_sus" class="form-label">Número do SUS</label>
                                                <input type="text"
                                                    class="form-control"
                                                    id="numero_sus"
                                                    name="numero_sus"
                                                    value="<?= esc($paciente['numero_sus'] ?? '') ?>"
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
                                                <label for="id_bairro" class="form-label">Bairro <span class="text-danger">*</span></label>
                                                <select class="form-select" id="id_bairro" name="id_bairro" required>
                                                    <option value="">Selecione o bairro</option>
                                                    <?php if (isset($bairros) && !empty($bairros)): ?>
                                                        <?php foreach ($bairros as $bairro): ?>
                                                            <?php
                                                            $selected = '';
                                                            // Se o paciente tem logradouro, buscar o bairro do logradouro
                                                            if (isset($paciente['id_logradouro']) && !empty($paciente['id_logradouro'])) {
                                                                // Buscar o bairro do logradouro atual
                                                                $bairroLogradouro = null;
                                                                if (isset($logradouros)) {
                                                                    foreach ($logradouros as $log) {
                                                                        if ($log['id_logradouro'] == $paciente['id_logradouro']) {
                                                                            $bairroLogradouro = $log['id_bairro'];
                                                                            break;
                                                                        }
                                                                    }
                                                                }
                                                                if ($bairroLogradouro == $bairro['id_bairro']) {
                                                                    $selected = 'selected';
                                                                }
                                                            }
                                                            ?>
                                                            <option value="<?= $bairro['id_bairro'] ?>" <?= $selected ?>>
                                                                <?= esc($bairro['nome_bairro']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="id_logradouro" class="form-label">Logradouro <span class="text-danger">*</span></label>
                                                <select class="form-select" id="id_logradouro" name="id_logradouro" required>
                                                    <option value="">Selecione o logradouro</option>
                                                    <?php if (isset($logradouros) && !empty($logradouros)): ?>
                                                        <?php foreach ($logradouros as $logradouro): ?>
                                                            <option value="<?= $logradouro['id_logradouro'] ?>"
                                                                data-bairro="<?= $logradouro['id_bairro'] ?>"
                                                                <?= (($paciente['id_logradouro'] ?? '') == $logradouro['id_logradouro']) ? 'selected' : '' ?>>
                                                                <?= esc($logradouro['tipo_logradouro']) ?> <?= esc($logradouro['nome_logradouro']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label for="numero" class="form-label">Número</label>
                                                <input type="text"
                                                    class="form-control"
                                                    id="numero"
                                                    name="numero"
                                                    value="<?= esc($paciente['numero'] ?? '') ?>"
                                                    placeholder="123">
                                            </div>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="mb-3">
                                                <label for="complemento" class="form-label">Complemento</label>
                                                <input type="text"
                                                    class="form-control"
                                                    id="complemento"
                                                    name="complemento"
                                                    value="<?= esc($paciente['complemento'] ?? '') ?>"
                                                    placeholder="Apartamento, Bloco, Casa, etc.">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informações Médicas -->
                                <div class="form-section mb-4">
                                    <h5 class="form-section-title">
                                        <i class="bi bi-heart-pulse"></i> Informações Médicas
                                    </h5>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="tipo_sanguineo" class="form-label">Tipo Sanguíneo</label>
                                                <select class="form-select" id="tipo_sanguineo" name="tipo_sanguineo">
                                                    <option value="">Selecione</option>
                                                    <option value="A+" <?= ($paciente['tipo_sanguineo'] ?? '') == 'A+' ? 'selected' : '' ?>>A+</option>
                                                    <option value="A-" <?= ($paciente['tipo_sanguineo'] ?? '') == 'A-' ? 'selected' : '' ?>>A-</option>
                                                    <option value="B+" <?= ($paciente['tipo_sanguineo'] ?? '') == 'B+' ? 'selected' : '' ?>>B+</option>
                                                    <option value="B-" <?= ($paciente['tipo_sanguineo'] ?? '') == 'B-' ? 'selected' : '' ?>>B-</option>
                                                    <option value="AB+" <?= ($paciente['tipo_sanguineo'] ?? '') == 'AB+' ? 'selected' : '' ?>>AB+</option>
                                                    <option value="AB-" <?= ($paciente['tipo_sanguineo'] ?? '') == 'AB-' ? 'selected' : '' ?>>AB-</option>
                                                    <option value="O+" <?= ($paciente['tipo_sanguineo'] ?? '') == 'O+' ? 'selected' : '' ?>>O+</option>
                                                    <option value="O-" <?= ($paciente['tipo_sanguineo'] ?? '') == 'O-' ? 'selected' : '' ?>>O-</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="nome_responsavel" class="form-label">Nome do Responsável</label>
                                                <input type="text"
                                                    class="form-control"
                                                    id="nome_responsavel"
                                                    name="nome_responsavel"
                                                    value="<?= esc($paciente['nome_responsavel'] ?? '') ?>"
                                                    placeholder="Nome do responsável (para menores de idade)">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="alergias" class="form-label">Alergias</label>
                                                <textarea class="form-control"
                                                    id="alergias"
                                                    name="alergias"
                                                    rows="2"
                                                    placeholder="Descreva as alergias conhecidas do paciente"><?= esc($paciente['alergias'] ?? '') ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="observacoes" class="form-label">Observações</label>
                                                <textarea class="form-control"
                                                    id="observacoes"
                                                    name="observacoes"
                                                    rows="3"
                                                    placeholder="Observações gerais sobre o paciente"><?= esc($paciente['observacoes'] ?? '') ?></textarea>
                                            </div>
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
            <?= $this->include('components/footer') ?>
        </div>
    </main>
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
    // Carregar jQuery Mask Plugin
    if (typeof $ !== 'undefined') {
        // Máscaras de entrada
        $('#cpf').mask('000.000.000-00');
        $('#telefone').mask('(00) 0000-0000');
        $('#celular').mask('(00) 00000-0000');

        // Filtrar logradouros por bairro
        $('#id_bairro').change(function() {
            var bairroId = $(this).val();
            var logradouroSelect = $('#id_logradouro');
            var currentLogradouro = logradouroSelect.val();

            if (bairroId) {
                // Mostrar apenas logradouros do bairro selecionado
                logradouroSelect.find('option').each(function() {
                    var option = $(this);
                    if (option.val() === '' || option.data('bairro') == bairroId) {
                        option.show();
                    } else {
                        option.hide();
                        if (option.val() === currentLogradouro) {
                            logradouroSelect.val('');
                        }
                    }
                });
            } else {
                // Mostrar todos os logradouros
                logradouroSelect.find('option').show();
            }
        });

        // Selecionar bairro baseado no logradouro atual
        $(document).ready(function() {
            var currentLogradouro = $('#id_logradouro').val();
            if (currentLogradouro) {
                var bairroId = $('#id_logradouro option:selected').data('bairro');
                if (bairroId) {
                    $('#id_bairro').val(bairroId);
                }
            }

            // Trigger inicial para filtrar logradouros
            $('#id_bairro').trigger('change');
        });

        // Calcular idade automaticamente
        $('#data_nascimento').change(function() {
            var nascimento = new Date($(this).val());
            var hoje = new Date();
            var idade = hoje.getFullYear() - nascimento.getFullYear();
            var mes = hoje.getMonth() - nascimento.getMonth();

            if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
                idade--;
            }

            // Atualizar exibição da idade
            $('.form-text strong').text(idade);

            // Se menor de 18, mostrar campo responsável como obrigatório
            if (idade < 18) {
                $('#nome_responsavel').prop('required', true);
                $('#nome_responsavel').closest('.mb-3').find('label').html('Nome do Responsável <span class="text-danger">*</span>');
            } else {
                $('#nome_responsavel').prop('required', false);
                $('#nome_responsavel').closest('.mb-3').find('label').html('Nome do Responsável');
            }
        });
    } else {
        // Fallback para quando jQuery não estiver disponível
        // Máscara para CPF
        document.getElementById('cpf').addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
            this.value = value;
        });
    }

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

<!-- Carregar jQuery e jQuery Mask se não estiverem carregados -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<style>
    .form-section {
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 1.5rem;
        background: var(--card-bg);
    }

    .form-section-title {
        color: var(--primary-color);
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--primary-color);
    }

    .form-section-title i {
        margin-right: 0.5rem;
    }

    .form-actions {
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid var(--border-color);
    }

    .form-actions .btn {
        margin-right: 0.5rem;
    }
</style>

<?= $this->endSection() ?>

<?php
// Helper function for risk classification colors
function getClassificacaoRiscoCor($classificacao)
{
    switch (strtolower($classificacao)) {
        case 'vermelho':
            return 'danger';
        case 'amarelo':
            return 'warning';
        case 'verde':
            return 'success';
        case 'azul':
            return 'info';
        default:
            return 'secondary';
    }
}
?>