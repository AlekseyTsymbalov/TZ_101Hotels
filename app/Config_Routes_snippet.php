<?php

// Add these routes to app/Config/Routes.php

$routes->get('/', 'Comments::index');
$routes->post('/comments', 'Comments::store');
$routes->delete('/comments/(:num)', 'Comments::delete/$1');
