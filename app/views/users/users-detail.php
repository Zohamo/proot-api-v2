<?php

/** @var \App\Entities\User $auth
 * @var \App\Entities\User $user */ ?>

<div class="container-fluid">
    <header class="page-header d-lg-flex justify-content-between align-items-center">
        <i class="page-icon fa-solid fa-users" aria-hidden="true"></i>
        <h1 class="page-title"><?= $title ?></h1>
        <div class="page-actions ms-auto text-center d-print-none">
            <button type="button" class="btn btn-outline-primary m-1" data-bs-toggle="modal" data-bs-target="#modalFormUser" data-id="<?= $user->usr_id ?>" data-test="edit">
                <i class="fa-solid fa-pen me-2" aria-hidden="true"></i> Modifier l'utilisateur
            </button>
            <?php if ($user->usr_deleted_at) : ?>
                <!-- Bouton de réactivation -->
                <a href="<?= url() . "/users/{$user->usr_id}/reactivate" ?>" class="btn btn-outline-success" data-test="reactivate">
                    <i class="fa-solid fa-check me-2" aria-hidden="true"></i> Réactiver l'utilisateur
                </a>
                <!-- Bouton de suppression -->
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalConfirmDeleteUser" data-id="<?= $user->usr_id ?>" data-test="delete">
                    <i class="fa-solid fa-trash me-2" aria-hidden="true"></i> Supprimer l'utilisateur
                </button>
            <?php else : ?>
                <!-- Bouton de désactivation -->
                <a href="<?= url() . "/users/{$user->usr_id}/deactivate" ?>" class="btn btn-outline-danger" data-test="deactivate">
                    <i class="fa-solid fa-times me-2" aria-hidden="true"></i> Désactiver l'utilisateur
                </a>
            <?php endif ?>
        </div>
    </header>
    <div class="page-content">
        <div class="row" data-test="detail-user">
            <div class="col-12 col-md-6">
                <div class="row">
                    <dl class="col-12 col-md-6" data-test="usr_lastname">
                        <dt><?= alias("user", "usr_lastname") ?></dt>
                        <dd class="h3"><?= $user->lastname() ?></dd>
                    </dl>
                    <dl class="col-12 col-md-6" data-test="usr_firstname">
                        <dt><?= alias("user", "usr_firstname") ?></dt>
                        <dd class="h3"><?= $user->usr_firstname ?></dd>
                    </dl>
                </div>
                <div class="row">
                    <dl class="col-12 col-md-6" data-test="usr_username">
                        <dt><?= alias("user", "usr_username") ?></dt>
                        <dd><?= $user->usr_username ?></dd>
                    </dl>
                    <dl class="col-12 col-md-6" data-test="usr_email">
                        <dt><?= alias("user", "usr_email") ?></dt>
                        <dd><?= $user->usr_email ?></dd>
                    </dl>
                </div>
                <div class="row">
                    <dl class="col-12" data-test="rol_label">
                        <dt><?= alias("user", "rol_label") ?></dt>
                        <dd><?= $user->role->rol_label ?></dd>
                    </dl>
                </div>
            </div>
            <?php if ($auth->isDev()) : ?>
                <div class="col-12 col-md-6">
                    <section class="card text-bg-secondary">
                        <div class="card-body">
                            <h3><i class="fa-solid fa-screwdriver-wrench me-1" aria-hidden="true"></i> Informations techniques</h3>
                            <div class="row">
                                <dl class="col-12 col-md-6">
                                    <dt><?= alias("user", "usr_created_at") ?></dt>
                                    <dd><?= formatDatetime($user->usr_created_at) ?></dd>
                                </dl>
                                <dl class="col-12 col-md-6">
                                    <dt><?= alias("user", "usr_deleted_at") ?></dt>
                                    <dd><?= $user->usr_deleted_at ? formatDatetime($user->usr_deleted_at) : "-" ?></dd>
                                </dl>
                            </div>
                            <div class="row">
                                <dl class="col-12 col-md-6">
                                    <dt><?= alias("user", "usr_last_login_at") ?></dt>
                                    <dd><?= $user->usr_last_login_at ? formatDatetime($user->usr_last_login_at) : "-" ?></dd>
                                </dl>
                                <dl class="col-12 col-md-6">
                                    <dt><?= alias("user", "usr_is_super_admin") ?></dt>
                                    <dd><?= $user->usr_is_super_admin ? "Oui" : "Non" ?></dd>
                                </dl>
                            </div>
                        </div>
                    </section>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modale : Formulaire de modification -->
<?php include "components/users-modal-form.php" ?>

<!-- Modale : Confirmation de suppression -->
<?php modalConfirmDelete(
    "modalConfirmDeleteUser",
    url() . "/users/{?}/delete",
    "Êtes-vous sûr·e de vouloir supprimer définitivement cet utilisateur&nbsp;?<br />"
        . "<strong>Cette action est irréversible&nbsp;!</strong>"
) ?>
