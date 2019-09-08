<?php

namespace Yocto;

class ControllerForum extends Controller
{

    /** @var stdClass[] Catégories */
    public $categories = [];

    /** @var stdClass Forum */
    public $forum;

    /** @var stdClass[] Sujets */
    public $topics = [];

    public function add(int $forumId)
    {
        // Forum
        $this->forum = Database::instance('forum')
            ->where('id', '=', $forumId)
            ->find();

        // Librairies
        $this->setVendor('https://cdn.ckeditor.com/ckeditor5/12.4.0/classic/ckeditor.js');
        
        // Affichage
        $this->setView('add');
        $this->setLayout('main');
    }

    public function addPost(int $forumId)
    {
        // Données du formulaire
        $title = $this->get('title', true);
        $content = $this->get('content', true);

        // Création du sujet
        $row = Database::instance('topic');
        $row->content = $content;
        $row->createdAt = time();
        $row->forumId = $forumId;
        $row->lastMessageAt = time();
        $row->memberId = 1; // TODO
        $row->title = $title;
        $row->updatedAt = time();
        $row->save();

        // Alerte
        $this->setAlert('Sujet créé avec succès.');

        // Affichage
        $this->add($forumId);
    }

    public function forum(int $forumId)
    {
        // Forum
        $this->forum = Database::instance('forum')
            ->where('id', '=', $forumId)
            ->find();

        // Sujets
        $this->topics = Database::instance('topic')
            ->where('forumId', '=', $forumId)
            ->orderBy('createdAt', 'DESC')
            ->findAll();
            
        // Affichage
        $this->setView('forum');
        $this->setLayout('main');
    }

    public function forums()
    {
        // Catégories
        $this->categories = Database::instance('category')
            ->orderBy('position', 'ASC')
            ->findAll();

        // Ajout des forums aux catégories
        foreach($this->categories as &$category) {
            $category->forums = Database::instance('forum')
                ->where('categoryId', '=', $category->id)
                ->orderBy('position', 'ASC')
                ->findAll();
        }
        unset($category);

        // Affichage
        $this->setView('forums');
        $this->setLayout('main');
    }

}