<aside class="admin-sidebar">
    <div class="admin-sidebar-header">
        <a href="/admin" class="admin-sidebar-logo">
            <i class="bi bi-shield-check"></i>
            <div>
                <h3 class="admin-sidebar-logo-text">Admin Panel</h3>
                <p class="admin-sidebar-subtitle">Superadmin</p>
            </div>
        </a>
    </div>
    
    <nav class="admin-sidebar-nav">
        <div class="admin-nav-section">
            <div class="admin-nav-section-title">Painel Principal</div>
            <a href="/admin" class="admin-nav-item <?= (current_url() == site_url('admin')) ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
            <a href="/" class="admin-nav-item" target="_blank">
                <i class="bi bi-arrow-up-right-square"></i>
                Ver Site
            </a>
        </div>
        
        <div class="admin-nav-section">
            <div class="admin-nav-section-title">Gerenciamento</div>
            <a href="/admin/users" class="admin-nav-item <?= (strpos(current_url(), 'admin/users') !== false) ? 'active' : '' ?>">
                <i class="bi bi-people"></i>
                Usuários
            </a>
            <a href="/admin/settings" class="admin-nav-item <?= (strpos(current_url(), 'admin/settings') !== false) ? 'active' : '' ?>">
                <i class="bi bi-gear"></i>
                Configurações
            </a>
        </div>
        
        <div class="admin-nav-section">
            <div class="admin-nav-section-title">Monitoramento</div>
            <a href="/admin/logs" class="admin-nav-item <?= (strpos(current_url(), 'admin/logs') !== false) ? 'active' : '' ?>">
                <i class="bi bi-file-text"></i>
                Logs do Sistema
            </a>
            <a href="/admin/reports" class="admin-nav-item <?= (strpos(current_url(), 'admin/reports') !== false) ? 'active' : '' ?>">
                <i class="bi bi-bar-chart"></i>
                Relatórios
            </a>
        </div>
        
        <div class="admin-nav-section">
            <div class="admin-nav-section-title">Sistema</div>
            <a href="#" class="admin-nav-item" onclick="confirmLogout()">
                <i class="bi bi-box-arrow-right"></i>
                Sair
            </a>
        </div>
    </nav>
</aside>

<script>
function confirmLogout() {
    Swal.fire({
        title: 'Sair do Sistema?',
        text: 'Você será redirecionado para a página de login.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Sim, sair',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '/logout';
        }
    });
}
</script>
