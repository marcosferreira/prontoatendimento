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
                                <h1><i class="bi bi-pencil"></i> Editar Usuário</h1>
                                <p class="admin-subtitle">Modificar informações de <?= esc($user->username) ?></p>
                            </div>
                            <div>
                                <a href="<?php echo base_url('/admin/users'); ?>" class="btn btn-secondary">
                                    <i class="bi bi-arrow-left"></i> Voltar
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="admin-content-wrapper">
                        <div class="row justify-content-center">
                            <div class="col-lg-8">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-person-badge"></i>
                                        Informações do Usuário
                                    </h2>
                                    
                                    <?php if(session()->has('errors')): ?>
                                    <div class="alert alert-danger">
                                        <h6><i class="bi bi-exclamation-triangle"></i> Erros encontrados:</h6>
                                        <ul class="mb-0">
                                            <?php foreach(session('errors') as $error): ?>
                                                <li><?= esc($error) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <form action="<?php echo base_url('/admin/users/update/' . $user->id); ?>" method="post" class="admin-form">
                                        <?= csrf_field() ?>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="username" class="form-label">
                                                        <i class="bi bi-person"></i> Nome de Usuário *
                                                    </label>
                                                    <input type="text" 
                                                           class="form-control" 
                                                           id="username" 
                                                           name="username" 
                                                           value="<?= old('username', $user->username) ?>"
                                                           required>
                                                    <div class="form-text">
                                                        Nome único para login no sistema
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label">
                                                        <i class="bi bi-envelope"></i> Email *
                                                    </label>
                                                    <input type="email" 
                                                           class="form-control" 
                                                           id="email" 
                                                           name="email" 
                                                           value="<?= old('email', $user->email) ?>"
                                                           required>
                                                    <div class="form-text">
                                                        Email válido para comunicações
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="password" class="form-label">
                                                        <i class="bi bi-lock"></i> Nova Senha
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="password" 
                                                               class="form-control" 
                                                               id="password" 
                                                               name="password">
                                                        <button class="btn btn-outline-secondary" 
                                                                type="button" 
                                                                onclick="togglePassword('password')">
                                                            <i class="bi bi-eye" id="password-icon"></i>
                                                        </button>
                                                    </div>
                                                    <div class="form-text">
                                                        Deixe em branco para manter a senha atual. Mínimo de 8 caracteres se alterada.
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="group" class="form-label">
                                                        <i class="bi bi-shield"></i> Grupo de Acesso *
                                                    </label>
                                                    <select class="form-select" id="group" name="group" required>
                                                        <option value="">Selecione um grupo</option>
                                                        <?php 
                                                        $currentGroup = !empty($userGroups) ? $userGroups[0] : '';
                                                        foreach($groups as $groupKey => $groupInfo): 
                                                        ?>
                                                            <option value="<?= $groupKey ?>" 
                                                                    <?= (old('group', $currentGroup) === $groupKey) ? 'selected' : '' ?>>
                                                                <?= esc($groupInfo['title']) ?> - <?= esc($groupInfo['description']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="form-text">
                                                        Define as permissões do usuário
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="admin-info-box">
                                                        <h6><i class="bi bi-info-circle"></i> Informações Adicionais</h6>
                                                        <p><strong>ID:</strong> <?= $user->id ?></p>
                                                        <p><strong>Criado em:</strong> <?= $user->created_at->format('d/m/Y H:i:s') ?></p>
                                                        <p><strong>Atualizado em:</strong> <?= $user->updated_at->format('d/m/Y H:i:s') ?></p>
                                                        <?php if(isset($user->last_active) && $user->last_active): ?>
                                                        <p><strong>Último login:</strong> <?= $user->last_active->format('d/m/Y H:i:s') ?></p>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="admin-permissions-preview" id="permissionsPreview">
                                                        <h6><i class="bi bi-key"></i> Permissões do Grupo:</h6>
                                                        <div id="permissionsList"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <?php if($user->id === auth()->id()): ?>
                                        <div class="alert alert-warning">
                                            <i class="bi bi-exclamation-triangle"></i> 
                                            <strong>Atenção:</strong> Você está editando sua própria conta. Tenha cuidado ao alterar permissões.
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="admin-form-actions">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check"></i> Atualizar Usuário
                                            </button>
                                            <a href="<?php echo base_url('/admin/users'); ?>" class="btn btn-secondary">
                                                <i class="bi bi-x"></i> Cancelar
                                            </a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(inputId + '-icon');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }

    // Show permissions when group is selected
    function updatePermissions() {
        const groupKey = document.getElementById('group').value;
        const permissionsPreview = document.getElementById('permissionsPreview');
        const permissionsList = document.getElementById('permissionsList');
        
        if (!groupKey) {
            permissionsPreview.style.display = 'none';
            return;
        }
        
        const permissions = {
            'superadmin': ['admin.*', 'users.*', 'beta.*'],
            'admin': ['admin.access', 'users.create', 'users.edit', 'users.delete', 'beta.access'],
            'developer': ['admin.access', 'admin.settings', 'users.create', 'users.edit', 'beta.access'],
            'user': ['Sem permissões especiais'],
            'beta': ['beta.access']
        };
        
        if (permissions[groupKey]) {
            permissionsList.innerHTML = permissions[groupKey].map(p => 
                `<span class="badge bg-secondary me-1">${p}</span>`
            ).join('');
            permissionsPreview.style.display = 'block';
        }
    }

    document.getElementById('group').addEventListener('change', updatePermissions);
    
    // Show permissions on page load
    updatePermissions();
    </script>
<?php echo $this->endSection(); ?>
