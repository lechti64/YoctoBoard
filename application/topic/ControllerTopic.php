<?php

namespace Yocto;

use Westsworld\TimeAgo;

class ControllerTopic extends Controller
{

    /** @var Database[] Messages */
    public $messages = [];

    /** @var Database[] Sujet */
    public $topic = [];

    /** @var int Page courante */
    public $pageCurrent = 1;

    /** @var int Première page affichée dans les liens */
    public $pageFirstLink = 1;

    /** @var int Dernière page affichée dans les liens */
    public $pageLastLink = 1;

    /** @var int Page suivante */
    public $pageNext = 1;

    /** @var int Page precédente */
    public $pagePrevious = 1;

    /** @var int Nombre de pages */
    public $pagesNb = 1;

    public function topic($topicId)
    {
        // Forum
        $this->topic = Database::instance('topic')
            ->where('id', '=', (int)$topicId)
            ->find();

        // Accès
        $this->getSession()->checkAccess($this->topic->forum->category->rightReadGroupIds);

        // Nombre de messages par page
        $messagesPerPage = 20;

        // Nombre de pages suivantes / précédentes
        $pageLinksNb = 5;

        // Nombre de messages
        $messagesNb = Database::instance('message')
            ->where('topicId', '=', (int)$topicId)
            ->findAll()
            ->count();

        // Nombre de pages
        $this->pagesNb = (int)ceil($messagesNb / $messagesPerPage);

        // Page courante, précédente et suivante
        $this->pageCurrent = min($this->pagesNb, max(1, (int)$this->get('page')));
        $this->pagePrevious = $this->pageCurrent - 1;
        $this->pageNext = $this->pageCurrent + 1;
        $this->pageFirstLink = max(1, $this->pageCurrent - $pageLinksNb);
        $this->pageLastLink = min($this->pagesNb, $this->pageCurrent + $pageLinksNb);

        // Offset de la requête
        $offset = $messagesPerPage * ($this->pageCurrent - 1);

        // Messages à afficher
        $this->messages = Database::instance('message')
            ->where('topicId', '=', (int)$topicId)
            ->orderBy('createdAt', 'DESC')
            ->limit($offset, $messagesPerPage)
            ->findAll();

        // Conversion des dates en format lisible par l'homme
        $timeAgoLang = new TimeAgo\Translations\Fr();
        $timeAgo = new TimeAgo($timeAgoLang);
        foreach ($this->messages as &$message) {
            $message->createdAt = $timeAgo->inWords(new \DateTime($message->createdAt));
            $message->updatedAt = $timeAgo->inWords(new \DateTime($message->updatedAt));
        }
        unset($message);

        // Affichage
        $this->setView('topic');
        $this->setLayout('main');
    }

}