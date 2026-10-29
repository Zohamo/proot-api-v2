<?php

/** @var \App\Entities\User[] $categoryResults */ ?>

<h2>Utilisateur <small><em>(<?= formatNumber(count($categoryResults)) ?> résultat<?= count($categoryResults) > 1 ? "s" : "" ?>)</em></small></h2>
<div class="table-responsive">
    <table class="table table-sm table-striped table-hover align-middle" aria-label="Liste des utilisateurs trouvés lors de la recherche">
        <thead>
            <th scope="col">NOM Prénom</th>
            <?php foreach (['usr_email', 'rol_label', 'usr_created_at', 'usr_deleted_at'] as $col) : ?>
                <th scope="col"><?= alias('user', $col) ?></th>
            <?php endforeach ?>
        </thead>
        <tbody>
            <?php foreach ($categoryResults as $user) : ?>
                <tr>
                    <td class="font-weight-bold">
                        <a href="<?= url() ?>/users/<?= $user->usr_id ?>" title="voir le détail">
                            <?= $user->fullName() ?>
                        </a>
                    </td>
                    <td><?= $user->usr_email ?></td>
                    <td><?= $user->role->rol_label ?></td>
                    <td class="small"><?= formatDatetime($user->usr_created_at, AppConstants::FORMAT_DATE_DISPLAY) ?></td>
                    <td class="small"><?= formatDatetime($user->usr_deleted_at, AppConstants::FORMAT_DATE_DISPLAY) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>
