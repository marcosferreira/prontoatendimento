<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ActiveUserFilter implements FilterInterface
{
    /**
     * Verifica se o usuário logado está ativo.
     * Se o usuário estiver desativado, faz logout e redireciona para login.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar se há um usuário logado
        if (auth()->loggedIn()) {
            $user = auth()->user();
            
            // Verificar se o usuário está ativo
            if (!$user->active) {
                // Fazer logout do usuário desativado
                auth()->logout();
                
                // Limpar a sessão
                if (session()->has('user')) {
                    session()->remove('user');
                }
                
                // Redirecionar para login com mensagem de erro
                return redirect()->to('/login')
                    ->with('error', 'Sua conta foi desativada. Entre em contato com o administrador do sistema.');
            }
        }
        
        return null;
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
