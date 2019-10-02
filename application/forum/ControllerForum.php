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
        // Forum
        $forum = Database::instance('forum')
            ->where('id', '=', (int)$forumId)
            ->find();

        // Accès
        $this->getSession()->checkAccess($forum->category->rightWriteGroupIds);

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
        $topic->prepare();

        // Création du message
        $message = Database::instance('message');
        $message->content = $content;
        $message->memberId = $this->getSession()->getMember()->id;
        $message->topicId = $topic->id;
        $message->prepare();

        // Incrémente le nombre de messages du membre
        $member = $this->getSession()->getMember();
        $member->messagesNb++;
        $member->prepare();

        // Ajout de l'id du message au sujet
        $topic->lastMessageId = $message->id;
        $topic->prepare();

        // Incrémente le nombre de messages / sujets et ajoute l'id du message au forum
        $forum->lastMessageId = $message->id;
        $forum->messagesNb++;
        $forum->topicsNb++;
        $forum->prepare();

        // Enregistrement
        if (Database::save($topic, $message, $member, $forum)) {
            // Redirection
            $this->redirect('./?application=topic&controller=' . $topic->id);
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

        $timeAgoLang = new TimeAgo\Translations\Fr();
        $timeAgo = new TimeAgo($timeAgoLang);
        foreach ($this->topics as &$topic) {
            // Sujet lu ou non, date de lecture du sujet => à la date de création du dernier message posté
            $topicRead = Database::instance('topic-read')
                ->where('memberId', '=', $this->getSession()->getMember()->id)
                ->andWhere('topicId', '=', $topic->id)
                ->orderBy('updatedAt', 'ASC')
                ->find();
            $topic->read = ($topicRead->id && $topicRead->updatedAt >= $topic->lastMessage->createdAt);
            // Conversion des dates en format lisible par l'homme
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

        $timeAgoLang = new TimeAgo\Translations\Fr();
        $timeAgo = new TimeAgo($timeAgoLang);
        foreach ($this->categories as &$category) {
            // Ajout des forums aux catégories
            $category->forums = Database::instance('forum')
                ->where('categoryId', '=', $category->id)
                ->orderBy('position', 'ASC')
                ->findAll();
            foreach ($category->forums as &$forum) {
                $this->forumOptions[$category->title][$forum->id] = $forum->title;
                // Forum lu ou non, nombre de sujets lu = nombre de sujet s+ date de lecture du dernier sujet => à la date de création du dernier message posté
                $topicsRead = Database::instance('topic-read')
                    ->where('memberId', '=', $this->getSession()->getMember()->id)
                    ->andWhere('forumId', '=', $forum->id)
                    ->orderBy('updatedAt', 'ASC')
                    ->findAll();
                $topicsReadNb = $topicsRead->count();
                $topicRead = $topicsRead->find();
                $forum->read = (
                    $topicsReadNb === $forum->topicsNb
                    && $topicRead->id
                    && $topicRead->updatedAt >= $forum->lastMessage->createdAt
                );
                // Conversion de la date du dernier message en format lisible par l'homme
                $forum->lastMessage->createdAt = $timeAgo->inWords(new \DateTime($forum->lastMessage->createdAt));
            }
            unset($forum);
        }
        unset($category);

        // Affichage
        $this->setView('forums');
        $this->setLayout('main');
    }

}