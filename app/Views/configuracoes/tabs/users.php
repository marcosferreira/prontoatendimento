<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Gestão de Usuários</h4>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newUserModal">
        <i class="bi bi-person-plus"></i> Novo Usuário
    </button>
</div>

<div class="table-responsive">
    <table class="table modern-table" id="usersTable">
        <thead>
            <tr>
                <th>Nome</th>
                <th>CPF</th>
                <th>Email</th>
                <th>Perfil</th>
                <th>Status</th>
                <th>Último Acesso</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
            <tr data-user-id="<?= $usuario['id'] ?>">
                <td><strong><?= esc($usuario['nome'] ?? $usuario['username']) ?></strong></td>
                <td><?= esc($usuario['cpf'] ?? 'N/A') ?></td>
                <td><?= esc($usuario['email'] ?? 'N/A') ?></td>
                <td>
                    <?php
                    $grupoColors = [
                        'admin' => 'background: #fee2e2; color: #991b1b;',
                        'superadmin' => 'background: #fee2e2; color: #991b1b;',
                        'medico' => 'background: #dbeafe; color: #1e40af;',
                        'enfermeiro' => 'background: #ecfdf5; color: #047857;',
                        'farmaceutico' => 'background: #dcfce7; color: #047857;',
                        'recepcionista' => 'background: #fef3c7; color: #b45309;',
                        'gestor' => 'background: #f3e8ff; color: #7c3aed;'
                    ];
                    $grupo = $usuario['grupo_nome'] ?? 'N/A';
                    $style = $grupoColors[$grupo] ?? 'background: #f3f4f6; color: #374151;';
                    ?>
                    <span class="status-badge" style="<?= $style ?>"><?= ucfirst($grupo) ?></span>
                </td>
                <td>
                    <?php if ($usuario['active']): ?>
                        <span class="status-badge status-normal">Ativo</span>
                    <?php else: ?>
                        <span class="status-badge status-danger">Inativo</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php 
                    if ($usuario['last_active'] && $usuario['last_active'] !== '0000-00-00 00:00:00') {
                        echo date('d/m/Y H:i', strtotime($usuario['last_active']));
                    } else {
                        echo '<span class="text-muted">Nunca acessou</span>';
                    }
                    ?>
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-primary btn-sm btn-edit-user" 
                                data-user-id="<?= $usuario['id'] ?>" 
                                title="Editar">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-warning btn-sm btn-reset-password" 
                                data-user-id="<?= $usuario['id'] ?>" 
                                title="Resetar Senha">
                            <i class="bi bi-key"></i>
                        </button>
                        <button class="btn btn-outline-<?= $usuario['active'] ? 'danger' : 'success' ?> btn-sm btn-toggle-status" 
                                data-user-id="<?= $usuario['id'] ?>" 
                                data-current-status="<?= $usuario['active'] ?>"
                                title="<?= $usuario['active'] ? 'Desativar' : 'Ativar' ?>">
                            <i class="bi bi-<?= $usuario['active'] ? 'x-circle' : 'check-circle' ?>"></i>
                        </button>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php if (empty($usuarios)): ?>
<div class="text-center py-4">
    <i class="bi bi-people display-1 text-muted"></i>
    <h4 class="text-muted">Nenhum usuário encontrado</h4>
    <p class="text-muted">Clique no botão "Novo Usuário" para adicionar o primeiro usuário.</p>
</div>
<?php endif; ?>
