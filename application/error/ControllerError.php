<?php

namespace Yocto;

class ControllerError extends Controller
{

    public function forbidden()
    {
        // Header
        header('HTTP/1.1 403 Forbidden');
        // Affichage
        $this->setView('forbidden');
        $this->setLayout('main');
    }

    public function notfound()
    {
        // Header
        header("HTTP/1.0 404 Not Found");
        // Affichage
        $this->setView('notfound');
        $this->setLayout('main');
    }

}