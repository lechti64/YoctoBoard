<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="?application=forum"><i class="fas fa-home"></i> Accueil</a></li>
        <li class="breadcrumb-item"><a
                    href="?application=forum&controller=<?php echo $this->forum->id; ?>"><?php echo $this->forum->title; ?></a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">Créer un sujet</li>
    </ol>
</nav>

<h1>Créer un sujet</h1>

<form method="post">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-9">
                    <div class="form-group">
                        <?php echo $this->getForm()->label('title', 'Titre'); ?>
                        <?php echo $this->getForm()->input('title'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->getForm()->textarea('content', ''); ?>
                    </div>
                </div>
                <div class="col-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h3>Options</h3>
                            <?php echo $this->getForm()->checkbox('pin', 'Épingler le sujet'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-center">
            <a href="?application=forum&controller=<?php echo $this->forum->id; ?>">
                <button type="button" class="btn btn-outline-secondary">Annuler</button>
            </a>
            <button type="submit" class="btn btn-primary">Créer le sujet</button>
        </div>
    </div>
</form>
