<?php

use ScssPhp\ScssPhp\Compiler;

define('ROOT', __DIR__);

// Ouvre une session
session_start();

// Charge les autoloaders
require ROOT . '/vendor/autoload.php';
require ROOT . '/src/Autoloader.php';
Yocto\Autoloader::register();

// Charge le gestionnaire d'erreurs
//$whoops = new Whoops\Run;
//$whoops->pushHandler(new Whoops\Handler\PrettyPageHandler);
//$whoops->register();
function dump($expression)
{
    ob_start();
    var_dump($expression);
    echo '<pre>' . ob_get_clean() . '</pre>';
}

// Génère la base de données par défaut
// $checkConfiguration = Yocto\Database::instance('configuration')
//     ->where('id', '=', 1)
//     ->find();
// if ($checkConfiguration->id === 0) {
//     $defaultData = json_decode(file_get_contents(Yocto\Database::PATH . '/default.json'), true);
//     foreach ($defaultData as $table => $rows) {
//         foreach ($rows as $index => $columns) {
//             $row = Yocto\Database::instance($table);
//             foreach ($columns as $columnId => $columnValue) {
//                 $row->{$columnId} = $columnValue;
//                 $row->save();
//             }
//         }
//     }
// }

// Récupère la configuration
$_configuration = Yocto\Database::instance('configuration')
    ->where('id', '=', 1)
    ->find();

// Génère la feuille de styles
if (!is_file(ROOT . '/public/main.css')) {
    $scss = new Compiler();
    $scss->setImportPaths(ROOT . '/layout/main');
    $css = $scss->compile('@import "main.scss";');
    file_put_contents(ROOT . '/public/main.css', $css);
}

// Récupère l'application
$_application = (empty($_GET['application'])
    ? 'forum'
    : $_GET['application']
);

// Importe le routeur de l'application
/** @var Yocto\Controller $controller */
$controller = require ROOT . '/application/' . $_application . '/router.php';
$controller->loadLayout();