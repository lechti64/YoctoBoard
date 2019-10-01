<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?application=forum"><i class="fas fa-home"></i> Accueil</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo $this->forum->title; ?></li>
    </ol>
</nav>

<?php if ($this->getSession()->getMember()->id): ?>
    <div class="text-right">
        <a href="?application=forum&controller=<?php echo $this->forum->id; ?>/add">
            <button type="button" class="btn btn-success">Commencer un sujet</button>
        </a>
    </div>
<?php endif; ?>

<h1><?php echo $this->forum->title; ?></h1>

<?php require __DIR__ . '/inc.pagination.php'; ?>

<div class="card my-3">
    <ul class="list-group list-group-flush">
        <?php foreach ($this->topics as $topic): ?>
            <li class="list-group-item">
                <div class="row">
                    <div class="col-7 align-self-center">
                        <div class="d-flex">
                            <div class="align-self-center pr-3">
                                <i class="fas fa-circle text-warning"></i>
                            </div>
                            <div class="flex-grow-1 align-self-center">
                                <?php if ($topic->pin): ?>
                                    <small class="fas fa-thumbtack mr-1" data-toggle="tooltip" data-placement="top"
                                           title="Sujet épinglé"></small>
                                <?php endif; ?>
                                <h5 class="card-title mb-n1 d-inline-block">
                                    <a href="?application=topic&controller=<?php echo $topic->id; ?>/">
                                        <?php echo $topic->title; ?>
                                    </a>
                                </h5>
                                <p class="card-text">
                                    <small class="text-muted">
                                        Par
                                        <a href="?application=member&controller=<?php echo $topic->memberId; ?>">
                                            <?php echo $topic->member->name; ?>
                                        </a>
                                    </small>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-2 align-self-center text-right">
                        <p class="card-text mb-n1">
                            <small>
                                <?php echo $topic->messagesNb; ?>
                                <?php echo $topic->messagesNb > 1 ? 'messages' : 'message'; ?>
                            </small>
                        </p>
                        <p class="card-text">
                            <small class="text-muted">
                                <?php echo $topic->viewsNb; ?>
                                <?php echo $topic->viewsNb > 1 ? 'vues' : 'vue'; ?>
                            </small>
                        </p>
                    </div>
                    <div class="col-3 align-self-center">
                        <?php if ($topic->lastMessageId): ?>
                            <div class="d-flex">
                                <div class="topic__member-picture align-self-center rounded-circle bg-light text-center font-weight-bold text-uppercase">
                                    <?php echo $topic->lastMessage->member->name[0]; ?>
                                </div>
                                <div class="pl-3">
                                    <p class="card-text mb-n1">
                                        <small>
                                            Par
                                            <a href="?application=member&controller=<?php echo $topic->lastMessage->member->id; ?>">
                                                <?php echo $topic->lastMessage->member->name; ?>
                                            </a>
                                        </small>
                                    </p>
                                    <p class="card-text">
                                        <small class="text-muted">
                                            <?php echo strftime('%e %B %G', (new DateTime($topic->lastMessage->createdAt))->getTimestamp()); ?>
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

<?php require __DIR__ . '/inc.pagination.php'; ?>