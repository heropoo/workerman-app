<?php
use Moon\Routing\Router;
use Moon\Request\Request;

/** @var Router $router */

$router->get('/', 'IndexController::index');
$router->controller('/test', 'TestController');
$router->resource('/user/', 'UserController');

$router->get('/hello/{username}', function (Request $request, $username) {
    return $request->getMethod().'. Hello '. $username;
});
