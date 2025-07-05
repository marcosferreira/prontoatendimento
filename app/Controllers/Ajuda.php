<?php

namespace App\Controllers;

class Ajuda extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'Central de Ajuda',
            'description' => 'Central de Ajuda - SisPAM',
            'keywords' => 'ajuda, suporte, documentação, tutorial, SisPAM',
            'articles' => $this->getPopularArticles(),
            'categories' => $this->getHelpCategories(),
            'faq' => $this->getFaqItems(),
            'updates' => $this->getRecentUpdates()
        ];
        
        return view('ajuda/index', $data);
    }

    /**
     * Busca ajuda por termo
     */
    public function search()
    {
        $searchTerm = $this->request->getGet('q') ?? '';
        
        if (empty($searchTerm)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Por favor, digite um termo de busca.'
            ]);
        }

        $results = $this->searchHelpContent($searchTerm);
        
        return $this->response->setJSON([
            'status' => 'success',
            'results' => $results,
            'total' => count($results)
        ]);
    }

    /**
     * Exibe artigos de uma categoria específica
     */
    public function categoria($categorySlug = null)
    {
        if (!$categorySlug) {
            return redirect()->to('/ajuda');
        }

        $category = $this->getCategoryBySlug($categorySlug);
        
        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $data = [
            'title' => $category['name'] . ' - Ajuda',
            'description' => $category['name'] . ' - Central de Ajuda',
            'keywords' => 'ajuda, ' . strtolower($category['name']) . ', SisPAM',
            'category' => $category,
            'articles' => $this->getArticlesByCategory($categorySlug)
        ];
        
        return view('ajuda/categoria', $data);
    }

    /**
     * Exibe um artigo específico
     */
    public function artigo($articleSlug = null)
    {
        if (!$articleSlug) {
            return redirect()->to('/ajuda');
        }

        $article = $this->getArticleBySlug($articleSlug);
        
        if (!$article) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        // Incrementa visualizações
        $this->incrementArticleViews($articleSlug);

        $data = [
            'title' => $article['title'] . ' - Ajuda',
            'description' => $article['description'],
            'keywords' => 'ajuda, tutorial, ' . strtolower($article['title']) . ', SisPAM',
            'article' => $article,
            'relatedArticles' => $this->getRelatedArticles($article['category'])
        ];
        
        return view('ajuda/artigo', $data);
    }

    /**
     * Retorna artigos populares
     */
    private function getPopularArticles(): array
    {
        return [
            [
                'slug' => 'login',
                'title' => 'Como fazer login no sistema',
                'description' => 'Passo a passo para acessar o SisPAM pela primeira vez',
                'views' => 1247,
                'icon' => 'box-arrow-in-right',
                'icon_color' => 'primary',
                'category' => 'primeiros-passos'
            ],
            [
                'slug' => 'cadastrar-paciente',
                'title' => 'Cadastrar novo paciente',
                'description' => 'Como registrar um novo paciente no sistema',
                'views' => 892,
                'icon' => 'person-plus',
                'icon_color' => 'success',
                'category' => 'pacientes'
            ],
            [
                'slug' => 'realizar-atendimento',
                'title' => 'Realizar atendimento médico',
                'description' => 'Fluxo completo do atendimento no pronto socorro',
                'views' => 756,
                'icon' => 'clipboard-pulse',
                'icon_color' => 'warning',
                'category' => 'consultas'
            ],
            [
                'slug' => 'prescrever-medicamentos',
                'title' => 'Prescrever medicamentos',
                'description' => 'Como criar prescrições e dispensar medicamentos',
                'views' => 634,
                'icon' => 'prescription',
                'icon_color' => 'info',
                'category' => 'medicamentos'
            ],
            [
                'slug' => 'gerar-relatorios',
                'title' => 'Gerar relatórios',
                'description' => 'Como acessar e exportar relatórios gerenciais',
                'views' => 423,
                'icon' => 'graph-up',
                'icon_color' => 'danger',
                'category' => 'relatorios'
            ],
            [
                'slug' => 'triagem-pacientes',
                'title' => 'Processo de triagem',
                'description' => 'Como realizar a classificação de risco dos pacientes',
                'views' => 387,
                'icon' => 'shield-check',
                'icon_color' => 'warning',
                'category' => 'consultas'
            ],
            [
                'slug' => 'gerenciar-estoque',
                'title' => 'Gerenciar estoque de medicamentos',
                'description' => 'Controle de entrada, saída e inventário',
                'views' => 298,
                'icon' => 'boxes',
                'icon_color' => 'info',
                'category' => 'medicamentos'
            ],
            [
                'slug' => 'backup-dados',
                'title' => 'Backup e recuperação de dados',
                'description' => 'Como fazer backup e restaurar informações do sistema',
                'views' => 234,
                'icon' => 'cloud-arrow-down',
                'icon_color' => 'primary',
                'category' => 'sistema'
            ],
            [
                'slug' => 'configuracoes-sistema',
                'title' => 'Configurações do Sistema',
                'description' => 'Como acessar e gerenciar configurações globais do SisPAM',
                'views' => 189,
                'icon' => 'gear-fill',
                'icon_color' => 'secondary',
                'category' => 'configuracoes'
            ],
            [
                'slug' => 'notificacoes-bi',
                'title' => 'Central de Notificações BI',
                'description' => 'Monitoramento inteligente e alertas do sistema',
                'views' => 145,
                'icon' => 'bell-fill',
                'icon_color' => 'warning',
                'category' => 'monitoramento'
            ],
            [
                'slug' => 'configurar-alertas',
                'title' => 'Configurar Alertas e Monitoramento',
                'description' => 'Como personalizar alertas e notificações automáticas',
                'views' => 112,
                'icon' => 'exclamation-triangle-fill',
                'icon_color' => 'danger',
                'category' => 'monitoramento'
            ],
            [
                'slug' => 'parametros-sistema',
                'title' => 'Gerenciar Parâmetros do Sistema',
                'description' => 'Configuração de limites, thresholds e comportamentos',
                'views' => 98,
                'icon' => 'sliders',
                'icon_color' => 'info',
                'category' => 'configuracoes'
            ],
            [
                'slug' => 'analise-bi-dados',
                'title' => 'Análise BI e Insights',
                'description' => 'Como interpretar dados e gráficos do sistema BI',
                'views' => 87,
                'icon' => 'graph-up-arrow',
                'icon_color' => 'success',
                'category' => 'monitoramento'
            ]
        ];
    }

    /**
     * Retorna categorias de ajuda
     */
    private function getHelpCategories(): array
    {
        return [
            [
                'slug' => 'primeiros-passos',
                'name' => 'Primeiros Passos',
                'description' => 'Como começar a usar o SisPAM',
                'icon' => 'play-circle',
                'color' => 'primary',
                'articles_count' => 8
            ],
            [
                'slug' => 'pacientes',
                'name' => 'Gestão de Pacientes',
                'description' => 'Cadastro, busca e atualização',
                'icon' => 'person-badge',
                'color' => 'success',
                'articles_count' => 12
            ],
            [
                'slug' => 'consultas',
                'name' => 'Consultas e Atendimentos',
                'description' => 'Atendimentos, triagem e prontuários',
                'icon' => 'clipboard-check',
                'color' => 'warning',
                'articles_count' => 15
            ],
            [
                'slug' => 'medicamentos',
                'name' => 'Medicamentos e Farmácia',
                'description' => 'Prescrições, estoque e dispensação',
                'icon' => 'capsule',
                'color' => 'info',
                'articles_count' => 10
            ],
            [
                'slug' => 'relatorios',
                'name' => 'Relatórios e Estatísticas',
                'description' => 'Geração e análise de dados',
                'icon' => 'graph-up',
                'color' => 'danger',
                'articles_count' => 8
            ],
            [
                'slug' => 'sistema',
                'name' => 'Configurações do Sistema',
                'description' => 'Backup, segurança e manutenção',
                'icon' => 'gear',
                'color' => 'secondary',
                'articles_count' => 6
            ],
            [
                'slug' => 'configuracoes',
                'name' => 'Configurações Avançadas',
                'description' => 'Parâmetros, limites e personalizações',
                'icon' => 'gear-fill',
                'color' => 'dark',
                'articles_count' => 8
            ],
            [
                'slug' => 'monitoramento',
                'name' => 'Monitoramento e BI',
                'description' => 'Alertas, notificações e análise inteligente',
                'icon' => 'activity',
                'color' => 'purple',
                'articles_count' => 12
            ]
        ];
    }

    /**
     * Retorna itens do FAQ
     */
    private function getFaqItems(): array
    {
        return [
            [
                'question' => 'Esqueci minha senha, como posso recuperá-la?',
                'answer' => 'Entre em contato com o administrador do sistema ou TI para resetar sua senha. Por segurança, não é possível recuperar senhas automaticamente.'
            ],
            [
                'question' => 'Como posso alterar meus dados pessoais?',
                'answer' => 'Acesse o menu "Configurações" > "Meu Perfil" para atualizar suas informações pessoais. Alguns dados podem exigir aprovação do administrador.'
            ],
            [
                'question' => 'O sistema está lento, o que fazer?',
                'answer' => 'Primeiro, verifique sua conexão com a internet. Feche abas desnecessárias do navegador. Se o problema persistir, entre em contato com o suporte técnico informando o horário e as ações que estava realizando.'
            ],
            [
                'question' => 'Como fazer backup dos dados?',
                'answer' => 'O backup é realizado automaticamente pelo sistema todos os dias às 2h da manhã. Para backups manuais emergenciais, procure o administrador do sistema.'
            ],
            [
                'question' => 'Posso acessar o sistema pelo celular?',
                'answer' => 'Sim, o SisPAM é totalmente responsivo e pode ser acessado através de qualquer dispositivo com navegador web atualizado.'
            ],
            [
                'question' => 'Erro ao imprimir documentos, como resolver?',
                'answer' => 'Verifique se a impressora está ligada e configurada. Tente gerar um PDF primeiro e depois imprimir. Se persistir, verifique os drivers da impressora.'
            ],
            [
                'question' => 'Como cancelar um atendimento já iniciado?',
                'answer' => 'Atendimentos podem ser cancelados apenas por médicos ou administradores através do menu "Ações" > "Cancelar Atendimento". É necessário informar o motivo do cancelamento.'
            ],
            [
                'question' => 'Posso fazer alterações em prontuários antigos?',
                'answer' => 'Por questões legais, prontuários finalizados não podem ser alterados. Em casos excepcionais, é possível adicionar adendos com aprovação do responsável técnico.'
            ],
            [
                'question' => 'Como verificar se um medicamento está em estoque?',
                'answer' => 'Acesse "Medicamentos" > "Estoque" ou, durante uma prescrição, o sistema indicará automaticamente a disponibilidade de cada medicamento.'
            ],
            [
                'question' => 'Sistema desconectou, perdi os dados que estava digitando?',
                'answer' => 'O sistema salva automaticamente os dados a cada 30 segundos. Ao reconectar, você deve encontrar suas informações na tela de rascunhos.'
            ],
            [
                'question' => 'Como interpretar as notificações BI do sistema?',
                'answer' => 'As notificações BI alertam sobre padrões anômalos: vermelho indica situações críticas que precisam ação imediata, amarelo são alertas que merecem atenção, e verde são informativos. Acesse a Central de Notificações para detalhes.'
            ],
            [
                'question' => 'Posso personalizar os alertas e limites do sistema?',
                'answer' => 'Sim, administradores podem acessar "Configurações" > "Parâmetros do Sistema" para ajustar limites de alertas, thresholds de monitoramento e configurações de notificações.'
            ],
            [
                'question' => 'O que fazer quando recebo uma notificação crítica?',
                'answer' => 'Notificações críticas exigem ação imediata. Clique na notificação para ver detalhes, siga as ações sugeridas pelo sistema e marque como resolvida após tomar as medidas apropriadas.'
            ],
            [
                'question' => 'Como acessar relatórios de análise BI?',
                'answer' => 'Acesse "Monitoramento" > "Central de Notificações BI" > "Relatórios". Lá você encontra análises de tendências, estatísticas de atendimento e insights sobre o funcionamento da unidade.'
            ],
            [
                'question' => 'Posso desativar certas notificações automáticas?',
                'answer' => 'Administradores podem configurar quais tipos de notificações são ativadas em "Configurações" > "Notificações BI". Não é recomendado desativar alertas críticos de segurança.'
            ]
        ];
    }

    /**
     * Retorna atualizações recentes
     */
    private function getRecentUpdates(): array
    {
        return [
            [
                'version' => '2.2.0',
                'date' => '05/07/2025',
                'type' => 'major',
                'changes' => [
                    'Nova Central de Notificações BI com monitoramento inteligente',
                    'Sistema de configurações avançadas e parâmetros personalizáveis',
                    'Análise automática de padrões e alertas preventivos',
                    'Dashboard interativo com gráficos Chart.js',
                    'Melhorias na segurança e auditoria do sistema'
                ]
            ],
            [
                'version' => '2.1.0',
                'date' => '01/06/2025',
                'type' => 'major',
                'changes' => [
                    'Nova interface para gestão de prontuários',
                    'Melhorias na performance do sistema',
                    'Correções de bugs reportados pelos usuários'
                ]
            ],
            [
                'version' => '2.0.3',
                'date' => '15/05/2025',
                'type' => 'patch',
                'changes' => [
                    'Correção na validação de CPF',
                    'Melhoria na busca de pacientes',
                    'Otimização do carregamento de relatórios'
                ]
            ],
            [
                'version' => '2.0.0',
                'date' => '01/05/2025',
                'type' => 'major',
                'changes' => [
                    'Nova arquitetura do sistema',
                    'Interface completamente redesenhada',
                    'Novos módulos de atendimento'
                ]
            ]
        ];
    }

    /**
     * Busca conteúdo de ajuda
     */
    private function searchHelpContent($searchTerm): array
    {
        $articles = $this->getPopularArticles();
        $categories = $this->getHelpCategories();
        $results = [];

        // Buscar em artigos
        foreach ($articles as $article) {
            $score = 0;
            
            // Busca no título (peso maior)
            if (stripos($article['title'], $searchTerm) !== false) {
                $score += 10;
            }
            
            // Busca na descrição
            if (stripos($article['description'], $searchTerm) !== false) {
                $score += 5;
            }
            
            // Busca na categoria
            if (stripos($article['category'], $searchTerm) !== false) {
                $score += 3;
            }
            
            // Palavras-chave específicas para novas funcionalidades
            $keywords = [
                'notificação' => ['notificacoes-bi', 'configurar-alertas', 'analise-bi-dados'],
                'bi' => ['notificacoes-bi', 'analise-bi-dados'],
                'alerta' => ['notificacoes-bi', 'configurar-alertas'],
                'configuração' => ['configuracoes-sistema', 'parametros-sistema'],
                'parâmetro' => ['parametros-sistema'],
                'monitoramento' => ['notificacoes-bi', 'analise-bi-dados'],
                'dashboard' => ['notificacoes-bi', 'analise-bi-dados'],
                'gráfico' => ['analise-bi-dados']
            ];
            
            foreach ($keywords as $keyword => $relatedSlugs) {
                if (stripos($searchTerm, $keyword) !== false && in_array($article['slug'], $relatedSlugs)) {
                    $score += 15;
                }
            }
            
            if ($score > 0) {
                $article['relevance_score'] = $score;
                $results[] = $article;
            }
        }

        // Ordenar por relevância
        usort($results, function($a, $b) {
            return $b['relevance_score'] - $a['relevance_score'];
        });

        return $results;
    }

    /**
     * Obtém categoria por slug
     */
    private function getCategoryBySlug($slug): ?array
    {
        $categories = $this->getHelpCategories();
        
        foreach ($categories as $category) {
            if ($category['slug'] === $slug) {
                return $category;
            }
        }

        return null;
    }

    /**
     * Obtém artigos por categoria
     */
    private function getArticlesByCategory($categorySlug): array
    {
        $allArticles = $this->getPopularArticles();
        $categoryArticles = [];

        foreach ($allArticles as $article) {
            if ($article['category'] === $categorySlug) {
                $categoryArticles[] = $article;
            }
        }

        // Se não encontrou artigos específicos, retorna os mais populares como fallback
        return !empty($categoryArticles) ? $categoryArticles : array_slice($allArticles, 0, 6);
    }

    /**
     * Obtém artigo por slug
     */
    private function getArticleBySlug($slug): ?array
    {
        $articles = $this->getPopularArticles();
        
        foreach ($articles as $article) {
            if ($article['slug'] === $slug) {
                // Adiciona conteúdo completo do artigo
                $article['content'] = $this->getArticleContent($slug);
                return $article;
            }
        }

        return null;
    }

    /**
     * Incrementa visualizações do artigo
     */
    private function incrementArticleViews($slug): void
    {
        // Aqui você implementaria a lógica para incrementar as visualizações
        // Pode ser em banco de dados ou arquivo de log
    }

    /**
     * Obtém artigos relacionados
     */
    private function getRelatedArticles($category): array
    {
        return array_slice($this->getPopularArticles(), 0, 3);
    }

    /**
     * Obtém conteúdo completo do artigo
     */
    private function getArticleContent($slug): string
    {
        $content = [
            'login' => $this->getLoginArticleContent(),
            'cadastrar-paciente' => $this->getCadastrarPacienteContent(),
            'realizar-atendimento' => $this->getRealizarAtendimentoContent(),
            'prescrever-medicamentos' => $this->getPrescreverMedicamentosContent(),
            'gerar-relatorios' => $this->getGerarRelatoriosContent(),
            'triagem-pacientes' => $this->getTriagemPacientesContent(),
            'gerenciar-estoque' => $this->getGerenciarEstoqueContent(),
            'backup-dados' => $this->getBackupDadosContent(),
            'configuracoes-sistema' => $this->getConfiguracoesSistemaContent(),
            'notificacoes-bi' => $this->getNotificacoesBIContent(),
            'configurar-alertas' => $this->getConfigurarAlertasContent(),
            'parametros-sistema' => $this->getParametrosSistemaContent(),
            'analise-bi-dados' => $this->getAnaliseBIDadosContent()
        ];

        return $content[$slug] ?? '<p>Conteúdo não encontrado.</p>';
    }

    private function getLoginArticleContent(): string
    {
        return '
        <h3>Passo a passo para fazer login</h3>
        <ol>
            <li><strong>Acesse o sistema:</strong> Digite o endereço do SisPAM no seu navegador</li>
            <li><strong>Insira suas credenciais:</strong> Digite seu usuário e senha nos campos correspondentes</li>
            <li><strong>Clique em "Entrar":</strong> Aguarde o carregamento do sistema</li>
            <li><strong>Primeiro acesso:</strong> Se for seu primeiro acesso, você será direcionado para alterar sua senha</li>
        </ol>
        
        <h4>Problemas comuns</h4>
        <ul>
            <li><strong>Senha incorreta:</strong> Verifique se o Caps Lock está desativado</li>
            <li><strong>Usuário bloqueado:</strong> Entre em contato com o administrador</li>
            <li><strong>Sistema indisponível:</strong> Verifique sua conexão com a internet</li>
        </ul>

        <div class="alert alert-info mt-3">
            <i class="bi bi-info-circle"></i>
            <strong>Dica:</strong> Mantenha sempre suas credenciais em segurança e não compartilhe com terceiros.
        </div>';
    }

    private function getCadastrarPacienteContent(): string
    {
        return '
        <h3>Como cadastrar um novo paciente</h3>
        <ol>
            <li><strong>Acesse o menu "Pacientes":</strong> Clique em "Pacientes" no menu lateral</li>
            <li><strong>Clique em "Novo Paciente":</strong> Botão localizado no canto superior direito</li>
            <li><strong>Preencha os dados obrigatórios:</strong>
                <ul>
                    <li>Nome completo</li>
                    <li>CPF (será validado automaticamente)</li>
                    <li>Data de nascimento</li>
                    <li>Telefone de contato</li>
                </ul>
            </li>
            <li><strong>Adicione informações complementares:</strong> Endereço, convênio, contato de emergência</li>
            <li><strong>Salve o cadastro:</strong> Clique em "Salvar" para finalizar</li>
        </ol>

        <h4>Campos obrigatórios</h4>
        <ul>
            <li>Nome completo</li>
            <li>CPF válido</li>
            <li>Data de nascimento</li>
            <li>Telefone principal</li>
        </ul>

        <div class="alert alert-warning mt-3">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Atenção:</strong> Certifique-se de que o CPF esteja correto, pois não será possível alterá-lo posteriormente.
        </div>';
    }

    private function getRealizarAtendimentoContent(): string
    {
        return '
        <h3>Fluxo do atendimento médico</h3>
        <ol>
            <li><strong>Busque o paciente:</strong> Use a busca por nome ou CPF</li>
            <li><strong>Inicie o atendimento:</strong> Clique em "Novo Atendimento"</li>
            <li><strong>Registre a triagem:</strong> Dados vitais e queixa principal</li>
            <li><strong>Realize a consulta:</strong> Anamnese, exame físico e diagnóstico</li>
            <li><strong>Prescreva tratamento:</strong> Medicamentos e orientações</li>
            <li><strong>Finalize o atendimento:</strong> Salve todas as informações</li>
        </ol>

        <h4>Documentos gerados</h4>
        <ul>
            <li>Prontuário médico</li>
            <li>Receita médica (se houver prescrição)</li>
            <li>Atestado médico (se necessário)</li>
            <li>Guias para exames (se solicitados)</li>
        </ul>';
    }

    private function getPrescreverMedicamentosContent(): string
    {
        return '
        <h3>Como prescrever medicamentos</h3>
        <ol>
            <li><strong>Durante o atendimento:</strong> Acesse a aba "Prescrição"</li>
            <li><strong>Busque o medicamento:</strong> Digite o nome ou princípio ativo</li>
            <li><strong>Defina a posologia:</strong> Dose, frequência e duração</li>
            <li><strong>Adicione orientações:</strong> Forma de uso e cuidados especiais</li>
            <li><strong>Finalize a prescrição:</strong> Revise e salve</li>
        </ol>

        <h4>Controle de estoque</h4>
        <p>O sistema automaticamente:</p>
        <ul>
            <li>Verifica disponibilidade no estoque</li>
            <li>Reserva a quantidade prescrita</li>
            <li>Gera alerta para medicamentos em falta</li>
            <li>Registra a dispensação na farmácia</li>
        </ul>';
    }

    private function getGerarRelatoriosContent(): string
    {
        return '
        <h3>Como gerar relatórios</h3>
        <ol>
            <li><strong>Acesse "Relatórios":</strong> Menu lateral > Relatórios</li>
            <li><strong>Escolha o tipo:</strong> Atendimentos, medicamentos, estatísticas</li>
            <li><strong>Defina o período:</strong> Data inicial e final</li>
            <li><strong>Aplique filtros:</strong> Médico, especialidade, convênio</li>
            <li><strong>Gere o relatório:</strong> Clique em "Gerar"</li>
            <li><strong>Exporte:</strong> PDF, Excel ou imprima diretamente</li>
        </ol>

        <h4>Tipos de relatórios disponíveis</h4>
        <ul>
            <li>Atendimentos realizados</li>
            <li>Medicamentos dispensados</li>
            <li>Estatísticas gerenciais</li>
            <li>Relatórios financeiros</li>
            <li>Indicadores de qualidade</li>
        </ul>';
    }

    private function getTriagemPacientesContent(): string
    {
        return '
        <h3>Processo de Triagem de Pacientes</h3>
        <p>A triagem é o primeiro passo do atendimento e determina a prioridade de cada paciente.</p>
        
        <h4>Classificação de Risco</h4>
        <ol>
            <li><strong>Vermelho (Emergência):</strong> Risco iminente de morte
                <ul>
                    <li>Parada cardiorrespiratória</li>
                    <li>Trauma grave</li>
                    <li>Choque</li>
                    <li>Coma</li>
                </ul>
            </li>
            <li><strong>Amarelo (Urgência):</strong> Risco de agravamento
                <ul>
                    <li>Dor intensa</li>
                    <li>Febre alta em crianças</li>
                    <li>Dificuldade respiratória moderada</li>
                </ul>
            </li>
            <li><strong>Verde (Pouco Urgente):</strong> Sem risco imediato
                <ul>
                    <li>Sintomas estáveis</li>
                    <li>Dor leve a moderada</li>
                    <li>Problemas crônicos controlados</li>
                </ul>
            </li>
            <li><strong>Azul (Não Urgente):</strong> Casos simples
                <ul>
                    <li>Consultas de rotina</li>
                    <li>Renovação de receitas</li>
                    <li>Curativos simples</li>
                </ul>
            </li>
        </ol>

        <h4>Como realizar a triagem</h4>
        <ol>
            <li><strong>Acolhimento:</strong> Receba o paciente de forma humanizada</li>
            <li><strong>Avaliação inicial:</strong> Ouça a queixa principal</li>
            <li><strong>Sinais vitais:</strong> Pressão, temperatura, frequência</li>
            <li><strong>Classifique o risco:</strong> Use os protocolos estabelecidos</li>
            <li><strong>Encaminhamento:</strong> Direcione para o atendimento adequado</li>
        </ol>

        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Importante:</strong> Em caso de dúvida na classificação, sempre opte pela categoria de maior prioridade.
        </div>';
    }

    private function getGerenciarEstoqueContent(): string
    {
        return '
        <h3>Gerenciamento de Estoque de Medicamentos</h3>
        <p>O controle adequado do estoque garante disponibilidade e evita desperdícios.</p>

        <h4>Entrada de Medicamentos</h4>
        <ol>
            <li><strong>Acesse:</strong> Medicamentos > Estoque > Entrada</li>
            <li><strong>Escaneie ou digite:</strong> Código de barras do produto</li>
            <li><strong>Confira:</strong> Nome, lote, validade e quantidade</li>
            <li><strong>Registre:</strong> Fornecedor, nota fiscal e data de entrada</li>
            <li><strong>Armazene:</strong> No local apropriado conforme temperatura</li>
        </ol>

        <h4>Saída de Medicamentos</h4>
        <ul>
            <li><strong>Dispensação automática:</strong> Sistema registra ao prescrever</li>
            <li><strong>Baixa manual:</strong> Para perdas, vencimentos ou transferências</li>
            <li><strong>Transferência:</strong> Entre setores ou unidades</li>
        </ul>

        <h4>Controle de Validade</h4>
        <ol>
            <li><strong>Relatório de vencimentos:</strong> Gere semanalmente</li>
            <li><strong>FEFO (First Expired, First Out):</strong> Use primeiro o que vence primeiro</li>
            <li><strong>Alertas automáticos:</strong> Sistema avisa 60 dias antes do vencimento</li>
            <li><strong>Segregação:</strong> Separe imediatamente medicamentos vencidos</li>
        </ol>

        <h4>Inventário</h4>
        <ol>
            <li><strong>Inventário mensal:</strong> Conte fisicamente todo o estoque</li>
            <li><strong>Acerto de divergências:</strong> Registre diferenças encontradas</li>
            <li><strong>Relatório de perdas:</strong> Documente perdas e motivos</li>
        </ol>

        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Dica:</strong> Configure alertas de estoque mínimo para evitar falta de medicamentos essenciais.
        </div>';
    }

    private function getBackupDadosContent(): string
    {
        return '
        <h3>Backup e Recuperação de Dados</h3>
        <p>O backup garante a segurança e integridade das informações do sistema.</p>

        <h4>Tipos de Backup</h4>
        <ul>
            <li><strong>Backup Automático Diário:</strong> Realizado todos os dias às 2h</li>
            <li><strong>Backup Manual:</strong> Pode ser executado quando necessário</li>
            <li><strong>Backup Incremental:</strong> Apenas dados alterados</li>
            <li><strong>Backup Completo:</strong> Todos os dados do sistema</li>
        </ul>

        <h4>Como verificar o backup</h4>
        <ol>
            <li><strong>Acesse:</strong> Configurações > Backup e Segurança</li>
            <li><strong>Status:</strong> Verifique data e hora do último backup</li>
            <li><strong>Logs:</strong> Consulte relatório de execução</li>
            <li><strong>Integridade:</strong> Execute teste de integridade mensal</li>
        </ol>

        <h4>Backup Manual de Emergência</h4>
        <ol>
            <li><strong>Acesse:</strong> Configurações > Backup</li>
            <li><strong>Clique:</strong> "Criar Backup Manual"</li>
            <li><strong>Selecione:</strong> Tipo de backup (completo ou incremental)</li>
            <li><strong>Aguarde:</strong> Processo de criação do backup</li>
            <li><strong>Confirme:</strong> Sucesso da operação</li>
        </ol>

        <h4>Restauração de Dados</h4>
        <ol>
            <li><strong>Identifique:</strong> Tipo de problema (hardware, software, dados)</li>
            <li><strong>Contate:</strong> Suporte técnico imediatamente</li>
            <li><strong>Informe:</strong> Data/hora do problema e último backup</li>
            <li><strong>Aguarde:</strong> Processo de restauração pelo técnico</li>
        </ol>

        <h4>Políticas de Retenção</h4>
        <ul>
            <li><strong>Backups diários:</strong> Mantidos por 30 dias</li>
            <li><strong>Backups semanais:</strong> Mantidos por 6 meses</li>
            <li><strong>Backups mensais:</strong> Mantidos por 2 anos</li>
            <li><strong>Backups anuais:</strong> Mantidos por 7 anos</li>
        </ul>

        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Atenção:</strong> NUNCA tente restaurar dados sem orientação técnica. Contate sempre o suporte.
        </div>';
    }

    private function getConfiguracoesSistemaContent(): string
    {
        return '
        <h3>Configurações do Sistema SisPAM</h3>
        <p>O sistema de configurações permite personalizar o comportamento do SisPAM de acordo com as necessidades da sua unidade de saúde.</p>

        <h4>Como acessar as configurações</h4>
        <ol>
            <li><strong>Acesse o menu:</strong> Clique em "Configurações" no menu lateral</li>
            <li><strong>Autenticação:</strong> Apenas administradores têm acesso completo</li>
            <li><strong>Categorias:</strong> As configurações são organizadas por módulos</li>
        </ol>

        <h4>Principais seções de configuração</h4>
        <ul>
            <li><strong>Geral:</strong> Nome da unidade, endereço, contatos</li>
            <li><strong>Atendimento:</strong> Horários, tipos de consulta, especialidades</li>
            <li><strong>Medicamentos:</strong> Controle de estoque, alertas de vencimento</li>
            <li><strong>Relatórios:</strong> Modelos, assinaturas digitais, cabeçalhos</li>
            <li><strong>Segurança:</strong> Políticas de senha, sessões, auditoria</li>
            <li><strong>Notificações:</strong> Alertas automáticos, limites, thresholds</li>
        </ul>

        <h4>Configurações importantes</h4>
        <ol>
            <li><strong>Horário de funcionamento:</strong> Define disponibilidade do sistema</li>
            <li><strong>Tempo de sessão:</strong> Controla logout automático por segurança</li>
            <li><strong>Backup automático:</strong> Frequência e retenção de backups</li>
            <li><strong>Alertas de estoque:</strong> Níveis mínimos de medicamentos</li>
            <li><strong>Integração:</strong> APIs externas e sistemas terceiros</li>
        </ol>

        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Atenção:</strong> Alterações nas configurações afetam todo o sistema. Sempre documente as mudanças e teste em ambiente controlado.
        </div>

        <h4>Backup de configurações</h4>
        <p>Antes de fazer alterações importantes:</p>
        <ol>
            <li>Acesse "Configurações" > "Backup/Restaurar"</li>
            <li>Clique em "Exportar Configurações Atuais"</li>
            <li>Salve o arquivo em local seguro</li>
            <li>Para restaurar, use "Importar Configurações"</li>
        </ol>';
    }

    private function getNotificacoesBIContent(): string
    {
        return '
        <h3>Central de Notificações BI</h3>
        <p>A Central de Notificações BI é um sistema inteligente que monitora o funcionamento do SisPAM e gera alertas automáticos sobre situações que merecem atenção.</p>

        <h4>Como acessar</h4>
        <ol>
            <li><strong>Menu principal:</strong> Clique em "Monitoramento" > "Notificações BI"</li>
            <li><strong>Dashboard:</strong> Visualize estatísticas e gráficos em tempo real</li>
            <li><strong>Lista de alertas:</strong> Veja todas as notificações ativas</li>
        </ol>

        <h4>Tipos de notificações</h4>
        <ul>
            <li><strong>Paciente Recorrente:</strong> Pacientes com muitos atendimentos em pouco tempo</li>
            <li><strong>Surto de Sintomas:</strong> Aumento anormal de casos similares em uma região</li>
            <li><strong>Alta Demanda:</strong> Sobrecarga no atendimento acima da capacidade</li>
            <li><strong>Anomalia Estatística:</strong> Padrões fora do esperado em indicadores</li>
            <li><strong>Medicamento Crítico:</strong> Estoque baixo de medicamentos essenciais</li>
            <li><strong>Equipamento:</strong> Falhas ou manutenção preventiva necessária</li>
        </ul>

        <h4>Níveis de severidade</h4>
        <ul>
            <li><strong class="text-danger">Crítica:</strong> Requer ação imediata (vermelho)</li>
            <li><strong class="text-warning">Alta:</strong> Importante, ação em até 2 horas (laranja)</li>
            <li><strong class="text-info">Média:</strong> Atenção necessária no dia (amarelo)</li>
            <li><strong class="text-success">Baixa:</strong> Informativo, ação quando possível (verde)</li>
        </ul>

        <h4>Como resolver notificações</h4>
        <ol>
            <li><strong>Clique na notificação:</strong> Para ver detalhes completos</li>
            <li><strong>Analise os dados:</strong> Gráficos e parâmetros relacionados</li>
            <li><strong>Siga as ações sugeridas:</strong> O sistema sugere medidas específicas</li>
            <li><strong>Execute as ações:</strong> Tome as medidas necessárias</li>
            <li><strong>Marque como resolvida:</strong> Com observações sobre o que foi feito</li>
        </ol>

        <h4>Dashboard interativo</h4>
        <ul>
            <li><strong>Gráfico de severidade:</strong> Distribuição dos alertas ativos</li>
            <li><strong>Gráfico de tipos:</strong> Quais problemas são mais frequentes</li>
            <li><strong>Tendência 7 dias:</strong> Evolução dos alertas na semana</li>
            <li><strong>Cards de estatísticas:</strong> Números consolidados</li>
        </ul>

        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Dica:</strong> O sistema aprende com suas ações. Quanto mais você resolve notificações adequadamente, mais precisos ficam os alertas futuros.
        </div>';
    }

    private function getConfigurarAlertasContent(): string
    {
        return '
        <h3>Configurar Alertas e Monitoramento</h3>
        <p>Personalize os alertas automáticos para atender às necessidades específicas da sua unidade de saúde.</p>

        <h4>Acessando configurações de alertas</h4>
        <ol>
            <li><strong>Acesse:</strong> Configurações > Notificações BI</li>
            <li><strong>Autenticação:</strong> Requer permissão de administrador</li>
            <li><strong>Categorias:</strong> Organizadas por tipo de alerta</li>
        </ol>

        <h4>Configurações por tipo de alerta</h4>

        <h5>📋 Paciente Recorrente</h5>
        <ul>
            <li><strong>Limite de atendimentos:</strong> Quantidade de consultas no período</li>
            <li><strong>Período de análise:</strong> Dias para considerar (padrão: 30 dias)</li>
            <li><strong>Severidade:</strong> Nível de alerta baseado na frequência</li>
            <li><strong>Exceções:</strong> Pacientes com condições crônicas conhecidas</li>
        </ul>

        <h5>🦠 Surto de Sintomas</h5>
        <ul>
            <li><strong>Threshold de casos:</strong> Número mínimo para gerar alerta</li>
            <li><strong>Aumento percentual:</strong> % de crescimento que dispara alerta</li>
            <li><strong>Período de monitoramento:</strong> Janela de tempo para análise</li>
            <li><strong>Sintomas monitorados:</strong> Lista de sintomas para vigilância</li>
        </ul>

        <h5>📈 Alta Demanda</h5>
        <ul>
            <li><strong>Capacidade máxima:</strong> Número de atendimentos simultâneos</li>
            <li><strong>Limite de fila:</strong> Quantidade máxima na espera</li>
            <li><strong>Tempo de espera:</strong> Minutos máximos aceitáveis</li>
            <li><strong>Horários críticos:</strong> Períodos de maior atenção</li>
        </ul>

        <h5>💊 Medicamentos Críticos</h5>
        <ul>
            <li><strong>Estoque mínimo:</strong> Quantidade que dispara alerta</li>
            <li><strong>Dias de antecedência:</strong> Prazo antes do vencimento</li>
            <li><strong>Medicamentos prioritários:</strong> Lista de essenciais</li>
            <li><strong>Fornecedores:</strong> Contatos para reposição urgente</li>
        </ul>

        <h4>Personalizações avançadas</h4>
        <ol>
            <li><strong>Horários de notificação:</strong> Quando enviar alertas</li>
            <li><strong>Canais de comunicação:</strong> Email, SMS, sistema interno</li>
            <li><strong>Responsáveis:</strong> Quem recebe cada tipo de alerta</li>
            <li><strong>Escalação:</strong> Para quem encaminhar se não resolvido</li>
        </ol>

        <h4>Testando configurações</h4>
        <ol>
            <li><strong>Modo teste:</strong> Ative para simular alertas</li>
            <li><strong>Dados históricos:</strong> Use para validar configurações</li>
            <li><strong>Feedback da equipe:</strong> Colete opiniões sobre efetividade</li>
            <li><strong>Ajustes finos:</strong> Refine baseado na experiência real</li>
        </ol>

        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Importante:</strong> Evite configurar muitos alertas de baixa relevância, pois pode gerar "fadiga de alerta" na equipe.
        </div>';
    }

    private function getParametrosSistemaContent(): string
    {
        return '
        <h3>Gerenciar Parâmetros do Sistema</h3>
        <p>Os parâmetros controlam comportamentos específicos do SisPAM, permitindo fine-tuning para otimizar a operação.</p>

        <h4>Categorias de parâmetros</h4>

        <h5>⚙️ Parâmetros Gerais</h5>
        <ul>
            <li><strong>Timeout de sessão:</strong> Tempo de inatividade antes do logout</li>
            <li><strong>Itens por página:</strong> Quantos registros mostrar em listas</li>
            <li><strong>Formato de data:</strong> Como exibir datas no sistema</li>
            <li><strong>Moeda padrão:</strong> Para valores financeiros</li>
            <li><strong>Fuso horário:</strong> Timezone da unidade</li>
        </ul>

        <h5>🏥 Parâmetros de Atendimento</h5>
        <ul>
            <li><strong>Tempo médio consulta:</strong> Duração estimada por tipo</li>
            <li><strong>Intervalo entre consultas:</strong> Tempo de preparação</li>
            <li><strong>Capacidade simultânea:</strong> Atendimentos paralelos</li>
            <li><strong>Horário de funcionamento:</strong> Início e fim das atividades</li>
            <li><strong>Tolerância atraso:</strong> Minutos aceitáveis de atraso</li>
        </ul>

        <h5>📊 Parâmetros de Análise BI</h5>
        <ul>
            <li><strong>Frequência de análise:</strong> A cada quantos minutos executar</li>
            <li><strong>Período de histórico:</strong> Quantos dias considerar</li>
            <li><strong>Sensibilidade alertas:</strong> Quão sensível aos padrões</li>
            <li><strong>Confiança estatística:</strong> Nível de certeza necessário</li>
            <li><strong>Retenção de dados:</strong> Por quanto tempo manter dados</li>
        </ul>

        <h5>🔒 Parâmetros de Segurança</h5>
        <ul>
            <li><strong>Complexidade senha:</strong> Regras para senhas válidas</li>
            <li><strong>Tentativas de login:</strong> Máximo antes de bloquear</li>
            <li><strong>Validade da senha:</strong> Dias antes de exigir troca</li>
            <li><strong>Sessões simultâneas:</strong> Quantas por usuário</li>
            <li><strong>Auditoria detalhada:</strong> Nível de logs de segurança</li>
        </ul>

        <h4>Como alterar parâmetros</h4>
        <ol>
            <li><strong>Acesse:</strong> Configurações > Parâmetros do Sistema</li>
            <li><strong>Selecione categoria:</strong> Use abas para navegar</li>
            <li><strong>Encontre o parâmetro:</strong> Use busca se necessário</li>
            <li><strong>Altere o valor:</strong> Digite novo valor ou use controles</li>
            <li><strong>Valide entrada:</strong> Sistema verifica se é válido</li>
            <li><strong>Salve alterações:</strong> Clique em "Aplicar"</li>
            <li><strong>Teste funcionamento:</strong> Verifique se funciona como esperado</li>
        </ol>

        <h4>Parâmetros críticos</h4>
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle"></i>
            <strong>Atenção especial para:</strong>
            <ul class="mb-0 mt-2">
                <li>Configurações de banco de dados</li>
                <li>Parâmetros de segurança</li>
                <li>Integrações com sistemas externos</li>
                <li>Configurações de backup</li>
            </ul>
        </div>

        <h4>Backup e restauração</h4>
        <ol>
            <li><strong>Backup automático:</strong> Feito antes de cada alteração</li>
            <li><strong>Backup manual:</strong> "Exportar Configurações Atuais"</li>
            <li><strong>Restauração:</strong> "Importar Configurações" ou "Restaurar Backup"</li>
            <li><strong>Valores padrão:</strong> "Restaurar Configurações de Fábrica"</li>
        </ol>

        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            <strong>Dica:</strong> Documente todas as alterações de parâmetros com data, motivo e responsável para facilitar troubleshooting futuro.
        </div>';
    }

    private function getAnaliseBIDadosContent(): string
    {
        return '
        <h3>Análise BI e Insights</h3>
        <p>O módulo de Business Intelligence analisa automaticamente os dados do SisPAM para identificar padrões, tendências e anomalias.</p>

        <h4>O que é analisado</h4>
        <ul>
            <li><strong>Padrões de atendimento:</strong> Horários de pico, sazonalidade</li>
            <li><strong>Perfil epidemiológico:</strong> Doenças mais comuns por região/época</li>
            <li><strong>Eficiência operacional:</strong> Tempos de espera, produtividade</li>
            <li><strong>Uso de recursos:</strong> Medicamentos, equipamentos, pessoal</li>
            <li><strong>Qualidade do cuidado:</strong> Indicadores de satisfação e desfecho</li>
        </ul>

        <h4>Tipos de gráficos e relatórios</h4>

        <h5>📊 Dashboard Principal</h5>
        <ul>
            <li><strong>Gráfico de pizza:</strong> Distribuição por severidade de alertas</li>
            <li><strong>Gráfico de barras:</strong> Tipos de notificações mais frequentes</li>
            <li><strong>Gráfico de linha:</strong> Tendência dos últimos 7 dias</li>
            <li><strong>Cards estatísticos:</strong> KPIs resumidos</li>
        </ul>

        <h5>📈 Análises Avançadas</h5>
        <ul>
            <li><strong>Heatmaps:</strong> Concentração geográfica de casos</li>
            <li><strong>Séries temporais:</strong> Evolução de indicadores ao longo do tempo</li>
            <li><strong>Correlações:</strong> Relações entre diferentes variáveis</li>
            <li><strong>Previsões:</strong> Projeções baseadas em dados históricos</li>
        </ul>

        <h4>Como interpretar os dados</h4>

        <h5>🔴 Alertas Críticos</h5>
        <ul>
            <li><strong>Ação:</strong> Requer intervenção imediata</li>
            <li><strong>Exemplo:</strong> Surto de doença infecciosa detectado</li>
            <li><strong>Resposta:</strong> Ativar protocolos de emergência</li>
        </ul>

        <h5>🟡 Alertas de Atenção</h5>
        <ul>
            <li><strong>Ação:</strong> Monitorar de perto, planejar intervenção</li>
            <li><strong>Exemplo:</strong> Aumento de 30% em determinado sintoma</li>
            <li><strong>Resposta:</strong> Investigar causas, preparar recursos</li>
        </ul>

        <h5>🟢 Informações</h5>
        <ul>
            <li><strong>Ação:</strong> Acompanhar, usar para planejamento</li>
            <li><strong>Exemplo:</strong> Tendência sazonal esperada</li>
            <li><strong>Resposta:</strong> Ajustar recursos conforme padrão</li>
        </ul>

        <h4>Utilizando insights para tomada de decisão</h4>
        <ol>
            <li><strong>Revisão diária:</strong> Verifique alertas e tendências</li>
            <li><strong>Análise semanal:</strong> Identifique padrões emergentes</li>
            <li><strong>Planejamento mensal:</strong> Use dados para alocar recursos</li>
            <li><strong>Avaliação trimestral:</strong> Ajuste processos e protocolos</li>
        </ol>

        <h4>Exportando e compartilhando dados</h4>
        <ul>
            <li><strong>PDF:</strong> Relatórios executivos para gestão</li>
            <li><strong>Excel:</strong> Dados detalhados para análise externa</li>
            <li><strong>Imagens:</strong> Gráficos para apresentações</li>
            <li><strong>API:</strong> Integração com outros sistemas</li>
        </ul>

        <h4>Configurando análises personalizadas</h4>
        <ol>
            <li><strong>Defina KPIs:</strong> Quais indicadores são importantes</li>
            <li><strong>Configure filtros:</strong> Foque nos dados relevantes</li>
            <li><strong>Agende relatórios:</strong> Automação de análises regulares</li>
            <li><strong>Personalize dashboards:</strong> Organize conforme necessidade</li>
        </ol>

        <div class="alert alert-success">
            <i class="bi bi-lightbulb"></i>
            <strong>Insight:</strong> O BI é mais efetivo quando usado consistentemente. Reserve tempo diário para revisar os indicadores e agir com base nos insights gerados.
        </div>

        <h4>Limitações e cuidados</h4>
        <ul>
            <li><strong>Qualidade dos dados:</strong> Análises dependem de dados bem registrados</li>
            <li><strong>Contexto local:</strong> Considere particularidades da sua região</li>
            <li><strong>Correlação vs causalidade:</strong> Nem toda correlação indica causa</li>
            <li><strong>Fatores externos:</strong> Eventos externos podem influenciar dados</li>
        </ul>';
    }

    /**
     * Retorna guias rápidos para novas funcionalidades
     */
    public function getQuickStartGuides(): array
    {
        return [
            [
                'title' => 'Central de Notificações BI',
                'description' => 'Sistema inteligente de monitoramento automático',
                'icon' => 'bell-fill',
                'color' => 'warning',
                'url' => 'notificacoes',
                'help_article' => 'notificacoes-bi',
                'steps' => [
                    'Acesse o menu "Monitoramento" > "Notificações BI"',
                    'Visualize alertas ativos no dashboard',
                    'Clique em uma notificação para ver detalhes',
                    'Execute ações sugeridas pelo sistema',
                    'Marque como resolvida após tratamento'
                ],
                'benefits' => [
                    'Detecção automática de padrões anômalos',
                    'Alertas preventivos em tempo real',
                    'Sugestões inteligentes de ação',
                    'Dashboard visual com gráficos',
                    'Histórico e tendências'
                ]
            ],
            [
                'title' => 'Sistema de Configurações',
                'description' => 'Personalize o SisPAM conforme sua necessidade',
                'icon' => 'gear-fill',
                'color' => 'secondary',
                'url' => 'configuracoes',
                'help_article' => 'configuracoes-sistema',
                'steps' => [
                    'Acesse "Configurações" no menu lateral',
                    'Escolha a categoria desejada',
                    'Ajuste os parâmetros conforme necessário',
                    'Salve as alterações',
                    'Teste o funcionamento'
                ],
                'benefits' => [
                    'Interface personalizada por unidade',
                    'Parâmetros flexíveis de operação',
                    'Backup automático de configurações',
                    'Controle granular de comportamentos',
                    'Integração com sistemas externos'
                ]
            ],
            [
                'title' => 'Análise BI e Insights',
                'description' => 'Relatórios inteligentes e análise de dados',
                'icon' => 'graph-up-arrow',
                'color' => 'info',
                'url' => 'notificacoes',
                'help_article' => 'analise-bi-dados',
                'steps' => [
                    'Acesse a Central de Notificações BI',
                    'Visualize gráficos no dashboard',
                    'Gere relatórios personalizados',
                    'Exporte dados para análise externa',
                    'Configure alertas personalizados'
                ],
                'benefits' => [
                    'Insights baseados em dados reais',
                    'Identificação de tendências',
                    'Otimização de recursos',
                    'Tomada de decisão informada',
                    'Prevenção de problemas'
                ]
            ]
        ];
    }

    /**
     * Página de guias rápidos
     */
    public function guias()
    {
        $data = [
            'title' => 'Guias Rápidos - Novas Funcionalidades',
            'description' => 'Aprenda a usar as novas funcionalidades do SisPAM',
            'keywords' => 'guias, tutorial, novas funcionalidades, SisPAM',
            'guides' => $this->getQuickStartGuides()
        ];
        
        return view('ajuda/guias', $data);
    }
}
