<h4>Log de Auditoria</h4>

<div class="row mb-3">
    <div class="col-md-2">
        <select class="form-select" id="filterAcao">
            <option value="">Todas as Ações</option>
            <option value="Login">Login</option>
            <option value="Logout">Logout</option>
            <option value="Cadastro">Cadastro</option>
            <option value="Edição">Edição</option>
            <option value="Exclusão">Exclusão</option>
            <option value="Consulta">Consulta</option>
            <option value="Backup">Backup</option>
            <option value="Configuração">Configuração</option>
        </select>
    </div>
    <div class="col-md-2">
        <select class="form-select" id="filterModulo">
            <option value="">Todos os Módulos</option>
            <option value="Sistema">Sistema</option>
            <option value="Usuários">Usuários</option>
            <option value="Pacientes">Pacientes</option>
            <option value="Médicos">Médicos</option>
            <option value="Atendimentos">Atendimentos</option>
            <option value="Exames">Exames</option>
            <option value="Procedimentos">Procedimentos</option>
            <option value="Configurações">Configurações</option>
            <option value="Backup">Backup</option>
        </select>
    </div>
    <div class="col-md-2">
        <input type="text" class="form-control" id="filterUsuario" placeholder="Usuário">
    </div>
    <div class="col-md-2">
        <input type="date" class="form-control" id="filterDataInicio" value="<?= date('Y-m-d') ?>">
    </div>
    <div class="col-md-2">
        <input type="date" class="form-control" id="filterDataFim" value="<?= date('Y-m-d') ?>">
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary w-100" id="applyFilters">
            <i class="bi bi-search"></i> Filtrar
        </button>
    </div>
</div>

<div class="table-responsive">
    <table class="table modern-table" id="auditTable">
        <thead>
            <tr>
                <th>Data/Hora</th>
                <th>Usuário</th>
                <th>Ação</th>
                <th>Módulo</th>
                <th>Detalhes</th>
                <th>IP</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody id="auditTableBody">
            <?php foreach ($logs_recentes as $log): ?>
            <tr>
                <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                <td><?= esc($log['usuario_nome']) ?></td>
                <td>
                    <?php
                    $acaoColors = [
                        'Login' => 'bg-primary',
                        'Logout' => 'bg-secondary',
                        'Cadastro' => 'bg-success',
                        'Edição' => 'bg-warning',
                        'Exclusão' => 'bg-danger',
                        'Consulta' => 'bg-info',
                        'Backup' => 'bg-dark'
                    ];
                    $color = $acaoColors[$log['acao']] ?? 'bg-light text-dark';
                    ?>
                    <span class="badge <?= $color ?>"><?= esc($log['acao']) ?></span>
                </td>
                <td><?= esc($log['modulo']) ?></td>
                <td>
                    <span class="text-truncate d-inline-block" style="max-width: 200px;" title="<?= esc($log['detalhes']) ?>">
                        <?= esc($log['detalhes']) ?>
                    </span>
                </td>
                <td><?= esc($log['ip_address']) ?></td>
                <td>
                    <button class="btn btn-outline-info btn-sm" onclick="viewAuditDetails(<?= $log['id'] ?>)" title="Ver Detalhes">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (empty($logs_recentes)): ?>
<div class="text-center py-4">
    <i class="bi bi-list-check display-1 text-muted"></i>
    <h4 class="text-muted">Nenhum log encontrado</h4>
    <p class="text-muted">Não há registros de auditoria para os filtros aplicados.</p>
</div>
<?php endif; ?>

<nav aria-label="Navegação de páginas" id="auditPagination">
    <!-- Pagination will be loaded here -->
</nav>

<!-- Modal para detalhes do log -->
<div class="modal fade" id="auditDetailsModal" tabindex="-1" aria-labelledby="auditDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="auditDetailsModalLabel">
                    <i class="bi bi-info-circle"></i> Detalhes do Log de Auditoria
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="auditDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>
