<?php
namespace App\Controllers;

use CodeIgniter\Shield\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class UsuariosMedicosApi extends ResourceController
{
    /**
     * Retorna usuários do grupo 'medico' para uso em selects AJAX.
     */
    public function index()
    {
        $userModel = new UserModel();
        $usuarios = $userModel->select('users.id as id, users.username, users.nome, auth_groups_users.group')
            ->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
            ->where('auth_groups_users.group', 'medico')
            ->findAll();

        return $this->response->setJSON($usuarios);
    }
}
