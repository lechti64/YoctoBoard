<?php

$controller = new Yocto\ControllerForum($_configuration);
$router = new Yocto\Router($controller->get('controller'));
$router->map('GET', '/', function () use ($controller) {
    $controller->forums();
    return $controller;
});
$router->map('GET', '/:forumId/add', function ($forumId) use ($controller) {
    $controller->add($forumId);
    return $controller;
});
$router->map('POST', '/:forumId/add', function ($forumId) use ($controller) {
    $controller->addPost($forumId);
    return $controller;
});
$router->map('GET', '/:forumId', function ($forumId) use ($controller) {
    $controller->forum($forumId);
    return $controller;
});
return $router->run();