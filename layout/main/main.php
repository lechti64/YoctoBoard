<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?php echo $this->_configuration->title; ?></title>
    <meta name="description" content="<?php echo $this->_configuration->description ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:100,300,700">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css"
          integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <?php foreach ($this->vendors as $url => $sri): ?>
        <?php if (pathinfo($url, PATHINFO_EXTENSION) === 'css'): ?>
            <?php if ($sri): ?>
                <link rel="stylesheet" href="<?php echo $url; ?>" integrity="<?php echo $sri; ?>"
                      crossorigin="anonymous">
            <?php else: ?>
                <link rel="stylesheet" href="<?php echo $url; ?>">
            <?php endif; ?>
        <?php endif; ?>
    <?php endforeach; ?>
    <link rel="stylesheet" href="public/main.css">
    <?php $this->loadViewCss(); ?>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="?application=forum"><?php echo $this->_configuration->title; ?></a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavContent"
                aria-controls="navbarNavContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavContent">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="?application=forum">Accueil</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?application=login">Connexion</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?application=register">S'inscrire</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarMember" role="button" data-toggle="dropdown"
                       aria-haspopup="true" aria-expanded="false">
                        <?php echo $this->getSession()->getMember()->name; ?>
                    </a>
                    <div class="dropdown-menu" aria-labelledby="navbarMember">
                        <a class="dropdown-item" href="?application=member&controller=1">Mon compte</a>
                        <a class="dropdown-item" href="?application=administration">Administration</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="?application=logout">Déconnexion</a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</nav>
<section class="py-5">
    <div class="container-fluid">
        <?php $this->loadView(); ?>
    </div>
</section>
<footer class="text-center py-3">
    <div class="container-fluid">
        <small>Powered by <a href="https://yoctoboard.com" target="_blank">Yocto Board</a></small>
    </div>
</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"
        integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="vendor/twitter/bootstrap/dist/js/bootstrap.min.js"></script>
<?php foreach ($this->vendors as $url => $sri): ?>
    <?php if (pathinfo($url, PATHINFO_EXTENSION) === 'js'): ?>
        <?php if ($sri): ?>
            <script src="<?php echo $url; ?>" integrity="<?php echo $sri; ?>" crossorigin="anonymous"></script>
        <?php else: ?>
            <script src="<?php echo $url; ?>"></script>
        <?php endif; ?>
    <?php endif; ?>
<?php endforeach; ?>
<?php $this->loadViewJs(); ?>
</body>
</html>