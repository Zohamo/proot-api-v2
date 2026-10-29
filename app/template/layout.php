<?php

/** @var \App\Entities\User $auth */
/** @var array[] $scripts */
/** @var string $view */ ?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title><?= titleHead($title) ?></title>
    <link rel="icon" href="<?= url('public') ?>/favicon.svg" />

    <?php scripts('head', $scripts) ?>

</head>

<body class="d-flex flex-column">
    <header class="main-header d-print-none order-0">
        <?php include path('template') . "/components/header-navbar.php"; ?>
    </header>
    <main class="pb-5 mb-auto order-1">
        <?php include $view; ?>
    </main>
    <footer class="main-footer d-print-none order-3">
        <?php include path('template') . "/components/footer.php"; ?>
    </footer>
    <?php scripts('end', $scripts) ?>
</body>

</html>
