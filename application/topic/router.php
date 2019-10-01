<?php

$controller = new Yocto\ControllerTopic($_configuration);
$router = new Yocto\Router($controller->get('controller'));
$router->map('GET', '/:topicId', function ($topicId) use ($controller) {
    $controller->topic($topicId);
    return $controller;
});
return $router->run();