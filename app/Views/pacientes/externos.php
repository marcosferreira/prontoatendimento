<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-geo-alt"></i> Pacientes de Outras Cidades</h1>
                <p class="subtitle">Pacientes cadastrados em cidades externas</p>
            </div>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-left m-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="search-container position-relative">
                                <input type="text" id="searchPaciente" class="form-control search-input pe-5"
                                    placeholder="Buscar por nome, CPF ou cidade..."
                                    value="<?= esc($search ?? '') ?>">
                                <i class="bi bi-search search-icon position-absolute top-50 end-0 translate-middle-y me-3"></i>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="filtroCidade" onchange="filtrarPorCidade()">
                                <option value="">Todas as Cidades</option>
                                <?php foreach ($cidades as $cidade): ?>
                                    <option value="<?= esc($cidade) ?>" <?= ($cidade_filtro === $cidade) ? 'selected' : '' ?>>
                                        <?= esc($cidade) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="button" class="btn btn-outline-secondary d-flex align-items-center" onclick="limparFiltros()">
                                <i class="bi bi-arrow-clockwise"></i> Limpar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="action-right m-4">
                    <a href="<?= base_url('pacientes') ?>" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row m-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= $total_externos ?></div>
                        <div class="stat-label">Total Pacientes Externos</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= count($cidades) ?></div>
                        <div class="stat-label">Cidades Diferentes</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number"><?= count($pacientes) ?></div>
                        <div class="stat-label">Resultados Filtrados</div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-item">
                        <div class="stat-number">
                            <?= !empty($estatisticas) ? $estatisticas[0]['total_pacientes'] : '0' ?>
                        </div>
                        <div class="stat-label">
                            Maior Cidade: <?= !empty($estatisticas) ? esc($estatisticas[0]['cidade_externa']) : 'N/A' ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estatísticas por Cidade -->
            <?php if (!empty($estatisticas)): ?>
            <div class="row m-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-bar-chart"></i> Estatísticas por Cidade
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($estatisticas as $index => $stat): ?>
                                    <?php if ($index < 6): // Mostrar apenas as 6 primeiras ?>
                                    <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
                                        <div class="text-center">
                                            <h4 class="text-primary"><?= $stat['total_pacientes'] ?></h4>
                                            <small class="text-muted"><?= esc($stat['cidade_externa']) ?></small>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Tabela de Pacientes -->
            <div class="content-section m-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="bi bi-table"></i> Lista de Pacientes Externos
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($pacientes)): ?>
                            <div class="text-center py-5">
                                <i class="bi bi-info-circle text-muted" style="font-size: 3rem;"></i>
                                <h5 class="text-muted mt-3">Nenhum paciente externo encontrado</h5>
                                <p class="text-muted">
                                    <?php if ($cidade_filtro): ?>
                                        Não há pacientes cadastrados na cidade "<?= esc($cidade_filtro) ?>".
                                    <?php else: ?>
                                        Ainda não há pacientes de outras cidades cadastrados no sistema.
                                    <?php endif; ?>
                                </p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover" id="tabelaPacientesExternos">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Nome</th>
                                            <th>CPF</th>
                                            <th>Data Nascimento</th>
                                            <th>Telefone</th>
                                            <th>Cidade</th>
                                            <th>Endereço Externo</th>
                                            <th>CEP</th>
                                            <th>Cadastrado em</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pacientes as $paciente): ?>
                                            <tr>
                                                <td><?= $paciente['id_paciente'] ?></td>
                                                <td>
                                                    <strong><?= esc($paciente['nome']) ?></strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= $paciente['sexo'] === 'M' ? 'Masculino' : 'Feminino' ?>
                                                    </small>
                                                </td>
                                                <td><?= esc($paciente['cpf']) ?></td>
                                                <td>
                                                    <?= date('d/m/Y', strtotime($paciente['data_nascimento'])) ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?php
                                                        $idade = date_diff(
                                                            date_create($paciente['data_nascimento']),
                                                            date_create('today')
                                                        )->y;
                                                        echo $idade . ' anos';
                                                        ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <?php if (!empty($paciente['telefone'])): ?>
                                                        <?= esc($paciente['telefone']) ?><br>
                                                    <?php endif; ?>
                                                    <?php if (!empty($paciente['celular'])): ?>
                                                        <small class="text-muted"><?= esc($paciente['celular']) ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">
                                                        <?= esc($paciente['cidade_externa']) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if (!empty($paciente['logradouro_externo'])): ?>
                                                        <?= esc($paciente['logradouro_externo']) ?>
                                                        <?php if (!empty($paciente['numero'])): ?>
                                                            , <?= esc($paciente['numero']) ?>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <span class="text-muted">Não informado</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= esc($paciente['cep_externo'] ?? 'N/A') ?></td>
                                                <td>
                                                    <?= date('d/m/Y', strtotime($paciente['created_at'])) ?>
                                                    <br>
                                                    <small class="text-muted">
                                                        <?= date('H:i', strtotime($paciente['created_at'])) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-primary btn-sm"
                                                            onclick="visualizarPaciente(<?= $paciente['id_paciente'] ?>)"
                                                            title="Visualizar">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-secondary btn-sm"
                                                            onclick="editarPaciente(<?= $paciente['id_paciente'] ?>)"
                                                            title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <a href="<?= base_url('atendimentos/create?paciente=' . $paciente['id_paciente']) ?>"
                                                           class="btn btn-outline-success btn-sm"
                                                           title="Novo Atendimento">
                                                            <i class="bi bi-clipboard-plus"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </main>
</div>

<!-- Modal para Visualizar Paciente -->
<div class="modal fade" id="visualizarPacienteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Paciente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="conteudoPaciente">
                <!-- Conteúdo carregado via AJAX -->
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTable se houver dados
    <?php if (!empty($pacientes)): ?>
    if (typeof DataTable !== 'undefined') {
        new DataTable('#tabelaPacientesExternos', {
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
            },
            order: [[8, 'desc']], // Ordenar por data de cadastro (mais recente primeiro)
            pageLength: 25,
            responsive: true,
            columnDefs: [
                { orderable: false, targets: [9] } // Coluna de ações não ordenável
            ]
        });
    }
    <?php endif; ?>

    // Busca em tempo real
    const searchInput = document.getElementById('searchPaciente');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            const search = this.value;
            
            // Limpar timeout anterior
            if (searchTimeout) {
                clearTimeout(searchTimeout);
            }
            
            // Criar novo timeout
            searchTimeout = setTimeout(() => {
                if (search === searchInput.value) {
                    let url = '<?= base_url('pacientes/externos') ?>?search=' + encodeURIComponent(search);
                    const cidadeFiltro = '<?= $cidade_filtro ? urlencode($cidade_filtro) : '' ?>';
                    if (cidadeFiltro) {
                        url += '&cidade=' + cidadeFiltro;
                    }
                    window.location.href = url;
                }
            }, 500);
        });
    }
});

// Filtrar por cidade
function filtrarPorCidade() {
    const cidadeSelect = document.getElementById('filtroCidade');
    const searchInput = document.getElementById('searchPaciente');
    
    if (!cidadeSelect || !searchInput) return;
    
    const cidade = cidadeSelect.value;
    const search = searchInput.value;
    
    let url = '<?= base_url('pacientes/externos') ?>';
    let params = [];
    
    if (cidade) params.push('cidade=' + encodeURIComponent(cidade));
    if (search) params.push('search=' + encodeURIComponent(search));
    
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    window.location.href = url;
}

// Limpar filtros
function limparFiltros() {
    window.location.href = '<?= base_url('pacientes/externos') ?>';
}

// Visualizar paciente
function visualizarPaciente(id) {
    window.location.href = '<?= base_url('pacientes/show') ?>/' + id;
    // const conteudoElement = document.getElementById('conteudoPaciente');
    // const modalElement = document.getElementById('visualizarPacienteModal');
    
    // if (!conteudoElement || !modalElement) return;
    
    // // Mostrar loading
    // conteudoElement.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Carregando...</span></div></div>';
    
    // // Mostrar modal usando Bootstrap
    // const modal = new bootstrap.Modal(modalElement);
    // modal.show();
    
    // Fazer requisição AJAX
    // fetch('<?= base_url('pacientes/show') ?>/' + id)
    //     .then(response => {
    //         if (!response.ok) {
    //             throw new Error('Erro na requisição: ' + response.status);
    //         }
    //         return response.text();
    //     })
    //     .then(data => {
    //         conteudoElement.innerHTML = data;
    //     })
    //     .catch(error => {
    //         console.error('Erro ao carregar paciente:', error);
    //         conteudoElement.innerHTML = '<div class="alert alert-danger">Erro ao carregar dados do paciente. Tente novamente.</div>';
    //     });
}

// Editar paciente
function editarPaciente(id) {
    window.location.href = '<?= base_url('pacientes/edit') ?>/' + id;
}
</script>
<?= $this->endSection() ?>
