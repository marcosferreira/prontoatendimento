<?php

namespace App\Libraries;

use CodeIgniter\Email\Email as BaseEmail;

/**
 * Email personalizado que funciona em ambiente de desenvolvimento
 * sem depender de configurações complexas de servidor de email
 */
class Email extends BaseEmail
{
    /**
     * Sobrescreve o método send para lidar com ambientes de desenvolvimento
     * onde o envio de email real pode não estar disponível
     */
    public function send($autoClear = true)
    {
        // Em ambiente de desenvolvimento, apenas logar os emails
        if (ENVIRONMENT === 'development') {
            return $this->logEmail($autoClear);
        }
        
        // Em outros ambientes, usar o comportamento padrão
        try {
            return parent::send($autoClear);
        } catch (\Exception $e) {
            // Se falhar, logar o erro e o email
            log_message('error', 'Falha ao enviar email: ' . $e->getMessage());
            return $this->logEmail($autoClear);
        }
    }
    
    /**
     * Loga o email em vez de enviá-lo (útil para desenvolvimento)
     */
    protected function logEmail($autoClear = true)
    {
        $emailData = [
            'to' => implode(', ', $this->recipients),
            'subject' => $this->subject,
            'body' => $this->finalBody,
            'from' => $this->fromEmail,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        // Logar os detalhes do email
        log_message('info', 'Email simulado enviado: ' . json_encode($emailData, JSON_PRETTY_PRINT));
        
        // Salvar em arquivo para debug (opcional)
        $logPath = WRITEPATH . 'logs/emails/';
        if (!is_dir($logPath)) {
            mkdir($logPath, 0755, true);
        }
        
        $filename = $logPath . 'email_' . date('Y-m-d') . '.log';
        $logEntry = date('Y-m-d H:i:s') . " - Email simulado:\n";
        $logEntry .= "Para: " . implode(', ', $this->recipients) . "\n";
        $logEntry .= "Assunto: " . $this->subject . "\n";
        $logEntry .= "Corpo:\n" . $this->finalBody . "\n";
        $logEntry .= str_repeat('-', 50) . "\n\n";
        
        file_put_contents($filename, $logEntry, FILE_APPEND | LOCK_EX);
        
        if ($autoClear) {
            $this->clear();
        }
        
        return true;
    }
}
