<?php

define('ROOT', __DIR__);

// Ouvre une session
session_start();

// Charge les autoloaders
require ROOT . '/vendor/autoload.php';
require ROOT . '/src/Autoloader.php';
Yocto\Autoloader::register();

// Charge le gestionnaire d'erreurs
$whoops = new Whoops\Run;
$whoops->prependHandler(new Whoops\Handler\PrettyPageHandler);
$whoops->register();
function dump($expression)
{
    ob_start();
    var_dump($expression);
    echo '<pre>' . ob_get_clean() . '</pre>';
}

// Initialise le coeur
$core = new Yocto\Core();
$core->generateCss();
$core->importRouter();