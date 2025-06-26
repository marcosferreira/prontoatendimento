<?php echo $this->extend('admin/layout/base'); ?>

<?php echo $this->section('content'); ?>
    <div class="admin-app-container">
        <!-- Sidebar -->
        <?php echo $this->include('admin/components/sidebar'); ?>

        <!-- Main Content Area -->
        <div class="admin-main-wrapper">
            <!-- Topbar -->
            <?php echo $this->include('admin/components/topbar'); ?>  

            <!-- Main Content -->   
            <main class="admin-main-content">
                <div class="admin-main-container">
                    <!-- Header -->
                    <div class="admin-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1><i class="bi bi-people"></i> Gerenciar Usuários</h1>
                                <p class="admin-subtitle">Administração de usuários do sistema</p>
                            </div>
                            <div>
                                <a href="<?php echo base_url('/admin/users/create'); ?>" class="btn btn-primary">
                                    <i class="bi bi-person-plus"></i> Novo Usuário
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="admin-content-wrapper">
                        <div class="row">
                            <div class="col-12">
                                <div class="admin-section-card">
                                    <div class="admin-card-header">
                                        <h2 class="admin-section-title">
                                            <i class="bi bi-list"></i>
                                            Lista de Usuários
                                        </h2>
                                        <div class="admin-card-actions">
                                            <div class="input-group input-group-sm" style="width: 250px;">
                                                <input type="text" class="form-control" placeholder="Buscar usuários..." id="searchUsers">
                                                <button class="btn btn-outline-secondary" type="button">
                                                    <i class="bi bi-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table admin-table" id="usersTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Usuário</th>
                                                    <th>Email</th>
                                                    <th>Grupos</th>
                                                    <th>Status</th>
                                                    <th>Último Login</th>
                                                    <th>Criado em</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($users as $user): ?>
                                                <tr>
                                                    <td><?= $user->id ?></td>
                                                    <td>
                                                        <div class="admin-user-cell">
                                                            <div class="admin-user-avatar-sm">
                                                                <?= strtoupper(substr($user->username, 0, 2)) ?>
                                                            </div>
                                                            <div>
                                                                <strong><?= esc($user->username) ?></strong>
                                                                <?php if($user->id === auth()->id()): ?>
                                                                    <small class="text-muted">(Você)</small>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><?= esc($user->email) ?></td>
                                                    <td>
                                                        <?php 
                                                        $groups = $user->getGroups();
                                                        if (!empty($groups)): 
                                                            foreach($groups as $group):
                                                                $badgeClass = match($group) {
                                                                    'superadmin' => 'bg-danger',
                                                                    'admin' => 'bg-warning',
                                                                    'developer' => 'bg-info',
                                                                    'beta' => 'bg-secondary',
                                                                    default => 'bg-primary'
                                                                };
                                                        ?>
                                                            <span class="badge <?= $badgeClass ?> me-1"><?= esc($group) ?></span>
                                                        <?php 
                                                            endforeach;
                                                        else: 
                                                        ?>
                                                            <span class="badge bg-light text-dark">Sem grupo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if($user->active): ?>
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-check-circle"></i> Ativo
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">
                                                                <i class="bi bi-x-circle"></i> Inativo
                                                            </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if(isset($user->last_active) && $user->last_active): ?>
                                                            <?= $user->last_active->format('d/m/Y H:i') ?>
                                                        <?php else: ?>
                                                            <span class="text-muted">Nunca</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= $user->created_at->format('d/m/Y H:i') ?></td>
                                                    <td>
                                                        <div class="admin-action-buttons">
                                                            <a href="<?php echo base_url('/admin/users/edit/' . $user->id); ?>" 
                                                               class="btn btn-sm btn-outline-primary" 
                                                               title="Editar">
                                                                <i class="bi bi-pencil"></i>
                                                            </a>
                                                            <?php if($user->id !== auth()->id()): ?>
                                                            <button type="button" 
                                                                    class="btn btn-sm btn-outline-danger" 
                                                                    title="Deletar"
                                                                    onclick="confirmDelete('<?php echo base_url('/admin/users/delete/' . $user->id); ?>', 'Deletar usuário?', 'O usuário <?= esc($user->username) ?> será permanentemente removido.')">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <?php if(empty($users)): ?>
                                    <div class="text-center py-5">
                                        <i class="bi bi-people display-1 text-muted"></i>
                                        <h3 class="text-muted mt-3">Nenhum usuário encontrado</h3>
                                        <p class="text-muted">Comece criando o primeiro usuário do sistema.</p>
                                        <a href="<?php echo base_url('/admin/users/create'); ?>" class="btn btn-primary">
                                            <i class="bi bi-person-plus"></i> Criar Primeiro Usuário
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
    </div>

    <script>
    // Simple search functionality
    document.getElementById('searchUsers').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const tableRows = document.querySelectorAll('#usersTable tbody tr');
        
        tableRows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
    </script>
<?php echo $this->endSection(); ?>
