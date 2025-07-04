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
            <h1 class="topbar-title">Central de Ajuda</h1>
            <nav class="breadcrumb">
                <span>Sistema</span>
                <i class="bi bi-chevron-right"></i>
                <span>Ajuda</span>
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
                <h1><i class="bi bi-question-circle"></i> Central de Ajuda</h1>
                <p class="subtitle">Documentação, Tutoriais e Suporte do SisPAM</p>
            </div>

            <!-- Content -->
            <div class="content-wrapper">
                <!-- Search Help -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="section-card">
                            <div class="help-search-header">
                                <h2>Como podemos ajudar você?</h2>
                                <p>Digite sua dúvida ou navegue pelas categorias abaixo</p>
                            </div>
                            <div class="row">
                                <div class="col-md-8 mx-auto">
                                    <div class="input-group input-group-lg">
                                        <input type="text" class="form-control" placeholder="Digite sua dúvida..." id="helpSearch">
                                        <button class="btn btn-primary" type="button" onclick="searchHelp()">
                                            <i class="bi bi-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Help Categories -->
                <div class="row mb-4">
                    <?php foreach ($categories as $category): ?>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="help-category-card" onclick="showCategory('<?php echo $category['slug']; ?>')">
                            <div class="help-icon bg-<?php echo $category['color']; ?>">
                                <i class="bi bi-<?php echo $category['icon']; ?>"></i>
                            </div>
                            <h5><?php echo esc($category['name']); ?></h5>
                            <p><?php echo esc($category['description']); ?></p>
                            <span class="help-badge"><?php echo $category['articles_count']; ?> artigos</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Popular Articles -->
                <div class="row mb-4">
                    <div class="col-lg-8">
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-star"></i>
                                Artigos Populares
                            </h2>
                            
                            <div class="help-article-list">
                                <?php foreach ($articles as $article): ?>
                                <div class="help-article" onclick="showArticle('<?php echo $article['slug']; ?>')">
                                    <div class="help-article-icon">
                                        <i class="bi bi-<?php echo $article['icon']; ?> text-<?php echo $article['icon_color']; ?>"></i>
                                    </div>
                                    <div class="help-article-content">
                                        <h5><?php echo esc($article['title']); ?></h5>
                                        <p><?php echo esc($article['description']); ?></p>
                                        <small class="text-muted">
                                            <i class="bi bi-eye"></i> <?php echo number_format($article['views']); ?> visualizações
                                        </small>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-4">
                        <!-- Quick Actions -->
                        <div class="section-card">
                            <h3 class="section-title">
                                <i class="bi bi-lightning"></i>
                                Ações Rápidas
                            </h3>
                            
                            <div class="d-grid gap-2">
                                <button class="btn btn-outline-primary" onclick="openVideoTutorial()">
                                    <i class="bi bi-play-circle"></i> Vídeo Tutorial
                                </button>
                                <button class="btn btn-outline-success" onclick="downloadManual()">
                                    <i class="bi bi-download"></i> Manual do Usuário
                                </button>
                                <button class="btn btn-outline-info" onclick="openTicket()">
                                    <i class="bi bi-headset"></i> Abrir Chamado
                                </button>
                                <button class="btn btn-outline-warning" onclick="contactSupport()">
                                    <i class="bi bi-telephone"></i> Contatar Suporte
                                </button>
                            </div>
                        </div>
                        
                        <!-- System Info -->
                        <div class="section-card mt-3">
                            <h3 class="section-title">
                                <i class="bi bi-info-circle"></i>
                                Informações do Sistema
                            </h3>
                            
                            <div class="system-info">
                                <p><strong>Versão:</strong> SisPAM v2.1.0</p>
                                <p><strong>Última Atualização:</strong> 01/06/2025</p>
                                <p><strong>Status:</strong> <span class="badge bg-success">Online</span></p>
                                <p><strong>Suporte:</strong> (11) 3333-3333</p>
                                <p><strong>Email:</strong> suporte@pam.gov.br</p>
                            </div>
                        </div>
                        
                        <!-- FAQ -->
                        <div class="section-card mt-3">
                            <h3 class="section-title">
                                <i class="bi bi-question-circle"></i>
                                FAQ Rápido
                            </h3>
                            
                            <div class="accordion accordion-flush" id="faqAccordion">
                                <?php foreach ($faq as $index => $item): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faq<?php echo $index + 1; ?>">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse<?php echo $index + 1; ?>">
                                            <?php echo esc($item['question']); ?>
                                        </button>
                                    </h2>
                                    <div id="faqCollapse<?php echo $index + 1; ?>" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            <?php echo esc($item['answer']); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Updates -->
                <div class="row">
                    <div class="col-12">
                        <div class="section-card">
                            <h2 class="section-title">
                                <i class="bi bi-clock-history"></i>
                                Atualizações Recentes
                            </h2>
                            
                            <div class="timeline">
                                <?php foreach ($updates as $update): ?>
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-<?php echo $update['type'] === 'major' ? 'primary' : ($update['type'] === 'patch' ? 'info' : 'success'); ?>"></div>
                                    <div class="timeline-content">
                                        <h6><?php echo esc($update['date']); ?> - Versão <?php echo esc($update['version']); ?></h6>
                                        <ul>
                                            <?php foreach ($update['changes'] as $change): ?>
                                            <li><?php echo esc($change); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                                <?php endforeach; ?>
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
// Search help with loading state
function searchHelp() {
    const searchInput = document.getElementById('helpSearch');
    const searchTerm = searchInput.value.trim();
    
    if (!searchTerm) {
        showAlert('Por favor, digite um termo de busca.', 'warning');
        return;
    }

    // Show loading state
    searchInput.disabled = true;
    const searchBtn = document.querySelector('button[onclick="searchHelp()"]');
    const originalBtnText = searchBtn.innerHTML;
    searchBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Buscando...';
    searchBtn.disabled = true;

    fetch(`<?php echo base_url('ajuda/search'); ?>?q=${encodeURIComponent(searchTerm)}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                displaySearchResults(data.results, searchTerm);
            } else {
                showAlert(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Erro na busca:', error);
            showAlert('Erro ao realizar busca. Tente novamente.', 'error');
        })
        .finally(() => {
            // Restore original state
            searchInput.disabled = false;
            searchBtn.innerHTML = originalBtnText;
            searchBtn.disabled = false;
        });
}

// Display search results in a modal
function displaySearchResults(results, searchTerm) {
    if (results.length === 0) {
        showAlert('Nenhum resultado encontrado para "' + searchTerm + '".', 'info');
        return;
    }
    
    let resultHtml = `
        <div class="modal fade" id="searchResultsModal" tabindex="-1" aria-labelledby="searchResultsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="searchResultsModalLabel">
                            <i class="bi bi-search"></i> Resultados da busca: "${searchTerm}"
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">Encontrados ${results.length} resultado(s)</p>
                        <div class="help-article-list">
    `;
    
    results.forEach(result => {
        resultHtml += `
            <div class="help-article" onclick="showArticle('${result.slug}'); bootstrap.Modal.getInstance(document.getElementById('searchResultsModal')).hide();">
                <div class="help-article-icon">
                    <i class="bi bi-${result.icon || 'file-text'} text-${result.icon_color || 'primary'}"></i>
                </div>
                <div class="help-article-content">
                    <h5>${result.title}</h5>
                    <p>${result.description}</p>
                    <small class="text-muted">
                        <i class="bi bi-eye"></i> ${result.views || 0} visualizações
                    </small>
                </div>
            </div>
        `;
    });
    
    resultHtml += `
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('searchResultsModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', resultHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('searchResultsModal'));
    modal.show();
}

// Enhanced category navigation
function showCategory(category) {
    // Add loading effect
    const categoryCard = event.currentTarget;
    categoryCard.classList.add('loading');
    
    setTimeout(() => {
        window.location.href = `<?php echo base_url('ajuda/categoria'); ?>/${category}`;
    }, 200);
}

// Enhanced article navigation
function showArticle(articleSlug) {
    // Add loading effect if element exists
    if (event && event.currentTarget) {
        event.currentTarget.classList.add('loading');
    }
    
    setTimeout(() => {
        window.location.href = `<?php echo base_url('ajuda/artigo'); ?>/${articleSlug}`;
    }, 200);
}

// Enhanced quick actions
function openVideoTutorial() {
    showAlert('Carregando vídeo tutorial do SisPAM...', 'info');
    // Simulate loading
    setTimeout(() => {
        window.open('https://www.youtube.com/watch?v=dQw4w9WgXcQ', '_blank');
    }, 1000);
}

function downloadManual() {
    showAlert('Preparando download do manual do usuário...', 'info');
    // Simulate file preparation
    setTimeout(() => {
        // In a real application, you would trigger an actual download
        showAlert('Manual do usuário baixado com sucesso!', 'success');
    }, 2000);
}

function openTicket() {
    const ticketHtml = `
        <div class="modal fade" id="ticketModal" tabindex="-1" aria-labelledby="ticketModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ticketModalLabel">
                            <i class="bi bi-headset"></i> Abrir Chamado de Suporte
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="ticketForm">
                            <div class="mb-3">
                                <label for="ticketSubject" class="form-label">Assunto *</label>
                                <select class="form-select" id="ticketSubject" required>
                                    <option value="">Selecione...</option>
                                    <option value="login">Problema de Login</option>
                                    <option value="performance">Sistema Lento</option>
                                    <option value="bug">Erro no Sistema</option>
                                    <option value="feature">Solicitação de Funcionalidade</option>
                                    <option value="training">Treinamento</option>
                                    <option value="other">Outro</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="ticketDescription" class="form-label">Descrição do Problema *</label>
                                <textarea class="form-control" id="ticketDescription" rows="4" required 
                                    placeholder="Descreva detalhadamente o problema ou dúvida..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="ticketPriority" class="form-label">Prioridade</label>
                                <select class="form-select" id="ticketPriority">
                                    <option value="low">Baixa</option>
                                    <option value="normal" selected>Normal</option>
                                    <option value="high">Alta</option>
                                    <option value="urgent">Urgente</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" onclick="submitTicket()">
                            <i class="bi bi-send"></i> Enviar Chamado
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('ticketModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', ticketHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('ticketModal'));
    modal.show();
}

function submitTicket() {
    const form = document.getElementById('ticketForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const submitBtn = event.currentTarget;
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';
    submitBtn.disabled = true;
    
    // Simulate API call
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        bootstrap.Modal.getInstance(document.getElementById('ticketModal')).hide();
        showAlert('Chamado enviado com sucesso! Você receberá uma resposta em breve.', 'success');
    }, 2000);
}

function contactSupport() {
    const contactHtml = `
        <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="contactModalLabel">
                            <i class="bi bi-telephone"></i> Informações de Contato
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-telephone-fill text-primary me-3"></i>
                                    <div>
                                        <strong>Telefone:</strong><br>
                                        <a href="tel:+551133333333" class="text-decoration-none">(11) 3333-3333</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-envelope-fill text-primary me-3"></i>
                                    <div>
                                        <strong>Email:</strong><br>
                                        <a href="mailto:suporte@pam.gov.br" class="text-decoration-none">suporte@pam.gov.br</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-clock-fill text-primary me-3"></i>
                                    <div>
                                        <strong>Horário de Atendimento:</strong><br>
                                        Segunda a Sexta: 8h às 18h<br>
                                        Sábado: 8h às 12h
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i>
                                    <strong>Dica:</strong> Para problemas urgentes, ligue diretamente. Para dúvidas gerais, prefira o email.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" onclick="window.open('tel:+551133333333')">
                            <i class="bi bi-telephone"></i> Ligar Agora
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('contactModal');
    if (existingModal) {
        existingModal.remove();
    }
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', contactHtml);
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('contactModal'));
    modal.show();
}

// Alert system
function showAlert(message, type = 'info') {
    const alertClass = {
        'success': 'alert-success',
        'error': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    };
    
    const iconClass = {
        'success': 'check-circle-fill',
        'error': 'exclamation-triangle-fill',
        'warning': 'exclamation-triangle-fill',
        'info': 'info-circle-fill'
    };
    
    const alertHtml = `
        <div class="alert ${alertClass[type]} alert-dismissible fade show position-fixed" 
             style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;" role="alert">
            <i class="bi bi-${iconClass[type]} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            if (alert.textContent.includes(message)) {
                alert.remove();
            }
        });
    }, 5000);
}

// Search on enter key
document.addEventListener('DOMContentLoaded', function() {
    const helpSearch = document.getElementById('helpSearch');
    if (helpSearch) {
        helpSearch.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchHelp();
            }
        });
        
        // Focus on search input when page loads
        helpSearch.focus();
    }
    
    // Add hover effects to category cards
    const categoryCards = document.querySelectorAll('.help-category-card');
    categoryCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', function() {
            if (!this.classList.contains('loading')) {
                this.style.transform = 'translateY(0)';
            }
        });
    });
    
    // Add click animation to articles
    const articles = document.querySelectorAll('.help-article');
    articles.forEach(article => {
        article.addEventListener('click', function() {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
});
</script>

<?php echo $this->section('scripts'); ?>
<script src="<?= base_url('assets/js/ajuda.js') ?>"></script>
<?php echo $this->endSection(); ?>

<?php echo $this->endSection(); ?>
