<?php
declare(strict_types=1);

use Phalcon\Mvc\Dispatcher;
use Phalcon\Html\Escaper;
use Phalcon\Flash\Direct as Flash;
use Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter;
use Phalcon\Mvc\View;
use Phalcon\Mvc\View\Engine\Php as PhpEngine;
use Phalcon\Mvc\View\Engine\Volt as VoltEngine;
use Phalcon\Session\Adapter\Stream as SessionAdapter;
use Phalcon\Session\Manager as SessionManager;
use Phalcon\Mvc\Url as UrlResolver;
use Phalcon\Events\Manager as EventsManager;
/**
 * Shared configuration service
 */
$di->setShared('config', function () {
    return include APP_PATH . "/config/config.php";
});

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->setShared('url', function () {
    $config = $this->getConfig();

    $url = new UrlResolver();
    $url->setBaseUri($config->application->baseUri);

    return $url;
});

/**
 * Setting up the view component
 */
$di->setShared('view', function () {
    $config = $this->getConfig();

    $view = new View();
    $view->setDI($this);
    $view->setViewsDir($config->application->viewsDir);

    $view->registerEngines([
        '.volt' => function ($view) {
            $config = $this->getConfig();

            $volt = new VoltEngine($view, $this);

            $volt->setOptions([
                'path' => $config->application->cacheDir,
                'separator' => '_'
            ]);

            return $volt;
        },
        '.phtml' => PhpEngine::class

    ]);

    return $view;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->setShared('db', function () {
    $config = $this->getConfig();

    $class = 'Phalcon\Db\Adapter\Pdo\\' . $config->database->adapter;
    $params = [
        'host'     => $config->database->host,
        'username' => $config->database->username,
        'password' => $config->database->password,
        'dbname'   => $config->database->dbname,
        'charset'  => $config->database->charset
    ];

    if ($config->database->adapter == 'Postgresql') {
        unset($params['charset']);
    }

    return new $class($params);
});


/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->setShared('modelsMetadata', function () {
    return new MetaDataAdapter();
});

/**
 * Register the session flash service with the Twitter Bootstrap classes
 */
$di->set('flash', function () {
    $escaper = new Escaper();
    $flash = new Flash($escaper);
    $flash->setImplicitFlush(false);
    $flash->setCssClasses([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);

    return $flash;
});

/**
 * Start the session the first time some component request the session service
 */
$di->setShared('session', function () {
    $session = new SessionManager();
    $files = new SessionAdapter([
        'savePath' => sys_get_temp_dir(),
    ]);
    $session->setAdapter($files);
    $session->start();

    return $session;
});

$di->setShared('eventsManager', function () {
    return new \Phalcon\Events\Manager();
});


//acl
$di->set('acl', function(){
    return Acl::getAcl();
});


//middleware check quyền

$di->setShared('dispatcher', function () use ($di) {
    $eventsManager = new EventsManager();

    $eventsManager->attach('dispatch:beforeExecuteRoute', function ($event, $dispatcher) use ($di) {
        $role = $di->get('session')->get('role');
        $controller = $dispatcher->getControllerName();
        $action = $dispatcher->getActionName();

        // Kiểm tra nếu chưa đăng nhập
        if (!$role) {
            if ($controller === 'index' && in_array($action, ['index', 'login'])) {
                return true;
            }

            $dispatcher->forward([
                'controller' => 'index',
                'action'     => 'index',
            ]);
            return false;
        }

        // Kiểm tra quyền truy cập
        if (!$di->get('acl')->isAllowed($role, $controller, $action)) {
            // echo "Access Denied for role: $role, controller: $controller, action: $action";
            // return false;
             $di->get('session')->destroy();
              return $di->get('response')->redirect('/prjPhalcon', true);
        }
    });

    $dispatcher = new Dispatcher();
    $dispatcher->setEventsManager($eventsManager);
    return $dispatcher;
});


// //middleware check quyền
// $di->setShared('dispatcher', function() use ($di) {
//     $eventManager = $di->getShared('eventsManager');
//     $dispatcher = new \Phalcon\Mvc\Dispatcher();

//  $eventManager->attach('dispatch:beforeExecuteRoute', function($event, $dispatcher) use ($di) {
//     $session = $di->getShared('session');
//     $role = $session->get('role');
//     $controller = strtolower($dispatcher->getControllerName());
//     $action = strtolower($dispatcher->getActionName());

//     // Chưa đăng nhập
//     if (!$role) {
//         if ($controller === 'index' && in_array($action, ['index', 'login'])) {
//             return true;
//         }
//         $dispatcher->forward([
//             'controller' => 'index',
//             'action' => 'login'
//         ]);
//         return false;
//     }

//     // Đăng nhập rồi, nhưng không có quyền
//     $acl = $di->get('acl');
//     if (!$acl->isAllowed($role, $controller, $action)) {
//         // Chặn hẳn, không cho chạy tiếp controller
//         $dispatcher->forward([
//             'controller' => 'index',
//             'action' => 'index'
//         ]);
//         return false;
//     }
// });

//     $dispatcher->setEventsManager($eventManager);
//     return $dispatcher;
// });


//flashsession
$di->setShared('flashSession', function () {
    $escaper = new Escaper(); 
    $session = $this->getShared('session');
    $flash = new \Phalcon\Flash\Session($escaper, $session);  // Truyền session vào
    $flash->setCssClasses([
        'error'   => 'alert alert-danger',
        'success' => 'alert alert-success',
        'notice'  => 'alert alert-info',
        'warning' => 'alert alert-warning'
    ]);
    return $flash;
});