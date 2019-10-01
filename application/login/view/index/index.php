<form method="post">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-body">
                    <h1>Connexion</h1>
                    <div class="form-group">
                        <?php echo $this->getForm()->label('auth', 'Pseudo ou adresse email (test: Rémi)'); ?>
                        <?php echo $this->getForm()->input('auth'); ?>
                    </div>
                    <div class="form-group">
                        <?php echo $this->getForm()->label('password', 'Mot de passe (test: password)'); ?>
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