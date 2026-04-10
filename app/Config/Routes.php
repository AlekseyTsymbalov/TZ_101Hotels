<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Comments::index');

$routes->group('comments', function($routes) {
    $routes->post('/', 'Comments::store');
    $routes->delete('(:num)', 'Comments::delete/$1');
});
