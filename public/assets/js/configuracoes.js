/**
 * Configurações - JavaScript
 * Sistema de Pronto Atendimento Municipal
 */

class ConfiguracoesManager {
    constructor() {
        this.baseUrl = window.location.origin;
        this.currentPage = 1;
        this.perPage = 50;
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeMasks();
        this.loadInitialData();
    }

    bindEvents() {
        // Tab events
        document.addEventListener('shown.bs.tab', (event) => {
            const targetTab = event.target.getAttribute('data-bs-target');
            if (targetTab === '#audit') {
                this.loadAuditLogs();
            } else if (targetTab === '#backup') {
                this.loadBackupHistory();
            }
        });

        // System configuration form
        const systemForm = document.getElementById('systemConfigForm');
        if (systemForm) {
            systemForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveSystemConfig();
            });
        }

        // Backup configuration form
        const backupForm = document.getElementById('backupConfigForm');
        if (backupForm) {
            backupForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveBackupConfig();
            });
        }

        // User management events
        this.bindUserEvents();
        
        // Audit events
        this.bindAuditEvents();

        // Password generator
        const generateBtn = document.getElementById('generatePassword');
        if (generateBtn) {
            generateBtn.addEventListener('click', () => {
                document.getElementById('userPassword').value = this.generatePassword();
            });
        }

        // Password toggle
        const toggleBtn = document.getElementById('togglePassword');
        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                const passwordField = document.getElementById('userPassword');
                const icon = toggleBtn.querySelector('i');
                
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    icon.className = 'bi bi-eye-slash';
                } else {
                    passwordField.type = 'password';
                    icon.className = 'bi bi-eye';
                }
            });
        }
    }

    bindUserEvents() {
        // New user form
        const newUserForm = document.getElementById('newUserForm');
        if (newUserForm) {
            newUserForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.createUser();
            });
        }

        // Edit user form
        const editUserForm = document.getElementById('editUserForm');
        if (editUserForm) {
            editUserForm.addEventListener('submit', (e) => {
                e.preventDefault();
                this.updateUser();
            });
        }

        // User action buttons
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-edit-user')) {
                const userId = e.target.closest('.btn-edit-user').dataset.userId;
                this.editUser(userId);
            } else if (e.target.closest('.btn-reset-password')) {
                const userId = e.target.closest('.btn-reset-password').dataset.userId;
                this.resetPassword(userId);
            } else if (e.target.closest('.btn-toggle-status')) {
                const userId = e.target.closest('.btn-toggle-status').dataset.userId;
                const currentStatus = e.target.closest('.btn-toggle-status').dataset.currentStatus;
                this.toggleUserStatus(userId, currentStatus);
            }
        });
    }

    bindAuditEvents() {
        // Audit filters
        const applyFiltersBtn = document.getElementById('applyFilters');
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', () => {
                this.loadAuditLogs();
            });
        }

        // Enter key on filter inputs
        ['filterAcao', 'filterModulo', 'filterUsuario', 'filterDataInicio', 'filterDataFim'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        this.loadAuditLogs();
                    }
                });
            }
        });
    }

    initializeMasks() {
        // CPF mask
        const cpfInputs = document.querySelectorAll('input[name="cpf"]');
        cpfInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                e.target.value = value;
            });
        });

        // Phone mask
        const phoneInputs = document.querySelectorAll('input[name="unidade_telefone"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
                e.target.value = value;
            });
        });

        // CNPJ mask
        const cnpjInputs = document.querySelectorAll('input[name="unidade_cnpj"]');
        cnpjInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{2})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1/$2');
                value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
                e.target.value = value;
            });
        });
    }

    loadInitialData() {
        // Load backup history if on backup tab
        if (document.querySelector('#backup.active')) {
            this.loadBackupHistory();
        }
    }

    // System Configuration Methods
    async saveSystemConfig() {
        const form = document.getElementById('systemConfigForm');
        const formData = new FormData(form);
        const data = {};

        // Convert FormData to object
        for (let [key, value] of formData.entries()) {
            if (key === 'sistema_notificacoes_email') {
                data[key] = 1;
            } else {
                data[key] = value;
            }
        }

        // Handle checkbox that might not be in FormData if unchecked
        if (!formData.has('sistema_notificacoes_email')) {
            data['sistema_notificacoes_email'] = 0;
        }

        const button = form.querySelector('button[type="submit"]');
        this.setButtonLoading(button, true);

        try {
            const response = await fetch(`${this.baseUrl}/configuracoes/salvarConfiguracoes`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('success', result.message);
            } else {
                this.showAlert('error', result.message);
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showAlert('error', 'Erro ao salvar configurações');
        } finally {
            this.setButtonLoading(button, false);
        }
    }

    async saveBackupConfig() {
        const form = document.getElementById('backupConfigForm');
        const formData = new FormData(form);
        const data = {};

        for (let [key, value] of formData.entries()) {
            if (key === 'backup_automatico_ativo') {
                data[key] = 1;
            } else {
                data[key] = value;
            }
        }

        if (!formData.has('backup_automatico_ativo')) {
            data['backup_automatico_ativo'] = 0;
        }

        const button = form.querySelector('button[type="submit"]');
        this.setButtonLoading(button, true);

        try {
            const response = await fetch(`${this.baseUrl}/configuracoes/salvarConfiguracoes`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('success', result.message);
            } else {
                this.showAlert('error', result.message);
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showAlert('error', 'Erro ao salvar configurações de backup');
        } finally {
            this.setButtonLoading(button, false);
        }
    }

    // User Management Methods
    async createUser() {
        const form = document.getElementById('newUserForm');
        const formData = new FormData(form);

        const button = form.querySelector('button[type="submit"]');
        this.setButtonLoading(button, true);

        try {
            const response = await fetch(`${this.baseUrl}/configuracoes/criarUsuario`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('success', result.message);
                form.reset();
                bootstrap.Modal.getInstance(document.getElementById('newUserModal')).hide();
                this.refreshUsersTable();
            } else {
                this.showAlert('error', result.message);
                if (result.errors) {
                    this.showFormErrors(form, result.errors);
                }
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showAlert('error', 'Erro ao criar usuário');
        } finally {
            this.setButtonLoading(button, false);
        }
    }

    async editUser(userId) {
        // Get user data and populate modal
        try {
            const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
            if (!userRow) return;

            const cells = userRow.querySelectorAll('td');
            const modal = document.getElementById('editUserModal');
            
            document.getElementById('editUserId').value = userId;
            document.getElementById('editUserName').value = cells[0].textContent.trim();
            document.getElementById('editUserCPF').value = cells[1].textContent.trim();
            document.getElementById('editUserEmail').value = cells[2].textContent.trim();
            
            // Set profile
            const profileText = cells[3].textContent.trim().toLowerCase();
            document.getElementById('editUserProfile').value = profileText;
            
            // Set active status
            const isActive = cells[4].textContent.includes('Ativo');
            document.getElementById('editUserActive').checked = isActive;

            bootstrap.Modal.getOrCreateInstance(modal).show();
        } catch (error) {
            console.error('Erro ao carregar dados do usuário:', error);
            this.showAlert('error', 'Erro ao carregar dados do usuário');
        }
    }

    async updateUser() {
        const form = document.getElementById('editUserForm');
        const formData = new FormData(form);
        const userId = formData.get('user_id');

        const button = form.querySelector('button[type="submit"]');
        this.setButtonLoading(button, true);

        try {
            const response = await fetch(`${this.baseUrl}/configuracoes/editarUsuario/${userId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('success', result.message);
                bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                this.refreshUsersTable();
            } else {
                this.showAlert('error', result.message);
                if (result.errors) {
                    this.showFormErrors(form, result.errors);
                }
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showAlert('error', 'Erro ao atualizar usuário');
        } finally {
            this.setButtonLoading(button, false);
        }
    }

    async resetPassword(userId) {
        if (!confirm('Deseja realmente resetar a senha deste usuário?')) {
            return;
        }

        try {
            const response = await fetch(`${this.baseUrl}/configuracoes/resetarSenha/${userId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('success', `${result.message}<br><strong>Nova senha temporária:</strong> ${result.nova_senha}`);
            } else {
                this.showAlert('error', result.message);
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showAlert('error', 'Erro ao resetar senha');
        }
    }

    /**
     * Toggle user status with confirmation
     */
    async toggleUserStatus(userId, currentStatus) {
        const action = currentStatus === '1' ? 'desativar' : 'ativar';
        const actionText = currentStatus === '1' ? 'desativado' : 'ativado';
        
        if (!confirm(`Deseja realmente ${action} este usuário?`)) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('ativo', currentStatus === '1' ? '0' : '1');

            const response = await fetch(`${this.baseUrl}/configuracoes/editarUsuario/${userId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('success', `Usuário ${actionText} com sucesso!`);
                this.refreshUsersTable();
            } else {
                this.showAlert('error', result.message);
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showAlert('error', `Erro ao ${action} usuário`);
        }
    }

    async refreshUsersTable() {
        // Reload the page to refresh the users table
        window.location.reload();
    }

    // Audit Methods
    async loadAuditLogs(page = 1) {
        const filters = {
            acao: document.getElementById('filterAcao')?.value || '',
            modulo: document.getElementById('filterModulo')?.value || '',
            usuario: document.getElementById('filterUsuario')?.value || '',
            data_inicio: document.getElementById('filterDataInicio')?.value || '',
            data_fim: document.getElementById('filterDataFim')?.value || '',
            page: page,
            per_page: this.perPage
        };

        const queryString = new URLSearchParams(filters).toString();

        try {
            const response = await fetch(`${this.baseUrl}/configuracoes/auditoria?${queryString}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.renderAuditTable(result.data.data);
                this.renderAuditPagination(result.data);
            } else {
                this.showAlert('error', result.message);
            }
        } catch (error) {
            console.error('Erro:', error);
            this.showAlert('error', 'Erro ao carregar logs de auditoria');
        }
    }

    renderAuditTable(logs) {
        const tbody = document.getElementById('auditTableBody');
        if (!tbody) return;

        if (logs.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="bi bi-list-check display-1 text-muted"></i>
                        <h4 class="text-muted">Nenhum log encontrado</h4>
                        <p class="text-muted">Não há registros para os filtros aplicados.</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = logs.map(log => {
            const acaoColors = {
                'Login': 'bg-primary',
                'Logout': 'bg-secondary',
                'Cadastro': 'bg-success',
                'Edição': 'bg-warning',
                'Exclusão': 'bg-danger',
                'Consulta': 'bg-info',
                'Backup': 'bg-dark'
            };
            const color = acaoColors[log.acao] || 'bg-light text-dark';

            return `
                <tr>
                    <td>${this.formatDateTime(log.created_at)}</td>
                    <td>${log.usuario_nome}</td>
                    <td><span class="badge ${color}">${log.acao}</span></td>
                    <td>${log.modulo}</td>
                    <td>
                        <span class="text-truncate d-inline-block" style="max-width: 200px;" title="${log.detalhes}">
                            ${log.detalhes}
                        </span>
                    </td>
                    <td>${log.ip_address}</td>
                    <td>
                        <button class="btn btn-outline-info btn-sm" onclick="configManager.viewAuditDetails(${log.id})" title="Ver Detalhes">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    renderAuditPagination(data) {
        const container = document.getElementById('auditPagination');
        if (!container) return;

        if (data.totalPages <= 1) {
            container.innerHTML = '';
            return;
        }

        let pagination = '<ul class="pagination justify-content-center mt-3">';
        
        // Previous button
        pagination += `
            <li class="page-item ${data.page === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="configManager.loadAuditLogs(${data.page - 1})" tabindex="-1">Anterior</a>
            </li>
        `;

        // Page numbers
        const startPage = Math.max(1, data.page - 2);
        const endPage = Math.min(data.totalPages, data.page + 2);

        for (let i = startPage; i <= endPage; i++) {
            pagination += `
                <li class="page-item ${i === data.page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="configManager.loadAuditLogs(${i})">${i}</a>
                </li>
            `;
        }

        // Next button
        pagination += `
            <li class="page-item ${data.page === data.totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" onclick="configManager.loadAuditLogs(${data.page + 1})">Próximo</a>
            </li>
        `;

        pagination += '</ul>';
        container.innerHTML = pagination;
    }

    async viewAuditDetails(logId) {
        // Implementation for viewing audit details
        this.showAlert('info', 'Funcionalidade em desenvolvimento');
    }

    // Backup Methods
    async loadBackupHistory() {
        // Mock data for backup history
        const historyContainer = document.getElementById('backupHistory');
        if (!historyContainer) return;

        const mockHistory = [
            { date: '10/06/2025 02:00', type: 'Backup automático', status: 'Sucesso' },
            { date: '09/06/2025 02:00', type: 'Backup automático', status: 'Sucesso' },
            { date: '08/06/2025 02:00', type: 'Backup automático', status: 'Sucesso' }
        ];

        historyContainer.innerHTML = `
            <div class="list-group list-group-flush">
                ${mockHistory.map(backup => `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${backup.date}</strong><br>
                            <small class="text-muted">${backup.type}</small>
                        </div>
                        <span class="badge bg-success">${backup.status}</span>
                    </div>
                `).join('')}
            </div>
        `;

        // Update last backup info
        const lastBackupInfo = document.getElementById('lastBackupInfo');
        if (lastBackupInfo) {
            lastBackupInfo.textContent = '10/06/2025 02:00 (Sucesso)';
        }
    }

    // Utility Methods
    generatePassword() {
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
        let password = '';
        for (let i = 0; i < 8; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return password;
    }

    setButtonLoading(button, loading) {
        if (loading) {
            button.disabled = true;
            const icon = button.querySelector('i');
            if (icon) {
                icon.className = 'bi bi-hourglass-split';
            }
            button.setAttribute('data-original-text', button.innerHTML);
        } else {
            button.disabled = false;
            const originalText = button.getAttribute('data-original-text');
            if (originalText) {
                button.innerHTML = originalText;
            }
        }
    }

    showAlert(type, message) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert-floating');
        existingAlerts.forEach(alert => alert.remove());

        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        const alertIcon = {
            'success': 'bi-check-circle',
            'error': 'bi-exclamation-triangle',
            'warning': 'bi-exclamation-triangle',
            'info': 'bi-info-circle'
        }[type] || 'bi-info-circle';

        const alert = document.createElement('div');
        alert.className = `alert ${alertClass} alert-dismissible fade show alert-floating`;
        alert.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        alert.innerHTML = `
            <i class="bi ${alertIcon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(alert);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    showFormErrors(form, errors) {
        // Clear previous errors
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

        // Show new errors
        Object.keys(errors).forEach(field => {
            const input = form.querySelector(`[name="${field}"]`);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = errors[field];
                }
            }
        });
    }

    formatDateTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit'
        });
    }
}

// Global functions for onclick events
window.createBackup = async function(type) {
    const button = document.getElementById(type === 'completo' ? 'createFullBackup' : 'createDataBackup');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-hourglass-split"></i> Criando...';
    button.disabled = true;

    try {
        const response = await fetch(`${window.location.origin}/configuracoes/criarBackup`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `tipo=${type}`
        });

        const result = await response.json();

        if (result.success) {
            configManager.showAlert('success', result.message);
            configManager.loadBackupHistory(); // Refresh history
        } else {
            configManager.showAlert('error', result.message);
        }
    } catch (error) {
        console.error('Erro:', error);
        configManager.showAlert('error', 'Erro ao criar backup');
    } finally {
        button.innerHTML = originalText;
        button.disabled = false;
    }
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.configManager = new ConfiguracoesManager();
});
