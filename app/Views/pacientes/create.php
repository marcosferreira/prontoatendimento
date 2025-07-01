<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>
    
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
                                Novo Paciente
                            </li>
                        </ol>
                    </nav>
                    <h1><i class="bi bi-person-plus"></i> Novo Paciente</h1>
                    <p class="subtitle">Cadastrar um novo paciente no sistema</p>
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

                                <form action="<?= base_url('pacientes/store') ?>" method="POST" id="formNovoPaciente">
                                    <?= csrf_field() ?>
                                    
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
                                                           value="<?= old('nome') ?>"
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
                                                           value="<?= old('data_nascimento') ?>"
                                                           required>
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
                                                           value="<?= old('cpf') ?>"
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
                                                           value="<?= old('rg') ?>"
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
                                                        <option value="M" <?= old('sexo') == 'M' ? 'selected' : '' ?>>Masculino</option>
                                                        <option value="F" <?= old('sexo') == 'F' ? 'selected' : '' ?>>Feminino</option>
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
                                                           value="<?= old('telefone') ?>"
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
                                                           value="<?= old('celular') ?>"
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
                                                           value="<?= old('email') ?>"
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
                                                           value="<?= old('numero_sus') ?>"
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
                                            <div class="col-md-3">
                                                <div class="mb-3">
                                                    <label for="cep" class="form-label">CEP</label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="cep" 
                                                           name="cep" 
                                                           value="<?= old('cep') ?>"
                                                           placeholder="00000-000"
                                                           data-mask="00000-000">
                                                </div>
                                            </div>
                                            <div class="col-md-7">
                                                <div class="mb-3">
                                                    <label for="endereco" class="form-label">Endereço</label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="endereco" 
                                                           name="endereco" 
                                                           value="<?= old('endereco') ?>"
                                                           placeholder="Digite o endereço">
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="mb-3">
                                                    <label for="numero" class="form-label">Número</label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="numero" 
                                                           name="numero" 
                                                           value="<?= old('numero') ?>"
                                                           placeholder="123">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="complemento" class="form-label">Complemento</label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="complemento" 
                                                           name="complemento" 
                                                           value="<?= old('complemento') ?>"
                                                           placeholder="Apto, Bloco, etc.">
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="id_bairro" class="form-label">Bairro</label>
                                                    <select class="form-select" id="id_bairro" name="id_bairro">
                                                        <option value="">Selecione o bairro</option>
                                                        <?php if (isset($bairros) && !empty($bairros)): ?>
                                                            <?php foreach ($bairros as $bairro): ?>
                                                                <?php 
                                                                $selected = '';
                                                                if (old('id_bairro') == $bairro['id_bairro']) {
                                                                    $selected = 'selected';
                                                                } elseif (!old('id_bairro') && isset($bairro_selecionado) && $bairro_selecionado == $bairro['id_bairro']) {
                                                                    $selected = 'selected';
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
                                            <div class="col-md-4">
                                                <div class="mb-3">
                                                    <label for="cidade" class="form-label">Cidade</label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="cidade" 
                                                           name="cidade" 
                                                           value="<?= old('cidade') ?>"
                                                           placeholder="Digite a cidade">
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
                                                        <option value="A+" <?= old('tipo_sanguineo') == 'A+' ? 'selected' : '' ?>>A+</option>
                                                        <option value="A-" <?= old('tipo_sanguineo') == 'A-' ? 'selected' : '' ?>>A-</option>
                                                        <option value="B+" <?= old('tipo_sanguineo') == 'B+' ? 'selected' : '' ?>>B+</option>
                                                        <option value="B-" <?= old('tipo_sanguineo') == 'B-' ? 'selected' : '' ?>>B-</option>
                                                        <option value="AB+" <?= old('tipo_sanguineo') == 'AB+' ? 'selected' : '' ?>>AB+</option>
                                                        <option value="AB-" <?= old('tipo_sanguineo') == 'AB-' ? 'selected' : '' ?>>AB-</option>
                                                        <option value="O+" <?= old('tipo_sanguineo') == 'O+' ? 'selected' : '' ?>>O+</option>
                                                        <option value="O-" <?= old('tipo_sanguineo') == 'O-' ? 'selected' : '' ?>>O-</option>
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
                                                           value="<?= old('nome_responsavel') ?>"
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
                                                              placeholder="Descreva as alergias conhecidas do paciente"><?= old('alergias') ?></textarea>
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
                                                              placeholder="Observações gerais sobre o paciente"><?= old('observacoes') ?></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Buttons -->
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle"></i> Salvar Paciente
                                        </button>
                                        <a href="<?= base_url('pacientes') ?>" class="btn btn-secondary">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Sidebar Info -->
                        <div class="col-lg-4">
                            <div class="info-card">
                                <h5 class="info-title">
                                    <i class="bi bi-info-circle"></i> Informações
                                </h5>
                                <ul class="info-list">
                                    <li><strong>Campos obrigatórios:</strong> Nome, Data de Nascimento, CPF e Sexo</li>
                                    <li><strong>CPF:</strong> Deve ser único no sistema</li>
                                    <li><strong>Telefone:</strong> Pelo menos um contato é recomendado</li>
                                    <li><strong>SUS:</strong> Número do cartão do SUS se disponível</li>
                                    <li><strong>Responsável:</strong> Obrigatório para menores de 18 anos</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
</div>

<!-- JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
$(document).ready(function() {
    // Máscaras
    $('#cpf').mask('000.000.000-00');
    $('#cep').mask('00000-000');
    $('#telefone').mask('(00) 0000-0000');
    $('#celular').mask('(00) 00000-0000');
    
    // Buscar CEP
    $('#cep').blur(function() {
        var cep = $(this).val().replace(/\D/g, '');
        
        if (cep.length == 8) {
            $.getJSON('https://viacep.com.br/ws/' + cep + '/json/', function(data) {
                if (!data.erro) {
                    $('#endereco').val(data.logradouro);
                    $('#cidade').val(data.localidade);
                    
                    // Buscar bairro no select
                    var bairroNome = data.bairro.toLowerCase();
                    $('#id_bairro option').each(function() {
                        if ($(this).text().toLowerCase().indexOf(bairroNome) !== -1) {
                            $(this).prop('selected', true);
                            return false;
                        }
                    });
                }
            });
        }
    });
    
    // Validação de CPF
    $('#cpf').blur(function() {
        var cpf = $(this).val().replace(/\D/g, '');
        
        if (cpf.length == 11) {
            if (!validarCPF(cpf)) {
                alert('CPF inválido!');
                $(this).focus();
            }
        }
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
        
        // Se menor de 18, mostrar campo responsável como obrigatório
        if (idade < 18) {
            $('#nome_responsavel').prop('required', true);
            $('#nome_responsavel').closest('.mb-3').find('label').html('Nome do Responsável <span class="text-danger">*</span>');
        } else {
            $('#nome_responsavel').prop('required', false);
            $('#nome_responsavel').closest('.mb-3').find('label').html('Nome do Responsável');
        }
    });
});

function validarCPF(cpf) {
    var sum = 0;
    var remainder;
    
    if (cpf == "00000000000") return false;
    
    for (i = 1; i <= 9; i++) {
        sum = sum + parseInt(cpf.substring(i-1, i)) * (11 - i);
    }
    
    remainder = (sum * 10) % 11;
    
    if ((remainder == 10) || (remainder == 11)) remainder = 0;
    if (remainder != parseInt(cpf.substring(9, 10))) return false;
    
    sum = 0;
    for (i = 1; i <= 10; i++) {
        sum = sum + parseInt(cpf.substring(i-1, i)) * (12 - i);
    }
    
    remainder = (sum * 10) % 11;
    
    if ((remainder == 10) || (remainder == 11)) remainder = 0;
    if (remainder != parseInt(cpf.substring(10, 11))) return false;
    
    return true;
}
</script>

<?= $this->endSection() ?>
