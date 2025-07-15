<?php

namespace App\Controllers;

class Admin extends BaseController
{
    /**
     * Dashboard principal do admin
     */
    public function index(): string
    {
        $db = \Config\Database::connect();
        
        // Busca usuários recentes com dados completos
        $builder = $db->table('users u')
            ->select('u.id, u.username, u.nome, u.cpf, u.active, u.created_at, ai.secret as email, gu.group as grupo_nome')
            ->join('auth_identities ai', 'ai.user_id = u.id AND ai.type = "email_password"', 'left')
            ->join('auth_groups_users gu', 'gu.user_id = u.id', 'left')
            ->where('u.deleted_at', null)
            ->orderBy('u.created_at', 'DESC')
            ->limit(5);
        
        $recentUsers = $builder->get()->getResultArray();
        
        // Processa os dados para garantir valores padrão
        foreach ($recentUsers as &$user) {
            $user['nome'] = $user['nome'] ?? $user['username'];
            $user['email'] = $user['email'] ?? 'N/A';
            $user['grupo_nome'] = $user['grupo_nome'] ?? 'N/A';
            $user['active'] = (int)($user['active'] ?? 0);
        }

        $data = [
            'title' => 'Administração',
            'description' => 'Painel Administrativo - Superadmin',
            'keywords' => 'admin, administração, superadmin, painel',
            'recentUsers' => $recentUsers
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Gerenciamento de usuários
     */
    public function users(): string
    {
        $db = \Config\Database::connect();
        
        // Query otimizada com JOIN para buscar todos os dados necessários
        $builder = $db->table('users u')
            ->select('u.id, u.username, u.nome, u.cpf, u.active, u.last_active, ai.secret as email, gu.group as grupo_nome')
            ->join('auth_identities ai', 'ai.user_id = u.id AND ai.type = "email_password"', 'left')
            ->join('auth_groups_users gu', 'gu.user_id = u.id', 'left')
            ->where('u.deleted_at', null)
            ->orderBy('u.username', 'ASC');
        
        $users = $builder->get()->getResultArray();
        
        // Processa os dados para garantir valores padrão
        foreach ($users as &$user) {
            $user['nome'] = $user['nome'] ?? $user['username'];
            $user['cpf'] = $user['cpf'] ?? 'N/A';
            $user['email'] = $user['email'] ?? 'N/A';
            $user['grupo_nome'] = $user['grupo_nome'] ?? 'N/A';
            $user['active'] = (int)($user['active'] ?? 0);
        }

        $data = [
            'title' => 'Gerenciar Usuários',
            'description' => 'Gerenciamento de Usuários do Sistema',
            'users' => $users
        ];

        return view('admin/users/index', $data);
    }

    /**
     * Criar novo usuário
     */
    public function createUser(): string
    {
        $authConfig = new \Config\AuthGroups();
        
        $data = [
            'title' => 'Criar Usuário',
            'description' => 'Criar Novo Usuário no Sistema',
            'groups' => $authConfig->groups
        ];

        return view('admin/users/create', $data);
    }

    /**
     * Salvar novo usuário
     */
    public function storeUser()
    {
        $rules = [
            'nome'     => 'required|min_length[3]|max_length[255]',
            'cpf'      => 'permit_empty|exact_length[14]|is_unique[users.cpf]',
            'email'    => 'required|valid_email',
            'username' => 'required|is_unique[users.username]',
            'password' => 'required|min_length[8]',
            'group'    => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userProvider = auth()->getProvider();
        
        // Check if email already exists
        $existingUser = $userProvider->findByCredentials(['email' => $this->request->getPost('email')]);
        if ($existingUser) {
            return redirect()->back()->withInput()->with('errors', ['email' => 'Email já está em uso']);
        }

        try {
            $userProvider->db->transStart();

            $userEntity = new \CodeIgniter\Shield\Entities\User([
                'username' => $this->request->getPost('username'),
            ]);

            // Set the email identity
            $userEntity->email = $this->request->getPost('email');
            $userEntity->password = $this->request->getPost('password');
            
            // Set active status (default to active if not provided)
            $activeValue = $this->request->getPost('active');
            $userEntity->active = $activeValue !== null ? (int) $activeValue : 1;

            $userProvider->save($userEntity);
            
            // Get the new user ID
            $userId = $userProvider->getInsertID();
            
            // Update custom fields using query builder
            $db = \Config\Database::connect();
            $updateData = [
                'nome' => $this->request->getPost('nome')
            ];
            
            // Add CPF only if provided
            if (!empty($this->request->getPost('cpf'))) {
                $updateData['cpf'] = $this->request->getPost('cpf');
            }
            
            $db->table('users')->where('id', $userId)->update($updateData);
            
            // Reload user to get the ID
            $userEntity = $userProvider->findByCredentials(['username' => $this->request->getPost('username')]);
            
            // Adicionar ao grupo selecionado
            $userEntity->addGroup($this->request->getPost('group'));

            $userProvider->db->transComplete();

            if ($userProvider->db->transStatus()) {
                return redirect()->to('/admin/users')->with('message', 'Usuário criado com sucesso!');
            } else {
                throw new \Exception('Erro na transação do banco de dados');
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro ao criar usuário no Admin: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('errors', ['general' => 'Erro ao criar usuário: ' . $e->getMessage()]);
        }
    }

    /**
     * Editar usuário
     */
    public function editUser($id): string
    {
        $db = \Config\Database::connect();
        
        // Busca dados completos do usuário
        $builder = $db->table('users u')
            ->select('u.id, u.username, u.nome, u.cpf, u.active, u.last_active, ai.secret as email, gu.group as grupo_nome')
            ->join('auth_identities ai', 'ai.user_id = u.id AND ai.type = "email_password"', 'left')
            ->join('auth_groups_users gu', 'gu.user_id = u.id', 'left')
            ->where('u.id', $id)
            ->where('u.deleted_at', null);
        
        $userData = $builder->get()->getRowArray();

        if (!$userData) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Processa os dados para garantir valores padrão
        $userData['nome'] = $userData['nome'] ?? $userData['username'];
        $userData['cpf'] = $userData['cpf'] ?? '';
        $userData['email'] = $userData['email'] ?? '';
        $userData['grupo_nome'] = $userData['grupo_nome'] ?? '';

        // Busca o objeto User para compatibilidade com as funções existentes
        $userModel = auth()->getProvider();
        $user = $userModel->find($id);

        $authConfig = new \Config\AuthGroups();

        $data = [
            'title' => 'Editar Usuário',
            'description' => 'Editar Usuário do Sistema',
            'user' => $user,
            'userData' => $userData, // Dados completos com nome e CPF
            'groups' => $authConfig->groups,
            'userGroups' => $user->getGroups()
        ];

        return view('admin/users/edit', $data);
    }

    /**
     * Atualizar usuário
     */
    public function updateUser($id)
    {
        $userModel = auth()->getProvider();
        $user = $userModel->find($id);

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $rules = [
            'nome'     => 'required|min_length[3]|max_length[255]',
            'cpf'      => 'permit_empty|exact_length[14]|is_unique[users.cpf,id,' . $id . ']',
            'email'    => 'required|valid_email',
            'username' => "required|is_unique[users.username,id,{$id}]",
            'group'    => 'required'
        ];

        // Se uma nova senha foi fornecida
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[8]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Check if email already exists for another user
        $existingUser = $userModel->findByCredentials(['email' => $this->request->getPost('email')]);
        if ($existingUser && $existingUser->id != $id) {
            return redirect()->back()->withInput()->with('errors', ['email' => 'Email já está em uso por outro usuário']);
        }

        try {
            $userModel->db->transStart();

            // Update basic user data
            $user->username = $this->request->getPost('username');
            $user->email = $this->request->getPost('email');
            
            // Update active status - prevent user from deactivating themselves
            $activeValue = $this->request->getPost('active');
            $requestedActive = $activeValue !== null ? (int) $activeValue : 1;
            if ($user->id === auth()->id() && $requestedActive === 0) {
                return redirect()->back()->withInput()->with('errors', ['active' => 'Você não pode desativar sua própria conta!']);
            }
            $user->active = $requestedActive;
            
            if ($this->request->getPost('password')) {
                $user->password = $this->request->getPost('password');
            }

            $userModel->save($user);

            // Update custom fields using query builder
            $db = \Config\Database::connect();
            $updateData = [
                'nome' => $this->request->getPost('nome')
            ];
            
            // Add CPF only if provided
            $cpf = $this->request->getPost('cpf');
            if (!empty($cpf)) {
                $updateData['cpf'] = $cpf;
            }
            
            $db->table('users')->where('id', $id)->update($updateData);

            // Atualizar grupos - syncGroups espera string, não array
            $user->syncGroups($this->request->getPost('group'));

            $userModel->db->transComplete();

            if ($userModel->db->transStatus()) {
                return redirect()->to('/admin/users')->with('message', 'Usuário atualizado com sucesso!');
            } else {
                throw new \Exception('Erro na transação do banco de dados');
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar usuário no Admin: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('errors', ['general' => 'Erro ao atualizar usuário: ' . $e->getMessage()]);
        }
    }

    /**
     * Deletar usuário
     */
    public function deleteUser($id)
    {
        $userModel = auth()->getProvider();
        $user = $userModel->find($id);

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Não permitir deletar a si mesmo
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Você não pode deletar sua própria conta!');
        }

        $userModel->delete($id);

        return redirect()->to('/admin/users')->with('message', 'Usuário deletado com sucesso!');
    }

    /**
     * Configurações do sistema
     */
    public function settings(): string
    {
        $data = [
            'title' => 'Configurações',
            'description' => 'Configurações do Sistema'
        ];

        return view('admin/settings/index', $data);
    }

    /**
     * Logs do sistema
     */
    public function logs(): string
    {
        $data = [
            'title' => 'Logs do Sistema',
            'description' => 'Logs e Auditoria do Sistema'
        ];

        return view('admin/logs/index', $data);
    }

    /**
     * Relatórios administrativos
     */
    public function reports(): string
    {
        $data = [
            'title' => 'Relatórios',
            'description' => 'Relatórios Administrativos'
        ];

        return view('admin/reports/index', $data);
    }
}
