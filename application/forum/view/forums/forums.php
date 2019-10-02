<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page"><i class="fas fa-home"></i> Accueil</li>
    </ol>
</nav>

<?php if ($this->getSession()->getMember()->id): ?>
    <div class="text-right">
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addTopicModal">
            Commencer un sujet
        </button>
    </div>
<?php endif; ?>

<h1>Forums</h1>

<?php foreach ($this->categories as $category): ?>
    <div class="card my-3">
        <div class="card-header"><?php echo $category->title; ?></div>
        <ul class="list-group list-group-flush">
            <?php foreach ($category->forums as $forum): ?>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-8 col-lg-9 align-self-lg-center">
                            <div class="d-flex">
                                <div class="align-self-start align-self-lg-center pr-3">
                                    <?php if ($forum->read): ?>
                                        <div class="forum__status bg-light rounded-circle text-center d-inline-block">
                                            <i class="fas fa-comments"></i>
                                        </div>
                                    <?php else: ?>
                                        <div class="forum__status text-white bg-warning rounded-circle text-center d-inline-block">
                                            <i class="fas fa-comments"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class="d-flex w-100 justify-content-between flex-column flex-lg-row">
                                    <div class="flex-grow-1 align-self-lg-center">
                                        <h5 class="card-title mb-0">
                                            <a href="?application=forum&controller=<?php echo $forum->id; ?>">
                                                <?php echo $forum->title; ?>
                                            </a>
                                        </h5>
                                        <?php if ($forum->description): ?>
                                            <p class="card-text">
                                                <small><?php echo $forum->description; ?></small>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                    <div class="align-self-lg-center text-lg-center">
                                        <h5 class="card-title d-inline d-lg-block mb-0 forum__messages-nb"><?php echo $forum->messagesNb; ?></h5>
                                        <p class="card-text d-inline d-lg-block">
                                            <small class="text-muted">
                                                <?php echo $forum->messagesNb > 1 ? 'messages' : 'message'; ?>
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-lg-3 align-self-lg-center">
                            <?php if ($forum->lastMessageId): ?>
                                <div class="d-flex flex-column flex-lg-row justify-content-end justify-content-lg-start">
                                    <div class="align-self-end align-self-lg-center">
                                        <a href="?application=member&controller=<?php echo $forum->lastMessage->memberId; ?>">
                                            <?php echo Yocto\Helper::getMemberPicture($forum->lastMessage->member); ?>
                                        </a>
                                    </div>
                                    <div class="pl-lg-3 text-right text-lg-left">
                                        <p class="card-text mb-n1 d-none d-lg-block">
                                            <a href="?application=topic&controller=<?php echo $forum->lastMessage->topicId; ?>&page=last">
                                                <?php echo $forum->lastMessage->topic->title; ?>
                                            </a>
                                        </p>
                                        <p class="card-text mb-n1 d-none d-lg-block">
                                            <small>
                                                Par
                                                <a href="?application=member&controller=<?php echo $forum->lastMessage->member->id; ?>">
                                                    <?php echo $forum->lastMessage->member->name; ?>
                                                </a>
                                            </small>
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <?php echo $forum->lastMessage->createdAt; ?>
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