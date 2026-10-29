<?php

/** @var array[] $breadcrumbs */ ?>

<div class="container-fluid mt-2" data-test="breadcrumbs">
    <nav aria-label="breadcrumb" role="navigation">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item">
                <a href="<?= url() ?>">Accueil</a>
            </li>
            <?php if (!empty($breadcrumbs)) : ?>
                <?php foreach ($breadcrumbs as $ii => $breadcrumb) : ?>
                    <li class="breadcrumb-item">
                        <a href="<?= url() . "/" . $breadcrumb['url'] ?>">
                            <?= $breadcrumb['title'] ?>
                        </a>
                    </li>
                <?php endforeach ?>
            <?php endif ?>
            <li class="breadcrumb-item active" aria-current="page">
                <?= $title ?: "Accueil" ?>
            </li>
        </ol>
    </nav>
</div>
