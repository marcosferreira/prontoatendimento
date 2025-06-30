<?php

namespace App\Controllers;

class Admin extends BaseController
{
    protected $helpers = ['auth'];

    /**
     * Dashboard principal do admin
     */
    public function index(): string
    {
        $data = [
            'title' => 'Administração',
            'description' => 'Painel Administrativo - Superadmin',
            'keywords' => 'admin, administração, superadmin, painel'
        ];

        return view('admin/dashboard', $data);
    }

    /**
     * Gerenciamento de usuários
     */
    public function users(): string
    {
        $userModel = auth()->getProvider();
        $users = $userModel->findAll();

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
        
        // Reload user to get the ID
        $userEntity = $userProvider->findByCredentials(['username' => $this->request->getPost('username')]);
        
        // Adicionar ao grupo selecionado
        $userEntity->addGroup($this->request->getPost('group'));

        return redirect()->to('/admin/users')->with('message', 'Usuário criado com sucesso!');
    }

    /**
     * Editar usuário
     */
    public function editUser($id): string
    {
        $userModel = auth()->getProvider();
        $user = $userModel->find($id);

        if (!$user) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $authConfig = new \Config\AuthGroups();

        $data = [
            'title' => 'Editar Usuário',
            'description' => 'Editar Usuário do Sistema',
            'user' => $user,
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

        // Atualizar grupos - syncGroups espera string, não array
        $user->syncGroups($this->request->getPost('group'));

        return redirect()->to('/admin/users')->with('message', 'Usuário atualizado com sucesso!');
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
