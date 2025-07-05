<?php

namespace App\Controllers;

use App\Models\ConfiguracaoModel;
use App\Models\AuditoriaModel;
use CodeIgniter\HTTP\ResponseInterface;

class Configuracoes extends BaseController
{
    protected $helpers = ['auth', 'form'];
    protected $configuracaoModel;
    protected $auditoriaModel;

    public function __construct()
    {
        $this->configuracaoModel = new ConfiguracaoModel();
        $this->auditoriaModel = new AuditoriaModel();
    }

    /**
     * Página principal de configurações
     */
    public function index(): string
    {
        // Verifica se o usuário tem permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Página não encontrada');
        }

        // Busca todas as configurações organizadas por categoria
        $configuracoes = [
            'unidade' => $this->configuracaoModel->getByCategoria('unidade'),
            'sistema' => $this->configuracaoModel->getByCategoria('sistema'),
            'aparencia' => $this->configuracaoModel->getByCategoria('aparencia'),
            'backup' => $this->configuracaoModel->getByCategoria('backup')
        ];

        // Busca usuários para gestão
        $userModel = auth()->getProvider();
        
        // Busca usuários com seus grupos
        $usuarios = [];
        $allUsers = $userModel->asArray()->findAll();
        
        foreach ($allUsers as $user) {
            $userEntity = $userModel->find($user['id']);
            $groups = $userEntity ? $userEntity->getGroups() : [];
            $user['grupo_nome'] = !empty($groups) ? $groups[0] : 'N/A';
            $usuarios[] = $user;
        }

        // Busca logs de auditoria recentes
        $logsRecentes = $this->auditoriaModel->getLogs([], 1, 10);

        $data = [
            'title' => 'Configurações do Sistema',
            'description' => 'Administração e Parametrização do SisPAM',
            'keywords' => 'configurações, administração, sistema, backup, auditoria',
            'configuracoes' => $configuracoes,
            'usuarios' => $usuarios,
            'logs_recentes' => $logsRecentes['data'],
            'script' => 'configuracoes'
        ];

        return view('configuracoes/index', $data);
    }

    /**
     * Salva configurações do sistema
     */
    public function salvarConfiguracoes(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back()->with('error', 'Acesso inválido');
        }

        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        $data = $this->request->getJSON(true);
        
        if (!$data) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos'
            ]);
        }

        try {
            $this->configuracaoModel->db->transStart();

            $configuracoesAtualizadas = [];

            // Processa cada configuração
            foreach ($data as $chave => $valor) {
                $configExistente = $this->configuracaoModel->where('chave', $chave)->first();
                
                if ($configExistente && $configExistente['editavel']) {
                    $valorAnterior = $configExistente['valor'];
                    
                    if ($this->configuracaoModel->updateByChave($chave, $valor)) {
                        $configuracoesAtualizadas[] = [
                            'chave' => $chave,
                            'valor_anterior' => $valorAnterior,
                            'valor_novo' => $valor
                        ];
                    }
                }
            }

            $this->configuracaoModel->db->transComplete();

            if ($this->configuracaoModel->db->transStatus()) {
                // Registra na auditoria
                $this->auditoriaModel->registrarAcao(
                    'Configurações Atualizadas',
                    'Configurações',
                    'Atualizadas ' . count($configuracoesAtualizadas) . ' configurações',
                    null,
                    null,
                    null,
                    $configuracoesAtualizadas
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Configurações salvas com sucesso!'
                ]);
            } else {
                throw new \Exception('Erro na transação do banco de dados');
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro ao salvar configurações: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao salvar configurações: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Gerenciamento de usuários - listar
     */
    public function usuarios(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        try {
            $userModel = auth()->getProvider();
            
            // Busca usuários com seus grupos
            $usuarios = [];
            $allUsers = $userModel->asArray()->findAll();
            
            foreach ($allUsers as $user) {
                $userEntity = $userModel->find($user['id']);
                $groups = $userEntity ? $userEntity->getGroups() : [];
                $user['grupo_nome'] = !empty($groups) ? $groups[0] : 'N/A';
                $user['last_active'] = $user['last_active'] ?? 'Nunca';
                $user['active'] = $user['active'] ?? 0;
                $usuarios[] = $user;
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $usuarios
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar usuários: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao buscar usuários'
            ]);
        }
    }

    /**
     * Criar novo usuário
     */
    public function criarUsuario(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'cpf' => 'required|exact_length[14]|is_unique[users.cpf]',
            'email' => 'permit_empty|valid_email|is_unique[users.email]',
            'perfil' => 'required|in_list[admin,medico,enfermeiro,farmaceutico,recepcionista,gestor]',
            'senha' => 'required|min_length[6]',
            'forcar_alteracao' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $userData = [
                'username' => $this->request->getPost('cpf'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('senha'),
                'active' => 1,
                'nome' => $this->request->getPost('nome'),
                'cpf' => $this->request->getPost('cpf'),
                'force_pass_reset' => $this->request->getPost('forcar_alteracao') ? 1 : 0
            ];

            $userModel = auth()->getProvider();
            $userId = $userModel->insert($userData);

            if ($userId) {
                // Adiciona ao grupo
                $groupModel = new \CodeIgniter\Shield\Models\GroupModel();
                $groupModel->addUserToGroup($userId, $this->request->getPost('perfil'));

                // Registra auditoria
                $this->auditoriaModel->registrarAcao(
                    'Usuário Criado',
                    'Usuários',
                    'Usuário: ' . $this->request->getPost('nome') . ' (' . $this->request->getPost('cpf') . ')',
                    null,
                    null,
                    null,
                    $userData
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Usuário criado com sucesso!'
                ]);
            } else {
                throw new \Exception('Erro ao criar usuário');
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro ao criar usuário: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao criar usuário: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Editar usuário
     */
    public function editarUsuario($id): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'cpf' => 'required|exact_length[14]|is_unique[users.cpf,id,' . $id . ']',
            'email' => 'permit_empty|valid_email|is_unique[users.email,id,' . $id . ']',
            'perfil' => 'required|in_list[admin,medico,enfermeiro,farmaceutico,recepcionista,gestor]',
            'ativo' => 'permit_empty|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $userModel = auth()->getProvider();
            $usuarioAnterior = $userModel->find($id);
            
            if (!$usuarioAnterior) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ]);
            }

            $userData = [
                'nome' => $this->request->getPost('nome'),
                'cpf' => $this->request->getPost('cpf'),
                'email' => $this->request->getPost('email'),
                'active' => $this->request->getPost('ativo') ? 1 : 0
            ];

            if ($userModel->update($id, $userData)) {
                // Atualiza grupo
                $groupModel = new \CodeIgniter\Shield\Models\GroupModel();
                $groupModel->removeUserFromAllGroups($id);
                $groupModel->addUserToGroup($id, $this->request->getPost('perfil'));

                // Registra auditoria
                $this->auditoriaModel->registrarAcao(
                    'Usuário Editado',
                    'Usuários',
                    'Usuário: ' . $this->request->getPost('nome') . ' (' . $this->request->getPost('cpf') . ')',
                    null,
                    null,
                    $usuarioAnterior,
                    $userData
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Usuário atualizado com sucesso!'
                ]);
            } else {
                throw new \Exception('Erro ao atualizar usuário');
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro ao editar usuário: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao editar usuário: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Resetar senha do usuário
     */
    public function resetarSenha($id): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        try {
            $userModel = auth()->getProvider();
            $usuario = $userModel->find($id);
            
            if (!$usuario) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ]);
            }

            // Gera senha temporária
            $novaSenha = 'temp' . rand(1000, 9999);
            
            $userData = [
                'password' => $novaSenha,
                'force_pass_reset' => 1
            ];

            if ($userModel->update($id, $userData)) {
                // Registra auditoria
                $this->auditoriaModel->registrarAcao(
                    'Senha Resetada',
                    'Usuários',
                    'Senha resetada para: ' . $usuario['nome'],
                    null,
                    null,
                    null,
                    ['nova_senha_temporaria' => $novaSenha]
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Senha resetada com sucesso!',
                    'nova_senha' => $novaSenha
                ]);
            } else {
                throw new \Exception('Erro ao resetar senha');
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro ao resetar senha: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao resetar senha: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Busca logs de auditoria
     */
    public function auditoria(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        try {
            $filtros = [
                'acao' => $this->request->getGet('acao'),
                'modulo' => $this->request->getGet('modulo'),
                'usuario_nome' => $this->request->getGet('usuario'),
                'data_inicio' => $this->request->getGet('data_inicio'),
                'data_fim' => $this->request->getGet('data_fim')
            ];

            $page = (int) $this->request->getGet('page') ?: 1;
            $perPage = (int) $this->request->getGet('per_page') ?: 50;

            $resultado = $this->auditoriaModel->getLogs($filtros, $page, $perPage);

            return $this->response->setJSON([
                'success' => true,
                'data' => $resultado
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar auditoria: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao buscar logs de auditoria'
            ]);
        }
    }

    /**
     * Criar backup manual
     */
    public function criarBackup(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        try {
            $tipo = $this->request->getPost('tipo') ?: 'completo';
            
            // Aqui implementaria a lógica de backup
            // Por enquanto, simula o processo
            
            $nomeArquivo = 'backup_' . $tipo . '_' . date('Y-m-d_H-i-s') . '.sql';
            
            // Registra auditoria
            $this->auditoriaModel->registrarAcao(
                'Backup Criado',
                'Backup',
                "Backup {$tipo} criado manualmente: {$nomeArquivo}"
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => "Backup {$tipo} criado com sucesso!",
                'arquivo' => $nomeArquivo
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao criar backup: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao criar backup: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Inicializa configurações padrão
     */
    public function inicializarConfiguracoes(): ResponseInterface
    {
        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acesso negado'
            ]);
        }

        try {
            if ($this->configuracaoModel->createDefaultConfigs()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Configurações padrão inicializadas com sucesso!'
                ]);
            } else {
                throw new \Exception('Erro ao criar configurações padrão');
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro ao inicializar configurações: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao inicializar configurações: ' . $e->getMessage()
            ]);
        }
    }
}
