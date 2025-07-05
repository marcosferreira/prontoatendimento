<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $description ?> - Dashboard | Pronto Atendimento Municipal</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Boostrap CSS 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url('assets/css/style.css'); ?>">
    <?php echo $this->renderSection('styles'); ?>
</head>

<body>

    <?php echo $this->renderSection("content"); ?>
    <!-- Bootstrap JS 5 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    
    <!-- Custom JavaScript -->
    <script>
        // Sidebar toggle functionality for all screen sizes
        document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
            const sidebar = document.querySelector('.sidebar');
            const body = document.body;
            
            if (window.innerWidth <= 768) {
                // Mobile behavior: toggle mobile-open class
                sidebar.classList.toggle('mobile-open');
            } else {
                // Desktop behavior: toggle collapsed state
                sidebar.classList.toggle('sidebar-mini');
                body.classList.toggle('sidebar-collapsed');
                
                // Save collapsed state in localStorage
                if (body.classList.contains('sidebar-collapsed')) {
                    localStorage.setItem('sidebar-collapsed', 'true');
                } else {
                    localStorage.removeItem('sidebar-collapsed');
                }
            }
        });
        
        // Restore sidebar state on page load for desktop
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth > 768) {
                const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                if (isCollapsed) {
                    document.querySelector('.sidebar').classList.add('sidebar-mini');
                    document.body.classList.add('sidebar-collapsed');
                }
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.querySelector('.sidebar');
            const body = document.body;
            
            if (window.innerWidth <= 768) {
                // Mobile: remove desktop classes and ensure mobile behavior
                sidebar.classList.remove('sidebar-mini');
                body.classList.remove('sidebar-collapsed');
            } else {
                // Desktop: remove mobile classes and restore saved state
                sidebar.classList.remove('mobile-open');
                const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
                if (isCollapsed) {
                    sidebar.classList.add('sidebar-mini');
                    body.classList.add('sidebar-collapsed');
                }
            }
        });
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth <= 768) {
                const sidebar = document.querySelector('.sidebar');
                const toggle = document.querySelector('.mobile-menu-toggle');
                
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('mobile-open');
                }
            }
        });
        
        // Update current time
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('pt-BR');
            const dateString = now.toLocaleDateString('pt-BR');
            
            // You can add a time display element if needed
            console.log(`${dateString} ${timeString}`);
        }
        
        setInterval(updateTime, 1000);
    </script>
    <?php echo $this->renderSection('scripts'); ?>
</body>

</html>