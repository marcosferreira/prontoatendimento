<aside class="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo base_url('#'); ?>" class="sidebar-logo">
            <img src="<?php echo base_url('assets/images/logo-white.svg'); ?>" alt="SisPAM Logo" class="sidebar-logo-img">
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
            <a href="<?php echo base_url('atendimento-procedimentos'); ?>" class="nav-item <?= (strpos(current_url(), 'atendimento-procedimentos') !== false) ? 'active' : '' ?>" data-tooltip="Atend. Procedimentos">
                <i class="bi bi-clipboard2-check"></i>
                <span>Atend. Procedimentos</span>
            </a>
            <a href="<?php echo base_url('atendimento-exames'); ?>" class="nav-item <?= (strpos(current_url(), 'atendimento-exames') !== false) ? 'active' : '' ?>" data-tooltip="Atend. Exames">
                <i class="bi bi-file-medical"></i>
                <span>Atend. Exames</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Monitoramento</div>
            <a href="<?php echo base_url('notificacoes'); ?>" class="nav-item <?= (strpos(current_url(), 'notificacoes') !== false) ? 'active' : '' ?>" data-tooltip="Notificações BI">
                <i class="bi bi-bell-fill"></i>
                <span>Notificações BI</span>
                <?php 
                // Busca notificações críticas para badge
                if (class_exists('\App\Models\NotificacaoModel')) {
                    $notificacaoModel = new \App\Models\NotificacaoModel();
                    $criticas = $notificacaoModel->where('status', 'ativa')->where('severidade', 'critica')->countAllResults();
                    if ($criticas > 0): ?>
                        <span class=" badge rounded-pill bg-danger"><?= min($criticas, 99) ?></span>
                    <?php endif;
                } ?>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Relatórios</div>
            <a href="<?php echo base_url('atendimentos/relatorio'); ?>" class="nav-item <?= (strpos(current_url(), 'atendimentos/relatorio') !== false) ? 'active' : '' ?>" data-tooltip="Relatório de Atendimentos">
                <i class="bi bi-bar-chart"></i>
                <span>Rel. Atendimentos</span>
            </a>
            <a href="<?php echo base_url('atendimento-procedimentos/relatorio'); ?>" class="nav-item <?= (strpos(current_url(), 'atendimento-procedimentos/relatorio') !== false) ? 'active' : '' ?>" data-tooltip="Relatório de Procedimentos">
                <i class="bi bi-file-earmark-text"></i>
                <span>Rel. Procedimentos</span>
            </a>
            <a href="<?php echo base_url('atendimento-exames/relatorio'); ?>" class="nav-item <?= (strpos(current_url(), 'atendimento-exames/relatorio') !== false) ? 'active' : '' ?>" data-tooltip="Relatório de Exames">
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
            <?php if(auth()->user() && (auth()->user()->inGroup('superadmin') || auth()->user()->inGroup('admin'))): ?>
            <a href="<?php echo base_url('configuracoes'); ?>" class="nav-item <?= (strpos(current_url(), 'configuracoes') !== false) ? 'active' : '' ?>" data-tooltip="Configurações">
                <i class="bi bi-gear"></i>
                <span>Configurações</span>
            </a>
            <?php endif; ?>
            <a href="<?php echo base_url('ajuda') ?>" class="nav-item <?= (strpos(current_url(), 'ajuda') !== false) ? 'active' : '' ?>" data-tooltip="Ajuda">
                <i class="bi bi-question-circle"></i>
                <span>Ajuda</span>
            </a>
            <a href="<?php echo base_url('/logout'); ?>" class="nav-item" data-tooltip="Sair">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sair</span>
            </a>
        </div>
    </nav>
</aside>