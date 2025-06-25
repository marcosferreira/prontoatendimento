<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Shield\Models\UserModel;

/**
 * Filtro que bloqueia o acesso à rota de registro se já existir pelo menos um usuário no sistema.
 * Este filtro é útil para sistemas onde apenas o primeiro usuário deve ser registrado diretamente,
 * e os demais usuários devem ser criados por administradores ou através de outros meios.
 */
class NoRegistrationIfUsersExist implements FilterInterface
{
    /**
     * Executa o filtro antes do processamento da requisição.
     * 
     * Verifica se existem usuários na base de dados. Se existir pelo menos um usuário,
     * retorna um erro 404 (página não encontrada) para esconder a funcionalidade de registro.
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return \CodeIgniter\HTTP\RedirectResponse|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Obter o modelo de usuários do Shield
        $userModel = new UserModel();
        
        // Verificar se existe pelo menos um usuário no sistema
        $userCount = $userModel->countAllResults();
        
        if ($userCount > 0) {
            // Se já existem usuários, retornar erro 404 para esconder a rota de registro
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                'A página solicitada não foi encontrada.'
            );
        }
        
        // Se não há usuários, permitir acesso à rota de registro
        return null;
    }

    /**
     * Executa o filtro após o processamento da requisição.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Não é necessário fazer nada após a requisição
    }
}
