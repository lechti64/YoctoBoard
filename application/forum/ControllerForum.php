<?php

namespace Yocto;

use Westsworld\TimeAgo;

class ControllerForum extends Controller
{

    /** @var Database[] Catégories */
    public $categories = [];

    /** @var Database Forum */
    public $forum;

    /** @var string[] Liste des forums pour le select */
    public $forumOptions;

    /** @var int Nombre de pages */
    public $pagesNb = 1;

    /** @var int Page courante */
    public $pageCurrent = 1;

    /** @var int Page precédente */
    public $pagePrevious = 1;

    /** @var int Page suivante */
    public $pageNext = 1;

    /** @var int Première page affichée dans les liens */
    public $pageFirstLink = 1;

    /** @var int Dernière page affichée dans les liens */
    public $pageLastLink = 1;

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
        $content = $this->get('content', true);
        $pin = $this->get('pin');
        $title = $this->get('title', true);

        // Création du sujet
        $topic = Database::instance('topic');
        $topic->forumId = $forumId;
        $topic->memberId = $this->getSession()->getMember()->id;
        $topic->messagesNb = 1;
        $topic->pin = $pin;
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

        // Nombre de sujets par page
        $topicsPerPage = 10;

        // Nombre de pages suivantes / précédentes
        $pageLinksNb = 5;

        // Nombre de sujets
        $topicsNb = Database::instance('topic')
            ->where('forumId', '=', (int)$forumId)
            ->findAll()
            ->count();

        // Nombre de pages
        $this->pagesNb = (int)ceil($topicsNb / $topicsPerPage);

        // Page courante, précédente et suivante
        $this->pageCurrent = min($this->pagesNb, max(1, (int)$this->get('page')));
        $this->pagePrevious = $this->pageCurrent - 1;
        $this->pageNext = $this->pageCurrent + 1;
        $this->pageFirstLink = max(1, $this->pageCurrent - $pageLinksNb);
        $this->pageLastLink = min($this->pagesNb, $this->pageCurrent + $pageLinksNb);

        // Offset de la requête
        $offset = $topicsPerPage * ($this->pageCurrent - 1);

        // Sujets à afficher
        $this->topics = Database::instance('topic')
            ->where('forumId', '=', (int)$forumId)
            ->orderBy('pin', 'DESC')
            ->orderBy('createdAt', 'DESC')
            ->limit($offset, $topicsPerPage)
            ->findAll();

        // Conversion des dates en format lisible par l'homme
        $timeAgoLang = new TimeAgo\Translations\Fr();
        $timeAgo = new TimeAgo($timeAgoLang);
        foreach ($this->topics as &$topic) {
            $topic->createdAt = $timeAgo->inWords(new \DateTime($topic->createdAt));
            $topic->lastMessage->createdAt = $timeAgo->inWords(new \DateTime($topic->lastMessage->createdAt));
        }
        unset($topic);

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

        // Ajout des forums aux catégories et conversion de la date du dernier message en format lisible par l'homme
        $timeAgoLang = new TimeAgo\Translations\Fr();
        $timeAgo = new TimeAgo($timeAgoLang);
        foreach ($this->categories as &$category) {
            $category->forums = Database::instance('forum')
                ->where('categoryId', '=', $category->id)
                ->orderBy('position', 'ASC')
                ->findAll();
            foreach ($category->forums as &$forum) {
                $forum->lastMessage->createdAt = $timeAgo->inWords(new \DateTime($forum->lastMessage->createdAt));
                $this->forumOptions[$category->title][$forum->id] = $forum->title;
            }
            unset($forum);
        }
        unset($category);

        // Affichage
        $this->setView('forums');
        $this->setLayout('main');
    }

}