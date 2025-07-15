<div class="user-profile-container dropdown">
    <div class="user-info" id="userProfileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <?php
        // Busca o nome do usuário para o avatar
        $user = auth()->user();
        $db = \Config\Database::connect();
        $userData = $db->table('users')->select('nome, username')->where('id', $user->id)->get()->getFirstRow();
        $displayName = ($userData && !empty($userData->nome)) ? $userData->nome : ($user->username ?? 'Usuário');
        $avatarText = strtoupper(substr($displayName, 0, 2));
        ?>
        <div class="user-avatar">
            <?= $avatarText ?>
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
            
            // Busca o nome do usuário da tabela users
            $db = \Config\Database::connect();
            $userData = $db->table('users')->select('nome, username')->where('id', $user->id)->get()->getFirstRow();
            $displayName = ($userData && !empty($userData->nome)) ? $userData->nome : ($user->username ?? 'Usuário');
            ?>
            <p class="user-name"><?php echo esc($displayName) ?></p>
            <p class="user-role"><?php echo esc($displayRole) ?></p>
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