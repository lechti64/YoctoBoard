<?php

$controller = new Yocto\ControllerLogout($_configuration);
$router = new Yocto\Router($controller->get('controller'));
$router->map('GET', '/', function () use ($controller) {
    $controller->index();
    return $controller;
});
return $router->run();