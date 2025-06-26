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
                        <h1><i class="bi bi-shield-check"></i> Painel Administrativo</h1>
                        <p class="admin-subtitle">Gerenciamento Completo do Sistema | Superadmin</p>
                    </div>

                    <!-- Content -->
                    <div class="admin-content-wrapper">
                        <!-- Stats Cards -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="admin-stat-card">
                                    <div class="admin-stat-icon bg-primary">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="admin-stat-content">
                                        <h3><?= auth()->getProvider()->countAllResults() ?></h3>
                                        <p>Total de Usuários</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="admin-stat-card">
                                    <div class="admin-stat-icon bg-success">
                                        <i class="bi bi-person-check"></i>
                                    </div>
                                    <div class="admin-stat-content">
                                        <h3><?= auth()->getProvider()->where('active', 1)->countAllResults() ?></h3>
                                        <p>Usuários Ativos</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="admin-stat-card">
                                    <div class="admin-stat-icon bg-warning">
                                        <i class="bi bi-shield-check"></i>
                                    </div>
                                    <div class="admin-stat-content">
                                        <h3><?= count(array_filter(auth()->getProvider()->findAll(), function($user) { return $user->inGroup('superadmin'); })) ?></h3>
                                        <p>Superadmins</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="admin-stat-card">
                                    <div class="admin-stat-icon bg-info">
                                        <i class="bi bi-calendar-check"></i>
                                    </div>
                                    <div class="admin-stat-content">
                                        <h3><?= date('d/m/Y') ?></h3>
                                        <p>Data Atual</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-lightning"></i>
                                        Ações Rápidas
                                    </h2>
                                    <div class="row">
                                        <div class="col-lg-3 col-md-6 mb-3">
                                            <a href="<?php echo base_url('/admin/users/create'); ?>" class="admin-quick-action">
                                                <i class="bi bi-person-plus"></i>
                                                <span>Criar Usuário</span>
                                            </a>
                                        </div>
                                        <div class="col-lg-3 col-md-6 mb-3">
                                            <a href="<?php echo base_url('/admin/users'); ?>" class="admin-quick-action">
                                                <i class="bi bi-people"></i>
                                                <span>Gerenciar Usuários</span>
                                            </a>
                                        </div>
                                        <div class="col-lg-3 col-md-6 mb-3">
                                            <a href="<?php echo base_url('/admin/settings'); ?>" class="admin-quick-action">
                                                <i class="bi bi-gear"></i>
                                                <span>Configurações</span>
                                            </a>
                                        </div>
                                        <div class="col-lg-3 col-md-6 mb-3">
                                            <a href="<?php echo base_url('/admin/logs'); ?>" class="admin-quick-action">
                                                <i class="bi bi-file-text"></i>
                                                <span>Ver Logs</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Users -->
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-people"></i>
                                        Usuários Recentes
                                    </h2>
                                    <div class="table-responsive">
                                        <table class="table admin-table">
                                            <thead>
                                                <tr>
                                                    <th>Usuário</th>
                                                    <th>Email</th>
                                                    <th>Grupo</th>
                                                    <th>Status</th>
                                                    <th>Criado em</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $recentUsers = auth()->getProvider()->orderBy('created_at', 'DESC')->findAll(5);
                                                foreach($recentUsers as $user): 
                                                ?>
                                                <tr>
                                                    <td>
                                                        <div class="admin-user-cell">
                                                            <div class="admin-user-avatar-sm">
                                                                <?= strtoupper(substr($user->username, 0, 2)) ?>
                                                            </div>
                                                            <?= esc($user->username) ?>
                                                        </div>
                                                    </td>
                                                    <td><?= esc($user->email) ?></td>
                                                    <td>
                                                        <?php 
                                                        $groups = $user->getGroups();
                                                        echo !empty($groups) ? '<span class="badge bg-primary">' . esc($groups[0]) . '</span>' : '<span class="badge bg-secondary">Sem grupo</span>';
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php if($user->active): ?>
                                                            <span class="badge bg-success">Ativo</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Inativo</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?= $user->created_at->format('d/m/Y H:i') ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="<?php echo base_url('/admin/users'); ?>" class="btn btn-outline-primary">
                                            Ver Todos os Usuários
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-info-circle"></i>
                                        Informações do Sistema
                                    </h2>
                                    <div class="admin-system-info">
                                        <div class="admin-info-item">
                                            <strong>Versão do CodeIgniter:</strong>
                                            <span><?= \CodeIgniter\CodeIgniter::CI_VERSION ?></span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Versão do PHP:</strong>
                                            <span><?= PHP_VERSION ?></span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Servidor:</strong>
                                            <span><?= $_SERVER['SERVER_SOFTWARE'] ?? 'N/A' ?></span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Ambiente:</strong>
                                            <span class="badge bg-<?= ENVIRONMENT === 'production' ? 'success' : 'warning' ?>">
                                                <?= ucfirst(ENVIRONMENT) ?>
                                            </span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Timezone:</strong>
                                            <span><?= date_default_timezone_get() ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
<?php echo $this->endSection(); ?>
