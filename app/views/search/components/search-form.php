<?php

/** @var string[] $categories */
/** @var string $category */ ?>

<form class="form-horizontal form-page-search" method="POST" action="<?= url() ?>/search">
    <div class="d-flex flex-column flex-md-row align-items-md-end gap-3">
        <div class="flex-grow-1 mb-3">
            <label class="form-label" for="query">Que recherchez-vous&nbsp;?</label>
            <input class="form-control" type="search" value="<?= isset($query) ? $query : "" ?>" minlength="3" id="query" name="query" placeholder="<?= alias('recherche', 'placeholder') ?>" aria-label="Recherche" />
        </div>
        <?php if (count($categories) > 1) : ?>
            <div class="mb-3">
                <label class="form-label" for="category"><?= alias("recherche", "dans") ?></label>
                <select class="form-select" id="category" name="category">
                    <?php foreach ($categories as $cat) : ?>
                        <option value="<?= $cat ?>" <?= $cat == $category ? 'selected="true"' : "" ?>><?= alias('recherche', $cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif ?>
        <button type="submit" class="btn btn-primary mb-3" style="height: 38px;" title="<?= alias("recherche", "rechercher") ?>" aria-label="Rechercher">
            <?= alias("recherche", "rechercher") ?>
        </button>
    </div>
</form>
