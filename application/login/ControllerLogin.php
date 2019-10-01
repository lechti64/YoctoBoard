<?php

namespace Yocto;

class ControllerLogin extends Controller
{

    public function index()
    {
        // Affichage
        $this->setView('index');
        $this->setLayout('main');
    }

    public function login()
    {
        // Données du formulaire
        $auth = $this->get('auth', true);
        $password = $this->get('password', true);

        // Recherche du membre
        $member = Database::instance('member')
            ->where('name', '=', $auth)
            ->orWhere('email', '=', $auth)
            ->find();

        // Connexion réussie
        if (
            $member->id
            AND password_verify($password, $member->password)
        ) {
            // Création de la session
            $this->getSession()->create($member);
            // Redirection sur la page d'accueil
            $this->redirect('./');
        }

        // Affichage
        $this->index();
    }

}