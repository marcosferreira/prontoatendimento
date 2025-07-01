<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/test-email', 'Home::testEmail');

// Rotas de Pacientes
$routes->group('pacientes', static function ($routes) {
    $routes->get('/', 'Pacientes::index');
    $routes->get('create', 'Pacientes::create');
    $routes->post('store', 'Pacientes::store');
    $routes->get('show/(:num)', 'Pacientes::show/$1');
    $routes->get('edit/(:num)', 'Pacientes::edit/$1');
    $routes->post('update/(:num)', 'Pacientes::update/$1');
    $routes->get('delete/(:num)', 'Pacientes::delete/$1');
    
    // AJAX routes
    $routes->get('search', 'Pacientes::search');
    $routes->get('modal/(:num)', 'Pacientes::modal/$1');
    $routes->post('validate-cpf', 'Pacientes::validateCpf');
    
    // Print and export
    $routes->get('print/(:num)', 'Pacientes::print/$1');
    $routes->get('export', 'Pacientes::export');
});

// Rotas de Bairros
$routes->group('bairros', static function ($routes) {
    $routes->get('/', 'Bairros::index');
    $routes->get('create', 'Bairros::create');
    $routes->post('/', 'Bairros::store');
    $routes->get('(:num)', 'Bairros::show/$1');
    $routes->get('(:num)/edit', 'Bairros::edit/$1');
    $routes->put('(:num)', 'Bairros::update/$1');
    $routes->delete('(:num)', 'Bairros::delete/$1');
    
    // AJAX routes
    $routes->get('search', 'Bairros::search');
    $routes->get('(:num)/modal', 'Bairros::modal/$1');
    $routes->post('validateNome', 'Bairros::validateNome');
    
    // Export
    $routes->get('export', 'Bairros::export');
});

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
