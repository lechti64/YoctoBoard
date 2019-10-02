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
        // Sujet
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
        $this->pageCurrent = $this->get('page') === 'last' ? $this->pagesNb : (int)$this->get('page');
        $this->pageCurrent = min($this->pagesNb, max(1, $this->pageCurrent));
        $this->pagePrevious = $this->pageCurrent - 1;
        $this->pageNext = $this->pageCurrent + 1;
        $this->pageFirstLink = max(1, $this->pageCurrent - $pageLinksNb);
        $this->pageLastLink = min($this->pagesNb, $this->pageCurrent + $pageLinksNb);

        // Offset de la requête
        $offset = $messagesPerPage * ($this->pageCurrent - 1);

        // Messages à afficher
        $this->messages = Database::instance('message')
            ->where('topicId', '=', (int)$topicId)
            ->orderBy('createdAt', 'ASC')
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

        // Incrémente le nombre de messages du sujet
        $this->topic->viewsNb++;
        $this->topic->prepare();

        // Marque le topic comme lus
        if ($this->pageCurrent === $this->pagesNb) {
            $topicRead = Database::instance('topic-read')
                ->where('memberId', '=', $this->getSession()->getMember()->id)
                ->andWhere('topicId', '=', (int)$topicId)
                ->find();
            $topicRead->memberId = $this->getSession()->getMember()->id;
            $topicRead->topicId = $topicId;
            $topicRead->forumId = $this->topic->forumId;
            $topicRead->prepare();
        }

        // Enregistrement
        Database::save($this->topic, $topicRead);

        // Librairies
        $this->setVendor(
            'https://cdnjs.cloudflare.com/ajax/libs/tinymce/5.0.15/tinymce.min.js',
            'sha256-sUcUXrcbjQo1TED1nZIj4IndwPGhvnuuhhlRi94D7A8='
        );

        // Affichage
        $this->setView('topic');
        $this->setLayout('main');
    }

    public function topicPost($topicId)
    {
        // Sujet
        $topic = Database::instance('topic')
            ->where('id', '=', (int)$topicId)
            ->find();

        // Accès
        $this->getSession()->checkAccess($topic->forum->category->rightWriteGroupIds);

        // Données du formulaire
        $content = $this->get('content', true);

        // Création du message
        $message = Database::instance('message');
        $message->content = $content;
        $message->memberId = $this->getSession()->getMember()->id;
        $message->topicId = $topicId;
        $message->prepare();

        // Incrémente le nombre de messages du membre
        $member = $this->getSession()->getMember();
        $member->messagesNb++;
        $member->prepare();

        // Incrémente le nombre de messages et ajoute l'id du message au sujet
        $topic->messagesNb++;
        $topic->lastMessageId = $message->id;
        $topic->prepare();

        // Incrémente le nombre de messages et ajoute l'id du message au forum
        $forum = Database::instance('forum')
            ->where('id', '=', $topic->forumId)
            ->find();
        $forum->lastMessageId = $message->id;
        $forum->messagesNb++;
        $forum->prepare();

        // Redirection si aucune erreur
        if (Database::save($message, $member, $topic, $forum)) {
            $this->redirect('./?application=topic&controller=' . $topicId . '&page=last#messsage-' . $message->id);
        }

        // Affichage
        $this->topic($topicId);
    }

}