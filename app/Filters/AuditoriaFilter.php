<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\AuditoriaModel;

class AuditoriaFilter implements FilterInterface
{
    protected $auditoriaModel;

    public function __construct()
    {
        $this->auditoriaModel = new AuditoriaModel();
    }

    public function before(RequestInterface $request, $arguments = null)
    {
        // Não faz nada no before
        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Registra apenas se for uma requisição POST, PUT, DELETE (mudanças de dados)
        $method = $request->getMethod();
        if (!in_array(strtoupper($method), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            return;
        }

        // Ignora rotas específicas para evitar loops
        $uri = $request->getUri()->getPath();
        $ignoredRoutes = [
            '/login',
            '/logout',
            '/register',
            '/configuracoes/auditoria',
            '/assets/',
            '/uploads/'
        ];

        foreach ($ignoredRoutes as $ignored) {
            if (strpos($uri, $ignored) !== false) {
                return;
            }
        }

        try {
            // Determina a ação baseada no método HTTP
            $acaoMap = [
                'POST' => 'Cadastro',
                'PUT' => 'Edição',
                'PATCH' => 'Edição', 
                'DELETE' => 'Exclusão'
            ];

            $acao = $acaoMap[strtoupper($method)] ?? 'Ação';

            // Determina o módulo baseado na URI
            $modulo = $this->determinarModulo($uri);

            // Detalhes da ação
            $detalhes = $this->montarDetalhes($request, $response);

            // Registra a auditoria
            $this->auditoriaModel->registrarAcao(
                $acao,
                $modulo,
                $detalhes
            );

        } catch (\Exception $e) {
            // Log o erro mas não interrompe a aplicação
            log_message('error', 'Erro no filtro de auditoria: ' . $e->getMessage());
        }
    }

    /**
     * Determina o módulo baseado na URI
     */
    private function determinarModulo(string $uri): string
    {
        $uriSegments = explode('/', trim($uri, '/'));
        $firstSegment = $uriSegments[0] ?? '';

        $moduloMap = [
            'pacientes' => 'Pacientes',
            'medicos' => 'Médicos',
            'bairros' => 'Bairros',
            'logradouros' => 'Logradouros',
            'atendimentos' => 'Atendimentos',
            'procedimentos' => 'Procedimentos',
            'exames' => 'Exames',
            'atendimento_procedimentos' => 'Atendimento Procedimentos',
            'atendimento_exames' => 'Atendimento Exames',
            'configuracoes' => 'Configurações',
            'admin' => 'Administração',
            'users' => 'Usuários'
        ];

        return $moduloMap[$firstSegment] ?? ucfirst($firstSegment);
    }

    /**
     * Monta os detalhes da ação
     */
    private function montarDetalhes(RequestInterface $request, ResponseInterface $response): string
    {
        $uri = $request->getUri()->getPath();
        $method = $request->getMethod();
        $statusCode = $response->getStatusCode();

        $detalhes = "{$method} {$uri}";

        // Adiciona informações do status da resposta
        if ($statusCode >= 200 && $statusCode < 300) {
            $detalhes .= " - Sucesso";
        } elseif ($statusCode >= 400) {
            $detalhes .= " - Erro ({$statusCode})";
        }

        // Adiciona informações específicas baseadas na URI
        if (preg_match('/\/(\d+)/', $uri, $matches)) {
            $id = $matches[1];
            $detalhes .= " - ID: {$id}";
        }

        return $detalhes;
    }
}
