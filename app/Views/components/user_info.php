<div class="user-profile-container dropdown">
    <div class="user-info" id="userProfileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="user-avatar">
            <?= strtoupper(substr(auth()->user()->username, 0, 2)) ?>
        </div>
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
        <i class="bi bi-chevron-down dropdown-arrow"></i>
    </div>
    <div class="dropdown-menu user-dropdown" aria-labelledby="userProfileDropdown">
        <a class="dropdown-item" href="<?= site_url('logout') ?>">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sair</span>
        </a>
    </div>
</div>