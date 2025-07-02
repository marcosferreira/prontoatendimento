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
            <a href="<?php echo base_url(''); ?>" class="nav-item <?= (current_url() == site_url('')) ? 'active' : '' ?>" data-tooltip="Dashboard">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?php echo base_url('pacientes'); ?>" class="nav-item <?= (strpos(current_url(), 'pacientes') !== false) ? 'active' : '' ?>" data-tooltip="Pacientes">
                <i class="bi bi-person-badge"></i>
                <span>Pacientes</span>
            </a>
            <a href="<?php echo base_url('bairros'); ?>" class="nav-item <?= (strpos(current_url(), 'bairros') !== false) ? 'active' : '' ?>" data-tooltip="Bairros">
                <i class="bi bi-geo-alt"></i>
                <span>Bairros</span>
            </a>
            <a href="<?php echo base_url('logradouros'); ?>" class="nav-item <?= (strpos(current_url(), 'logradouros') !== false) ? 'active' : '' ?>" data-tooltip="Logradouros">
                <i class="bi bi-signpost"></i>
                <span>Logradouros</span>
            </a>
            <a href="<?php echo base_url('pages/agendamentos/lista.html'); ?>" class="nav-item <?= (strpos(current_url(), 'agendamentos') !== false) ? 'active' : '' ?>" data-tooltip="Agendamentos">
                <i class="bi bi-calendar-check"></i>
                <span>Agendamentos</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Atendimento</div>
            <a href="<?php echo base_url('pages/consultas/lista.html'); ?>" class="nav-item <?= (strpos(current_url(), 'consultas') !== false) ? 'active' : '' ?>" data-tooltip="Consultas">
                <i class="bi bi-clipboard-check"></i>
                <span>Consultas</span>
            </a>
            <a href="<?php echo base_url('pages/prontuarios/lista.html'); ?>" class="nav-item <?= (strpos(current_url(), 'prontuarios') !== false) ? 'active' : '' ?>" data-tooltip="Prontuários">
                <i class="bi bi-file-medical"></i>
                <span>Prontuários</span>
            </a>
            <a href="<?php echo base_url('pages/medicamentos/lista.html'); ?>" class="nav-item <?= (strpos(current_url(), 'medicamentos') !== false) ? 'active' : '' ?>" data-tooltip="Medicamentos">
                <i class="bi bi-capsule"></i>
                <span>Medicamentos</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Relatórios</div>
            <a href="<?php echo base_url('pages/relatorios/lista.html'); ?>" class="nav-item <?= (strpos(current_url(), 'relatorios') !== false) ? 'active' : '' ?>" data-tooltip="Estatísticas">
                <i class="bi bi-bar-chart"></i>
                <span>Estatísticas</span>
            </a>
            <a href="<?php echo base_url('pages/relatorios/lista.html'); ?>" class="nav-item <?= (strpos(current_url(), 'relatorios') !== false) ? 'active' : '' ?>" data-tooltip="Relatórios">
                <i class="bi bi-file-earmark-text"></i>
                <span>Relatórios</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Sistema</div>
            <?php if(auth()->user() && auth()->user()->inGroup('superadmin')): ?>
            <a href="<?php echo base_url('/admin'); ?>" class="nav-item <?= (current_url() == site_url('admin')) ? 'active' : '' ?>" data-tooltip="Administração">
                <i class="bi bi-shield-check"></i>
                <span>Administração</span>
            </a>
            <?php endif; ?>
            <a href="<?php echo base_url('pages/configuracoes/geral.html'); ?>" class="nav-item <?= (strpos(current_url(), 'configuracoes') !== false) ? 'active' : '' ?>" data-tooltip="Configurações">
                <i class="bi bi-gear"></i>
                <span>Configurações</span>
            </a>
            <a href="<?php echo base_url('pages/ajuda/index.html'); ?>" class="nav-item <?= (strpos(current_url(), 'ajuda') !== false) ? 'active' : '' ?>" data-tooltip="Ajuda">
                <i class="bi bi-question-circle"></i>
                <span>Ajuda</span>
            </a>
        </div>
    </nav>
</aside>