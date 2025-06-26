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
                                <h1><i class="bi bi-person-plus"></i> Criar Novo Usuário</h1>
                                <p class="admin-subtitle">Adicionar um novo usuário ao sistema</p>
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
                                    
                                    <form action="<?php echo base_url('/admin/users/store'); ?>" method="post" class="admin-form">
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
                                                           value="<?= old('username') ?>"
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
                                                           value="<?= old('email') ?>"
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
                                                        <i class="bi bi-lock"></i> Senha *
                                                    </label>
                                                    <div class="input-group">
                                                        <input type="password" 
                                                               class="form-control" 
                                                               id="password" 
                                                               name="password" 
                                                               required>
                                                        <button class="btn btn-outline-secondary" 
                                                                type="button" 
                                                                onclick="togglePassword('password')">
                                                            <i class="bi bi-eye" id="password-icon"></i>
                                                        </button>
                                                    </div>
                                                    <div class="form-text">
                                                        Mínimo de 8 caracteres
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
                                                        <?php foreach($groups as $groupKey => $groupInfo): ?>
                                                            <option value="<?= $groupKey ?>" 
                                                                    <?= old('group') === $groupKey ? 'selected' : '' ?>>
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
                                            <div class="admin-permissions-preview" id="permissionsPreview" style="display: none;">
                                                <h6><i class="bi bi-key"></i> Permissões do Grupo:</h6>
                                                <div id="permissionsList"></div>
                                            </div>
                                        </div>
                                        
                                        <div class="admin-form-actions">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check"></i> Criar Usuário
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
    document.getElementById('group').addEventListener('change', function() {
        const groupKey = this.value;
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
    });
    </script>
<?php echo $this->endSection(); ?>
