<?php

namespace Yocto;

class Autoloader
{

    /**
     * Enregistre la fonction d'autoload
     */
    public static function register()
    {
        spl_autoload_register([__CLASS__, 'autoload']);
    }

    /**
     * Fonction d'autoload
     * @param string $class Classe
     */
    public static function autoload($class)
    {
        // Supprime le namespace
        $class = str_replace('Yocto\\', '', $class);
        // Remplace les backslah par des slash
        $class = str_replace('\\', '//', $class);
        // Importe le fichier de la classe
        if (is_file(ROOT . '/src/' . $class . '.php')) {
            require ROOT . '/src/' . $class . '.php';
        } else {
            $directoryName = preg_replace_callback('/(?!^)([A-Z])/', function ($letter) {
                return strtolower('-' . $letter[1]);
            }, $class);
            $directoryName = str_replace('Controller-', '', $directoryName);
            if (is_file(ROOT . '/application/' . $directoryName . '/' . $class . '.php')) {
                require ROOT . '/application/' . $directoryName . '/' . $class . '.php';
            }
        }
    }

}