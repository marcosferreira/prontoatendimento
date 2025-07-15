<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    /**
     * --------------------------------------------------------------------
     * Default Group
     * --------------------------------------------------------------------
     * The group that a newly registered user is added to.
     */
    public string $defaultGroup = 'user';

    /**
     * --------------------------------------------------------------------
     * Groups
     * --------------------------------------------------------------------
     * An associative array of the available groups in the system, where the keys
     * are the group names and the values are arrays of the group info.
     *
     * Whatever value you assign as the key will be used to refer to the group
     * when using functions such as:
     *      $user->addGroup('superadmin');
     *
     * @var array<string, array<string, string>>
     *
     * @see https://codeigniter4.github.io/shield/quick_start_guide/using_authorization/#change-available-groups for more info
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'Controle total do sistema.',
        ],
        'admin' => [
            'title'       => 'Administrador',
            'description' => 'Administração geral do sistema.',
        ],
        'gestor' => [
            'title'       => 'Gestor',
            'description' => 'Acesso a relatórios e dados gerenciais.',
        ],
        'medico' => [
            'title'       => 'Médico',
            'description' => 'Acesso aos módulos de atendimento e prontuário.',
        ],
        'enfermeiro' => [
            'title'       => 'Enfermeiro',
            'description' => 'Acesso aos módulos de triagem e atendimento.',
        ],
        'farmaceutico' => [
            'title'       => 'Farmacêutico',
            'description' => 'Acesso ao módulo de farmácia e dispensação.',
        ],
        'recepcionista' => [
            'title'       => 'Recepcionista',
            'description' => 'Acesso ao cadastro de pacientes e agendamento.',
        ],
        'developer' => [
            'title'       => 'Developer',
            'description' => 'Programadores do sistema.',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'Usuários gerais do sistema. Geralmente pacientes.',
        ],
        'beta' => [
            'title'       => 'Beta User',
            'description' => 'Acesso a funcionalidades em teste.',
        ],
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions
     * --------------------------------------------------------------------
     * The available permissions in the system.
     *
     * If a permission is not listed here it cannot be used.
     */
    public array $permissions = [
        'admin.access'        => 'Pode acessar a área de administração',
        'admin.settings'      => 'Pode acessar as configurações gerais do sistema',
        'users.manage-admins' => 'Pode gerenciar outros administradores',
        'users.create'        => 'Pode criar novos usuários não-admin',
        'users.edit'          => 'Pode editar usuários não-admin existentes',
        'users.delete'        => 'Pode deletar usuários não-admin existentes',
        'beta.access'         => 'Pode acessar funcionalidades em teste',
        'atendimentos.view'   => 'Pode visualizar atendimentos',
        'pacientes.create'    => 'Pode criar novos pacientes',
        'relatorios.view'     => 'Pode visualizar relatórios gerenciais',
    ];

    /**
     * --------------------------------------------------------------------
     * Permissions Matrix
     * --------------------------------------------------------------------
     * Maps permissions to groups.
     *
     * This defines group-level permissions.
     */
    public array $matrix = [
        'superadmin' => [
            'admin.*',
            'users.*',
            'beta.*',
            'atendimentos.*',
            'pacientes.*',
            'relatorios.*',
        ],
        'admin' => [
            'admin.access',
            'users.create',
            'users.edit',
            'users.delete',
            'beta.access',
            'atendimentos.view',
            'pacientes.create',
            'relatorios.view',
        ],
        'gestor' => [
            'admin.access',
            'relatorios.view',
        ],
        'medico' => [
            'atendimentos.view',
            'pacientes.create',
        ],
        'enfermeiro' => [
            'atendimentos.view',
            'pacientes.create',
        ],
        'farmaceutico' => [
            'atendimentos.view',
        ],
        'recepcionista' => [
            'pacientes.create',
        ],
        'developer' => [
            'admin.access',
            'admin.settings',
            'users.create',
            'users.edit',
            'beta.access',
        ],
        'user' => [],
        'beta' => [
            'beta.access',
        ],
    ];
}
