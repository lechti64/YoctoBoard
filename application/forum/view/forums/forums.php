<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-home"></i> Accueil</li>
    </ol>
</nav>

<div class="text-right">
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addTopicModal">
        Commencer un sujet
    </button>
</div>

<h1>Forums</h1>

<?php foreach ($this->categories as $category): ?>
    <div class="card mt-3">
        <div class="card-header"><?php echo $category->title; ?></div>
        <ul class="list-group list-group-flush">
            <?php foreach ($category->forums as $forum): ?>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-8 align-self-center">
                            <div class="d-flex">
                                <div class="align-self-center pr-3">
                                    <div class="forum__status bg-warning rounded-circle text-center d-inline-block">
                                        <i class="fas fa-comments"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 align-self-center">
                                    <h5 class="card-title mb-0">
                                        <a href="?application=forum&controller=<?php echo $forum->id; ?>">
                                            <?php echo $forum->title; ?>
                                        </a>
                                    </h5>
                                    <?php if ($forum->description): ?>
                                        <p class="card-text"><?php echo $forum->description; ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-1 align-self-center text-center">
                            <h5 class="card-title mb-0"><?php echo $forum->messagesNb; ?></h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <?php echo $forum->messagesNb > 1 ? 'messages' : 'message'; ?>
                                </small>
                            </p>
                        </div>
                        <div class="col-3 align-self-center">
                            <?php if ($forum->lastMessageId): ?>
                                <div class="d-flex">
                                    <div class="forum__member-picture align-self-center rounded-circle bg-light text-center font-weight-bold text-uppercase">
                                        <?php echo $forum->lastMessage->member->name[0]; ?>
                                    </div>
                                    <div class="pl-3">
                                        <p class="card-text mb-n1">
                                            <a href="?application=topic&controller=<?php echo $forum->lastMessage->topicId; ?>">
                                                <?php echo $forum->lastMessage->topic->title; ?>
                                            </a>
                                        </p>
                                        <p class="card-text mb-n1">
                                            <small>
                                                Par
                                                <a href="?application=member&controller=<?php echo $forum->lastMessage->member->id; ?>">
                                                    <?php echo $forum->lastMessage->member->name; ?>
                                                </a>
                                            </small>
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <?php echo strftime('%e %B %G', (new DateTime($forum->lastMessage->createdAt))->getTimestamp()); ?>
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="card-text">Aucun message</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endforeach; ?>

<!-- Modal d'ajout de topic -->
<div class="modal fade" id="addTopicModal" tabindex="-1" role="dialog" aria-labelledby="addTopicModalLabel"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTopicModalLabel">Sélectionner un forum</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php echo $this->getForm()->select('addTopicForum', $this->forumOptions); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="addTopicSubmit">Continuer</button>
            </div>
        </div>
    </div>
</div>