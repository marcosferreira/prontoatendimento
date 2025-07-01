<?php echo $this->extend('layout/base'); ?>

<?php echo $this->section('content'); ?>
<div class="app-container">
    <!-- Sidebar -->
    <?php echo $this->include('components/sidebar'); ?>

    <!-- Topbar -->
    <?php echo $this->include('components/topbar'); ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-activity"></i> Painel de Controle</h1>
                <p class="subtitle">Monitoramento em Tempo Real | Pronto Atendimento Municipal</p>
            </div>

            <!-- Content -->
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-person-badge"></i>
                                Registro de Pacientes
                            </h2>
                            <div class="table-responsive">
                                <table class="table modern-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">ID</th>
                                            <th scope="col">Nome Completo</th>
                                            <th scope="col">Idade</th>
                                            <th scope="col">Diagnóstico Inicial</th>
                                            <th scope="col">Prioridade</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td data-label="ID"><strong>PA-001</strong></td>
                                            <td data-label="Nome Completo">João da Silva Santos</td>
                                            <td data-label="Idade">30 anos</td>
                                            <td data-label="Diagnóstico Inicial">Cefaleia intensa, hipertermia</td>
                                            <td data-label="Prioridade"><span class="priority-indicator priority-high"></span>Alta</td>
                                            <td data-label="Status"><span class="status-badge status-attention">Em Observação</span></td>
                                        </tr>
                                        <tr>
                                            <td data-label="ID"><strong>PA-002</strong></td>
                                            <td data-label="Nome Completo">Maria Oliveira Costa</td>
                                            <td data-label="Idade">25 anos</td>
                                            <td data-label="Diagnóstico Inicial">Tosse produtiva, dor torácica</td>
                                            <td data-label="Prioridade"><span class="priority-indicator priority-medium"></span>Média</td>
                                            <td data-label="Status"><span class="status-badge status-normal">Estável</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-clipboard-check"></i>
                                Atendimentos Realizados
                            </h2>
                            <ul class="recent-list">
                                <li class="recent-item">
                                    <strong>João da Silva Santos</strong> - 30 anos<br>
                                    <small><i class="bi bi-person-fill"></i> Dr. Carlos Eduardo Mendes | <i class="bi bi-clock"></i> 10:00 - Consultório 1</small>
                                </li>
                                <li class="recent-item">
                                    <strong>Maria Oliveira Costa</strong> - 25 anos<br>
                                    <small><i class="bi bi-person-fill"></i> Dra. Ana Paula Silva | <i class="bi bi-clock"></i> 10:30 - Consultório 2</small>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-bar-chart"></i>
                                Indicadores Operacionais
                            </h2>
                            <div class="stats-grid">
                                <div class="stat-item">
                                    <div class="stat-number">02</div>
                                    <div class="stat-label">Pacientes Ativos</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">02</div>
                                    <div class="stat-label">Atendimentos Hoje</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">27.5</div>
                                    <div class="stat-label">Idade Média</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number">01</div>
                                    <div class="stat-label">Casos Prioritários</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-shield-exclamation"></i>
                                Notificações do Sistema
                            </h2>
                            <div class="alert-modern" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <div>
                                    <strong>Alerta Médico:</strong> Paciente João da Silva Santos (PA-001) apresenta quadro clínico que requer monitoramento contínuo. Protocolo de observação ativado.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>&copy; 2025 Sistema de Pronto Atendimento Municipal. Todos os direitos reservados. | Versão 2.1.0</p>
            </div>
        </div>
    </main>
</div>
<?php echo $this->endSection('content'); ?>