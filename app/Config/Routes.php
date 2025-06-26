<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/test-email', 'Home::testEmail');

// Admin routes (protected by superadmin middleware)
$routes->group('admin', static function ($routes) {
    $routes->get('/', 'Admin::index');
    $routes->get('dashboard', 'Admin::index');
    
    // User management
    $routes->get('users', 'Admin::users');
    $routes->get('users/create', 'Admin::createUser');
    $routes->post('users/store', 'Admin::storeUser');
    $routes->get('users/edit/(:num)', 'Admin::editUser/$1');
    $routes->post('users/update/(:num)', 'Admin::updateUser/$1');
    $routes->get('users/delete/(:num)', 'Admin::deleteUser/$1');
    
    // Settings
    $routes->get('settings', 'Admin::settings');
    
    // Logs
    $routes->get('logs', 'Admin::logs');
    
    // Reports
    $routes->get('reports', 'Admin::reports');
});

service('auth')->routes($routes);
