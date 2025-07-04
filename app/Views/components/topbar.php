    <header class="topbar">
        <div class="topbar-left">
            <button class="mobile-menu-toggle" title="Alternar Sidebar">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="topbar-title">Dashboard Principal</h1>
            <nav class="breadcrumb">
                <span>Sistema</span>
                <i class="bi bi-chevron-right"></i>
                <span><?= $title ?? 'Dashboard' ?></span>
            </nav>
        </div>

        <div class="topbar-right">
            <?php echo view('components/user_info'); ?>
        </div>
    </header>