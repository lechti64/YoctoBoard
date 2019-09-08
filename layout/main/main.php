<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title><?php echo $this->_configuration->title; ?></title>
    <meta name="description" content="<?php echo $this->_configuration->description ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,700|Montserrat:200">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
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
    <link rel="stylesheet" href="layout/main/main.css">
    <?php $this->loadViewCss(); ?>
</head>
<body class="body">
<nav role="navigation" class="navigation">
    <div class="container d-flex align-items-center justify-content-between">
        <ul class="navigation__items">
            <li class="navigation__item">
                <a class="navigation__link" href="?application=forum">Accueil</a>
            </li>
            <li class="navigation__item">
                <a class="navigation__link" href="?application=login">Connexion</a>
            </li>
            <li class="navigation__item">
                <a class="navigation__link" href="?application=register">S'inscrire</a>
            </li>
        </ul>
    </div>
</nav>
<header class="header">
    <div class="container">
        <h1 class="header__title"><?php echo $this->_configuration->title; ?></h1>
    </div>
</header>
<section class="section">
    <div class="container">
        <?php if ($this->getNotices() OR $this->getAlertText()): ?>
            <div class="alert alert-<?php echo $this->getNotices() ? 'danger' : $this->getAlertType(); ?> alert-dismissible fade show"
                 role="alert">
                <?php if ($this->getNotices()): ?>
                    Impossible de soumettre le formulaire, car il contient des erreurs.
                <?php else: ?>
                    <?php echo $this->getAlertText(); ?>
                <?php endif; ?>
                <button type="button" class="btn close" data-dismiss="alert" aria-label="Fermer">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        <?php endif; ?>
        <?php $this->loadView(); ?>
    </div>
</section>
<footer class="footer">
    <div class="container">
        Powered by <a class="footer__yocto" href="https://yoctoboard.com" target="_blank">Yocto Board</a>.
    </div>
</footer>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="layout/main/main.js"></script>
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