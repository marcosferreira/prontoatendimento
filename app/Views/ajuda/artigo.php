<?= $this->extend('layout/base') ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ajuda.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="app-container">
    <!-- Sidebar -->
    <?= $this->include('components/sidebar') ?>

    <!-- Topbar -->
    <?= $this->include('components/topbar') ?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-<?php echo $article['icon']; ?>"></i> <?php echo esc($article['title']); ?></h1>
                <p class="subtitle"><?php echo esc($article['description']); ?></p>
                <div class="article-meta">
                    <span class="badge bg-light text-dark me-2">
                        <i class="bi bi-eye"></i> <?php echo number_format($article['views']); ?> visualizações
                    </span>
                    <span class="badge bg-light text-dark">
                        <i class="bi bi-clock"></i> Última atualização: <?php echo date('d/m/Y'); ?>
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Article Content -->
                        <div class="section-card">
                            <div class="article-content">
                                <?php echo $article['content']; ?>
                            </div>
                            
                            <!-- Article Actions -->
                            <div class="article-actions mt-4 pt-4 border-top">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Este artigo foi útil?</h6>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-success btn-sm" onclick="rateArticle('helpful')">
                                                <i class="bi bi-hand-thumbs-up"></i> Sim
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="rateArticle('not-helpful')">
                                                <i class="bi bi-hand-thumbs-down"></i> Não
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="shareArticle()">
                                            <i class="bi bi-share"></i> Compartilhar
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printArticle()">
                                            <i class="bi bi-printer"></i> Imprimir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="article-navigation">
                            <div class="row">
                                <div class="col-6">
                                    <a href="<?php echo base_url('ajuda'); ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left"></i> Voltar à Central de Ajuda
                                    </a>
                                </div>
                                <div class="col-6 text-end">
                                    <button type="button" class="btn btn-outline-info" onclick="openTicket()">
                                        <i class="bi bi-headset"></i> Ainda precisa de ajuda?
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Table of Contents -->
                        <div class="section-card" style="top: 2rem;">
                            <h5 class="section-title">
                                <i class="bi bi-list-ul"></i>
                                Neste Artigo
                            </h5>
                            <div id="tableOfContents">
                                <!-- Será preenchido via JavaScript -->
                            </div>
                        </div>

                        <!-- Related Articles -->
                        <?php if (!empty($relatedArticles)): ?>
                        <div class="section-card mt-3">
                            <h5 class="section-title">
                                <i class="bi bi-bookmark"></i>
                                Artigos Relacionados
                            </h5>
                            
                            <?php foreach ($relatedArticles as $related): ?>
                            <div class="related-article mb-3">
                                <h6><a href="<?php echo base_url('ajuda/artigo/' . $related['slug']); ?>"><?php echo esc($related['title']); ?></a></h6>
                                <p class="text-muted small"><?php echo esc($related['description']); ?></p>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Quick Help -->
                        <div class="section-card mt-3">
                            <h5 class="section-title">
                                <i class="bi bi-question-circle"></i>
                                Precisa de Ajuda?
                            </h5>
                            
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary btn-sm" onclick="contactSupport()">
                                    <i class="bi bi-telephone"></i> Contatar Suporte
                                </button>
                                <button class="btn btn-outline-info btn-sm" onclick="openTicket()">
                                    <i class="bi bi-headset"></i> Abrir Chamado
                                </button>
                            </div>
                            
                            <hr>
                            
                            <div class="contact-info">
                                <p class="small mb-1"><strong>Telefone:</strong> (11) 3333-3333</p>
                                <p class="small mb-1"><strong>Email:</strong> suporte@pam.gov.br</p>
                                <p class="small mb-0"><strong>Horário:</strong> Seg-Sex, 8h às 18h</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="footer">
                <p>&copy; 2025 Sistema de Pronto Atendimento Municipal. Todos os direitos reservados. | Versão 2.1.0</p>
            </div>
        </div>
    </main>
</div>

<script>
// Generate table of contents
document.addEventListener('DOMContentLoaded', function() {
    generateTableOfContents();
});

function generateTableOfContents() {
    const content = document.querySelector('.article-content');
    const headings = content.querySelectorAll('h3, h4');
    const toc = document.getElementById('tableOfContents');
    
    if (headings.length === 0) {
        toc.innerHTML = '<p class="text-muted small">Nenhum tópico encontrado</p>';
        return;
    }
    
    let tocHtml = '<ul class="list-unstyled">';
    headings.forEach((heading, index) => {
        const id = `heading-${index}`;
        heading.id = id;
        
        const level = heading.tagName === 'H3' ? '' : 'ms-3';
        tocHtml += `<li class="${level}"><a href="#${id}" class="text-decoration-none toc-link">${heading.textContent}</a></li>`;
    });
    tocHtml += '</ul>';
    
    toc.innerHTML = tocHtml;
    
    // Smooth scroll para os links do índice
    document.querySelectorAll('.toc-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Rate article
function rateArticle(rating) {
    // Aqui você implementaria a lógica para avaliar o artigo
    const message = rating === 'helpful' ? 'Obrigado pelo feedback positivo!' : 'Obrigado pelo feedback. Vamos melhorar este artigo.';
    alert(message);
}

// Share article
function shareArticle() {
    if (navigator.share) {
        navigator.share({
            title: '<?php echo esc($article['title']); ?>',
            text: '<?php echo esc($article['description']); ?>',
            url: window.location.href
        });
    } else {
        // Fallback para navegadores que não suportam Web Share API
        navigator.clipboard.writeText(window.location.href).then(() => {
            alert('Link copiado para a área de transferência!');
        });
    }
}

// Print article
function printArticle() {
    window.print();
}

// Contact support
function contactSupport() {
    alert('Telefone: (11) 3333-3333\nEmail: suporte@pam.gov.br\nHorário: Segunda a Sexta, 8h às 18h');
}

// Open support ticket
function openTicket() {
    alert('Abrindo formulário de chamado de suporte...');
}
</script>

<style>
.article-meta {
    margin-top: 1rem;
}

.article-content {
    line-height: 1.7;
}

.article-content h3 {
    color: var(--primary-color);
    margin-top: 2rem;
    margin-bottom: 1rem;
    font-size: 1.375rem;
    font-weight: 600;
}

.article-content h4 {
    color: var(--text-primary);
    margin-top: 1.5rem;
    margin-bottom: 0.75rem;
    font-size: 1.125rem;
    font-weight: 600;
}

.article-content ol, .article-content ul {
    margin-bottom: 1.5rem;
}

.article-content li {
    margin-bottom: 0.5rem;
}

.article-content .alert {
    border-radius: 8px;
    border: none;
    padding: 1rem 1.25rem;
}

.article-navigation {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--medium-gray);
}

.related-article h6 a {
    color: var(--primary-color);
    text-decoration: none;
}

.related-article h6 a:hover {
    text-decoration: underline;
}

.contact-info {
    font-size: 0.875rem;
}

.toc-link {
    font-size: 0.875rem;
    color: var(--text-secondary);
    display: block;
    padding: 0.25rem 0;
    border-left: 2px solid transparent;
    padding-left: 0.5rem;
    margin-left: -0.5rem;
}

.toc-link:hover {
    color: var(--primary-color);
    border-left-color: var(--accent-color);
    background: var(--light-gray);
}

.breadcrumb-link {
    color: var(--text-secondary);
    text-decoration: none;
}

.breadcrumb-link:hover {
    color: var(--primary-color);
    text-decoration: underline;
}

@media print {
    .app-container > *:not(.main-content) {
        display: none !important;
    }
    
    .main-content {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .article-actions, 
    .article-navigation,
    .section-card:not(:first-child) {
        display: none !important;
    }
}
</style>

<?= $this->endSection() ?>
