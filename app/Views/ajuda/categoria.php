<?php echo $this->extend('layout/help'); ?>

<?php echo $this->section('styles'); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/ajuda.css') ?>">
<?php echo $this->endSection(); ?>

<?php echo $this->section('content'); ?>

<div class="app-container">
    <!-- Sidebar -->
    <?php echo $this->include('components/sidebar'); ?>

    <!-- Topbar -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="mobile-menu-toggle">
                <i class="bi bi-list"></i>
            </button>
            <h1 class="topbar-title"><?php echo esc($category['name']); ?></h1>
            <nav class="breadcrumb">
                <a href="<?php echo base_url('ajuda'); ?>" class="breadcrumb-link">Ajuda</a>
                <i class="bi bi-chevron-right"></i>
                <span><?php echo esc($category['name']); ?></span>
            </nav>
        </div>
        
        <div class="topbar-right">
            <?php echo $this->include('components/user_info'); ?>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="main-container">
            <!-- Header -->
            <div class="header">
                <h1><i class="bi bi-<?php echo $category['icon']; ?>"></i> <?php echo esc($category['name']); ?></h1>
                <p class="subtitle"><?php echo esc($category['description']); ?></p>
                <div class="category-meta">
                    <span class="badge bg-light text-dark">
                        <i class="bi bi-file-text"></i> <?php echo $category['articles_count']; ?> artigos disponíveis
                    </span>
                </div>
            </div>

            <!-- Content -->
            <div class="content-wrapper">
                <div class="row">
                    <div class="col-lg-8">
                        <!-- Articles List -->
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-list"></i>
                                Artigos da Categoria
                            </h2>
                            
                            <?php if (!empty($articles)): ?>
                            <div class="articles-grid">
                                <?php foreach ($articles as $article): ?>
                                <div class="article-card" onclick="showArticle('<?php echo $article['slug']; ?>')">
                                    <div class="article-card-header">
                                        <div class="article-icon">
                                            <i class="bi bi-<?php echo $article['icon']; ?> text-<?php echo $article['icon_color']; ?>"></i>
                                        </div>
                                        <div class="article-meta-info">
                                            <span class="article-views">
                                                <i class="bi bi-eye"></i> <?php echo number_format($article['views']); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="article-card-body">
                                        <h5><?php echo esc($article['title']); ?></h5>
                                        <p><?php echo esc($article['description']); ?></p>
                                    </div>
                                    <div class="article-card-footer">
                                        <button class="btn btn-outline-primary btn-sm">
                                            Ler Artigo <i class="bi bi-arrow-right"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-file-text-fill text-muted"></i>
                                <h4>Nenhum artigo encontrado</h4>
                                <p class="text-muted">Não há artigos disponíveis nesta categoria no momento.</p>
                                <a href="<?php echo base_url('ajuda'); ?>" class="btn btn-primary">
                                    <i class="bi bi-arrow-left"></i> Voltar à Central de Ajuda
                                </a>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Navigation -->
                        <div class="category-navigation">
                            <div class="row">
                                <div class="col-6">
                                    <a href="<?php echo base_url('ajuda'); ?>" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left"></i> Voltar à Central de Ajuda
                                    </a>
                                </div>
                                <div class="col-6 text-end">
                                    <button type="button" class="btn btn-outline-info" onclick="contactSupport()">
                                        <i class="bi bi-headset"></i> Precisa de Ajuda?
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Category Info -->
                        <div class="section-card" style="top: 2rem;">
                            <h5 class="section-title">
                                <i class="bi bi-info-circle"></i>
                                Sobre esta Categoria
                            </h5>
                            
                            <div class="category-info">
                                <div class="category-stats">
                                    <div class="stat-item">
                                        <div class="stat-icon bg-<?php echo $category['color']; ?>">
                                            <i class="bi bi-<?php echo $category['icon']; ?>"></i>
                                        </div>
                                        <div class="stat-content">
                                            <h6><?php echo $category['articles_count']; ?></h6>
                                            <p>Artigos Disponíveis</p>
                                        </div>
                                    </div>
                                </div>
                                
                                <p class="category-description"><?php echo esc($category['description']); ?></p>
                            </div>
                        </div>

                        <!-- Other Categories -->
                        <div class="section-card mt-3">
                            <h5 class="section-title">
                                <i class="bi bi-grid"></i>
                                Outras Categorias
                            </h5>
                            
                            <div class="other-categories">
                                <a href="<?php echo base_url('ajuda/categoria/primeiros-passos'); ?>" class="category-link <?php echo $category['slug'] === 'primeiros-passos' ? 'active' : ''; ?>">
                                    <i class="bi bi-play-circle text-primary"></i>
                                    Primeiros Passos
                                </a>
                                <a href="<?php echo base_url('ajuda/categoria/pacientes'); ?>" class="category-link <?php echo $category['slug'] === 'pacientes' ? 'active' : ''; ?>">
                                    <i class="bi bi-person-badge text-success"></i>
                                    Gestão de Pacientes
                                </a>
                                <a href="<?php echo base_url('ajuda/categoria/consultas'); ?>" class="category-link <?php echo $category['slug'] === 'consultas' ? 'active' : ''; ?>">
                                    <i class="bi bi-clipboard-check text-warning"></i>
                                    Consultas
                                </a>
                                <a href="<?php echo base_url('ajuda/categoria/medicamentos'); ?>" class="category-link <?php echo $category['slug'] === 'medicamentos' ? 'active' : ''; ?>">
                                    <i class="bi bi-capsule text-info"></i>
                                    Medicamentos
                                </a>
                            </div>
                        </div>

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
            <?php echo $this->include('components/footer'); ?>
        </div>
    </main>
</div>

<script>
// Show article
function showArticle(articleSlug) {
    window.location.href = `<?php echo base_url('ajuda/artigo'); ?>/${articleSlug}`;
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
.category-meta {
    margin-top: 1rem;
}

.articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.article-card {
    background: var(--white);
    border: 1px solid var(--medium-gray);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.2s ease;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.article-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
    border-color: var(--accent-color);
}

.article-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.article-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    border-radius: 8px;
    background: var(--light-gray);
}

.article-meta-info {
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.article-views {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.article-card-body {
    flex: 1;
    margin-bottom: 1rem;
}

.article-card-body h5 {
    color: var(--text-primary);
    margin-bottom: 0.5rem;
    font-size: 1.125rem;
    font-weight: 600;
}

.article-card-body p {
    color: var(--text-secondary);
    font-size: 0.875rem;
    line-height: 1.5;
    margin-bottom: 0;
}

.article-card-footer {
    margin-top: auto;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1rem;
}

.empty-state h4 {
    margin-bottom: 0.5rem;
}

.category-navigation {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--medium-gray);
}

.category-info {
    text-align: center;
}

.category-stats {
    margin-bottom: 1.5rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    background: var(--light-gray);
    padding: 1rem;
    border-radius: var(--border-radius);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.stat-content h6 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0;
    color: var(--text-primary);
}

.stat-content p {
    margin-bottom: 0;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.category-description {
    font-size: 0.875rem;
    color: var(--text-secondary);
    text-align: left;
}

.other-categories {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.category-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem;
    color: var(--text-primary);
    text-decoration: none;
    border-radius: var(--border-radius);
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.category-link:hover {
    background: var(--light-gray);
    color: var(--text-primary);
    border-color: var(--medium-gray);
}

.category-link.active {
    background: var(--accent-color);
    color: white;
    border-color: var(--accent-color);
}

.category-link.active i {
    color: white !important;
}

.contact-info {
    font-size: 0.875rem;
}

.breadcrumb-link {
    color: var(--text-secondary);
    text-decoration: none;
}

.breadcrumb-link:hover {
    color: var(--primary-color);
    text-decoration: underline;
}

@media (max-width: 768px) {
    .articles-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .article-card {
        padding: 1rem;
    }
    
    .stat-item {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
}
</style>

<?php echo $this->endSection(); ?>
