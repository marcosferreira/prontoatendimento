/**
 * Configurações - JavaScript
 * Sistema de Pronto Atendimento Municipal
 */

class ConfiguracoesManager {
    // ========================================
    // CONSTRUCTOR E INICIALIZAÇÃO
    // ========================================
    
    constructor() {
        // Define baseUrl considerando ambiente local ou produção
        const origin = window.location.origin;
        if (origin.includes('localhost') || origin.includes('127.0.0.1')) {
            this.baseUrl = origin;
        } else {
            this.baseUrl = origin + '/prontoatendimento/';
        }
        this.currentPage = 1;
        this.perPage = 50;
        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeMasks();
        this.loadInitialData();
    }

    // ========================================
    // EVENT BINDING METHODS
    // ========================================

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

        // Restore backup form
        const restoreForm = document.getElementById('restoreBackupForm');
        if (restoreForm) {
            this.initRestoreBackupForm();
        }

        // Load backup information if on backup tab
        if (document.getElementById('lastBackupInfo')) {
            this.loadLastBackupInfo();
            this.loadBackupHistory();
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

    // ========================================
    // INITIALIZATION METHODS
    // ========================================

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

        // Initialize restore backup form
        this.initRestoreBackupForm();
    }

    // ========================================
    // SYSTEM CONFIGURATION METHODS
    // ========================================

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

    // ========================================
    // USER MANAGEMENT METHODS
    // ========================================

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
            const profileText = cells[3].querySelector('.status-badge').textContent.trim().toLowerCase();
            document.getElementById('editUserProfile').value = profileText;
            
            // Set active status
            const isActive = cells[4].textContent.trim().toLowerCase() === 'ativo';
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
            const userRow = document.querySelector(`tr[data-user-id="${userId}"]`);
            if (!userRow) {
                this.showAlert('error', 'Usuário não encontrado na tabela.');
                return;
            }
            
            const cells = userRow.querySelectorAll('td');
            const nome = cells[0].textContent.trim();
            const cpf = cells[1].textContent.trim();
            const email = cells[2].textContent.trim();
            const perfil = cells[3].querySelector('.status-badge').textContent.trim().toLowerCase();

            const formData = new FormData();
            formData.append('nome', nome);
            formData.append('cpf', cpf);
            formData.append('email', email);
            formData.append('perfil', perfil);
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
        try {
            console.log('Iniciando refresh da tabela de usuários');
            const response = await fetch(`${this.baseUrl}/configuracoes/usuarios`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            console.log('Resposta recebida:', response.status, response.statusText);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const result = await response.json();
            console.log('Resultado parseado:', result);
            
            if (result.success) {
                const table = document.getElementById('usersTable');
                if (!table) {
                    throw new Error('Elemento usersTable não encontrado');
                }
                
                const tableBody = table.querySelector('tbody');
                if (!tableBody) {
                    throw new Error('Tbody da tabela não encontrado');
                }
                
                tableBody.innerHTML = this.renderUsersTableRows(result.data);
                console.log('Tabela atualizada com sucesso');
            } else {
                console.error('Falha na resposta do servidor:', result);
                this.showAlert('error', `Falha ao atualizar a lista de usuários: ${result.message || 'Erro desconhecido'}`);
            }
        } catch (error) {
            console.error('Erro ao atualizar tabela de usuários:', error);
            this.showAlert('error', `Erro ao atualizar a lista de usuários: ${error.message}`);
        }
    }

    renderUsersTableRows(users) {
        if (users.length === 0) {
            return `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <i class="bi bi-people display-1 text-muted"></i>
                        <h4 class="text-muted">Nenhum usuário encontrado</h4>
                    </td>
                </tr>
            `;
        }

        const grupoColors = {
            'admin': 'background: #fee2e2; color: #991b1b;',
            'superadmin': 'background: #fee2e2; color: #991b1b;',
            'medico': 'background: #dbeafe; color: #1e40af;',
            'enfermeiro': 'background: #ecfdf5; color: #047857;',
            'farmaceutico': 'background: #dcfce7; color: #047857;',
            'recepcionista': 'background: #fef3c7; color: #b45309;',
            'gestor': 'background: #f3e8ff; color: #7c3aed;'
        };

        return users.map(usuario => {
            const grupo = usuario.grupo_nome || 'N/A';
            const style = grupoColors[grupo.toLowerCase()] || 'background: #f3f4f6; color: #374151;';
            const statusBadge = usuario.active ? '<span class="status-badge status-normal">Ativo</span>' : '<span class="status-badge status-danger">Inativo</span>';
            const lastActive = this.formatLastActive(usuario.last_active);
            const toggleIcon = usuario.active ? 'x-circle' : 'check-circle';
            const toggleTitle = usuario.active ? 'Desativar' : 'Ativar';
            const toggleBtnClass = usuario.active ? 'danger' : 'success';

            return `
                <tr data-user-id="${usuario.id}">
                    <td><strong>${this.escapeHTML(usuario.nome || usuario.username)}</strong></td>
                    <td>${this.escapeHTML(usuario.cpf || 'N/A')}</td>
                    <td>${this.escapeHTML(usuario.email || 'N/A')}</td>
                    <td><span class="status-badge" style="${style}">${this.escapeHTML(this.ucfirst(grupo))}</span></td>
                    <td>${statusBadge}</td>
                    <td>${lastActive}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button class="btn btn-outline-primary btn-sm btn-edit-user" data-user-id="${usuario.id}" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-warning btn-sm btn-reset-password" data-user-id="${usuario.id}" title="Resetar Senha">
                                <i class="bi bi-key"></i>
                            </button>
                            <button class="btn btn-outline-${toggleBtnClass} btn-sm btn-toggle-status" data-user-id="${usuario.id}" data-current-status="${usuario.active}" title="${toggleTitle}">
                                <i class="bi bi-${toggleIcon}"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        }).join('');
    }

    // ========================================
    // AUDIT METHODS
    // ========================================

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

    // ========================================
    // BACKUP MANAGEMENT METHODS
    // ========================================

    // async loadBackupHistory() {
    //     try {
    //         const response = await fetch(`${this.baseUrl}/configuracoes/historicoBackups`, {
    //             method: 'GET',
    //             headers: {
    //                 'X-Requested-With': 'XMLHttpRequest'
    //             }
    //         });
    //         const result = await response.json();

    //         const historyContainer = document.getElementById('backupHistory');
    //         if (historyContainer && result.success) {
    //             historyContainer.innerHTML = result.html;
    //         }
    //     } catch (error) {
    //         console.error('Erro ao carregar histórico de backups:', error);
    //         const historyContainer = document.getElementById('backupHistory');
    //         if (historyContainer) {
    //             historyContainer.innerHTML = '<div class="text-danger">Erro ao carregar histórico</div>';
    //         }
    //     }

    //     // Load last backup info
    //     this.loadLastBackupInfo();
    // }

    renderEmptyBackupHistory() {
        const historyContainer = document.getElementById('backupHistory');
        historyContainer.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                <h6 class="mt-2">Nenhum backup encontrado</h6>
                <p class="small">Crie seu primeiro backup usando os botões acima</p>
            </div>
        `;
    }

    async loadBackupHistory(page = 1, limit = 10) {
        const historyContainer = document.getElementById('backupHistory');
        
        try {
            // Mostrar loading
            historyContainer.innerHTML = `
                <div class="text-center text-muted">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Carregando histórico de backups...
                </div>
            `;

            const response = await fetch(`${this.baseUrl}/configuracoes/historicoBackups?page=${page}&limit=${limit}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success && result.data) {
                this.renderBackupHistory(result.data, result.pagination);
            } else {
                this.renderEmptyBackupHistory();
            }
        } catch (error) {
            console.error('Erro ao carregar histórico:', error);
            historyContainer.innerHTML = `
                <div class="text-center text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    Erro ao carregar histórico de backups
                    <br><small>Verifique se você está logado no sistema</small>
                </div>
            `;
        }
    }

    async loadLastBackupInfo() {
        try {
            const response = await fetch(`${this.baseUrl}/configuracoes/ultimoBackup`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();

            const lastBackupInfo = document.getElementById('lastBackupInfo');
            if (lastBackupInfo && result.success) {
                lastBackupInfo.textContent = result.info;
            }
        } catch (error) {
            console.error('Erro ao carregar info do último backup:', error);
            const lastBackupInfo = document.getElementById('lastBackupInfo');
            if (lastBackupInfo) {
                lastBackupInfo.textContent = 'Erro ao carregar informações';
            }
        }
    }

    async createBackup(tipo) {
        const button = document.getElementById(tipo === 'completo' ? 'createFullBackup' : 'createDataBackup');
        
        if (!button) return;

        this.setButtonLoading(button, true);

        try {
            const formData = new FormData();
            formData.append('tipo', tipo);

            const response = await fetch(`${this.baseUrl}/configuracoes/criarBackup`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('success', result.message);
                
                // Atualiza histórico e informações
                this.loadBackupHistory();
                
                // Mostra informações do backup criado
                if (result.arquivo && result.tamanho) {
                    setTimeout(() => {
                        this.showAlert('info', `Arquivo: ${result.arquivo} (${result.tamanho})`);
                    }, 1000);
                }
            } else {
                this.showAlert('error', result.message || 'Erro ao criar backup');
            }
        } catch (error) {
            console.error('Erro ao criar backup:', error);
            this.showAlert('error', 'Erro ao comunicar com o servidor');
        } finally {
            this.setButtonLoading(button, false);
        }
    }

    async initRestoreBackupForm() {
        const form = document.getElementById('restoreBackupForm');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const fileInput = form.querySelector('input[type="file"]');
            const submitButton = form.querySelector('button[type="submit"]');
            
            if (!fileInput.files[0]) {
                this.showAlert('warning', 'Selecione um arquivo de backup');
                return;
            }

            // Confirmação
            if (!confirm('⚠️ ATENÇÃO: A restauração irá sobrescrever TODOS os dados atuais do sistema. Esta ação NÃO pode ser desfeita.\n\nTem certeza que deseja continuar?')) {
                return;
            }

            this.setButtonLoading(submitButton, true);

            try {
                const formData = new FormData(form);

                const response = await fetch(`${this.baseUrl}/configuracoes/restaurarBackup`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const result = await response.json();

                if (result.success) {
                    this.showAlert('success', result.message + ' A página será recarregada em 3 segundos...');
                    
                    // Recarrega a página após sucesso
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                } else {
                    this.showAlert('error', result.message || 'Erro ao restaurar backup');
                }
            } catch (error) {
                console.error('Erro ao restaurar backup:', error);
                this.showAlert('error', 'Erro ao comunicar com o servidor');
            } finally {
                this.setButtonLoading(submitButton, false);
            }
        });
    }

    renderBackupHistory(backups, pagination) {
        const historyContainer = document.getElementById('backupHistory');
        
        if (!backups || backups.length === 0) {
            this.renderEmptyBackupHistory();
            return;
        }

        let html = '';
        
        backups.forEach(backup => {
            const statusClass = backup.status === 'sucesso' ? 'success' : 'danger';
            const statusIcon = backup.status === 'sucesso' ? 'bi-check-circle' : 'bi-x-circle';
            const typeIcon = backup.tipo === 'completo' ? 'bi-database-fill' : 'bi-database';
            const typeLabel = backup.tipo === 'completo' ? 'Backup Completo' : 'Backup de Dados';
            
            html += `
                <div class="backup-item border rounded p-3 mb-2 ${backup.status !== 'sucesso' ? 'border-danger' : ''}">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-1">
                                <i class="${typeIcon} me-2 text-primary"></i>
                                <strong>${this.escapeHTML(backup.nome_arquivo)}</strong>
                                <span class="badge bg-${statusClass} ms-2">
                                    <i class="${statusIcon}"></i> ${this.ucfirst(backup.status)}
                                </span>
                            </div>
                            <div class="backup-details text-muted small">
                                <i class="bi bi-calendar3"></i> ${this.formatDateTime(backup.created_at)} •
                                <i class="bi bi-hdd"></i> ${this.formatFileSize(backup.tamanho)}
                            </div>
                            <div class="backup-type-info text-muted small mt-1">
                                <span class="badge bg-light text-dark">${typeLabel}</span>
                                ${backup.observacoes ? `• ${this.escapeHTML(backup.observacoes)}` : ''}
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                ${backup.status === 'sucesso' ? `
                                    <button class="btn btn-outline-primary" onclick="configManager.downloadBackup('${backup.id_backup}')" title="Download">
                                        <i class="bi bi-download"></i>
                                    </button>
                                ` : ''}
                                <button class="btn btn-outline-info" onclick="configManager.showBackupDetails('${backup.id_backup}')" title="Detalhes">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="configManager.deleteBackup('${backup.id_backup}')" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        historyContainer.innerHTML = html;

        // Renderizar paginação se necessário
        if (pagination && pagination.total_pages > 1) {
            this.renderBackupPagination(pagination);
        } else {
            document.getElementById('backupPagination').style.display = 'none';
        }
    }

    renderBackupPagination(pagination) {
        const paginationContainer = document.getElementById('backupPagination');
        const ul = paginationContainer.querySelector('ul');
        
        let html = '';
        
        // Botão anterior
        if (pagination.current_page > 1) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="configManager.loadBackupHistory(${pagination.current_page - 1}); return false;">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            `;
        }
        
        // Páginas
        for (let i = 1; i <= pagination.total_pages; i++) {
            const active = i === pagination.current_page ? 'active' : '';
            html += `
                <li class="page-item ${active}">
                    <a class="page-link" href="#" onclick="configManager.loadBackupHistory(${i}); return false;">
                        ${i}
                    </a>
                </li>
            `;
        }
        
        // Botão próximo
        if (pagination.current_page < pagination.total_pages) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="configManager.loadBackupHistory(${pagination.current_page + 1}); return false;">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            `;
        }
        
        ul.innerHTML = html;
        paginationContainer.style.display = 'block';
    }

    async downloadBackup(backupId) {
        try {
            window.location.href = `${this.baseUrl}/configuracoes/downloadBackup/${backupId}`;
        } catch (error) {
            console.error('Erro ao fazer download:', error);
            this.showAlert('error', 'Erro ao fazer download do backup');
        }
    }

    async showBackupDetails(backupId) {
        try {
            const response = await fetch(`${this.baseUrl}/configuracoes/detalhesBackup/${backupId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success && result.data) {
                this.renderBackupDetailsModal(result.data);
            } else {
                this.showAlert('error', result.message || 'Erro ao carregar detalhes do backup');
            }
        } catch (error) {
            console.error('Erro ao carregar detalhes:', error);
            this.showAlert('error', 'Erro ao carregar detalhes do backup');
        }
    }

    renderBackupDetailsModal(backup) {
        const modalId = 'backupDetailsModal';
        
        // Remove modal existente se houver
        const existingModal = document.getElementById(modalId);
        if (existingModal) {
            existingModal.remove();
        }

        const statusClass = backup.status === 'sucesso' ? 'success' : 'danger';
        const statusIcon = backup.status === 'sucesso' ? 'bi-check-circle' : 'bi-x-circle';
        const typeLabel = backup.tipo === 'completo' ? 'Backup Completo' : 'Backup de Dados';

        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-info-circle"></i> Detalhes do Backup
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Informações Gerais</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Arquivo:</strong></td>
                                            <td>${this.escapeHTML(backup.nome_arquivo)}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tipo:</strong></td>
                                            <td><span class="badge bg-primary">${typeLabel}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td><span class="badge bg-${statusClass}"><i class="${statusIcon}"></i> ${this.ucfirst(backup.status)}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tamanho:</strong></td>
                                            <td>${this.formatFileSize(backup.tamanho)}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Timing</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Data/Hora:</strong></td>
                                            <td>${this.formatDateTime(backup.created_at)}</td>
                                        </tr>
                                        
                                        <tr>
                                            <td><strong>Criado em:</strong></td>
                                            <td>${this.formatDateTime(backup.created_at)}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            ${backup.observacoes ? `
                                <div class="mt-3">
                                    <h6>Observações</h6>
                                    <div class="alert alert-info">
                                        ${this.escapeHTML(backup.observacoes)}
                                    </div>
                                </div>
                            ` : ''}
                            
                            ${backup.erro_msg ? `
                                <div class="mt-3">
                                    <h6>Mensagem de Erro</h6>
                                    <div class="alert alert-danger">
                                        <code>${this.escapeHTML(backup.erro_msg)}</code>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                        <div class="modal-footer">
                            ${backup.status === 'sucesso' ? `
                                <button type="button" class="btn btn-primary" onclick="configManager.downloadBackup('${backup.id_backup}')">
                                    <i class="bi bi-download"></i> Download
                                </button>
                            ` : ''}
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById(modalId));
        modal.show();

        // Remove modal do DOM quando fechado
        document.getElementById(modalId).addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    }

    async deleteBackup(backupId) {
        const result = await Swal.fire({
            title: 'Confirmar Exclusão',
            text: 'Tem certeza que deseja excluir este backup? Esta ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`${this.baseUrl}/configuracoes/excluirBackup/${backupId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const deleteResult = await response.json();

                if (deleteResult.success) {
                    this.showAlert('success', deleteResult.message);
                    this.loadBackupHistory(); // Recarregar lista
                    this.loadLastBackupInfo(); // Atualizar info do último backup
                } else {
                    this.showAlert('error', deleteResult.message);
                }
            } catch (error) {
                console.error('Erro ao excluir backup:', error);
                this.showAlert('error', 'Erro ao excluir backup');
            }
        }
    }

    // ========================================
    // UTILITY METHODS
    // ========================================

    generatePassword() {
        const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
        let password = '';
        for (let i = 0; i < 8; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return password;
    }

    formatFileSize(bytes) {
        if (!bytes) return '0 B';
        
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        
        return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
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

    escapeHTML(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    formatLastActive(lastActive) {
        if (!lastActive || lastActive === 'Nunca' || lastActive === '0000-00-00 00:00:00') {
            return '<span class="text-muted">Nunca acessou</span>';
        }
        
        try {
            const date = new Date(lastActive);
            if (isNaN(date.getTime())) {
                return '<span class="text-muted">Nunca acessou</span>';
            }
            
            return date.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            return '<span class="text-muted">Nunca acessou</span>';
        }
    }

    ucfirst(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    

    renderBackupHistory(backups, pagination) {
        const historyContainer = document.getElementById('backupHistory');
        
        if (!backups || backups.length === 0) {
            this.renderEmptyBackupHistory();
            return;
        }

        let html = '';
        
        backups.forEach(backup => {
            const statusClass = backup.status === 'sucesso' ? 'success' : 'danger';
            const statusIcon = backup.status === 'sucesso' ? 'bi-check-circle' : 'bi-x-circle';
            const typeIcon = backup.tipo === 'completo' ? 'bi-database-fill' : 'bi-database';
            const typeLabel = backup.tipo === 'completo' ? 'Backup Completo' : 'Backup de Dados';
            
            html += `
                <div class="backup-item border rounded p-3 mb-2 ${backup.status !== 'sucesso' ? 'border-danger' : ''}">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-1">
                                <i class="${typeIcon} me-2 text-primary"></i>
                                <strong>${this.escapeHTML(backup.nome_arquivo)}</strong>
                                <span class="badge bg-${statusClass} ms-2">
                                    <i class="${statusIcon}"></i> ${this.ucfirst(backup.status)}
                                </span>
                            </div>
                            <div class="backup-details text-muted small">
                                <i class="bi bi-calendar3"></i> ${this.formatDateTime(backup.created_at)} •
                                <i class="bi bi-hdd"></i> ${this.formatFileSize(backup.tamanho)}
                            </div>
                            <div class="backup-type-info text-muted small mt-1">
                                <span class="badge bg-light text-dark">${typeLabel}</span>
                                ${backup.observacoes ? `• ${this.escapeHTML(backup.observacoes)}` : ''}
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group btn-group-sm" role="group">
                                ${backup.status === 'sucesso' ? `
                                    <button class="btn btn-outline-primary" onclick="configManager.downloadBackup('${backup.id_backup}')" title="Download">
                                        <i class="bi bi-download"></i>
                                    </button>
                                ` : ''}
                                <button class="btn btn-outline-info" onclick="configManager.showBackupDetails('${backup.id_backup}')" title="Detalhes">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                                <button class="btn btn-outline-danger" onclick="configManager.deleteBackup('${backup.id_backup}')" title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        historyContainer.innerHTML = html;

        // Renderizar paginação se necessário
        if (pagination && pagination.total_pages > 1) {
            this.renderBackupPagination(pagination);
        } else {
            document.getElementById('backupPagination').style.display = 'none';
        }
    }

    

    renderBackupPagination(pagination) {
        const paginationContainer = document.getElementById('backupPagination');
        const ul = paginationContainer.querySelector('ul');
        
        let html = '';
        
        // Botão anterior
        if (pagination.current_page > 1) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="configManager.loadBackupHistory(${pagination.current_page - 1}); return false;">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                </li>
            `;
        }
        
        // Páginas
        for (let i = 1; i <= pagination.total_pages; i++) {
            const active = i === pagination.current_page ? 'active' : '';
            html += `
                <li class="page-item ${active}">
                    <a class="page-link" href="#" onclick="configManager.loadBackupHistory(${i}); return false;">
                        ${i}
                    </a>
                </li>
            `;
        }
        
        // Botão próximo
        if (pagination.current_page < pagination.total_pages) {
            html += `
                <li class="page-item">
                    <a class="page-link" href="#" onclick="configManager.loadBackupHistory(${pagination.current_page + 1}); return false;">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </li>
            `;
        }
        
        ul.innerHTML = html;
        paginationContainer.style.display = 'block';
    }

    async downloadBackup(backupId) {
        try {
            window.location.href = `${this.baseUrl}/configuracoes/downloadBackup/${backupId}`;
        } catch (error) {
            console.error('Erro ao fazer download:', error);
            this.showAlert('error', 'Erro ao fazer download do backup');
        }
    }

    async showBackupDetails(backupId) {
        try {
            const response = await fetch(`${this.baseUrl}/configuracoes/detalhesBackup/${backupId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success && result.data) {
                this.renderBackupDetailsModal(result.data);
            } else {
                this.showAlert('error', 'Erro ao carregar detalhes do backup');
            }
        } catch (error) {
            console.error('Erro ao carregar detalhes:', error);
            this.showAlert('error', 'Erro ao carregar detalhes do backup');
        }
    }

    renderBackupDetailsModal(backup) {
        const modalId = 'backupDetailsModal';
        
        // Remove modal existente se houver
        const existingModal = document.getElementById(modalId);
        if (existingModal) {
            existingModal.remove();
        }

        const statusClass = backup.status === 'sucesso' ? 'success' : 'danger';
        const statusIcon = backup.status === 'sucesso' ? 'bi-check-circle' : 'bi-x-circle';
        const typeLabel = backup.tipo === 'completo' ? 'Backup Completo' : 'Backup de Dados';

        const modalHtml = `
            <div class="modal fade" id="${modalId}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-info-circle"></i> Detalhes do Backup
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Informações Gerais</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Arquivo:</strong></td>
                                            <td>${this.escapeHTML(backup.nome_arquivo)}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tipo:</strong></td>
                                            <td><span class="badge bg-primary">${typeLabel}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status:</strong></td>
                                            <td><span class="badge bg-${statusClass}"><i class="${statusIcon}"></i> ${this.ucfirst(backup.status)}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tamanho:</strong></td>
                                            <td>${this.formatFileSize(backup.tamanho)}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <h6>Timing</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Data/Hora:</strong></td>
                                            <td>${this.formatDateTime(backup.created_at)}</td>
                                        </tr>
                                        
                                        <tr>
                                            <td><strong>Criado em:</strong></td>
                                            <td>${this.formatDateTime(backup.created_at)}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            ${backup.observacoes ? `
                                <div class="mt-3">
                                    <h6>Observações</h6>
                                    <div class="alert alert-info">
                                        ${this.escapeHTML(backup.observacoes)}
                                    </div>
                                </div>
                            ` : ''}
                            
                            ${backup.erro_msg ? `
                                <div class="mt-3">
                                    <h6>Mensagem de Erro</h6>
                                    <div class="alert alert-danger">
                                        <code>${this.escapeHTML(backup.erro_msg)}</code>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                        <div class="modal-footer">
                            ${backup.status === 'sucesso' ? `
                                <button type="button" class="btn btn-primary" onclick="configManager.downloadBackup('${backup.id_backup}')">
                                    <i class="bi bi-download"></i> Download
                                </button>
                            ` : ''}
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById(modalId));
        modal.show();

        // Remove modal do DOM quando fechado
        document.getElementById(modalId).addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    }

    async deleteBackup(backupId) {
        const result = await Swal.fire({
            title: 'Confirmar Exclusão',
            text: 'Tem certeza que deseja excluir este backup? Esta ação não pode ser desfeita.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sim, excluir',
            cancelButtonText: 'Cancelar'
        });

        if (result.isConfirmed) {
            try {
                const response = await fetch(`${this.baseUrl}/configuracoes/excluirBackup/${backupId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const deleteResult = await response.json();

                if (deleteResult.success) {
                    this.showAlert('success', deleteResult.message);
                    this.loadBackupHistory(); // Recarregar lista
                    this.loadLastBackupInfo(); // Atualizar info do último backup
                } else {
                    this.showAlert('error', deleteResult.message);
                }
            } catch (error) {
                console.error('Erro ao excluir backup:', error);
                this.showAlert('error', 'Erro ao excluir backup');
            }
        }
    }
}

// Variável global para acesso aos métodos
let configManager;

document.addEventListener('DOMContentLoaded', () => {
    configManager = new ConfiguracoesManager();
});
