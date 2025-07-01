    <header class="topbar">
        <div class="topbar-left">
            <button class="mobile-menu-toggle">
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
            <div class="user-info">
                <div class="user-avatar">DR</div>
                <div class="user-details">
                    <?php 
                    $user = auth()->user();
                    $userGroups = $user ? $user->getGroups() : [];
                    $groupNames = [];
                    
                    if (!empty($userGroups)) {
                        // Obter a configuração dos grupos
                        $authGroups = config('AuthGroups');
                        $availableGroups = $authGroups->groups ?? [];
                        
                        foreach ($userGroups as $group) {
                            if (isset($availableGroups[$group]['title'])) {
                                $groupNames[] = $availableGroups[$group]['title'];
                            } else {
                                $groupNames[] = ucfirst($group);
                            }
                        }
                    }
                    
                    $displayRole = !empty($groupNames) ? implode(', ', $groupNames) : 'Usuário';
                    ?>
                    <p class="user-name"><?php echo $user->username ?? 'Usuário' ?></p>
                    <p class="user-role"><?php echo $displayRole ?></p>
                </div>
            </div>
        </div>
    </header>