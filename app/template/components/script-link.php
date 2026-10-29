<?php

/** @var string $scriptUrlSuffix */
/** @var array[] $scripts */ ?>

<?php $scriptUrlSuffix = "?v" . config("version") ?>
<?php foreach ($scripts as $script) : ?>
    <?php switch ($script['type']):
        case "css": ?>
            <link type="text/css" href="<?= $script['url'] . $scriptUrlSuffix ?>" rel="stylesheet" />
        <?php break;
        case "js": ?>
            <script type="text/javascript" src="<?= $script['url'] . $scriptUrlSuffix ?>"></script>
        <?php break;
        case "js-module": ?>
            <script type="module" src="<?= $script['url'] . $scriptUrlSuffix ?>"></script>
    <?php break;
        default:
            throw new \DomainException("Le type de script '{$script['type']}' est inconnu.");
    endswitch ?>
<?php endforeach ?>
