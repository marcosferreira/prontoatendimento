<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LoginFilter implements FilterInterface
{
    /**
     * Verifica tentativas de login para usuários desativados.
     * 
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Verificar se é uma tentativa de login (POST para /login)
        if ($request->getMethod() === 'post' && 
            (strpos($request->getUri()->getPath(), '/login') !== false || 
             strpos($request->getUri()->getPath(), '/auth/login') !== false)) {
            
            // Obter credenciais do POST usando service request
            $serviceRequest = service('request');
            $email = $serviceRequest->getPost('email');
            $username = $serviceRequest->getPost('username');
            
            if ($email || $username) {
                $userProvider = auth()->getProvider();
                
                // Tentar encontrar o usuário pelas credenciais fornecidas
                $user = null;
                if ($email) {
                    $user = $userProvider->findByCredentials(['email' => $email]);
                } elseif ($username) {
                    $user = $userProvider->findByCredentials(['username' => $username]);
                }
                
                // Se o usuário existe mas está desativado, bloquear o login
                if ($user && !$user->active) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'Sua conta está desativada. Entre em contato com o administrador do sistema.');
                }
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
