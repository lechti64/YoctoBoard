<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?application=forum"><i class="fas fa-home"></i> Accueil</a></li>
        <li class="breadcrumb-item"><a
                    href="?application=forum&controller=<?php echo $this->topic->forum->id; ?>"><?php echo $this->topic->forum->title; ?></a>
        </li>
        <li class="breadcrumb-item active" aria-current="page"><?php echo $this->topic->title; ?></li>
    </ol>
</nav>

<?php if ($this->getSession()->getMember()->id): ?>
    <div class="text-right">
        <a href="?application=topic&controller=<?php echo $this->topic->id; ?>/add">
            <button type="button" class="btn btn-success">Répondre à ce sujet</button>
        </a>
    </div>
<?php endif; ?>

<h1><?php echo $this->topic->title; ?></h1>

<?php require __DIR__ . '/inc.pagination.php'; ?>

<?php foreach ($this->messages as $message): ?>
    <div class="card my-3">
        <div class="card-body">
            <div class="row">
                <div class="col-2 text-center">
                    <h5 class="card-title mb-0">
                        <a href="?application=member&controller=<?php echo $message->memberId; ?>">
                            <?php echo $message->member->name; ?>
                        </a>
                    </h5>
                    <p class="card-text mb-2">
                        <small><?php echo $message->member->group->name; ?></small>
                    </p>
                    <?php echo Yocto\Helper::getMemberPicture($message->member, 100); ?>
                </div>
                <div class="col-10">
                    <p class="card-text">
                        <small class="text-muted">Posté <?php echo $message->createdAt; ?></small>
                    </p>
                    <p class="card-text"><?php echo $message->content; ?>
                    <hr>
                    <div class="text-right">
                        <button type="button" class="btn btn-sm btn-outline-primary"><i class="fas fa-pencil-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php require __DIR__ . '/inc.pagination.php'; ?>