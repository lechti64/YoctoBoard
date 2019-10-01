<?php

$controller = new Yocto\ControllerTopic($_configuration);
$router = new Yocto\Router($controller->get('controller'));
$router->map('GET', '/:topicId', function ($topicId) use ($controller) {
    $controller->topic($topicId);
    return $controller;
});
$router->map('POST', '/:topicId', function ($topicId) use ($controller) {
    $controller->topicPost($topicId);
    return $controller;
});
return $router->run();