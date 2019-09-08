<?php

$controller = new Yocto\ControllerLogin($_configuration);
$router = new Yocto\Router($controller->get('controller'));
$router->map('GET', '/', function () use ($controller) {
    $controller->index();
    return $controller;
});
$router->map('POST', '/', function () use ($controller) {
    $controller->login();
    return $controller;
});
return $router->run();