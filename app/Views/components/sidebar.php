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
            <a href="<?php echo base_url('medicos'); ?>" class="nav-item <?= (strpos(current_url(), 'medicos') !== false) ? 'active' : '' ?>" data-tooltip="Médicos">
                <i class="bi bi-person-vcard"></i>
                <span>Médicos</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Atendimento</div>
            <a href="<?php echo base_url('atendimentos'); ?>" class="nav-item <?= (strpos(current_url(), 'atendimentos') !== false) ? 'active' : '' ?>" data-tooltip="Atendimentos">
                <i class="bi bi-clipboard-check"></i>
                <span>Atendimentos</span>
            </a>
            <a href="<?php echo base_url('procedimentos'); ?>" class="nav-item <?= (strpos(current_url(), 'procedimentos') !== false) ? 'active' : '' ?>" data-tooltip="Procedimentos">
                <i class="bi bi-list-check"></i>
                <span>Procedimentos</span>
            </a>
            <a href="<?php echo base_url('exames'); ?>" class="nav-item <?= (strpos(current_url(), 'exames') !== false) ? 'active' : '' ?>" data-tooltip="Exames">
                <i class="bi bi-clipboard2-data"></i>
                <span>Exames</span>
            </a>
            <a href="<?php echo base_url('atendimento_procedimentos'); ?>" class="nav-item <?= (strpos(current_url(), 'atendimento_procedimentos') !== false) ? 'active' : '' ?>" data-tooltip="Atend. Procedimentos">
                <i class="bi bi-clipboard2-check"></i>
                <span>Atend. Procedimentos</span>
            </a>
            <a href="<?php echo base_url('atendimento_exames'); ?>" class="nav-item <?= (strpos(current_url(), 'atendimento_exames') !== false) ? 'active' : '' ?>" data-tooltip="Atend. Exames">
                <i class="bi bi-file-medical"></i>
                <span>Atend. Exames</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Relatórios</div>
            <a href="<?php echo base_url('atendimentos/relatorio'); ?>" class="nav-item <?= (strpos(current_url(), 'atendimentos/relatorio') !== false) ? 'active' : '' ?>" data-tooltip="Relatório de Atendimentos">
                <i class="bi bi-bar-chart"></i>
                <span>Rel. Atendimentos</span>
            </a>
            <a href="<?php echo base_url('atendimento_procedimentos/relatorio'); ?>" class="nav-item <?= (strpos(current_url(), 'atendimento_procedimentos/relatorio') !== false) ? 'active' : '' ?>" data-tooltip="Relatório de Procedimentos">
                <i class="bi bi-file-earmark-text"></i>
                <span>Rel. Procedimentos</span>
            </a>
            <a href="<?php echo base_url('atendimento_exames/relatorio'); ?>" class="nav-item <?= (strpos(current_url(), 'atendimento_exames/relatorio') !== false) ? 'active' : '' ?>" data-tooltip="Relatório de Exames">
                <i class="bi bi-graph-up"></i>
                <span>Rel. Exames</span>
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
        </div>
    </nav>
</aside>