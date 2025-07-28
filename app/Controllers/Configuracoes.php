<?php

namespace App\Controllers;

use App\Models\ConfiguracaoModel;
use App\Models\AuditoriaModel;
use CodeIgniter\HTTP\ResponseInterface;

class Configuracoes extends BaseController
{
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
        $currentUser = auth()->user();
        $db = \Config\Database::connect();
        
        // Query base com JOIN para buscar todos os dados de uma vez
        $builder = $db->table('users u')
            ->select('u.id, u.username, u.nome, u.cpf, u.active, u.last_active, ai.secret as email, gu.group as grupo_nome')
            ->join('auth_identities ai', 'ai.user_id = u.id AND ai.type = "email_password"', 'left')
            ->join('auth_groups_users gu', 'gu.user_id = u.id', 'left')
            ->where('u.deleted_at', null);

        // Nunca mostrar superadmins na listagem de usuários
        $builder->where('gu.group !=', 'superadmin');
        
        $usuarios = $builder->get()->getResultArray();
        
        // Processa os dados para garantir valores padrão
        foreach ($usuarios as &$usuario) {
            $usuario['nome'] = $usuario['nome'] ?? $usuario['username'];
            $usuario['cpf'] = $usuario['cpf'] ?? 'N/A';
            $usuario['email'] = $usuario['email'] ?? 'N/A';
            $usuario['grupo_nome'] = $usuario['grupo_nome'] ?? 'N/A';
            $usuario['last_active'] = $usuario['last_active'] ?? null;
            $usuario['active'] = (int)($usuario['active'] ?? 0);
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
            $currentUser = auth()->user();
            $db = \Config\Database::connect();
            
            log_message('info', 'Iniciando busca de usuários para refresh da tabela');
            
            // Query base com JOIN para buscar todos os dados de uma vez
            $builder = $db->table('users u')
                ->select('u.id, u.username, u.nome, u.cpf, u.active, u.last_active, ai.secret as email, gu.group as grupo_nome')
                ->join('auth_identities ai', 'ai.user_id = u.id AND ai.type = "email_password"', 'left')
                ->join('auth_groups_users gu', 'gu.user_id = u.id', 'left')
                ->where('u.deleted_at', null);

            // Nunca mostrar superadmins na listagem de usuários
            $builder->where('gu.group !=', 'superadmin');
            
            $usuarios = $builder->get()->getResultArray();
            
            log_message('info', 'Encontrados ' . count($usuarios) . ' usuários');
            
            // Processa os dados para garantir valores padrão
            foreach ($usuarios as &$usuario) {
                $usuario['nome'] = $usuario['nome'] ?? $usuario['username'];
                $usuario['cpf'] = $usuario['cpf'] ?? 'N/A';
                $usuario['email'] = $usuario['email'] ?? 'N/A';
                $usuario['grupo_nome'] = $usuario['grupo_nome'] ?? 'N/A';
                
                // Formatar last_active de forma mais robusta
                if (!$usuario['last_active'] || $usuario['last_active'] === '0000-00-00 00:00:00') {
                    $usuario['last_active'] = null;
                } else {
                    // Manter o formato ISO para o JavaScript processar corretamente
                    $usuario['last_active'] = date('c', strtotime($usuario['last_active']));
                }
                
                $usuario['active'] = (int)($usuario['active'] ?? 0);
            }

            log_message('info', 'Dados dos usuários processados com sucesso');

            return $this->response->setJSON([
                'success' => true,
                'data' => $usuarios
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar usuários: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao buscar usuários',
                'error' => $e->getMessage() // Incluir erro específico para debug
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

        $currentUser = auth()->user();
        $perfil = $this->request->getPost('perfil');

        // Admin não pode criar superadmin
        if ($currentUser->inGroup('admin') && !$currentUser->inGroup('superadmin') && $perfil === 'superadmin') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Você não tem permissão para criar usuários Super Admin.'
            ]);
        }

        // Normaliza o campo forcar_alteracao
        $forcarAlteracao = $this->request->getPost('forcar_alteracao') ? 1 : 0;
        $postData = array_merge($this->request->getPost(), ['forcar_alteracao' => $forcarAlteracao]);

        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'cpf' => 'required|exact_length[14]|is_unique[users.cpf]',
            'email' => 'permit_empty|valid_email|is_unique[auth_identities.secret]',
            'perfil' => 'required|in_list[superadmin,admin,medico,enfermeiro,farmaceutico,recepcionista,gestor,developer,user,beta]',
            'senha' => 'required|min_length[6]',
            'forcar_alteracao' => 'in_list[0,1]'
        ];

        if (!$this->validateData($postData, $rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $userModel = auth()->getProvider();
            
            // Usa uma transação para garantir a consistência
            $userModel->db->transStart();

            // Primeiro, cria o usuário básico via Shield
            $shieldData = [
                'username'         => $postData['cpf'],
                'email'            => $postData['email'],
                'password'         => $postData['senha'],
                'active'           => 1,
                'force_pass_reset' => $postData['forcar_alteracao'],
            ];

            $user = new \CodeIgniter\Shield\Entities\User($shieldData);
            
            if (!$userModel->save($user)) {
                throw new \Exception('Erro ao salvar usuário: ' . implode(', ', $userModel->errors()));
            }
            
            // Pega o ID do usuário recém-criado
            $userId = $userModel->getInsertID();
            
            // Agora atualiza os campos personalizados usando query builder diretamente
            $db = \Config\Database::connect();
            // Executa o update - não verifica retorno pois pode ser false mesmo em caso de sucesso
            $db->table('users')->where('id', $userId)->update([
                'nome' => $postData['nome'],
                'cpf'  => $postData['cpf'],
            ]);
            
            // Adiciona ao grupo
            $user = $userModel->findById($userId);
            if (!$user) {
                throw new \Exception('Usuário criado mas não encontrado para adicionar ao grupo');
            }
            
            $user->addGroup($postData['perfil']);

            $userModel->db->transComplete();

            if ($userModel->db->transStatus()) {
                // Registra auditoria
                $auditoriaData = [
                    'username' => $postData['cpf'],
                    'email' => $postData['email'],
                    'nome' => $postData['nome'],
                    'cpf' => $postData['cpf'],
                    'perfil' => $postData['perfil'],
                    'active' => 1,
                    'force_pass_reset' => $postData['forcar_alteracao']
                ];
                
                $this->auditoriaModel->registrarAcao(
                    'Usuário Criado',
                    'Usuários',
                    'Usuário: ' . $postData['nome'] . ' (' . $postData['cpf'] . ')',
                    null,
                    null,
                    null,
                    $auditoriaData
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Usuário criado com sucesso!'
                ]);
            } else {
                throw new \Exception('Erro na transação do banco de dados ao criar usuário.');
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro ao criar usuário: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
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

        $userModel = auth()->getProvider();
        $currentUser = auth()->user();
        $targetUser = $userModel->find($id);

        if (!$targetUser) {
            return $this->response->setJSON(['success' => false, 'message' => 'Usuário não encontrado.']);
        }

        // Admin não pode editar superadmin
        if ($currentUser->inGroup('admin') && !$currentUser->inGroup('superadmin') && $targetUser->inGroup('superadmin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Você não tem permissão para editar usuários Super Admin.'
            ]);
        }

        // Normaliza o campo ativo
        $ativo = $this->request->getPost('ativo') ? 1 : 0;
        $postData = array_merge($this->request->getPost(), ['ativo' => $ativo]);

        $rules = [
            'nome' => 'required|min_length[3]|max_length[255]',
            'cpf' => 'required|exact_length[14]|is_unique[users.cpf,id,' . $id . ']',
            'email' => 'permit_empty|valid_email|is_unique[auth_identities.secret,user_id,' . $id . ']',
            'perfil' => 'required|in_list[superadmin,admin,medico,enfermeiro,farmaceutico,recepcionista,gestor,developer,user,beta]',
            'ativo' => 'in_list[0,1]'
        ];

        if (!$this->validateData($postData, $rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados inválidos',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $usuarioAnterior = $userModel->asArray()->find($id);
            
            if (!$usuarioAnterior) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuário não encontrado'
                ]);
            }

            $userModel->db->transStart();

            // Atualiza dados básicos usando query builder diretamente
            $db = \Config\Database::connect();
            
            // Atualiza campos na tabela users
            $updateData = [
                'username' => $postData['cpf'],
                'active'   => $postData['ativo'],
                'nome'     => $postData['nome'],
                'cpf'      => $postData['cpf'],
            ];
            
            // Executa o update - não verifica retorno pois pode ser false mesmo em caso de sucesso
            $db->table('users')->where('id', $id)->update($updateData);

            // Atualiza email na tabela 'auth_identities'
            $user = $userModel->findById($id);
            $identityModel = new \CodeIgniter\Shield\Models\UserIdentityModel();
            $identity = $identityModel->where('user_id', $user->id)->where('type', 'email_password')->first();
            
            if ($identity) {
                $identity->secret = $postData['email'];
                $identityModel->save($identity);
            }

            // Atualiza grupo
            $user->syncGroups($postData['perfil']);

            $userModel->db->transComplete();

            if ($userModel->db->transStatus()) {
                // Registra auditoria
                $auditoriaData = [
                    'username' => $postData['cpf'],
                    'email' => $postData['email'],
                    'nome' => $postData['nome'],
                    'cpf' => $postData['cpf'],
                    'perfil' => $postData['perfil'],
                    'active' => $postData['ativo']
                ];
                
                $this->auditoriaModel->registrarAcao(
                    'Usuário Editado',
                    'Usuários',
                    'Usuário: ' . $postData['nome'] . ' (' . $postData['cpf'] . ')',
                    null,
                    null,
                    $usuarioAnterior,
                    $auditoriaData
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Usuário atualizado com sucesso!'
                ]);
            } else {
                throw new \Exception('Erro na transação do banco de dados ao editar usuário.');
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

        $userModel = auth()->getProvider();
        $currentUser = auth()->user();
        $targetUser = $userModel->find($id);

        if (!$targetUser) {
            return $this->response->setJSON(['success' => false, 'message' => 'Usuário não encontrado.']);
        }

        // Admin não pode resetar a senha de um superadmin
        if ($currentUser->inGroup('admin') && !$currentUser->inGroup('superadmin') && $targetUser->inGroup('superadmin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Você não tem permissão para resetar a senha de usuários Super Admin.'
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
            
            // Usa query builder diretamente para atualizar a senha
            $db = \Config\Database::connect();
            $passwordHash = password_hash($novaSenha, PASSWORD_DEFAULT);
            
            $db->table('users')->where('id', $id)->update([
                'password_hash' => $passwordHash,
                'force_pass_reset' => 1
            ]);

            // Registra auditoria
            $this->auditoriaModel->registrarAcao(
                'Senha Resetada',
                'Usuários',
                'Senha resetada para: ' . ($usuario->nome ?? $usuario->username),
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
            
            // Carrega a biblioteca de backup
            $backupManager = new \App\Libraries\BackupManager();
            
            // Verifica se mysqldump está disponível
            if (!$backupManager->verificarMysqldump()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'mysqldump não está disponível. Instale o MySQL client para usar esta funcionalidade.'
                ]);
            }

            $resultado = match($tipo) {
                'dados' => $backupManager->criarBackupDados('Backup manual via interface'),
                default => $backupManager->criarBackupCompleto('Backup manual via interface')
            };

            if ($resultado['sucesso']) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Backup {$tipo} criado com sucesso!",
                    'arquivo' => $resultado['arquivo'],
                    'tamanho' => $this->formatarTamanho($resultado['tamanho']),
                    'id' => $resultado['id']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao criar backup: ' . $resultado['erro']
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Erro ao criar backup: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao criar backup: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Formatar tamanho de arquivo
     */
    private function formatarTamanho(int $bytes): string
    {
        if ($bytes === 0) return '0 B';
        
        $unidades = ['B', 'KB', 'MB', 'GB', 'TB'];
        $i = floor(log($bytes, 1024));
        
        return round($bytes / pow(1024, $i), 2) . ' ' . $unidades[$i];
    }

    /**
     * Obter informações do último backup
     */
    public function ultimoBackup(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        try {
            $backupManager = new \App\Libraries\BackupManager();
            $ultimoBackup = $backupManager->getUltimoBackup();

            if ($ultimoBackup) {
                $info = sprintf(
                    '%s - %s (%s)',
                    date('d/m/Y H:i:s', strtotime($ultimoBackup['created_at'])),
                    ucfirst($ultimoBackup['tipo']),
                    $this->formatarTamanho($ultimoBackup['tamanho'])
                );
            } else {
                $info = 'Nenhum backup realizado ainda';
            }

            return $this->response->setJSON([
                'success' => true,
                'info' => $info
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'info' => 'Erro ao obter informações do backup'
            ]);
        }
    }

    /**
     * Busca histórico de backups com paginação e detalhes completos
     */
    public function historicoBackups(): ResponseInterface
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
            $backupModel = new \App\Models\BackupModel();
            
            $page = (int) $this->request->getGet('page') ?: 1;
            $limit = (int) $this->request->getGet('limit') ?: 10;
            
            $backups = $backupModel->orderBy('created_at', 'DESC')
                                 ->paginate($limit, 'default', $page);

            $pager = $backupModel->pager;

            // Processa os dados dos backups
            foreach ($backups as &$backup) {
                $backup['created_at'] = $backup['created_at'];
                $backup['tamanho'] = (int)$backup['tamanho'];
            //     $backup['duracao_segundos'] = (int)$backup['duracao_segundos'];
            }

            $pagination = [
                'current_page' => $page,
                'total_pages' => $pager->getPageCount(),
                'total_records' => $pager->getTotal(),
                'per_page' => $limit
            ];

            return $this->response->setJSON([
                'success' => true,
                'data' => $backups,
                'pagination' => $pagination
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar histórico de backups: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao carregar histórico de backups: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Restaurar backup
     */
    public function restaurarBackup(): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Apenas superadmins podem restaurar backups'
            ]);
        }

        try {
            $arquivo = $this->request->getFile('backup_file');
            
            if (!$arquivo || !$arquivo->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Arquivo de backup inválido'
                ]);
            }

            // Verifica extensão
            $extensoesPermitidas = ['sql', 'backup', 'zip'];
            if (!in_array($arquivo->getClientExtension(), $extensoesPermitidas)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tipo de arquivo não suportado'
                ]);
            }

            // Move arquivo para pasta temporária
            $nomeTemp = 'restore_' . time() . '.' . $arquivo->getClientExtension();
            $caminhoTemp = WRITEPATH . 'uploads/' . $nomeTemp;
            
            if (!$arquivo->move(WRITEPATH . 'uploads/', $nomeTemp)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao fazer upload do arquivo'
                ]);
            }

            // Executa restauração
            $backupManager = new \App\Libraries\BackupManager();
            $resultado = $backupManager->restaurarBackup($caminhoTemp);

            // Remove arquivo temporário
            @unlink($caminhoTemp);

            if ($resultado['sucesso']) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Backup restaurado com sucesso!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao restaurar backup: ' . $resultado['erro']
                ]);
            }

        } catch (\Exception $e) {
            // Remove arquivo temporário em caso de erro
            if (isset($caminhoTemp) && file_exists($caminhoTemp)) {
                @unlink($caminhoTemp);
            }
            
            log_message('error', 'Erro ao restaurar backup: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao restaurar backup: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Download de backup
     */
    public function downloadBackup(int $backupId): ResponseInterface
    {
        // Verifica permissão
        if (!auth()->user()->inGroup('superadmin', 'admin')) {
            return redirect()->back()->with('error', 'Acesso negado');
        }

        try {
            $backupModel = new \App\Models\BackupModel();
            $backup = $backupModel->find($backupId);

            if (!$backup) {
                return redirect()->back()->with('error', 'Backup não encontrado');
            }

            $caminhoArquivo = WRITEPATH . 'backups/' . $backup['nome_arquivo'];

            if (!file_exists($caminhoArquivo)) {
                return redirect()->back()->with('error', 'Arquivo de backup não encontrado no servidor');
            }

            return $this->response->download($caminhoArquivo, null);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao fazer download do backup: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao fazer download do backup');
        }
    }

    /**
     * Detalhes de um backup específico
     */
    public function detalhesBackup(int $backupId): ResponseInterface
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
            $backupModel = new \App\Models\BackupModel();
            $backup = $backupModel->find($backupId);

            if (!$backup) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Backup não encontrado'
                ]);
            }

            // Verifica se o arquivo ainda existe
            $caminhoArquivo = WRITEPATH . 'backups/' . $backup['nome_arquivo'];
            $backup['arquivo_existe'] = file_exists($caminhoArquivo);

            return $this->response->setJSON([
                'success' => true,
                'data' => $backup
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar detalhes do backup: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao carregar detalhes do backup'
            ]);
        }
    }

    /**
     * Excluir backup
     */
    public function excluirBackup(int $backupId): ResponseInterface
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Verifica permissão - apenas superadmin pode excluir backups
        if (!auth()->user()->inGroup('superadmin')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Apenas superadmins podem excluir backups'
            ]);
        }

        try {
            $backupModel = new \App\Models\BackupModel();
            $backup = $backupModel->find($backupId);

            if (!$backup) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Backup não encontrado'
                ]);
            }

            // Determina o caminho do arquivo
            // Verifica se tem caminho completo ou usa o padrão
            $caminhoArquivo = !empty($backup['caminho_arquivo']) 
                ? $backup['caminho_arquivo']
                : WRITEPATH . 'backups/' . $backup['nome_arquivo'];

            // Remove o arquivo físico se existir
            $arquivoRemovido = false;
            if (file_exists($caminhoArquivo)) {
                if (unlink($caminhoArquivo)) {
                    $arquivoRemovido = true;
                    log_message('info', "Arquivo de backup removido: {$caminhoArquivo}");
                } else {
                    log_message('warning', "Falha ao remover arquivo físico: {$caminhoArquivo}");
                    throw new \Exception('Erro ao excluir arquivo físico do backup');
                }
            } else {
                log_message('warning', "Arquivo de backup não encontrado: {$caminhoArquivo}");
                // Continua a execução para remover do banco mesmo sem o arquivo
            }

            // Remove do banco de dados
            if (!$backupModel->delete($backupId)) {
                throw new \Exception('Erro ao excluir registro do backup do banco de dados');
            }

            // Log de auditoria
            $this->auditoriaModel->registrarAcao(
                'DELETE',
                'Backup',
                "Backup excluído: {$backup['nome_arquivo']}" . ($arquivoRemovido ? ' (arquivo físico removido)' : ' (apenas registro do banco)')
            );

            $mensagem = 'Backup excluído com sucesso';
            if (!$arquivoRemovido && !file_exists($caminhoArquivo)) {
                $mensagem .= ' (arquivo físico não encontrado)';
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $mensagem
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao excluir backup ID ' . $backupId . ': ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao excluir backup: ' . $e->getMessage()
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
