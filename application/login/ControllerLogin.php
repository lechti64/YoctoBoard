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
            // Création du cookie
            setcookie('yocto_member_id', $member->id, time() + 3600 * 24);
            // Redirection sur la page d'accueil
            $this->redirect('./');
        }

        // Affichage
        $this->index();
    }

}