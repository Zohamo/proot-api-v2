<div class="container p-5">
    <h1>Page introuvable</h1>
    <?php if (!empty($message)) : ?>
        <p class="text-muted"><?= $message ?></p>
    <?php else : ?>
        <p>Désolé, la page que vous cherchez n'existe pas.</p>
    <?php endif ?>
</div>
