<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-person-badge"></i> Pacientes</h1>
                <p class="subtitle">Gerenciamento de Pacientes Cadastrados</p>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-left m-4">
                    <div class="search-container position-relative">
                        <input type="text" id="searchPaciente" class="form-control search-input pe-5"
                        placeholder="Buscar por nome, CPF ou SUS...">
                        <i class="bi bi-search search-icon position-absolute top-50 end-0 translate-middle-y me-3"></i>
                    </div>
                </div>
                <div class="action-right m-4">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#novoPacienteModal">
                        <i class="bi bi-plus-circle"></i> Novo Paciente
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row m-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= isset($stats['total']) ? $stats['total'] : '0' ?></div>
                        <div class="stat-label">Total de Pacientes</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= isset($stats['hoje']) ? $stats['hoje'] : '0' ?></div>
                        <div class="stat-label">Cadastrados Hoje</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= isset($stats['mes']) ? $stats['mes'] : '0' ?></div>
                        <div class="stat-label">Este Mês</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= isset($stats['idade_media']) ? $stats['idade_media'] : '0' ?></div>
                        <div class="stat-label">Idade Média</div>
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="content-wrapper">
                <div class="section-card">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="bi bi-list-ul"></i>
                            Lista de Pacientes
                        </h3>
                        <div class="section-actions">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-outline-primary btn-sm active" onclick="showAll()">
                                    Todos
                                </button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="showRecent()">
                                    Recentes
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table modern-table" id="pacientesTable">
                            <thead>
                                <tr>
                                    <th scope="col">Nome</th>
                                    <th scope="col">CPF</th>
                                    <th scope="col">SUS</th>
                                    <th scope="col">Idade</th>
                                    <th scope="col">Logradouro</th>
                                    <th scope="col">Bairro</th>
                                    <th scope="col">Cadastro</th>
                                    <th scope="col">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($pacientes) && !empty($pacientes)): ?>
                                    <?php foreach ($pacientes as $paciente): ?>
                                        <tr>
                                            <td>
                                                <div class="user-info">
                                                    <div class="user-avatar-small">
                                                        <?= substr($paciente['nome'], 0, 2) ?>
                                                    </div>
                                                    <div>
                                                        <strong><?= esc($paciente['nome']) ?></strong>
                                                        <?php if (!empty($paciente['endereco'])): ?>
                                                            <br><small class="text-muted"><?= esc(substr($paciente['endereco'], 0, 30)) ?>...</small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><?= esc($paciente['cpf']) ?></td>
                                            <td><?= esc($paciente['sus'] ?? '-') ?></td>
                                            <td><?= esc($paciente['idade'] ?? '-') ?> anos</td>
                                            <td><?= esc($paciente['nome_logradouro'] ?? '-') ?></td>
                                            <td><?= esc($paciente['nome_bairro'] ?? '-') ?></td>
                                            <td><?= date('d/m/Y', strtotime($paciente['created_at'])) ?></td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                                        onclick="viewPaciente(<?= $paciente['id_paciente'] ?>)"
                                                        title="Visualizar">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-warning btn-sm"
                                                        onclick="editPaciente(<?= $paciente['id_paciente'] ?>)"
                                                        title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-success btn-sm"
                                                        onclick="newAtendimento(<?= $paciente['id_paciente'] ?>)"
                                                        title="Novo Atendimento">
                                                        <i class="bi bi-plus-circle"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="bi bi-person-x text-muted" style="font-size: 3rem;"></i>
                                                <h5 class="text-muted mt-2">Nenhum paciente encontrado</h5>
                                                <p class="text-muted">Clique em "Novo Paciente" para adicionar o primeiro paciente.</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if (isset($pager)): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <?= $pager->links() ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Novo Paciente -->
<div class="modal fade" id="novoPacienteModal" tabindex="-1" aria-labelledby="novoPacienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="novoPacienteModalLabel">
                    <i class="bi bi-person-plus"></i> Cadastrar Novo Paciente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="novoPacienteForm" action="<?= base_url('pacientes/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <!-- Dados Pessoais -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title">
                            <i class="bi bi-person"></i> Dados Pessoais
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="modal_nome" class="form-label">
                                        Nome Completo <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="modal_nome" name="nome" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_data_nascimento" class="form-label">
                                        Data de Nascimento <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" id="modal_data_nascimento" name="data_nascimento" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_cpf" class="form-label">
                                        CPF <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control" id="modal_cpf" name="cpf" 
                                           placeholder="000.000.000-00" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_rg" class="form-label">RG</label>
                                    <input type="text" class="form-control" id="modal_rg" name="rg" 
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
                                    <input type="text" class="form-control" id="modal_telefone" name="telefone" 
                                           placeholder="(00) 0000-0000">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="modal_celular" class="form-label">Celular</label>
                                    <input type="text" class="form-control" id="modal_celular" name="celular" 
                                           placeholder="(00) 00000-0000">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="modal_email" class="form-label">E-mail</label>
                                    <input type="email" class="form-control" id="modal_email" name="email" 
                                           placeholder="exemplo@email.com">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_numero_sus" class="form-label">Número do SUS</label>
                                    <input type="text" class="form-control" id="modal_numero_sus" name="numero_sus" 
                                           placeholder="000000000000000" maxlength="15">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Endereço -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title">
                            <i class="bi bi-geo-alt"></i> Endereço
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <label for="modal_cep" class="form-label">CEP</label>
                                    <input type="text" class="form-control" id="modal_cep" name="cep" 
                                           placeholder="00000-000">
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="mb-3">
                                    <label for="modal_endereco" class="form-label">Endereço</label>
                                    <input type="text" class="form-control" id="modal_endereco" name="endereco" 
                                           placeholder="Digite o endereço">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="mb-3">
                                    <label for="modal_numero" class="form-label">Número</label>
                                    <input type="text" class="form-control" id="modal_numero" name="numero" 
                                           placeholder="123">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_complemento" class="form-label">Complemento</label>
                                    <input type="text" class="form-control" id="modal_complemento" name="complemento" 
                                           placeholder="Apto, Bloco, etc.">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_id_bairro" class="form-label">Bairro</label>
                                    <select class="form-select" id="modal_id_bairro" name="id_bairro">
                                        <option value="">Selecione o bairro</option>
                                        <?php if (isset($bairros)): ?>
                                            <?php foreach ($bairros as $bairro): ?>
                                                <option value="<?= $bairro['id_bairro'] ?>"><?= esc($bairro['nome_bairro']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_cidade" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="modal_cidade" name="cidade" 
                                           placeholder="Digite a cidade">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informações Médicas -->
                    <div class="form-section mb-4">
                        <h6 class="form-section-title">
                            <i class="bi bi-heart-pulse"></i> Informações Médicas
                        </h6>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="modal_tipo_sanguineo" class="form-label">Tipo Sanguíneo</label>
                                    <select class="form-select" id="modal_tipo_sanguineo" name="tipo_sanguineo">
                                        <option value="">Selecione</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="modal_nome_responsavel" class="form-label">Nome do Responsável</label>
                                    <input type="text" class="form-control" id="modal_nome_responsavel" name="nome_responsavel" 
                                           placeholder="Nome do responsável (para menores de idade)">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="modal_alergias" class="form-label">Alergias</label>
                                    <textarea class="form-control" id="modal_alergias" name="alergias" rows="2"
                                              placeholder="Descreva as alergias conhecidas do paciente"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="modal_observacoes" class="form-label">Observações</label>
                                    <textarea class="form-control" id="modal_observacoes" name="observacoes" rows="2"
                                              placeholder="Observações gerais sobre o paciente"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Salvar Paciente
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Visualizar Paciente -->
<div class="modal fade" id="viewPacienteModal" tabindex="-1" aria-labelledby="viewPacienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewPacienteModalLabel">
                    <i class="bi bi-person-circle"></i> Dados do Paciente
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewPacienteContent">
                <!-- Conteúdo será carregado via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
    // Busca de pacientes
    document.getElementById('searchPaciente').addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const table = document.getElementById('pacientesTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;

            for (let j = 0; j < cells.length - 1; j++) { // Exclui a coluna de ações
                if (cells[j].textContent.toLowerCase().includes(filter)) {
                    found = true;
                    break;
                }
            }

            row.style.display = found ? '' : 'none';
        }
    });

    // Configurar máscaras e validações quando o modal for aberto
    document.getElementById('novoPacienteModal').addEventListener('shown.bs.modal', function() {
        // Máscaras de entrada
        applyMask('modal_cpf', '000.000.000-00');
        applyMask('modal_cep', '00000-000');
        applyMask('modal_telefone', '(00) 0000-0000');
        applyMask('modal_celular', '(00) 00000-0000');

        // Buscar CEP
        document.getElementById('modal_cep').addEventListener('blur', function() {
            const cep = this.value.replace(/\D/g, '');
            
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            document.getElementById('modal_endereco').value = data.logradouro || '';
                            document.getElementById('modal_cidade').value = data.localidade || '';
                            
                            // Buscar bairro no select
                            const bairroNome = data.bairro.toLowerCase();
                            const selectBairro = document.getElementById('modal_id_bairro');
                            for (let option of selectBairro.options) {
                                if (option.text.toLowerCase().indexOf(bairroNome) !== -1) {
                                    option.selected = true;
                                    break;
                                }
                            }
                        }
                    })
                    .catch(error => console.error('Erro ao buscar CEP:', error));
            }
        });

        // Calcular idade e validar responsável
        document.getElementById('modal_data_nascimento').addEventListener('change', function() {
            const nascimento = new Date(this.value);
            const hoje = new Date();
            let idade = hoje.getFullYear() - nascimento.getFullYear();
            const mes = hoje.getMonth() - nascimento.getMonth();
            
            if (mes < 0 || (mes === 0 && hoje.getDate() < nascimento.getDate())) {
                idade--;
            }
            
            const responsavelField = document.getElementById('modal_nome_responsavel');
            const responsavelLabel = document.querySelector('label[for="modal_nome_responsavel"]');
            
            if (idade < 18) {
                responsavelField.required = true;
                responsavelLabel.innerHTML = 'Nome do Responsável <span class="text-danger">*</span>';
            } else {
                responsavelField.required = false;
                responsavelLabel.innerHTML = 'Nome do Responsável';
            }
        });
    });

    // Validação do formulário do modal
    document.getElementById('novoPacienteForm').addEventListener('submit', function(e) {
        const cpf = document.getElementById('modal_cpf').value.replace(/\D/g, '');

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

    // Função para aplicar máscara
    function applyMask(elementId, mask) {
        const element = document.getElementById(elementId);
        if (!element) return;

        element.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            let maskedValue = '';
            let valueIndex = 0;

            for (let i = 0; i < mask.length && valueIndex < value.length; i++) {
                if (mask[i] === '0') {
                    maskedValue += value[valueIndex];
                    valueIndex++;
                } else {
                    maskedValue += mask[i];
                }
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

    // Funções para ações da tabela
    function viewPaciente(id) {
        fetch(`<?= base_url('pacientes/modal') ?>/${id}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('viewPacienteContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('viewPacienteModal')).show();
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao carregar dados do paciente');
            });
    }

    function editPaciente(id) {
        window.location.href = `<?= base_url('pacientes') ?>/${id}/edit`;
    }

    function newAtendimento(id) {
        window.location.href = `<?= base_url('atendimentos/create') ?>?paciente=${id}`;
    }

    function showAll() {
        window.location.href = '<?= base_url('pacientes') ?>';
    }

    function showRecent() {
        window.location.href = '<?= base_url('pacientes') ?>?filter=recent';
    }
</script>

<!-- Estilos para o modal -->
<style>
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

.modal-xl .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}
</style>

<?= $this->endSection() ?>