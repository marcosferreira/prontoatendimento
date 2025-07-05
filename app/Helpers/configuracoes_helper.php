<?php

/**
 * Helper de Configurações
 * Sistema de Pronto Atendimento Municipal
 */

if (!function_exists('config_value')) {
    /**
     * Busca o valor de uma configuração do sistema
     *
     * @param string $chave Chave da configuração
     * @param mixed $default Valor padrão se não encontrar
     * @return mixed
     */
    function config_value(string $chave, $default = null)
    {
        static $configuracaoModel = null;
        
        if ($configuracaoModel === null) {
            $configuracaoModel = new \App\Models\ConfiguracaoModel();
        }
        
        try {
            $valor = $configuracaoModel->getByChave($chave);
            return $valor !== null ? $valor : $default;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar configuração: ' . $e->getMessage());
            return $default;
        }
    }
}

if (!function_exists('config_categoria')) {
    /**
     * Busca todas as configurações de uma categoria
     *
     * @param string $categoria Nome da categoria
     * @return array
     */
    function config_categoria(string $categoria): array
    {
        static $configuracaoModel = null;
        
        if ($configuracaoModel === null) {
            $configuracaoModel = new \App\Models\ConfiguracaoModel();
        }
        
        try {
            return $configuracaoModel->getByCategoria($categoria);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar configurações por categoria: ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('set_config_value')) {
    /**
     * Define o valor de uma configuração
     *
     * @param string $chave Chave da configuração
     * @param mixed $valor Valor a ser definido
     * @return bool
     */
    function set_config_value(string $chave, $valor): bool
    {
        static $configuracaoModel = null;
        
        if ($configuracaoModel === null) {
            $configuracaoModel = new \App\Models\ConfiguracaoModel();
        }
        
        try {
            return $configuracaoModel->updateByChave($chave, $valor);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao definir configuração: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('audit_log')) {
    /**
     * Registra uma ação no log de auditoria
     *
     * @param string $acao Ação realizada
     * @param string $modulo Módulo do sistema
     * @param string $detalhes Detalhes da ação
     * @param mixed $dadosAnteriores Dados antes da alteração
     * @param mixed $dadosNovos Dados após a alteração
     * @return bool
     */
    function audit_log(
        string $acao,
        string $modulo,
        string $detalhes = '',
        $dadosAnteriores = null,
        $dadosNovos = null
    ): bool {
        static $auditoriaModel = null;
        
        if ($auditoriaModel === null) {
            $auditoriaModel = new \App\Models\AuditoriaModel();
        }
        
        try {
            return $auditoriaModel->registrarAcao(
                $acao,
                $modulo,
                $detalhes,
                null,
                null,
                $dadosAnteriores,
                $dadosNovos
            );
        } catch (\Exception $e) {
            log_message('error', 'Erro ao registrar auditoria: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('sistema_info')) {
    /**
     * Retorna informações do sistema baseadas nas configurações
     *
     * @return array
     */
    function sistema_info(): array
    {
        return [
            'nome_unidade' => config_value('unidade_nome', 'SisPAM'),
            'versao' => '2.1.0',
            'timeout_sessao' => config_value('sistema_timeout_sessao', 60),
            'capacidade_maxima' => config_value('sistema_capacidade_maxima', 50),
            'tema' => config_value('aparencia_tema', 'claro'),
            'cor_primaria' => config_value('aparencia_cor_primaria', '#1e3a8a'),
        ];
    }
}

if (!function_exists('format_audit_action')) {
    /**
     * Formata o nome de uma ação de auditoria para exibição
     *
     * @param string $acao
     * @return string
     */
    function format_audit_action(string $acao): string
    {
        $formatMap = [
            'Login' => 'Login no Sistema',
            'Logout' => 'Logout do Sistema',
            'Cadastro' => 'Cadastro de Registro',
            'Edição' => 'Edição de Registro',
            'Exclusão' => 'Exclusão de Registro',
            'Consulta' => 'Consulta de Dados',
            'Backup' => 'Operação de Backup',
            'Configuração' => 'Alteração de Configuração',
            'Usuário Criado' => 'Criação de Usuário',
            'Usuário Editado' => 'Edição de Usuário',
            'Senha Resetada' => 'Reset de Senha',
            'Configurações Atualizadas' => 'Atualização de Configurações'
        ];
        
        return $formatMap[$acao] ?? $acao;
    }
}

if (!function_exists('backup_status')) {
    /**
     * Retorna o status do sistema de backup
     *
     * @return array
     */
    function backup_status(): array
    {
        return [
            'automatico_ativo' => config_value('backup_automatico_ativo', true),
            'frequencia' => config_value('backup_frequencia', 'diario'),
            'horario' => config_value('backup_horario', '02:00'),
            'retencao_dias' => config_value('backup_retencao_dias', 30),
            'ultimo_backup' => 'Em desenvolvimento' // Implementar lógica real
        ];
    }
}
