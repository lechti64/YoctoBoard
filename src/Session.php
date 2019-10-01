<?php

namespace Yocto;

use Yocto\Exception\ForbiddenException;

class Session
{

    /** @var Controller Contrôleur en provenance de ./src/Controller.php */
    private $controller;

    /** @var Database Membre connecté */
    private $member;

    /**
     * Constructeur de la classe
     * @param $controller
     * @throws \Exception
     */
    public function __construct($controller)
    {
        // Contrôleur
        $this->controller = $controller;
        // Utilisateur connecté
        $memberId = $this->controller->get('COOKIE:yocto_member_id');
        $memberPassword = $this->controller->get('COOKIE:yocto_member_key');
        $this->member = Database::instance('member')
            ->where('id', '=', (int)$memberId)
            ->andWhere('password', '=', $memberPassword)
            ->find();
    }


    /**
     * Vérifie l'accès du membre de la session en fonction d'une liste de groupes
     * @param array $groupIds Liste de groupes
     * @return bool
     * @throws ForbiddenException
     */
    public function checkAccess($groupIds)
    {
        if (!in_array($this->member->groupId, $groupIds)) {
            throw new ForbiddenException;
        }
        return true;
    }

    /**
     * Crée les cookies d'une session pour un membre donnée
     * @param Database $member Instance du membre
     */
    public function create($member)
    {
        setcookie('yocto_member_id', $member->id, time() + 3600 * 24);
        setcookie('yocto_member_key', $member->password, time() + 3600 * 24);
    }

    /**
     * Supprime les cookies de la session
     */
    public function delete()
    {
        setcookie('yocto_member_id', null, -1);
        setcookie('yocto_member_key', null, -1);
    }

    /**
     * Retourne le membre de la session
     * @return Database
     * @throws \Exception
     */
    public function getMember()
    {
        return $this->member;
    }


}