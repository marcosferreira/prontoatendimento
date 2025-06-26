<header class="admin-topbar">
    <div class="admin-topbar-left">
        <button class="mobile-menu-toggle">
            <i class="bi bi-list"></i>
        </button>
        <h1 class="admin-topbar-title"><?= $title ?? 'Administração' ?></h1>
        <nav class="admin-breadcrumb">
            <span>Admin</span>
            <i class="bi bi-chevron-right"></i>
            <span><?= $title ?? 'Dashboard' ?></span>
        </nav>
    </div>

    <div class="admin-topbar-right">
        <div class="admin-time-display">
            <i class="bi bi-clock"></i>
            <span id="current-time"></span>
        </div>
        
        <div class="admin-user-info">
            <div class="admin-user-avatar">
                <?= strtoupper(substr(auth()->user()->username ?? 'SA', 0, 2)) ?>
            </div>
            <div class="admin-user-details">
                <p class="admin-user-name"><?= auth()->user()->username ?? 'Superadmin' ?></p>
                <p class="admin-user-role">Superadministrador</p>
            </div>
            <div class="admin-user-dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/profile"><i class="bi bi-person"></i> Perfil</a></li>
                    <li><a class="dropdown-item" href="/admin/settings"><i class="bi bi-gear"></i> Configurações</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="confirmLogout()"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>
