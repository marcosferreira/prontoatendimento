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
                        <h1><i class="bi bi-bar-chart"></i> Relatórios Administrativos</h1>
                        <p class="admin-subtitle">Análises e estatísticas do sistema</p>
                    </div>

                    <!-- Content -->
                    <div class="admin-content-wrapper">
                        <!-- Quick Stats -->
                        <div class="row mb-4">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="admin-stat-card">
                                    <div class="admin-stat-icon bg-primary">
                                        <i class="bi bi-people"></i>
                                    </div>
                                    <div class="admin-stat-content">
                                        <h3><?= auth()->getProvider()->countAllResults() ?></h3>
                                        <p>Total de Usuários</p>
                                        <small class="text-muted">Todos os tempos</small>
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
                                        <small class="text-success">Online</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="admin-stat-card">
                                    <div class="admin-stat-icon bg-warning">
                                        <i class="bi bi-calendar-week"></i>
                                    </div>
                                    <div class="admin-stat-content">
                                        <h3><?= auth()->getProvider()->where('created_at >=', date('Y-m-d', strtotime('-7 days')))->countAllResults() ?></h3>
                                        <p>Novos (7 dias)</p>
                                        <small class="text-warning">Esta semana</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="admin-stat-card">
                                    <div class="admin-stat-icon bg-info">
                                        <i class="bi bi-calendar-month"></i>
                                    </div>
                                    <div class="admin-stat-content">
                                        <h3><?= auth()->getProvider()->where('created_at >=', date('Y-m-01'))->countAllResults() ?></h3>
                                        <p>Novos (30 dias)</p>
                                        <small class="text-info">Este mês</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Row -->
                        <div class="row mb-4">
                            <div class="col-lg-8">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-graph-up"></i>
                                        Usuários por Período
                                    </h2>
                                    <div class="admin-chart-container">
                                        <canvas id="usersChart" height="100"></canvas>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-4">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-pie-chart"></i>
                                        Distribuição por Grupos
                                    </h2>
                                    <div class="admin-chart-container">
                                        <canvas id="groupsChart" height="200"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Report Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-file-earmark-text"></i>
                                        Gerar Relatórios
                                    </h2>
                                    
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <div class="admin-report-card">
                                                <i class="bi bi-people-fill"></i>
                                                <h5>Relatório de Usuários</h5>
                                                <p>Lista completa de usuários com grupos e status</p>
                                                <button class="btn btn-outline-primary btn-sm" onclick="generateReport('users')">
                                                    Gerar Relatório
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 mb-3">
                                            <div class="admin-report-card">
                                                <i class="bi bi-shield-check"></i>
                                                <h5>Relatório de Segurança</h5>
                                                <p>Logs de acesso e tentativas de login</p>
                                                <button class="btn btn-outline-warning btn-sm" onclick="generateReport('security')">
                                                    Gerar Relatório
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 mb-3">
                                            <div class="admin-report-card">
                                                <i class="bi bi-activity"></i>
                                                <h5>Relatório de Atividade</h5>
                                                <p>Atividades dos usuários no sistema</p>
                                                <button class="btn btn-outline-info btn-sm" onclick="generateReport('activity')">
                                                    Gerar Relatório
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 mb-3">
                                            <div class="admin-report-card">
                                                <i class="bi bi-gear-fill"></i>
                                                <h5>Relatório do Sistema</h5>
                                                <p>Status geral e configurações do sistema</p>
                                                <button class="btn btn-outline-success btn-sm" onclick="generateReport('system')">
                                                    Gerar Relatório
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="row">
                            <div class="col-12">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-clock-history"></i>
                                        Atividade Recente
                                    </h2>
                                    
                                    <div class="admin-activity-timeline">
                                        <?php 
                                        $recentUsers = auth()->getProvider()->orderBy('created_at', 'DESC')->findAll(10);
                                        foreach($recentUsers as $user): 
                                        ?>
                                        <div class="admin-timeline-item">
                                            <div class="admin-timeline-marker bg-primary">
                                                <i class="bi bi-person-plus"></i>
                                            </div>
                                            <div class="admin-timeline-content">
                                                <h6>Novo usuário cadastrado</h6>
                                                <p><strong><?= esc($user->username) ?></strong> (<?= esc($user->email) ?>)</p>
                                                <small class="text-muted"><?= $user->created_at->humanize() ?></small>
                                            </div>
                                        </div>
                                        <?php endforeach; ?>
                                        
                                        <div class="admin-timeline-item">
                                            <div class="admin-timeline-marker bg-success">
                                                <i class="bi bi-check-circle"></i>
                                            </div>
                                            <div class="admin-timeline-content">
                                                <h6>Sistema iniciado</h6>
                                                <p>Aplicação iniciada com sucesso</p>
                                                <small class="text-muted">Sistema online</small>
                                            </div>
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

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
    // Users Chart
    const usersCtx = document.getElementById('usersChart').getContext('2d');
    const usersChart = new Chart(usersCtx, {
        type: 'line',
        data: {
            labels: ['30 dias', '25 dias', '20 dias', '15 dias', '10 dias', '5 dias', 'Hoje'],
            datasets: [{
                label: 'Novos Usuários',
                data: [2, 1, 3, 0, 2, 1, 4],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Groups Chart
    const groupsCtx = document.getElementById('groupsChart').getContext('2d');
    
    // Calcular distribuição de grupos
    <?php
    $groupCounts = [];
    $authConfig = new \Config\AuthGroups();
    foreach($authConfig->groups as $groupKey => $groupInfo) {
        $count = 0;
        foreach(auth()->getProvider()->findAll() as $u) {
            if(in_array($groupKey, $u->getGroups())) {
                $count++;
            }
        }
        $groupCounts[$groupKey] = $count;
    }
    ?>
    
    const groupsChart = new Chart(groupsCtx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_keys($groupCounts)) ?>,
            datasets: [{
                data: <?= json_encode(array_values($groupCounts)) ?>,
                backgroundColor: [
                    '#dc3545', // superadmin - red
                    '#ffc107', // admin - yellow
                    '#17a2b8', // developer - cyan
                    '#28a745', // user - green
                    '#6c757d'  // beta - gray
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    function generateReport(type) {
        let title, text;
        
        switch(type) {
            case 'users':
                title = 'Relatório de Usuários';
                text = 'Gerando relatório completo de usuários...';
                break;
            case 'security':
                title = 'Relatório de Segurança';
                text = 'Gerando relatório de logs de segurança...';
                break;
            case 'activity':
                title = 'Relatório de Atividade';
                text = 'Gerando relatório de atividades dos usuários...';
                break;
            case 'system':
                title = 'Relatório do Sistema';
                text = 'Gerando relatório de status do sistema...';
                break;
        }

        Swal.fire({
            title: title,
            text: text,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Relatório Gerado!',
                        text: 'O relatório foi gerado e está disponível para download.',
                        confirmButtonText: 'Download',
                        showCancelButton: true,
                        cancelButtonText: 'Fechar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Simulate download
                            const link = document.createElement('a');
                            link.href = '#';
                            link.download = `relatorio_${type}_${new Date().toISOString().split('T')[0]}.pdf`;
                            link.click();
                        }
                    });
                }, 2000);
            }
        });
    }
    </script>
<?php echo $this->endSection(); ?>
