<form method="post">
    <div class="row">
        <div class="col-md-6 offset-md-3">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="form-group">
                        <?php echo $this->getForm()->label('auth', 'Pseudo ou adresse email'); ?>
                        <?php echo $this->getForm()->input('auth'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->getForm()->label('password', 'Mot de passe'); ?>
                        <?php echo $this->getForm()->input('password', [
                            'type' => 'password',
                        ]); ?>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Connexion</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
