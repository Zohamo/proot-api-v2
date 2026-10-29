<div class="container p-5">
    <h1>Accès non autorisé</h1>
    <?php if (!empty($message)) : ?>
        <p class="text-muted"><?= $message ?></p>
    <?php else : ?>
        <p>Vous n'avez pas les droits suffisants pour accéder à cette page.</p>
    <?php endif ?>
</div>
