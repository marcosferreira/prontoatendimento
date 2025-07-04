<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-clipboard-check"></i> Atendimentos</h1>
                <p class="subtitle">Gerenciamento de Atendimentos Médicos</p>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-left m-4">
                    <div class="search-container position-relative">
                        <input type="text" id="searchAtendimento" class="form-control search-input pe-5"
                        placeholder="Buscar por paciente, médico ou data...">
                        <i class="bi bi-search search-icon position-absolute top-50 end-0 translate-middle-y me-3"></i>
                    </div>
                </div>
                <div class="action-right m-4">
                    <a href="<?= base_url('atendimentos/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Novo Atendimento
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row m-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= isset($stats['total']) ? $stats['total'] : '0' ?></div>
                        <div class="stat-label">Total de Atendimentos</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= isset($stats['hoje']) ? $stats['hoje'] : '0' ?></div>
                        <div class="stat-label">Atendimentos Hoje</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= isset($stats['urgentes']) ? $stats['urgentes'] : '0' ?></div>
                        <div class="stat-label">Casos Urgentes</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= isset($stats['mes']) ? $stats['mes'] : '0' ?></div>
                        <div class="stat-label">Este Mês</div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="filters m-4">
                <div class="row">
                    <div class="col-md-3">
                        <select id="filtroClassificacao" class="form-select">
                            <option value="">Todas as Classificações</option>
                            <option value="Verde" class="text-success">Verde</option>
                            <option value="Amarelo" class="text-warning">Amarelo</option>
                            <option value="Vermelho" class="text-danger">Vermelho</option>
                            <option value="Azul" class="text-info">Azul</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select id="filtroMedico" class="form-select">
                            <option value="">Todos os Médicos</option>
                            <?php if (isset($medicos)): ?>
                                <?php foreach ($medicos as $medico): ?>
                                    <option value="<?= $medico->id_medico ?>"><?= $medico->nome ?> - <?= $medico->crm ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="filtroDataInicio" class="form-control" placeholder="Data Início">
                    </div>
                    <div class="col-md-3">
                        <input type="date" id="filtroDataFim" class="form-control" placeholder="Data Fim">
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="table-container m-4">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="atendimentosTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Paciente</th>
                                <th>Médico</th>
                                <th>Data/Hora</th>
                                <th>Classificação</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (isset($atendimentos) && !empty($atendimentos)): ?>
                                <?php foreach ($atendimentos as $atendimento): ?>
                                    <tr>
                                        <td><?= $atendimento->id_atendimento ?></td>
                                        <td>
                                            <strong><?= esc($atendimento->paciente_nome) ?></strong><br>
                                            <small class="text-muted">CPF: <?= $atendimento->cpf ?></small>
                                        </td>
                                        <td>
                                            <strong><?= esc($atendimento->medico_nome) ?></strong><br>
                                            <small class="text-muted">CRM: <?= $atendimento->crm ?></small>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y H:i', strtotime($atendimento->data_atendimento)) ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= 
                                                $atendimento->classificacao_risco == 'Verde' ? 'success' :
                                                ($atendimento->classificacao_risco == 'Amarelo' ? 'warning' :
                                                ($atendimento->classificacao_risco == 'Vermelho' ? 'danger' : 'info'))
                                            ?>">
                                                <?= $atendimento->classificacao_risco ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($atendimento->obito): ?>
                                                <span class="badge bg-dark">Óbito</span>
                                            <?php elseif ($atendimento->encaminhamento): ?>
                                                <span class="badge bg-secondary"><?= $atendimento->encaminhamento ?></span>
                                            <?php else: ?>
                                                <span class="badge bg-primary">Em Atendimento</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="<?= base_url('atendimentos/show/' . $atendimento->id_atendimento) ?>" 
                                                   class="btn btn-outline-primary btn-sm" title="Visualizar">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="<?= base_url('atendimentos/edit/' . $atendimento->id_atendimento) ?>" 
                                                   class="btn btn-outline-secondary btn-sm" title="Editar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm" 
                                                        onclick="confirmarExclusao(<?= $atendimento->id_atendimento ?>)"
                                                        title="Excluir">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="empty-state">
                                            <i class="bi bi-clipboard-x display-1 text-muted"></i>
                                            <h5 class="mt-3">Nenhum atendimento encontrado</h5>
                                            <p class="text-muted">Comece criando um novo atendimento</p>
                                            <a href="<?= base_url('atendimentos/create') ?>" class="btn btn-primary">
                                                <i class="bi bi-plus-circle"></i> Novo Atendimento
                                            </a>
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
    </main>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalConfirmarExclusao" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este atendimento?</p>
                <p class="text-danger"><strong>Esta ação não pode ser desfeita!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarExclusaoBtn">Excluir</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    let atendimentoParaExcluir = null;

    // Search functionality
    $('#searchAtendimento').on('keyup', function() {
        const searchTerm = $(this).val().toLowerCase();
        $('#atendimentosTable tbody tr').each(function() {
            const row = $(this);
            const text = row.text().toLowerCase();
            if (text.includes(searchTerm)) {
                row.show();
            } else {
                row.hide();
            }
        });
    });

    // Filter functionality
    $('#filtroClassificacao, #filtroMedico, #filtroDataInicio, #filtroDataFim').on('change', function() {
        aplicarFiltros();
    });

    function aplicarFiltros() {
        const classificacao = $('#filtroClassificacao').val();
        const medico = $('#filtroMedico').val();
        const dataInicio = $('#filtroDataInicio').val();
        const dataFim = $('#filtroDataFim').val();

        $('#atendimentosTable tbody tr').each(function() {
            const row = $(this);
            let mostrar = true;

            // Filtro por classificação
            if (classificacao && !row.find('.badge').text().trim().includes(classificacao)) {
                mostrar = false;
            }

            // Filtro por médico
            if (medico && !row.find('td:nth-child(3)').text().includes(medico)) {
                mostrar = false;
            }

            // Filtros de data
            const dataAtendimento = row.find('td:nth-child(4)').text().trim();
            if (dataInicio || dataFim) {
                const partesData = dataAtendimento.split(' ')[0].split('/');
                const dataAtend = new Date(partesData[2], partesData[1] - 1, partesData[0]);
                
                if (dataInicio) {
                    const inicio = new Date(dataInicio);
                    if (dataAtend < inicio) mostrar = false;
                }
                
                if (dataFim) {
                    const fim = new Date(dataFim);
                    if (dataAtend > fim) mostrar = false;
                }
            }

            if (mostrar) {
                row.show();
            } else {
                row.hide();
            }
        });
    }

    // Confirm deletion
    window.confirmarExclusao = function(id) {
        atendimentoParaExcluir = id;
        $('#modalConfirmarExclusao').modal('show');
    };

    $('#confirmarExclusaoBtn').on('click', function() {
        if (atendimentoParaExcluir) {
            $.ajax({
                url: `<?= base_url('atendimentos/delete/') ?>${atendimentoParaExcluir}`,
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#modalConfirmarExclusao').modal('hide');
                    if (response.success) {
                        // Remove row from table
                        $(`#atendimentosTable tbody tr`).each(function() {
                            if ($(this).find('td:first').text() == atendimentoParaExcluir) {
                                $(this).remove();
                            }
                        });
                        
                        showAlert('success', 'Atendimento excluído com sucesso!');
                    } else {
                        showAlert('error', response.message || 'Erro ao excluir atendimento');
                    }
                },
                error: function() {
                    $('#modalConfirmarExclusao').modal('hide');
                    showAlert('error', 'Erro ao excluir atendimento');
                }
            });
        }
    });

    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        $('.main-container').prepend(alertHTML);
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
<?= $this->endSection() ?>
