<?= $this->extend('layout/base') ?>

<?= $this->section('content') ?>
<!-- CSS específico para bairros -->
<link rel="stylesheet" href="<?= base_url('assets/css/bairros.css') ?>">

<div class="app-container">
    <?= $this->include('components/sidebar') ?>

    <?= $this->include('components/topbar') ?>

    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-geo-alt"></i> Detalhes do Bairro</h1>
                <p class="subtitle"><?= esc($bairro['nome_bairro']) ?></p>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= base_url() ?>">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="<?= base_url('bairros') ?>">Bairros</a>
                    </li>
                    <li class="breadcrumb-item active"><?= esc($bairro['nome_bairro']) ?></li>
                </ol>
            </nav>

            <!-- Action Bar -->
            <div class="action-bar">
                <div class="action-left m-4">
                    <a href="<?= base_url('bairros') ?>" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
                <div class="action-right m-4">
                    <a href="<?= base_url('bairros/' . $bairro['id_bairro'] . '/edit') ?>" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBairroModal">
                        <i class="bi bi-trash"></i> Excluir
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-4">
                    <!-- Informações do Bairro -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-info-circle"></i> Informações do Bairro
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <strong>ID:</strong>
                                <span class="badge bg-secondary"><?= $bairro['id_bairro'] ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Nome:</strong>
                                <span><?= esc($bairro['nome_bairro']) ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Área/Região:</strong>
                                <span><?= $bairro['area'] ? esc($bairro['area']) : '<em class="text-muted">Não informado</em>' ?></span>
                            </div>
                            <div class="info-item">
                                <strong>Total de Pacientes:</strong>
                                <span class="badge bg-info"><?= count($pacientes) ?> pacientes</span>
                            </div>
                            <div class="info-item">
                                <strong>Total de Logradouros:</strong>
                                <span class="badge bg-success"><?= count($logradouros) ?> logradouros</span>
                            </div>
                            <div class="info-item">
                                <strong>Cadastrado em:</strong>
                                <span><?= date('d/m/Y \à\s H:i', strtotime($bairro['created_at'])) ?></span>
                            </div>
                            <?php if (isset($bairro['updated_at']) && $bairro['updated_at'] != $bairro['created_at']): ?>
                            <div class="info-item">
                                <strong>Última atualização:</strong>
                                <span><?= date('d/m/Y \à\s H:i', strtotime($bairro['updated_at'])) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Estatísticas -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-bar-chart"></i> Estatísticas
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="stat-item">
                                <div class="stat-icon bg-info">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="stat-info">
                                    <h4><?= count($pacientes) ?></h4>
                                    <p>Pacientes Cadastrados</p>
                                </div>
                            </div>

                            <div class="stat-item">
                                <div class="stat-icon bg-success">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div class="stat-info">
                                    <h4><?= count($logradouros) ?></h4>
                                    <p>Logradouros</p>
                                </div>
                            </div>
                            
                            <?php if (!empty($pacientes)): ?>
                                <?php 
                                $idades = array_column($pacientes, 'idade');
                                $idadeMedia = $idades ? round(array_sum($idades) / count($idades), 1) : 0;
                                ?>
                                <div class="stat-item">
                                    <div class="stat-icon bg-warning">
                                        <i class="bi bi-calendar"></i>
                                    </div>
                                    <div class="stat-info">
                                        <h4><?= $idadeMedia ?> anos</h4>
                                        <p>Idade Média</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <!-- Logradouros do Bairro -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-geo-alt"></i> Logradouros do Bairro
                            </h5>
                            <?php if (!empty($logradouros)): ?>
                                <a href="<?= base_url('logradouros?bairro=' . $bairro['id_bairro']) ?>" class="btn btn-sm btn-outline-success">
                                    Ver Todos
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($logradouros)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Nome do Logradouro</th>
                                                <th>CEP</th>
                                                <th>Cadastrado em</th>
                                                <th width="100">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($logradouros, 0, 10) as $logradouro): ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-primary"><?= esc($logradouro['tipo_logradouro']) ?></span>
                                                    </td>
                                                    <td>
                                                        <strong><?= esc($logradouro['nome_logradouro']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <?php if (!empty($logradouro['cep'])): ?>
                                                            <code><?= esc($logradouro['cep']) ?></code>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?= date('d/m/Y', strtotime($logradouro['created_at'])) ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('logradouros/' . $logradouro['id_logradouro']) ?>" 
                                                           class="btn btn-sm btn-outline-info"
                                                           title="Ver detalhes">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <?php if (count($logradouros) > 10): ?>
                                        <div class="text-center mt-3">
                                            <p class="text-muted">
                                                Mostrando 10 de <?= count($logradouros) ?> logradouros.
                                                <a href="<?= base_url('logradouros?bairro=' . $bairro['id_bairro']) ?>">Ver todos</a>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="bi bi-geo-alt text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted mt-2">Nenhum logradouro cadastrado</h5>
                                    <p class="text-muted">Este bairro ainda não possui logradouros cadastrados.</p>
                                    <a href="<?= base_url('logradouros/create?bairro=' . $bairro['id_bairro']) ?>" class="btn btn-success">
                                        <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Logradouro
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Pacientes do Bairro -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-people"></i> Pacientes do Bairro
                            </h5>
                            <?php if (!empty($pacientes)): ?>
                                <a href="<?= base_url('pacientes?bairro=' . $bairro['id_bairro']) ?>" class="btn btn-sm btn-outline-primary">
                                    Ver Todos
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($pacientes)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>CPF</th>
                                                <th>Idade</th>
                                                <th>Cadastrado em</th>
                                                <th width="100">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach (array_slice($pacientes, 0, 10) as $paciente): ?>
                                                <tr>
                                                    <td>
                                                        <strong><?= esc($paciente['nome']) ?></strong>
                                                    </td>
                                                    <td>
                                                        <code><?= esc($paciente['cpf']) ?></code>
                                                    </td>
                                                    <td>
                                                        <span class="badge bg-secondary"><?= $paciente['idade'] ?> anos</span>
                                                    </td>
                                                    <td>
                                                        <small class="text-muted">
                                                            <?= date('d/m/Y', strtotime($paciente['created_at'])) ?>
                                                        </small>
                                                    </td>
                                                    <td>
                                                        <a href="<?= base_url('pacientes/' . $paciente['id_paciente']) ?>" 
                                                           class="btn btn-sm btn-outline-info"
                                                           title="Ver detalhes">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <?php if (count($pacientes) > 10): ?>
                                        <div class="text-center mt-3">
                                            <p class="text-muted">
                                                Mostrando 10 de <?= count($pacientes) ?> pacientes.
                                                <a href="<?= base_url('pacientes?bairro=' . $bairro['id_bairro']) ?>">Ver todos</a>
                                            </p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="text-muted mt-2">Nenhum paciente cadastrado</h5>
                                    <p class="text-muted">Este bairro ainda não possui pacientes cadastrados.</p>
                                    <a href="<?= base_url('pacientes/create?bairro=' . $bairro['id_bairro']) ?>" class="btn btn-primary">
                                        <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Paciente
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Confirmar Exclusão -->
<div class="modal fade" id="deleteBairroModal" tabindex="-1" aria-labelledby="deleteBairroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteBairroModalLabel">
                    <i class="bi bi-exclamation-triangle text-danger"></i> Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o bairro <strong><?= esc($bairro['nome_bairro']) ?></strong>?</p>
                
                <?php if (!empty($pacientes) || !empty($logradouros)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Atenção:</strong> Este bairro possui:
                        <ul class="mb-0 mt-1">
                            <?php if (!empty($pacientes)): ?>
                                <li><?= count($pacientes) ?> paciente(s) cadastrado(s)</li>
                            <?php endif; ?>
                            <?php if (!empty($logradouros)): ?>
                                <li><?= count($logradouros) ?> logradouro(s) cadastrado(s)</li>
                            <?php endif; ?>
                        </ul>
                        <small>Não será possível excluí-lo até que todos os registros sejam transferidos ou removidos.</small>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Atenção:</strong> Esta ação não pode ser desfeita.
                    </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <?php if (empty($pacientes) && empty($logradouros)): ?>
                    <form action="<?= base_url('bairros/' . $bairro['id_bairro']) ?>" method="POST" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Excluir Bairro
                        </button>
                    </form>
                <?php else: ?>
                    <button type="button" class="btn btn-danger" disabled>
                        <i class="bi bi-trash"></i> Não é Possível Excluir
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
