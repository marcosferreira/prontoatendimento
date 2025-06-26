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
                        <h1><i class="bi bi-file-text"></i> Logs do Sistema</h1>
                        <p class="admin-subtitle">Monitoramento e auditoria do sistema</p>
                    </div>

                    <!-- Content -->
                    <div class="admin-content-wrapper">
                        <div class="row">
                            <div class="col-12">
                                <div class="admin-section-card">
                                    <div class="admin-card-header">
                                        <h2 class="admin-section-title">
                                            <i class="bi bi-list"></i>
                                            Logs Recentes
                                        </h2>
                                        <div class="admin-card-actions">
                                            <select class="form-select form-select-sm" id="logLevel" style="width: 150px;">
                                                <option value="">Todos os níveis</option>
                                                <option value="error">Error</option>
                                                <option value="warning">Warning</option>
                                                <option value="info">Info</option>
                                                <option value="debug">Debug</option>
                                            </select>
                                            <button class="btn btn-sm btn-outline-primary" onclick="refreshLogs()">
                                                <i class="bi bi-arrow-clockwise"></i> Atualizar
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="admin-logs-container">
                                        <?php
                                        $logPath = WRITEPATH . 'logs/';
                                        $logFiles = [];
                                        
                                        if (is_dir($logPath)) {
                                            $files = scandir($logPath);
                                            foreach ($files as $file) {
                                                if (pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                                                    $logFiles[] = $file;
                                                }
                                            }
                                            rsort($logFiles); // Mais recentes primeiro
                                        }
                                        ?>
                                        
                                        <?php if (!empty($logFiles)): ?>
                                            <div class="admin-log-tabs">
                                                <nav>
                                                    <div class="nav nav-tabs" id="logTabs" role="tablist">
                                                        <?php foreach (array_slice($logFiles, 0, 5) as $index => $file): ?>
                                                            <button class="nav-link <?= $index === 0 ? 'active' : '' ?>" 
                                                                    id="log-<?= $index ?>-tab" 
                                                                    data-bs-toggle="tab" 
                                                                    data-bs-target="#log-<?= $index ?>" 
                                                                    type="button" 
                                                                    role="tab">
                                                                <?= pathinfo($file, PATHINFO_FILENAME) ?>
                                                            </button>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </nav>
                                                
                                                <div class="tab-content" id="logTabsContent">
                                                    <?php foreach (array_slice($logFiles, 0, 5) as $index => $file): ?>
                                                        <div class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>" 
                                                             id="log-<?= $index ?>" 
                                                             role="tabpanel">
                                                            <div class="admin-log-content">
                                                                <?php
                                                                $filePath = $logPath . $file;
                                                                if (file_exists($filePath) && is_readable($filePath)) {
                                                                    $content = file_get_contents($filePath);
                                                                    $lines = array_reverse(explode("\n", $content));
                                                                    $displayLines = array_slice($lines, 0, 100); // Últimas 100 linhas
                                                                    
                                                                    foreach ($displayLines as $line) {
                                                                        if (trim($line)) {
                                                                            $logLevel = 'info';
                                                                            $badgeClass = 'bg-secondary';
                                                                            
                                                                            if (strpos($line, 'ERROR') !== false) {
                                                                                $logLevel = 'error';
                                                                                $badgeClass = 'bg-danger';
                                                                            } elseif (strpos($line, 'WARNING') !== false || strpos($line, 'WARN') !== false) {
                                                                                $logLevel = 'warning';
                                                                                $badgeClass = 'bg-warning';
                                                                            } elseif (strpos($line, 'INFO') !== false) {
                                                                                $logLevel = 'info';
                                                                                $badgeClass = 'bg-info';
                                                                            } elseif (strpos($line, 'DEBUG') !== false) {
                                                                                $logLevel = 'debug';
                                                                                $badgeClass = 'bg-secondary';
                                                                            }
                                                                            
                                                                            echo '<div class="admin-log-line" data-level="' . $logLevel . '">';
                                                                            echo '<span class="badge ' . $badgeClass . ' me-2">' . strtoupper($logLevel) . '</span>';
                                                                            echo '<span class="admin-log-text">' . esc($line) . '</span>';
                                                                            echo '</div>';
                                                                        }
                                                                    }
                                                                } else {
                                                                    echo '<p class="text-muted">Não foi possível ler o arquivo de log.</p>';
                                                                }
                                                                ?>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <div class="text-center py-5">
                                                <i class="bi bi-file-text display-1 text-muted"></i>
                                                <h3 class="text-muted mt-3">Nenhum log encontrado</h3>
                                                <p class="text-muted">Não há arquivos de log no diretório <code><?= $logPath ?></code></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Log Statistics -->
                        <div class="row mt-4">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="admin-stat-card">
                                    <div class="admin-stat-icon bg-danger">
                                        <i class="bi bi-exclamation-triangle"></i>
                                    </div>
                                    <div class="admin-stat-content">
                                        <h3 id="errorCount">0</h3>
                                        <p>Errors</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="admin-stat-card">
                                    <div class="admin-stat-icon bg-warning">
                                        <i class="bi bi-exclamation-circle"></i>
                                    </div>
                                    <div class="admin-stat-content">
                                        <h3 id="warningCount">0</h3>
                                        <p>Warnings</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="admin-stat-card">
                                    <div class="admin-stat-icon bg-info">
                                        <i class="bi bi-info-circle"></i>
                                    </div>
                                    <div class="admin-stat-content">
                                        <h3 id="infoCount">0</h3>
                                        <p>Info</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-lg-3 col-md-6 mb-3">
                                <div class="admin-stat-card">
                                    <div class="admin-stat-icon bg-secondary">
                                        <i class="bi bi-bug"></i>
                                    </div>
                                    <div class="admin-stat-content">
                                        <h3 id="debugCount">0</h3>
                                        <p>Debug</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Log Actions -->
                        <div class="row">
                            <div class="col-12">
                                <div class="admin-section-card">
                                    <h2 class="admin-section-title">
                                        <i class="bi bi-tools"></i>
                                        Ações de Log
                                    </h2>
                                    
                                    <div class="admin-log-actions">
                                        <button class="btn btn-outline-warning" onclick="clearLogs()">
                                            <i class="bi bi-trash"></i> Limpar Logs
                                        </button>
                                        <button class="btn btn-outline-primary" onclick="downloadLogs()">
                                            <i class="bi bi-download"></i> Download Logs
                                        </button>
                                        <button class="btn btn-outline-info" onclick="exportLogs()">
                                            <i class="bi bi-file-earmark-zip"></i> Exportar Logs
                                        </button>
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
    function refreshLogs() {
        Swal.fire({
            title: 'Atualizando Logs...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        });
    }

    function clearLogs() {
        Swal.fire({
            title: 'Limpar Logs?',
            text: 'Todos os arquivos de log serão removidos permanentemente.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sim, limpar!',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'info',
                    title: 'Funcionalidade em Desenvolvimento',
                    text: 'A limpeza de logs será implementada em breve.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }

    function downloadLogs() {
        Swal.fire({
            icon: 'info',
            title: 'Download em Desenvolvimento',
            text: 'Funcionalidade de download será implementada em breve.',
            timer: 2000,
            showConfirmButton: false
        });
    }

    function exportLogs() {
        Swal.fire({
            icon: 'info',
            title: 'Export em Desenvolvimento',
            text: 'Funcionalidade de exportação será implementada em breve.',
            timer: 2000,
            showConfirmButton: false
        });
    }

    // Filter logs by level
    document.getElementById('logLevel').addEventListener('change', function() {
        const level = this.value;
        const logLines = document.querySelectorAll('.admin-log-line');
        
        logLines.forEach(line => {
            if (!level || line.dataset.level === level) {
                line.style.display = 'block';
            } else {
                line.style.display = 'none';
            }
        });
    });

    // Count log levels
    function countLogLevels() {
        const counts = { error: 0, warning: 0, info: 0, debug: 0 };
        const logLines = document.querySelectorAll('.admin-log-line');
        
        logLines.forEach(line => {
            const level = line.dataset.level;
            if (counts.hasOwnProperty(level)) {
                counts[level]++;
            }
        });
        
        document.getElementById('errorCount').textContent = counts.error;
        document.getElementById('warningCount').textContent = counts.warning;
        document.getElementById('infoCount').textContent = counts.info;
        document.getElementById('debugCount').textContent = counts.debug;
    }

    // Count on page load
    document.addEventListener('DOMContentLoaded', countLogLevels);
    </script>
<?php echo $this->endSection(); ?>
