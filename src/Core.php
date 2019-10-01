<?php

namespace Yocto;

use ScssPhp\ScssPhp\Compiler;
use Yocto\Exception\ForbiddenException;
use Yocto\Exception\NotFoundException;

class Core
{

    /**
     * Génère la feuille de styles
     */
    public function generateCss()
    {
        if (!is_file(ROOT . '/public/main.css')) {
            $scss = new Compiler();
            $scss->setImportPaths(ROOT . '/layout/main');
            $css = $scss->compile('@import "main.scss";');
            file_put_contents(ROOT . '/public/main.css', $css);
        }
    }

    /**
     * Importe le routeur de l'application
     * @param $configuration
     * @throws \Exception
     */
    public function importRouter()
    {
        $_configuration = $this->loadConfiguration();
        try {
            $applicationPath = ROOT . '/application/' . $this->getApplication() . '/router.php';
            if (is_file($applicationPath)) {
                $controller = require $applicationPath;
                $controller->loadLayout();
            } else {
                throw new NotFoundException('Application not found');
            }
        } catch (NotFoundException | ForbiddenException $exception) {
            $controller = new ControllerError($_configuration);
            switch ($exception->getCode()) {
                case ForbiddenException::CODE:
                    $controller->forbidden();
                    break;
                case NotFoundException::CODE:
                    $controller->notfound();
                    break;
                default:
                    throw new \Exception('Unknown exception code');
            }
            if ($exception->getCode() === NotFoundException::CODE) {
                $controller->notfound();
            } elseif ($exception->getCode() === ForbiddenException::CODE) {
                $controller->forbidden();
            }
            $controller->loadLayout();
        }
    }
    
    /**
     * Récupère l'application
     * @return string
     */
    private function getApplication()
    {
        return $_application = (empty($_GET['application'])
            ? 'forum'
            : $_GET['application']
        );
    }

    /**
     * Charge la configuration
     * @return Database
     * @throws \Exception
     */
    private function loadConfiguration()
    {
        return $_configuration = Database::instance('configuration')
            ->where('id', '=', 1)
            ->find();
    }

}