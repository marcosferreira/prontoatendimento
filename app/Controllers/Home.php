<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'Início',
            'description' => 'Bem vindo ao SisPAM',
            'keywords' => 'home, bem vindo, application, SisPAM'
        ];
        return view('index', $data);
    }
    
    /**
     * Método para testar o envio de email
     * Acesse via: /test-email
     */
    public function testEmail()
    {
        $email = \Config\Services::email();
        
        $email->setTo('teste@exemplo.com');
        $email->setSubject('Teste de Email - Sistema Pronto Atendimento');
        $email->setMessage('Este é um email de teste para verificar se o sistema de email está funcionando corretamente.');
        
        if ($email->send()) {
            return 'Email enviado com sucesso! (Verifique os logs em writable/logs/emails/)';
        } else {
            return 'Falha ao enviar email. Erro: ' . $email->printDebugger();
        }
    }
}
