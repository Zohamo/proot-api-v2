<div class="container-fluid bg-dark">
    <div class="row align-items-center">
        <div class="text-center my-2">
            <strong class="text-light text-uppercase"><?= env("APP_TITLE") ?></strong><br />
            <small>
                Version <?= config("version") ?>
                <?php if (!in_array(env("ENV_NAME"), ['integration', 'production'])) : ?>
                    | Environnement <span class="text-uppercase"><?= env("ENV_NAME") ?></span>
                <?php endif ?>
            </small>
        </div>
    </div>
</div>
