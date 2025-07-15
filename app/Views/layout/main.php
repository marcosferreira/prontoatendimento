<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title : 'SisPAM' ?> - <?= isset($description) ? $description : 'Sistema de Pronto Atendimento Municipal' ?></title>

    <!-- Meta tags -->
    <?php if (isset($description)): ?>
    <meta name="description" content="<?= esc($description) ?>">
    <?php endif; ?>
    
    <?php if (isset($keywords)): ?>
    <meta name="keywords" content="<?= esc($keywords) ?>">
    <?php endif; ?>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/topbar.css') ?>">
    
    <!-- Additional CSS -->
    <?= $this->renderSection('styles') ?>
</head>

<body>
    <script>
        // Aplica o estado da sidebar do localStorage antes do DOM carregar para evitar 'flicker'.
        // Isso garante que o layout não mude visualmente após a renderização inicial.
        (function() {
            try {
                const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                const isAdminCollapsed = localStorage.getItem('admin-sidebar-collapsed') === 'true';

                if (isCollapsed) {
                    document.body.classList.add('sidebar-collapsed');
                }
                // A verificação para admin é separada para permitir diferentes estados
                if (isAdminCollapsed) {
                    document.body.classList.add('admin-sidebar-collapsed');
                }
            } catch (e) {
                // Silencia erros caso o localStorage não esteja disponível.
            }
        })();
    </script>
    <div class="app-container">
        <?= $this->include('components/sidebar') ?>

        <?= $this->include('components/topbar') ?>

        <main class="main-content">
            <?= $this->renderSection('content') ?>
            
            <!-- Footer -->
            <div class="footer">
                <p>&copy; <?= date('Y') ?> Sistema de Pronto Atendimento Municipal. Todos os direitos reservados. | Versão 2.1.0</p>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    <script src="<?= base_url('assets/js/main.js') ?>"></script>

    <!-- Page specific scripts -->
    <?php if (isset($script)): ?>
    <script src="<?= base_url('assets/js/' . $script . '.js') ?>"></script>
    <?php endif; ?>
    
    <!-- Additional scripts -->
    <?= $this->renderSection('scripts') ?>
</body>

</html>
