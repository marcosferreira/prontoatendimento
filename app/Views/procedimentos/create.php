<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-plus-circle"></i> <?= $title ?></h1>
                <p class="subtitle"><?= $description ?></p>
            </div>

            <div class="content-wrapper">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="my-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url('') ?>">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('procedimentos') ?>">Procedimentos</a></li>
                        <li class="breadcrumb-item active">Novo</li>
                    </ol>
                </nav>

                <!-- Form -->
                <div class="card my-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Dados do Procedimento</h5>
                    </div>
                    <div class="card-body">
                        <?= form_open('procedimentos/store', ['id' => 'formProcedimento', 'class' => 'needs-validation', 'novalidate' => '']) ?>

                        <div class="row">
                            <!-- Nome -->
                            <div class="col-md-8 mb-3">
                                <label for="nome" class="form-label">
                                    <i class="bi bi-clipboard-check"></i> Nome do Procedimento *
                                </label>
                                <input type="text" class="form-control" id="nome" name="nome"
                                    value="<?= old('nome', isset($procedimento) ? $procedimento['nome'] . ' (Cópia)' : '') ?>" required maxlength="255"
                                    placeholder="Ex: Curativo simples">
                                <div class="invalid-feedback">
                                    Por favor, informe o nome do procedimento.
                                </div>
                                <?php if (isset(session('validation')['nome'])): ?>
                                    <div class="text-danger small mt-1">
                                        <?= session('validation')['nome'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Código -->
                            <div class="col-md-4 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <label for="codigo" class="form-label mb-0 me-2">
                                        <i class="bi bi-hash"></i> Código
                                    </label>
                                    <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#tussModal" title="Ver códigos TUSS">
                                        <i class="bi bi-question-circle"></i> Ajuda TUSS
                                    </button>
                                </div>
                                <input type="text" class="form-control" id="codigo" name="codigo"
                                    value="<?= old('codigo', isset($procedimento) ? $procedimento['codigo'] . '_COPY' : '') ?>" maxlength="50"
                                    placeholder="Ex: PROC001 ou 0301010026">
                                <div class="form-text">Código interno ou TUSS (opcional)</div>
                                <?php if (isset(session('validation')['codigo'])): ?>
                                    <div class="text-danger small mt-1">
                                        <?= session('validation')['codigo'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Descrição -->
                        <div class="mb-3">
                            <label for="descricao" class="form-label">
                                <i class="bi bi-text-paragraph"></i> Descrição
                            </label>
                            <textarea class="form-control" id="descricao" name="descricao"
                                rows="4" placeholder="Descrição detalhada do procedimento..."><?= old('descricao', isset($procedimento) ? $procedimento['descricao'] : '') ?></textarea>
                            <div class="form-text">Descreva o procedimento, materiais necessários, tempo estimado, etc.</div>
                            <?php if (isset(session('validation')['descricao'])): ?>
                                <div class="text-danger small mt-1">
                                    <?= session('validation')['descricao'] ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('procedimentos') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Voltar
                            </a>
                            <div>
                                <button type="button" class="btn btn-outline-secondary me-2" onclick="resetForm()">
                                    <i class="bi bi-arrow-clockwise"></i> Limpar
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Salvar Procedimento
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

<!-- Modal TUSS -->
<div class="modal fade" id="tussModal" tabindex="-1" aria-labelledby="tussModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tussModalLabel">
                    <i class="bi bi-book"></i> Códigos TUSS - Procedimentos Médicos
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <input type="text" id="searchTuss" class="form-control" placeholder="Buscar por código ou descrição...">
                    </div>
                    <div class="col-md-6">
                        <select id="filterCategory" class="form-select">
                            <option value="">Todas as categorias</option>
                            <option value="03.01">Consultas/Atendimentos</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                    <table class="table table-striped table-hover" id="tussTable">
                        <thead class="table-dark sticky-top">
                            <tr>
                                <th style="width: 20%;">Código TUSS</th>
                                <th style="width: 60%;">Descrição</th>
                                <th style="width: 20%;">Ação</th>
                            </tr>
                        </thead>
                        <tbody id="tussTableBody">
                            <!-- Procedimentos de Consulta/Atendimento -->
                            <tr data-category="03.01">
                                <td><code>03.01.06.00.61</code></td>
                                <td>Atendimento de urgência em atenção espericalizada</td>
                                <td><button class="btn btn-sm btn-primary" onclick="selectTussCode('03.01.06.00.61', 'Atendimento de urgência em atenção espericalizada')">Usar</button></td>
                            </tr>
                            <tr data-category="03.01">
                                <td><code>03.01.01.01.18</code></td>
                                <td>Acolhimento com classificação de risco</td>
                                <td><button class="btn btn-sm btn-primary" onclick="selectTussCode('03.01.01.01.18', 'Acolhimento com classificação de risco')">Usar</button></td>
                            </tr>
                            <tr data-category="03.01">
                                <td><code>03.01.01.00.48</code></td>
                                <td>Consulta de Profissionais dfe Nível Superior na atenção especializada (exceto médico)</td>
                                <td><button class="btn btn-sm btn-primary" onclick="selectTussCode('03.01.01.00.48', 'Consulta de Profissionais dfe Nível Superior na atenção especializada (exceto médico)')">Usar</button></td>
                            </tr>
                            <tr data-category="03.01">
                                <td><code>03.01.01.00.72</code></td>
                                <td>Consulta médica em atenção especializada</td>
                                <td><button class="btn btn-sm btn-primary" onclick="selectTussCode('03.01.01.00.72', 'Consulta médica em atenção especializada')">Usar</button></td>
                            </tr>

                            <tr data-category="03.01">
                                <td><code>03.01.10.01.52</code></td>
                                <td>Retirada de pontos de cirurgias (por pacientes)</td>
                                <td><button class="btn btn-sm btn-primary" onclick="selectTussCode('03.01.10.01.52', 'Retirada de pontos de cirurgias (por pacientes)')">Usar</button></td>
                            </tr>
                            <tr data-category="03.01">
                                <td><code>03.01.10.00.39</code></td>
                                <td>Aferição de pressão arterial</td>
                                <td><button class="btn btn-sm btn-primary" onclick="selectTussCode('03.01.10.00.39', 'Aferição de pressão arterial')">Usar</button></td>
                            </tr>
                            <tr data-category="03.01">
                                <td><code>03.01.10.00.12</code></td>
                                <td>Medicamentos na atenção especializada</td>
                                <td><button class="btn btn-sm btn-primary" onclick="selectTussCode('03.01.10.00.12', 'Medicamentos na atenção especializada')">Usar</button></td>
                            </tr>
                            <tr data-category="03.01">
                                <td><code>04.01.01.01.12</code></td>
                                <td>Retirada de corpo estranho subcutaneo</td>
                                <td><button class="btn btn-sm btn-primary" onclick="selectTussCode('04.01.01.01.12', 'Retirada de corpo estranho subcutaneo')">Usar</button></td>
                            </tr>
                            <tr data-category="03.01">
                                <td><code>02.14.01.00.15</code></td>
                                <td>Glicemia capilar</td>
                                <td><button class="btn btn-sm btn-primary" onclick="selectTussCode('02.14.01.00.15', 'Glicemia capilar')">Usar</button></td>
                            </tr>
                            <tr data-category="03.01">
                                <td><code>03.01.10.00.12</code></td>
                                <td>Ingeção</td>
                                <td><button class="btn btn-sm btn-primary" onclick="selectTussCode('03.01.10.00.12', 'Ingeção')">Usar</button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle"></i>
                    <strong>Dica:</strong> Use os códigos TUSS para padronizar os procedimentos conforme a tabela oficial da ANS.
                    Isso facilita o faturamento e a comunicação com outros sistemas de saúde.
                    <br><small class="text-muted">Clique no botão "Usar" para preencher automaticamente o formulário.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <a href="https://www.ans.gov.br/prestadores/tiss-troca-de-informacao-de-saude-suplementar" target="_blank" class="btn btn-outline-primary">
                    <i class="bi bi-link-45deg"></i> Ver TUSS Completo
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Global variables
    window.formChanged = false;

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

        // Auto-generate code based on name
        $('#nome').on('input', function() {
            if ($('#codigo').val() === '') {
                let nome = $(this).val();
                let codigo = nome.toUpperCase()
                    .replace(/[^A-Z0-9\s]/g, '')
                    .replace(/\s+/g, '_')
                    .substring(0, 10);
                $('#codigo').val(codigo);
            }
        });

        // Confirm before leaving if form has changes
        $('#formProcedimento input, #formProcedimento textarea').on('change input', function() {
            window.formChanged = true;
        });

        $(window).on('beforeunload', function() {
            if (window.formChanged) {
                return 'Você tem alterações não salvas. Tem certeza que deseja sair?';
            }
        });

        $('#formProcedimento').on('submit', function() {
            window.formChanged = false;
        });

        // Initialize TUSS Modal when modal is shown
        $('#tussModal').on('shown.bs.modal', function () {
            console.log('Modal TUSS aberta!');
            initTussModal();
        });
        
        // Debug para verificar se função está disponível
        console.log('Função selectTussCode disponível:', typeof selectTussCode);
    });

    // Reset form function
    function resetForm() {
        if (confirm('Tem certeza que deseja limpar todos os campos?')) {
            document.getElementById('formProcedimento').reset();
            // Remove validation classes
            $('#formProcedimento').removeClass('was-validated');
            window.formChanged = false;
        }
    }

    // TUSS Modal Functions
    function initTussModal() {
        console.log('Inicializando modal TUSS...'); // Debug
        
        // Search functionality
        $('#searchTuss').off('input').on('input', function() {
            console.log('Buscando:', $(this).val()); // Debug
            filterTussTable();
        });

        // Category filter
        $('#filterCategory').off('change').on('change', function() {
            console.log('Filtrando categoria:', $(this).val()); // Debug
            filterTussTable();
        });
        
        // Adicionar eventos de click aos botões "Usar" como alternativa
        $('#tussTableBody').off('click', '.btn-primary').on('click', '.btn-primary', function(e) {
            e.preventDefault();
            console.log('Botão "Usar" clicado via event listener');
            
            const row = $(this).closest('tr');
            const code = row.find('td:first code').text();
            const description = row.find('td:eq(1)').text();
            
            console.log('Dados capturados - Código:', code, 'Descrição:', description);
            selectTussCode(code, description);
        });
    }

    function filterTussTable() {
        const searchTerm = $('#searchTuss').val().toLowerCase();
        const selectedCategory = $('#filterCategory').val();
        
        console.log('Filtros aplicados:', searchTerm, selectedCategory); // Debug

        $('#tussTableBody tr').each(function() {
            const row = $(this);
            const code = row.find('td:first code').text().toLowerCase();
            const description = row.find('td:eq(1)').text().toLowerCase();
            const category = row.attr('data-category') || '';

            let showRow = true;

            // Filter by search term
            if (searchTerm && !code.includes(searchTerm) && !description.includes(searchTerm)) {
                showRow = false;
            }

            // Filter by category
            if (selectedCategory && !category.startsWith(selectedCategory)) {
                showRow = false;
            }

            row.toggle(showRow);
        });
    }

    // Global function to select TUSS code
    function selectTussCode(code, description) {
        console.log('=== FUNÇÃO CHAMADA ===');
        console.log('Código:', code);
        console.log('Descrição:', description);
        
        try {
            // Verifica se os elementos existem
            const codigoField = document.getElementById('codigo');
            const nomeField = document.getElementById('nome');
            
            console.log('Campo código encontrado:', codigoField !== null);
            console.log('Campo nome encontrado:', nomeField !== null);
            
            if (!codigoField || !nomeField) {
                console.error('Campos não encontrados!');
                return;
            }
            
            // Set the code in the form
            codigoField.value = code;
            console.log('Código definido para:', codigoField.value);

            // Always update the name field with the TUSS description
            nomeField.value = description;
            console.log('Nome atualizado para:', nomeField.value);
            
            // Trigger input events to ensure validation and other listeners work
            const inputEvent = new Event('input', { bubbles: true });
            codigoField.dispatchEvent(inputEvent);
            nomeField.dispatchEvent(inputEvent);

            // Close the modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('tussModal'));
            if (modal) {
                modal.hide();
                console.log('Modal fechada via bootstrap');
            } else {
                $('#tussModal').modal('hide');
                console.log('Modal fechada via jQuery');
            }

            // Show success message
            showToast('Código TUSS selecionado!', 'Código ' + code + ' e nome "' + description + '" adicionados ao formulário.', 'success');

            // Mark form as changed
            window.formChanged = true;
            console.log('Form marcado como alterado');
            
        } catch (error) {
            console.error('Erro na função selectTussCode:', error);
        }
    }

    // Toast notification function
    function showToast(title, message, type = 'info') {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'primary'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        <strong>${title}</strong><br>${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        `;

        // Create toast container if it doesn't exist
        if ($('#toastContainer').length === 0) {
            $('body').append('<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>');
        }

        const $toast = $(toastHtml);
        $('#toastContainer').append($toast);

        const toast = new bootstrap.Toast($toast[0]);
        toast.show();

        // Remove toast element after it's hidden
        $toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
</script>

<style>
    /* TUSS Modal Styles */
    #tussModal .modal-dialog {
        max-width: 90%;
    }

    #tussTable {
        font-size: 0.9rem;
    }

    #tussTable code {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 0.25rem 0.5rem;
        font-family: 'Courier New', monospace;
        color: #0d6efd;
        font-weight: bold;
    }

    #tussTable tbody tr:hover {
        background-color: #f8f9fa;
    }

    .sticky-top {
        position: sticky;
        top: 0;
        z-index: 1020;
    }

    .toast-container {
        z-index: 1055;
    }

    /* Help button styles */
    .btn-outline-info {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
        vertical-align: top;
    }

    /* Category badges in modal */
    .category-badge {
        font-size: 0.7rem;
        padding: 0.2rem 0.4rem;
    }

    /* Search and filter styles */
    #searchTuss:focus,
    #filterCategory:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
</style>

<?= $this->endSection() ?>