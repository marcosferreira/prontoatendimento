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
    $routes->get('(:num)', 'Pacientes::show/$1'); // Rota alternativa para show
    $routes->post('(:num)', 'Pacientes::update/$1'); // Rota para update via POST
    $routes->put('(:num)', 'Pacientes::update/$1'); // Rota para update via PUT
    $routes->get('edit/(:num)', 'Pacientes::edit/$1');
    $routes->get('(:num)/edit', 'Pacientes::edit/$1'); // Rota alternativa para edit
    $routes->post('update/(:num)', 'Pacientes::update/$1');
    $routes->post('(:num)/update', 'Pacientes::update/$1'); // Rota alternativa para update
    $routes->get('delete/(:num)', 'Pacientes::delete/$1');
    
    // AJAX routes
    $routes->get('search', 'Pacientes::search');
    $routes->get('modal/(:num)', 'Pacientes::modal/$1');
    $routes->post('validate-cpf', 'Pacientes::validateCpf');
    $routes->get('logradouros-por-bairro', 'Pacientes::getLogradourosByBairro');
    
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

// Rotas de Logradouros
$routes->group('logradouros', static function ($routes) {
    $routes->get('/', 'Logradouros::index');
    $routes->get('create', 'Logradouros::create');
    $routes->post('/', 'Logradouros::store');
    $routes->get('(:num)', 'Logradouros::show/$1');
    $routes->get('(:num)/edit', 'Logradouros::edit/$1');
    $routes->put('(:num)', 'Logradouros::update/$1');
    $routes->post('(:num)/update', 'Logradouros::update/$1');
    $routes->delete('(:num)', 'Logradouros::delete/$1');
    $routes->get('delete/(:num)', 'Logradouros::delete/$1');
    
    // API routes
    $routes->get('api/bairro/(:num)', 'Logradouros::getByBairro/$1');
    $routes->get('api/cep/(:segment)', 'Logradouros::getByCep/$1');
    $routes->get('api/search', 'Logradouros::search');
    
    // Export
    $routes->get('export', 'Logradouros::export');
});

// Rotas de Médicos
$routes->group('medicos', static function ($routes) {
    $routes->get('/', 'Medicos::index');
    $routes->get('create', 'Medicos::create');
    $routes->post('store', 'Medicos::store');
    $routes->get('show/(:num)', 'Medicos::show/$1');
    $routes->get('(:num)', 'Medicos::show/$1');
    $routes->get('edit/(:num)', 'Medicos::edit/$1');
    $routes->post('update/(:num)', 'Medicos::update/$1');
    $routes->delete('delete/(:num)', 'Medicos::delete/$1');
    
    // AJAX routes
    $routes->post('search', 'Medicos::search');
    $routes->get('ativos', 'Medicos::getAtivos');
    
    // Export
    $routes->get('export', 'Medicos::export');
});

// Rotas de Procedimentos
$routes->group('procedimentos', static function ($routes) {
    $routes->get('/', 'Procedimentos::index');
    $routes->get('create', 'Procedimentos::create');
    $routes->post('store', 'Procedimentos::store');
    $routes->get('show/(:num)', 'Procedimentos::show/$1');
    $routes->get('(:num)', 'Procedimentos::show/$1');
    $routes->get('edit/(:num)', 'Procedimentos::edit/$1');
    $routes->post('update/(:num)', 'Procedimentos::update/$1');
    $routes->delete('delete/(:num)', 'Procedimentos::delete/$1');
    
    // AJAX routes
    $routes->post('search', 'Procedimentos::search');
    $routes->get('all', 'Procedimentos::getAll');
    
    // Export
    $routes->get('export', 'Procedimentos::export');
});

// Rotas de Exames
$routes->group('exames', static function ($routes) {
    $routes->get('/', 'Exames::index');
    $routes->get('create', 'Exames::create');
    $routes->post('store', 'Exames::store');
    $routes->get('show/(:num)', 'Exames::show/$1');
    $routes->get('(:num)', 'Exames::show/$1');
    $routes->get('edit/(:num)', 'Exames::edit/$1');
    $routes->post('update/(:num)', 'Exames::update/$1');
    $routes->delete('delete/(:num)', 'Exames::delete/$1');
    
    // AJAX routes
    $routes->get('modal/(:num)', 'Exames::modal/$1');
    $routes->post('search', 'Exames::search');
    $routes->get('tipo/(:segment)', 'Exames::getByTipo/$1');
    $routes->get('all', 'Exames::getAll');
    
    // Export
    $routes->get('export', 'Exames::export');
});

// Rotas de Atendimentos
$routes->group('atendimentos', static function ($routes) {
    $routes->get('/', 'Atendimentos::index');
    $routes->get('create', 'Atendimentos::create');
    $routes->post('store', 'Atendimentos::store');
    $routes->get('show/(:num)', 'Atendimentos::show/$1');
    $routes->get('(:num)', 'Atendimentos::show/$1');
    $routes->get('edit/(:num)', 'Atendimentos::edit/$1');
    $routes->post('update/(:num)', 'Atendimentos::update/$1');
    $routes->delete('delete/(:num)', 'Atendimentos::delete/$1');
    
    // Relatórios
    $routes->get('relatorio', 'Atendimentos::relatorio');
    
    // Export
    $routes->get('export', 'Atendimentos::export');
});

// Rotas de Atendimento Procedimentos
$routes->group('atendimento_procedimentos', static function ($routes) {
    $routes->get('/', 'AtendimentoProcedimentos::index');
    $routes->get('create', 'AtendimentoProcedimentos::create');
    $routes->post('store', 'AtendimentoProcedimentos::store');
    $routes->get('show/(:num)', 'AtendimentoProcedimentos::show/$1');
    $routes->get('(:num)', 'AtendimentoProcedimentos::show/$1');
    $routes->get('edit/(:num)', 'AtendimentoProcedimentos::edit/$1');
    $routes->post('update/(:num)', 'AtendimentoProcedimentos::update/$1');
    $routes->delete('delete/(:num)', 'AtendimentoProcedimentos::delete/$1');
    
    // Relatórios
    $routes->get('relatorio', 'AtendimentoProcedimentos::relatorio');
    
    // AJAX routes
    $routes->post('search', 'AtendimentoProcedimentos::search');
    $routes->get('por-atendimento/(:num)', 'AtendimentoProcedimentos::porAtendimento/$1');
    
    // Export
    $routes->get('export', 'AtendimentoProcedimentos::export');
});

// Rotas de Atendimento Exames
$routes->group('atendimento_exames', static function ($routes) {
    $routes->get('/', 'AtendimentoExames::index');
    $routes->get('create', 'AtendimentoExames::create');
    $routes->post('store', 'AtendimentoExames::store');
    $routes->get('show/(:num)', 'AtendimentoExames::show/$1');
    $routes->get('(:num)', 'AtendimentoExames::show/$1');
    $routes->get('edit/(:num)', 'AtendimentoExames::edit/$1');
    $routes->post('update/(:num)', 'AtendimentoExames::update/$1');
    $routes->delete('delete/(:num)', 'AtendimentoExames::delete/$1');
    
    // Relatórios
    $routes->get('relatorio', 'AtendimentoExames::relatorio');
    $routes->get('print/(:num)', 'AtendimentoExames::print/$1');
    
    // AJAX routes
    $routes->post('search', 'AtendimentoExames::search');
    $routes->post('update-status/(:num)', 'AtendimentoExames::updateStatus/$1');
    $routes->get('por-atendimento/(:num)', 'AtendimentoExames::porAtendimento/$1');
    
    // Export
    $routes->get('export', 'AtendimentoExames::export');
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
