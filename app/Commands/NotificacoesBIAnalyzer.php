<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\NotificacaoAnalyzer;

class NotificacoesBIAnalyzer extends BaseCommand
{
    protected $group       = 'Sistema';
    protected $name        = 'notificacoes:analisar';
    protected $description = 'Executa análise BI para gerar notificações inteligentes';
    protected $usage       = 'notificacoes:analisar [opcoes]';
    protected $arguments   = [];
    protected $options     = [
        '--force' => 'Força a execução mesmo se houver análise recente',
        '--tipo'  => 'Executa apenas um tipo específico de análise',
        '--quiet' => 'Executa em modo silencioso'
    ];

    public function run(array $params)
    {
        $force = CLI::getOption('force');
        $tipo = CLI::getOption('tipo');
        $quiet = CLI::getOption('quiet');

        if (!$quiet) {
            CLI::write('═══════════════════════════════════════════════════════════', 'cyan');
            CLI::write('              ANÁLISE BI - NOTIFICAÇÕES INTELIGENTES       ', 'cyan');
            CLI::write('═══════════════════════════════════════════════════════════', 'cyan');
            CLI::newLine();
        }

        try {
            // Verifica se deve executar (evita análises muito frequentes)
            if (!$force && $this->verificarAnaliseRecente()) {
                if (!$quiet) {
                    CLI::write('Análise já executada recentemente. Use --force para forçar.', 'yellow');
                }
                return;
            }

            $analyzer = new NotificacaoAnalyzer();
            
            if (!$quiet) {
                CLI::write('🔍 Iniciando análise BI...', 'green');
                CLI::newLine();
            }

            // Marca início da análise
            $this->marcarInicioAnalise();

            // Executa análise
            $resultado = $analyzer->executarAnaliseCompleta();

            if ($resultado['success']) {
                if (!$quiet) {
                    CLI::write('✅ Análise concluída com sucesso!', 'green');
                    CLI::write("📊 {$resultado['notificacoes_criadas']} notificações criadas", 'cyan');
                    
                    // Exibe informações sobre dependências
                    $this->exibirInformacoesDependencias($resultado['dependencias']);
                    
                    // Exibe detalhes das análises executadas
                    $this->exibirDetalhesAnalises($resultado['analises_detalhadas']);
                    
                    // Exibe resumo detalhado
                    $this->exibirResumoAnalise();
                }
                
                // Registra sucesso
                $this->registrarResultadoAnalise(true, $resultado['notificacoes_criadas']);
                
            } else {
                CLI::write('❌ Erro na análise: ' . $resultado['error'], 'red');
                if (isset($resultado['dependencias'])) {
                    $this->exibirInformacoesDependencias($resultado['dependencias']);
                }
                $this->registrarResultadoAnalise(false, 0, $resultado['error']);
                return EXIT_ERROR;
            }

        } catch (\Exception $e) {
            CLI::write('💥 Erro crítico: ' . $e->getMessage(), 'red');
            $this->registrarResultadoAnalise(false, 0, $e->getMessage());
            return EXIT_ERROR;
        }

        if (!$quiet) {
            CLI::newLine();
            CLI::write('🎯 Análise BI finalizada!', 'green');
        }

        return EXIT_SUCCESS;
    }

    /**
     * Verifica se houve análise recente (últimas 2 horas)
     */
    protected function verificarAnaliseRecente(): bool
    {
        $cache = \Config\Services::cache();
        $ultimaAnalise = $cache->get('notificacoes_ultima_analise');
        
        if ($ultimaAnalise) {
            $diferencaMinutos = (time() - $ultimaAnalise) / 60;
            return $diferencaMinutos < 120; // 2 horas
        }
        
        return false;
    }

    /**
     * Marca o início da análise no cache
     */
    protected function marcarInicioAnalise(): void
    {
        $cache = \Config\Services::cache();
        $cache->save('notificacoes_ultima_analise', time(), 3600 * 24); // 24 horas
    }

    /**
     * Exibe resumo detalhado da análise
     */
    protected function exibirResumoAnalise(): void
    {
        $notificacaoModel = new \App\Models\NotificacaoModel();
        
        CLI::newLine();
        CLI::write('📈 RESUMO DA ANÁLISE:', 'yellow');
        CLI::write('─────────────────────', 'yellow');
        
        // Estatísticas por severidade
        $stats = $notificacaoModel->getEstatisticas();
        
        CLI::write("🔴 Críticas:      {$stats['criticas']}", 'red');
        CLI::write("🟠 Altas:         {$stats['altas']}", 'yellow');
        CLI::write("🟡 Médias:        {$stats['medias']}", 'cyan');
        CLI::write("🟢 Baixas:        {$stats['baixas']}", 'green');
        
        CLI::newLine();
        CLI::write('📊 POR TIPO:', 'yellow');
        CLI::write('─────────────', 'yellow');
        
        foreach ($stats['por_tipo'] as $tipo => $total) {
            $tipoFormatado = str_replace('_', ' ', ucwords($tipo));
            CLI::write("• {$tipoFormatado}: {$total}", 'white');
        }
        
        // Notificações críticas recentes
        $criticas = $notificacaoModel->getBySeveridade('critica');
        if (!empty($criticas)) {
            CLI::newLine();
            CLI::write('🚨 NOTIFICAÇÕES CRÍTICAS:', 'red');
            CLI::write('─────────────────────────', 'red');
            
            foreach (array_slice($criticas, 0, 3) as $critica) {
                CLI::write("• {$critica['titulo']}", 'red');
            }
            
            if (count($criticas) > 3) {
                CLI::write("... e mais " . (count($criticas) - 3) . " notificações críticas", 'red');
            }
        }
    }

    /**
     * Exibe informações sobre dependências de tabelas
     */
    protected function exibirInformacoesDependencias(array $dependencias): void
    {
        CLI::newLine();
        CLI::write('🔍 VERIFICAÇÃO DE DEPENDÊNCIAS:', 'yellow');
        CLI::write('─────────────────────────────────', 'yellow');
        
        $tabelas = ['notificacoes', 'atendimentos', 'pacientes', 'bairros', 'logradouros'];
        
        foreach ($tabelas as $tabela) {
            $existe = $dependencias[$tabela] ?? false;
            $status = $existe ? '✅' : '❌';
            $cor = $existe ? 'green' : 'red';
            CLI::write("  {$status} {$tabela}", $cor);
        }
        
        if (!$dependencias['todas_ok']) {
            CLI::newLine();
            CLI::write('⚠️  AVISO: Algumas tabelas não foram encontradas:', 'yellow');
            foreach ($dependencias['faltando'] as $tabela) {
                CLI::write("   • {$tabela}", 'red');
            }
            CLI::write('   As análises que dependem dessas tabelas serão ignoradas.', 'yellow');
        } else {
            CLI::write('✅ Todas as dependências estão disponíveis!', 'green');
        }
    }

    /**
     * Exibe detalhes das análises executadas
     */
    protected function exibirDetalhesAnalises(array $analises): void
    {
        CLI::newLine();
        CLI::write('📋 DETALHES DAS ANÁLISES:', 'yellow');
        CLI::write('─────────────────────────', 'yellow');
        
        $tiposAnalise = [
            'pacientes_recorrentes' => 'Pacientes Recorrentes',
            'surtos_sintomas' => 'Surtos de Sintomas',
            'alta_demanda' => 'Alta Demanda',
            'anomalias' => 'Anomalias Estatísticas',
            'classificacao_risco' => 'Classificação de Risco',
            'demonstracao' => 'Notificações de Demonstração'
        ];
        
        foreach ($tiposAnalise as $tipo => $nome) {
            if (isset($analises[$tipo])) {
                $analise = $analises[$tipo];
                
                if ($analise['executada']) {
                    $notifs = $analise['notificacoes'] ?? 0;
                    CLI::write("  ✅ {$nome}: {$notifs} notificações", 'green');
                } else {
                    $erro = $analise['erro'] ?? 'Erro desconhecido';
                    CLI::write("  ❌ {$nome}: {$erro}", 'red');
                }
            }
        }
    }

    /**
     * Registra resultado da análise para auditoria
     */
    protected function registrarResultadoAnalise(bool $sucesso, int $notificacoesCriadas, string $erro = null): void
    {
        $auditoriaModel = new \App\Models\AuditoriaModel();
        
        $detalhes = $sucesso 
            ? "Análise BI executada com sucesso. {$notificacoesCriadas} notificações criadas."
            : "Erro na análise BI: {$erro}";
        
        $auditoriaModel->registrarAcao(
            'Análise BI Executada',
            'Notificações',
            $detalhes,
            null, // user_id será null para comandos CLI
            'Sistema CLI',
            null,
            [
                'sucesso' => $sucesso,
                'notificacoes_criadas' => $notificacoesCriadas,
                'erro' => $erro,
                'executado_via' => 'CLI'
            ]
        );
    }

    /**
     * Exibe ajuda personalizada
     */
    public function showHelp(): void
    {
        CLI::write('═══════════════════════════════════════════════════════════', 'cyan');
        CLI::write('                    ANÁLISE BI - AJUDA                     ', 'cyan');
        CLI::write('═══════════════════════════════════════════════════════════', 'cyan');
        CLI::newLine();
        
        CLI::write('DESCRIÇÃO:', 'yellow');
        CLI::write('  Executa análise de Business Intelligence no sistema de');
        CLI::write('  pronto atendimento para gerar notificações inteligentes.');
        CLI::newLine();
        
        CLI::write('USO:', 'yellow');
        CLI::write('  php spark notificacoes:analisar [opções]');
        CLI::newLine();
        
        CLI::write('OPÇÕES:', 'yellow');
        CLI::write('  --force    Força execução mesmo com análise recente');
        CLI::write('  --tipo     Executa tipo específico de análise');
        CLI::write('  --quiet    Executa em modo silencioso');
        CLI::newLine();
        
        CLI::write('EXEMPLOS:', 'yellow');
        CLI::write('  php spark notificacoes:analisar');
        CLI::write('  php spark notificacoes:analisar --force');
        CLI::write('  php spark notificacoes:analisar --quiet');
        CLI::newLine();
        
        CLI::write('TIPOS DE ANÁLISE:', 'yellow');
        CLI::write('  • Pacientes recorrentes (> 3 atendimentos/mês)');
        CLI::write('  • Surtos de sintomas por região');
        CLI::write('  • Períodos de alta demanda');
        CLI::write('  • Anomalias estatísticas');
        CLI::write('  • Padrões de classificação de risco');
        CLI::newLine();
        
        CLI::write('NOTAS:', 'yellow');
        CLI::write('  - Análise automática executa a cada 2 horas');
        CLI::write('  - Use --force para executar manualmente');
        CLI::write('  - Resultados são salvos no log de auditoria');
        CLI::write('  - Notificações críticas requerem ação imediata');
        CLI::newLine();
    }
}
