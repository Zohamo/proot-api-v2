<?php

/** @var string $categoryName */
/** @var array $categoryResults */ ?>

<h2><?= alias($categoryName, $categoryName) ?> <small><em>(<?= formatNumber(count($categoryResults)) ?> résultat<?= count($categoryResults) > 1 ? "s" : "" ?>)</em></small></h2>
<div class="table-responsive">
    <table class="table table-sm table-striped table-hover align-middle" aria-label="Résultats de la recherche de <?= $categoryName ?> dans <?= alias($categoryName, $categoryName) ?>">
        <thead>
            <?php foreach (array_keys((array) $categoryResults[0]) as $field) : ?>
                <th scope="col"><?= alias($categoryName, ltrim($field, "\x00*\x00")) ?></th>
            <?php endforeach ?>
        </thead>
        <tbody>
            <?php foreach ($categoryResults as $result) : ?>
                <tr>
                    <?php foreach ((array) $result as $value) : ?>
                        <td><?= is_string($value) ? $value : "" ?></td>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
