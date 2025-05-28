<?php

$router = $di->getRouter();

// Define your routes here
// $router->add('/prjPhalcon/', [
//     'controller' => 'index', 
//     'action' => 'index'
// ]);
// $router->add('/index/login', [
//     'controller' => 'index', 
//     'action' => 'login'
// ]);
// $router->add('/prjPhalcon/index/logout', [
//     'controller' => 'index', 
//     'action' => 'logout'
// ]);
// $router->add('prjPhalcon/user/index', [
//     'controller' => 'user', 
//     'action' => 'index'
// ]);

// $router->add('/admin', [
//     'controller' => 'admin', 
//     'action' => 'index'
// ]);
$router->handle($_SERVER['REQUEST_URI']);
