<?php

namespace Yocto;

class ControllerLogout extends Controller
{

    public function index()
    {
        // Suppression de la session
        $this->getSession()->delete();
        // Redirection sur la page d'accueil
        $this->redirect('./');
    }

}