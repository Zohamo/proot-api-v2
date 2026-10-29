<?php

/** @var \App\Entities\user $auth */
/** @var \App\Entities\user[] $users */ ?>

<div class="container-fluid">
    <header class="page-header d-lg-flex justify-content-between align-items-center">
        <i class="page-icon fa-solid fa-scroll" aria-hidden="true"></i>
        <h1 class="page-title"><?= $title ?></h1>
        <div class="page-actions ms-auto text-center d-print-none">
            <button type="button" class="btn btn-outline-primary m-1" data-bs-toggle="modal" data-bs-target="#modalFormUser" data-test="add">
                <i class="fa-solid fa-plus me-2" aria-hidden="true"></i> Ajouter un utilisateur
            </button>
            <button type="button" class="btn btn-secondary m-1" data-bs-toggle="modal" data-bs-target="#modalListRoles">
                <i class="fa-solid fa-user-tag me-2" aria-hidden="true"></i> Liste des rôles
            </button>
        </div>
    </header>
    <div class="page-content">
        <div class="table-responsive data-table-container">
            <table class="data-table table table-sm table-striped table-hover align-middle" id="tableUsers" aria-label="Liste des <?= $title ?>">
                <thead>
                    <tr>
                        <?php if ($auth->isDev()) : ?>
                            <th scope="col"><code><?= alias("user", "usr_id") ?></code></th>
                        <?php endif ?>
                        <?php foreach (["usr_username", "usr_fullname", "usr_email", "rol_label"] as $col) : ?>
                            <th scope="col"><?= alias("user", $col) ?></th>
                        <?php endforeach ?>
                        <th scope="col" class="d-print-none not-sortable"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user) : ?>
                        <tr class="<?= $user->usr_deleted_at ? "table-secondary text-muted small" : "" ?>">
                            <?php if ($auth->isDev()) : ?>
                                <td><code>#<?= $user->usr_id ?></code></td>
                            <?php endif ?>
                            <td class="font-weight-bold">
                                <a href="<?= url() ?>/users/<?= $user->usr_id ?>" title="voir le détail">
                                    <?= $user->usr_username ?>
                                </a>
                            </td>
                            <td class="font-weight-bold">
                                <a href="<?= url() ?>/users/<?= $user->usr_id ?>" title="voir le détail">
                                    <?= $user->usr_firstname ?> <?= $user->usr_lastname ?>
                                </a>
                            </td>
                            <td><?= $user->usr_email ?></td>
                            <td><?= $user->role->rol_label ?></td>
                            <td class="d-print-none">
                                <?php if (!$user->isDev() || $auth->isDev()) : ?>
                                    <div class="btn-group-actions">
                                        <!-- Bouton de modification -->
                                        <button type="button" class="btn btn-icon btn-outline-accent border-0" data-bs-hover="tooltip" title="Modifier" data-bs-toggle="modal" data-bs-target="#modalFormUser" data-id="<?= $user->usr_id ?>" aria-label="Modifier" data-test="edit">
                                            <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                        </button>
                                        <?php if ($user->usr_deleted_at) : ?>
                                            <!-- Bouton de réactivation -->
                                            <a href="<?= url() . "/users/{$user->usr_id}/reactivate" ?>" class="btn btn-icon btn-outline-success border-0" data-bs-hover="tooltip" title="Réactiver" aria-label="Réactiver" data-test="reactivate">
                                                <i class="fa-solid fa-check" aria-hidden="true"></i>
                                            </a>
                                            <!-- Bouton de suppression -->
                                            <button type="button" class="btn btn-icon btn-outline-danger border-0" data-bs-hover="tooltip" title="Supprimer définitivement" data-bs-toggle="modal" data-bs-target="#modalConfirmDeleteUser" data-id="<?= $user->usr_id ?>" aria-label="Supprimer définitivement" data-test="delete">
                                                <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                            </button>
                                        <?php else : ?>
                                            <!-- Bouton de désactivation -->
                                            <a href="<?= url() . "/users/{$user->usr_id}/deactivate" ?>" class="btn btn-icon btn-outline-danger border-0" data-bs-hover="tooltip" title="Désactiver" aria-label="Désactiver" data-test="deactivate">
                                                <i class="fa-solid fa-times" aria-hidden="true"></i>
                                            </a>
                                        <?php endif ?>
                                    </div>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modale : Liste des rôles -->
<?php include "components/users-roles-modal-list.php" ?>

<!-- Modale : Formulaire d'ajout/modification -->
<?php include path("views") . "/users/components/users-modal-form.php" ?>

<!-- Modale : Confirmation de suppression -->
<?php modalConfirmDelete(
    "modalConfirmDeleteUser",
    url() . "/users/{?}/delete",
    "Êtes-vous sûr·e de vouloir supprimer définitivement cet utilisateur&nbsp;?<br />"
        . "<strong>Cette action est irréversible&nbsp;!</strong>"
) ?>
