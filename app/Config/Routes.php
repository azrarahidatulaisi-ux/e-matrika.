<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ── Auth (tanpa filter) ──
$routes->get('/', 'AuthController::login');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');

// ── Halaman yang butuh login (filter: auth) ──
$routes->group('', ['filter' => 'auth'], function ($routes) {

    // Peta
    $routes->get('/map', 'MapController::index');

    // Unggah data
    $routes->get('/upload', 'UploadController::index');
    $routes->get('/upload/template', 'UploadController::template');
    $routes->post('/upload/do', 'UploadController::upload');
    $routes->get('/upload/mapping', 'UploadController::mapping');
    $routes->post('/upload/process', 'UploadController::process');
    $routes->post('/upload/delete/(:num)', 'UploadController::deleteBatch/$1');

    // API (JSON)
    $routes->get('/api/cases', 'ApiController::cases');
    $routes->get('/api/diagnoses', 'ApiController::diagnoses');
});

// ── Halaman khusus Admin (filter: admin) ──
$routes->group('', ['filter' => 'admin'], function ($routes) {
    $routes->get('/users', 'UserController::index');
    $routes->post('/users/create', 'UserController::create');
    $routes->post('/users/delete/(:num)', 'UserController::delete/$1');
});
