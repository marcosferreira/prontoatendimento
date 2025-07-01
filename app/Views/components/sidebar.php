<aside class="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo base_url('#'); ?>" class="sidebar-logo">
            <i class="bi bi-hospital"></i>
            <div>
                <h3 class="sidebar-logo-text">SisPAM</h3>
                <p class="sidebar-subtitle">Pronto Atendimento</p>
            </div>
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Principal</div>
            <a href="<?php echo base_url(''); ?>" class="nav-item <?= (current_url() == site_url('')) ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
            <a href="<?php echo base_url('pacientes'); ?>" class="nav-item <?= (strpos(current_url(), 'pacientes') !== false) ? 'active' : '' ?>">
                <i class="bi bi-person-badge"></i>
                Pacientes
            </a>
            <a href="<?php echo base_url('bairros'); ?>" class="nav-item <?= (strpos(current_url(), 'bairros') !== false) ? 'active' : '' ?>">
                <i class="bi bi-geo-alt"></i>
                Bairros
            </a>
            <a href="<?php echo base_url('pages/agendamentos/lista.html'); ?>" class="nav-item <?= (strpos(current_url(), 'agendamentos') !== false) ? 'active' : '' ?>">
                <i class="bi bi-calendar-check"></i>
                Agendamentos
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Atendimento</div>
            <a href="<?php echo base_url('pages/consultas/lista.html'); ?>" class="nav-item <?= (strpos(current_url(), 'consultas') !== false) ? 'active' : '' ?>">
                <i class="bi bi-clipboard-check"></i>
                Consultas
            </a>
            <a href="<?php echo base_url('pages/prontuarios/lista.html'); ?>" class="nav-item <?= (strpos(current_url(), 'prontuarios') !== false) ? 'active' : '' ?>">
                <i class="bi bi-file-medical"></i>
                Prontuários
            </a>
            <a href="<?php echo base_url('pages/medicamentos/lista.html'); ?>" class="nav-item <?= (strpos(current_url(), 'medicamentos') !== false) ? 'active' : '' ?>">
                <i class="bi bi-capsule"></i>
                Medicamentos
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Relatórios</div>
            <a href="<?php echo base_url('pages/relatorios/lista.html'); ?>" class="nav-item <?= (strpos(current_url(), 'relatorios') !== false) ? 'active' : '' ?>">
                <i class="bi bi-bar-chart"></i>
                Estatísticas
            </a>
            <a href="<?php echo base_url('pages/relatorios/lista.html'); ?>" class="nav-item <?= (strpos(current_url(), 'relatorios') !== false) ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text"></i>
                Relatórios
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Sistema</div>
            <?php if(auth()->user() && auth()->user()->inGroup('superadmin')): ?>
            <a href="<?php echo base_url('/admin'); ?>" class="nav-item <?= (current_url() == site_url('admin')) ? 'active' : '' ?>">
                <i class="bi bi-shield-check"></i>
                Administração
            </a>
            <?php endif; ?>
            <a href="<?php echo base_url('pages/configuracoes/geral.html'); ?>" class="nav-item <?= (strpos(current_url(), 'configuracoes') !== false) ? 'active' : '' ?>">
                <i class="bi bi-gear"></i>
                Configurações
            </a>
            <a href="<?php echo base_url('pages/ajuda/index.html'); ?>" class="nav-item <?= (strpos(current_url(), 'ajuda') !== false) ? 'active' : '' ?>">
                <i class="bi bi-question-circle"></i>
                Ajuda
            </a>
        </div>
    </nav>
</aside>