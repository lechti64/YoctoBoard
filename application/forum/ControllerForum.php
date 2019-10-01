<?php

namespace Yocto;

class ControllerForum extends Controller
{

    /** @var Database[] Catégories */
    public $categories = [];

    /** @var Database Forum */
    public $forum;

    /** @var string[] Liste des forums pour le select */
    public $forumOptions;

    /** @var Database[] Sujets */
    public $topics = [];

    public function add($forumId)
    {
        // Forum
        $this->forum = Database::instance('forum')
            ->where('id', '=', (int)$forumId)
            ->find();

        // Librairies
        $this->setVendor(
            'https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.0.15/tinymce.min.js',
            'sha256-sUcUXrcbjQo1TED1nZIj4IndwPGhvnuuhhlRi94D7A8='
        );

        // Affichage
        $this->setView('add');
        $this->setLayout('main');
    }

    public function addPost($forumId)
    {
        // Données du formulaire
        $title = $this->get('title', true);
        $content = $this->get('content', true);

        // Création du sujet
        $topic = Database::instance('topic');
        $topic->forumId = (int)$forumId;
        $topic->memberId = $this->getSession()->getMember()->id;
        $topic->messagesNb = 1;
        $topic->title = $title;
        $topic->save();

        // Création du message
        $message = Database::instance('message');
        $message->content = $content;
        $message->memberId = $this->getSession()->getMember()->id;
        $message->topicId = $topic->id;
        $message->save();

        // Ajout de l'id du message au sujet
        $topic->lastMessageId = $message->id;
        $topic->save();

        // Ajout de l'id du message au forum
        $forum = Database::instance('forum')
            ->where('id', '=', (int)$forumId)
            ->find();
        $forum->lastMessageId = $message->id;
        $forum->messagesNb++;
        $forum->save();

        // Redirection si aucune erreur
        if (!$this->getNotices()) {
            $this->redirect('./?application=topic&controller=' . $message->id);
        }

        // Affichage
        $this->add($forumId);
    }

    public function forum($forumId)
    {
        // Forum
        $this->forum = Database::instance('forum')
            ->where('id', '=', (int)$forumId)
            ->find();

        // Accès
        $this->getSession()->checkAccess($this->forum->category->rightReadGroupIds);

        // Sujets
        $this->topics = Database::instance('topic')
            ->where('forumId', '=', (int)$forumId)
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
            ->where('rightReadGroupIds', 'REVERSE IN', $this->getSession()->getMember()->groupId)
            ->orderBy('position', 'ASC')
            ->findAll();
        foreach ($this->categories as $category) {
            $this->forumOptions[$category->title] = [];
        }

        // Ajout des forums aux catégories
        foreach ($this->categories as &$category) {
            $category->forums = Database::instance('forum')
                ->where('categoryId', '=', $category->id)
                ->orderBy('position', 'ASC')
                ->findAll();
            foreach ($category->forums as $forum) {
                $this->forumOptions[$category->title][$forum->id] = $forum->title;
            }
        }
        unset($category);

        // Affichage
        $this->setView('forums');
        $this->setLayout('main');
    }

}