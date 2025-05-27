<?php

$router = $di->getRouter();

// Define your routes here
$router->add('/prjPhalcon/', [
    'controller' => 'index', 
    'action' => 'index'
]);
$router->handle($_SERVER['REQUEST_URI']);
