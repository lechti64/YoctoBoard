<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?application=forum"><i class="fas fa-home"></i> Accueil</a></li>
        <li class="breadcrumb-item"><a
                    href="?application=forum&controller=<?php echo $this->topic->forum->id; ?>"><?php echo $this->topic->forum->title; ?></a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo $this->topic->title; ?></li>
    </ol>
</nav>
<?php if ($this->getSession()->getMember()->id && $this->getSession()->checkAccess($this->topic->forum->category->rightWriteGroupIds)): ?>
    <div class="text-right">
        <a href="#add">
            <button type="button" class="btn btn-success">Répondre à ce sujet</button>
        </a>
    </div>
<?php endif; ?>

<h1><?php echo $this->topic->title; ?></h1>

<?php require __DIR__ . '/inc.pagination.php'; ?>

<?php foreach ($this->messages as $message): ?>
    <div id="messsage-<?php echo $message->id; ?>" class="card my-3">
        <div class="card-body">
            <div class="row">
                <div class="col-2 text-center">
                    <h5 class="card-title">
                        <a href="?application=member&controller=<?php echo $message->memberId; ?>">
                            <?php echo $message->member->name; ?>
                        </a>
                    </h5>
                    <a href="?application=member&controller=<?php echo $message->memberId; ?>">
                        <?php echo Yocto\Helper::getMemberPicture($message->member, 100); ?>
                    </a>
                    <p class="card-text mb-0 mt-2">
                        <small><?php echo $message->member->group->name; ?></small>
                    </p>
                    <p class="card-text">
                        <small class="text-muted">
                            <?php echo $message->member->messagesNb; ?>
                            <?php echo $message->member->messagesNb > 1 ? 'messages' : 'message'; ?>
                        </small>
                    </p>
                </div>
                <div class="col-10">
                    <p class="card-text">
                        <small class="text-muted">Posté <?php echo $message->createdAt; ?></small>
                    </p>
                    <p class="card-text"><?php echo $message->content; ?>
                    <hr>
                    <?php if (
                        $this->getSession()->getMember()->id === $message->memberId
                        || $this->getSession()->getMember()->group->moderator
                        || $this->getSession()->getMember()->group->administrator
                    ): ?>
                        <div class="text-right">
                            <button type="button" class="btn btn-sm btn-outline-secondary"><i
                                        class="fas fa-pencil-alt"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php if ($this->getSession()->getMember()->id && $this->getSession()->checkAccess($this->topic->forum->category->rightWriteGroupIds)): ?>
    <form method="post">
        <div id="add" class="card my-3 bg-light">
            <div class="card-body">
                <div class="form-group">
                    <?php echo $this->getForm()->textarea('content', ''); ?>
                </div>
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">Envoyer la réponse</button>
                </div>
            </div>
        </div>
    </form>
<?php endif; ?>

<?php require __DIR__ . '/inc.pagination.php'; ?>