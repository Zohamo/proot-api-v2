<?php

/** @var string $query */
/** @var int $nbResults */
/** @var array $results */ ?>

<div class="container-fluid">
    <header class="page-header">
        <i class="page-icon fa-solid fa-magnifying-glass" aria-hidden="true"></i>
        <h1 class="page-title"><?= $title ?></h1>
    </header>
    <!-- FORMULAIRE DE RECHERCHE -->
    <section class="border-bottom pb-4">
        <?php include "components/search-form.php" ?>
    </section>
    <?php if ($query) : ?>
        <!-- RESULTATS: header -->
        <section class="d-md-flex justify-content-between align-items-center my-3">
            <p class="my-3">
                Votre recherche
                <strong>“<?= $query ?>”</strong>&nbsp;:
                <span class="text-accent">
                    <strong><?= formatNumber($nbResults) ?></strong> résultat<?= $nbResults > 1 ? "s" : "" ?>
                    dans
                    <?php foreach (array_keys($results) as $categoryName) : ?>
                        <a href="#results-<?= $categoryName ?>" class="me-3"><?= ucfirst($categoryName) ?></a>
                    <?php endforeach ?>
                </span>
            </p>
        </section>
        <!-- RESULTATS: content -->
        <section class="search-resultats">
            <?php if ($results) : ?>
                <?php foreach ($results as $categoryName => $categoryResults) : ?>
                    <?php if (!empty($categoryResults)) : $displayResults = true; ?>
                        <section id="results-<?= $categoryName ?>">
                            <?php if (is_readable(path('views') . "/search/components/search-results-$categoryName.php")) : ?>
                                <?php include "components/search-results-$categoryName.php" ?>
                            <?php else : ?>
                                <?php include "components/search-results.php" ?>
                            <?php endif ?>
                        </section>
                    <?php endif ?>
                <?php endforeach ?>
            <?php endif ?>
            <?php if (!$results || empty($displayResults)) : ?>
                <h3 class="text-center"><em>Aucun résultat n'a été trouvé.</em></h3>
            <?php endif ?>
        </section>
    <?php endif ?>
</div>
