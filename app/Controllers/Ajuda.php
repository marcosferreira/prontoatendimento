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
        $results = [];

        foreach ($articles as $article) {
            if (stripos($article['title'], $searchTerm) !== false || 
                stripos($article['description'], $searchTerm) !== false) {
                $results[] = $article;
            }
        }

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
        // Aqui você implementaria a lógica para buscar artigos de uma categoria específica
        // Por enquanto, retornamos alguns exemplos
        return $this->getPopularArticles();
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
            'backup-dados' => $this->getBackupDadosContent()
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
}
