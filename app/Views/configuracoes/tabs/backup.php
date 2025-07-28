<h4>Backup e Segurança</h4>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-shield-check"></i> Backup Automático</h6>
            </div>
            <div class="card-body">
                <form id="backupConfigForm">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="backup_automatico_ativo" 
                               id="autoBackup" value="1"
                               <?= ($configuracoes['backup']['backup_automatico_ativo'] ?? false) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="autoBackup">
                            Ativar backup automático
                        </label>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Frequência</label>
                        <select class="form-select" name="backup_frequencia">
                            <option value="diario" <?= ($configuracoes['backup']['backup_frequencia'] ?? 'diario') === 'diario' ? 'selected' : '' ?>>Diário</option>
                            <option value="semanal" <?= ($configuracoes['backup']['backup_frequencia'] ?? 'diario') === 'semanal' ? 'selected' : '' ?>>Semanal</option>
                            <option value="mensal" <?= ($configuracoes['backup']['backup_frequencia'] ?? 'diario') === 'mensal' ? 'selected' : '' ?>>Mensal</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Horário</label>
                        <input type="time" class="form-control" name="backup_horario" 
                               value="<?= esc($configuracoes['backup']['backup_horario'] ?? '02:00') ?>">
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Retenção (dias)</label>
                        <input type="number" class="form-control" name="backup_retencao_dias" 
                               value="<?= esc($configuracoes['backup']['backup_retencao_dias'] ?? '30') ?>"
                               min="7" max="365">
                        <div class="form-text">Número de dias para manter os backups</div>
                    </div>
                    
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar Configurações
                        </button>
                    </div>
                </form>
                
                <hr>
                
                <div class="alert alert-info d-flex align-items-center" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <div>
                        <strong>Último backup:</strong><br>
                        <span id="lastBackupInfo">Carregando...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h6><i class="bi bi-download"></i> Backup Manual</h6>
            </div>
            <div class="card-body">
                <p>Realize um backup completo do sistema agora.</p>
                
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" id="createFullBackup" onclick="configManager.createBackup('completo')">
                        <i class="bi bi-download"></i> Criar Backup Completo
                    </button>
                    <button class="btn btn-outline-primary" id="createDataBackup" onclick="configManager.createBackup('dados')">
                        <i class="bi bi-database"></i> Backup Apenas Dados
                    </button>
                </div>
                
                <hr>
                
                <h6>Restaurar Backup</h6>
                <form id="restoreBackupForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <input class="form-control" type="file" name="backup_file" 
                               accept=".backup,.sql,.zip" required>
                        <div class="form-text">Selecione um arquivo de backup (.backup, .sql, .zip)</div>
                    </div>
                    <div class="alert alert-warning" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Atenção:</strong> A restauração irá sobrescrever todos os dados atuais. Esta ação não pode ser desfeita.
                    </div>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-upload"></i> Restaurar
                    </button>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0"><i class="bi bi-list-ul"></i> Histórico de Backups</h6>
                <button class="btn btn-sm btn-outline-secondary" onclick="configManager.loadBackupHistory()" title="Atualizar">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
            <div class="card-body">
                <div id="backupHistory">
                    <div class="text-center text-muted">
                        <i class="bi bi-hourglass-split"></i> Carregando histórico...
                    </div>
                </div>
                
                <!-- Template para item do histórico -->
                <div id="backupItemTemplate" style="display: none;">
                    <div class="backup-item border rounded p-3 mb-2">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-1">
                                    <i class="backup-icon me-2"></i>
                                    <strong class="backup-filename"></strong>
                                    <span class="backup-status-badge ms-2"></span>
                                </div>
                                <div class="backup-details text-muted small">
                                    <i class="bi bi-calendar3"></i> <span class="backup-date"></span> •
                                    <i class="bi bi-hdd"></i> <span class="backup-size"></span> •
                                </div>
                                <div class="backup-type-info text-muted small mt-1">
                                    <span class="backup-type-label"></span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-primary btn-download" title="Download">
                                        <i class="bi bi-download"></i>
                                    </button>
                                    <button class="btn btn-outline-info btn-details" title="Detalhes">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                    <button class="btn btn-outline-danger btn-delete" title="Excluir" onclick="console.log('Template estático clicado - isso não deveria acontecer')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Área para paginação -->
                <div id="backupPagination" class="mt-3" style="display: none;">
                    <nav aria-label="Navegação do histórico de backups">
                        <ul class="pagination pagination-sm justify-content-center">
                            <!-- Paginação será inserida aqui via JavaScript -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
