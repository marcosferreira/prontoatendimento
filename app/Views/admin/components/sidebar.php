<aside class="admin-sidebar">
    <div class="admin-sidebar-header">
        <a href="<?php echo base_url('/admin'); ?>" class="admin-sidebar-logo">
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
            <a href="<?php echo base_url('/admin'); ?>" class="admin-nav-item <?= (current_url() == site_url('admin')) ? 'active' : '' ?>" data-tooltip="Dashboard">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo base_url('/'); ?>" class="admin-nav-item" target="_blank" data-tooltip="Ver Site">
                <i class="bi bi-arrow-up-right-square"></i>
                <span>Ver Site</span>
            </a>
        </div>
        
        <div class="admin-nav-section">
            <div class="admin-nav-section-title">Gerenciamento</div>
            <a href="<?php echo base_url('/admin/users'); ?>" class="admin-nav-item <?= (strpos(current_url(), 'admin/users') !== false) ? 'active' : '' ?>" data-tooltip="Usuários">
                <i class="bi bi-people"></i>
                <span>Usuários</span>
            </a>
            <a href="<?php echo base_url('/admin/settings'); ?>" class="admin-nav-item <?= (strpos(current_url(), 'admin/settings') !== false) ? 'active' : '' ?>" data-tooltip="Configurações">
                <i class="bi bi-gear"></i>
                <span>Configurações</span>
            </a>
        </div>
        
        <div class="admin-nav-section">
            <div class="admin-nav-section-title">Monitoramento</div>
            <a href="<?php echo base_url('/admin/logs'); ?>" class="admin-nav-item <?= (strpos(current_url(), 'admin/logs') !== false) ? 'active' : '' ?>" data-tooltip="Logs do Sistema">
                <i class="bi bi-file-text"></i>
                <span>Logs do Sistema</span>
            </a>
            <a href="<?php echo base_url('/admin/reports'); ?>" class="admin-nav-item <?= (strpos(current_url(), 'admin/reports') !== false) ? 'active' : '' ?>" data-tooltip="Relatórios">
                <i class="bi bi-bar-chart"></i>
                <span>Relatórios</span>
            </a>
        </div>
        
        <div class="admin-nav-section">
            <div class="admin-nav-section-title">Sistema</div>
            <a href="#" class="admin-nav-item" onclick="confirmLogout()" data-tooltip="Sair">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sair</span>
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
            window.location.href = '<?php echo base_url('/logout'); ?>';
        }
    });
}
</script>
