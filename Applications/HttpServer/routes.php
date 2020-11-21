<?php

use Moon\Routing\Router;
use Workerman\Protocols\Http\Request;

/** @var Router $router */

$router->get('/', 'IndexController::index');
$router->controller('/test', 'TestController');
//$router->resource('/user/', 'UserController');
//
$router->get('/hello/{username}', function (Request $request, $username) {
    return $request->method() . '. Hello ' . $username;
});

$router->any('/test-request', function (Request $request) {
    return [
        'method' => $request->method(),
        'path' => $request->path(),
        'uri' => $request->uri(),
        'session_id' => $request->sessionId()
    ];
});
