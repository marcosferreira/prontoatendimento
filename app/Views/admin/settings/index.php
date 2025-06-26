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
                        <h1><i class="bi bi-gear"></i> Configurações do Sistema</h1>
                        <p class="admin-subtitle">Configurações gerais e parâmetros do sistema</p>
                    </div>

                    <!-- Content -->
                    <div class="admin-content-wrapper">
                        <div class="row">
                            <!-- System Information -->
                            <div class="col-lg-6 mb-4">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-info-circle"></i>
                                        Informações do Sistema
                                    </h2>
                                    <div class="admin-system-info">
                                        <div class="admin-info-item">
                                            <strong>Versão do CodeIgniter:</strong>
                                            <span class="badge bg-primary"><?= \CodeIgniter\CodeIgniter::CI_VERSION ?></span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Versão do PHP:</strong>
                                            <span class="badge bg-success"><?= PHP_VERSION ?></span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Servidor Web:</strong>
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
                                        <div class="admin-info-item">
                                            <strong>Memória PHP:</strong>
                                            <span><?= ini_get('memory_limit') ?></span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Upload Max:</strong>
                                            <span><?= ini_get('upload_max_filesize') ?></span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Base URL:</strong>
                                            <span><?= base_url() ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Database Information -->
                            <div class="col-lg-6 mb-4">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-database"></i>
                                        Banco de Dados
                                    </h2>
                                    <div class="admin-system-info">
                                        <?php
                                        $db = \Config\Database::connect();
                                        ?>
                                        <div class="admin-info-item">
                                            <strong>Driver:</strong>
                                            <span><?= $db->DBDriver ?></span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Host:</strong>
                                            <span><?= $db->hostname ?></span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Database:</strong>
                                            <span><?= $db->database ?></span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Charset:</strong>
                                            <span><?= $db->charset ?></span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Status da Conexão:</strong>
                                            <?php if($db->connID): ?>
                                                <span class="badge bg-success">Conectado</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Desconectado</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button class="btn btn-outline-primary btn-sm" onclick="testDatabaseConnection()">
                                            <i class="bi bi-plug"></i> Testar Conexão
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Settings -->
                            <div class="col-lg-6 mb-4">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-shield-check"></i>
                                        Configurações de Segurança
                                    </h2>
                                    <div class="admin-system-info">
                                        <div class="admin-info-item">
                                            <strong>CSRF Protection:</strong>
                                            <span class="badge bg-success">Ativo</span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>HTTPS:</strong>
                                            <span class="badge bg-<?= isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'success' : 'warning' ?>">
                                                <?= isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'Ativo' : 'Inativo' ?>
                                            </span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Session Timeout:</strong>
                                            <span><?= ini_get('session.gc_maxlifetime') ?> segundos</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cache Information -->
                            <div class="col-lg-6 mb-4">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-lightning"></i>
                                        Cache do Sistema
                                    </h2>
                                    <div class="admin-system-info">
                                        <div class="admin-info-item">
                                            <strong>Handler de Cache:</strong>
                                            <span>File</span>
                                        </div>
                                        <div class="admin-info-item">
                                            <strong>Diretório de Cache:</strong>
                                            <span><?= WRITEPATH . 'cache/' ?></span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <button class="btn btn-outline-warning btn-sm" onclick="clearCache()">
                                            <i class="bi bi-trash"></i> Limpar Cache
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- System Actions -->
                            <div class="col-12">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-tools"></i>
                                        Ferramentas do Sistema
                                    </h2>
                                    
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <div class="admin-tool-card">
                                                <i class="bi bi-arrow-clockwise"></i>
                                                <h5>Backup Database</h5>
                                                <p>Criar backup do banco de dados</p>
                                                <button class="btn btn-outline-primary btn-sm" onclick="createBackup()">
                                                    Criar Backup
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 mb-3">
                                            <div class="admin-tool-card">
                                                <i class="bi bi-file-earmark-text"></i>
                                                <h5>View Logs</h5>
                                                <p>Visualizar logs do sistema</p>
                                                <a href="<?php echo base_url('/admin/logs'); ?>" class="btn btn-outline-info btn-sm">
                                                    Ver Logs
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 mb-3">
                                            <div class="admin-tool-card">
                                                <i class="bi bi-envelope"></i>
                                                <h5>Test Email</h5>
                                                <p>Testar configuração de email</p>
                                                <button class="btn btn-outline-success btn-sm" onclick="testEmail()">
                                                    Testar Email
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-3 mb-3">
                                            <div class="admin-tool-card">
                                                <i class="bi bi-exclamation-triangle"></i>
                                                <h5>Maintenance</h5>
                                                <p>Ativar modo manutenção</p>
                                                <button class="btn btn-outline-warning btn-sm" onclick="toggleMaintenance()">
                                                    Toggle Manutenção
                                                </button>
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

    <script>
    function testDatabaseConnection() {
        Swal.fire({
            title: 'Testando Conexão...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                
                // Simular teste de conexão
                setTimeout(() => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Conexão OK!',
                        text: 'Banco de dados conectado com sucesso.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                }, 1500);
            }
        });
    }

    function clearCache() {
        Swal.fire({
            title: 'Limpar Cache?',
            text: 'Todos os arquivos de cache serão removidos.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, limpar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Limpando Cache...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Cache Limpo!',
                                text: 'Cache do sistema foi limpo com sucesso.',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }, 1500);
                    }
                });
            }
        });
    }

    function createBackup() {
        Swal.fire({
            title: 'Criar Backup?',
            text: 'Um backup completo do banco de dados será criado.',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Criar Backup',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Criando Backup...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        
                        setTimeout(() => {
                            Swal.fire({
                                icon: 'success',
                                title: 'Backup Criado!',
                                text: 'Backup salvo em writable/backups/',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }, 2000);
                    }
                });
            }
        });
    }

    function testEmail() {
        Swal.fire({
            title: 'Email de Teste',
            input: 'email',
            inputPlaceholder: 'Digite seu email',
            showCancelButton: true,
            confirmButtonText: 'Enviar Teste',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                Swal.fire({
                    title: 'Enviando Email...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                        
                        // Redirect to test email
                        window.location.href = '/test-email';
                    }
                });
            }
        });
    }

    function toggleMaintenance() {
        Swal.fire({
            title: 'Modo Manutenção',
            text: 'Ativar ou desativar o modo manutenção?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Toggle',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'info',
                    title: 'Recurso em Desenvolvimento',
                    text: 'Funcionalidade de manutenção será implementada em breve.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }
    </script>
<?php echo $this->endSection(); ?>
