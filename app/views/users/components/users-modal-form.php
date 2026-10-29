<?php

/** @var string $actionName
 * @var \App\Entities\UserRole[] $roles */ ?>

<form class="form-modal form-validatable" id="formUser" data-url-api="<?= url() ?>/api/users">
    <?= csrfToken($actionName) ?>
    <div class="modal fade" id="modalFormUser" data-bs-backdrop="static" tabindex="-1" aria-labelledby="modalFormUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content position-relative">
                <?php spinner(true) ?>
                <div class="modal-header">
                    <h5 class="modal-title text-accent" id="modalFormUserLabel">
                        <i class="fa-solid fa-edit" aria-hidden="true"></i> <span data-text-edit="Modifier un">Nouvel</span> utilisateur
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer" />
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="usr_lastname" name="usr_lastname" placeholder="<?= alias("user", "usr_lastname") ?>" maxlength="255" required />
                                <label for="usr_lastname" class="form-label"><?= alias("user", "usr_lastname") ?></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="usr_firstname" name="usr_firstname" placeholder="<?= alias("user", "usr_firstname") ?>" maxlength="255" required />
                                <label for="usr_firstname" class="form-label"><?= alias("user", "usr_firstname") ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="usr_username" name="usr_username" placeholder="<?= alias("user", "usr_username") ?>" maxlength="255" />
                                <label class="form-label" for="usr_username"><?= alias("user", "usr_username") ?></label>
                            </div>
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating">
                                <input type="email" class="form-control" id="usr_email" name="usr_email" placeholder="Compte LDAP" maxlength="255" required />
                                <label class="form-label" for="usr_email"><?= alias("user", "usr_email") ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <div class="form-floating">
                                <select class="form-select" id="fk_role_id" name="fk_role_id" required>
                                    <option value="">Choisissez un rôle&hellip;</option>
                                    <?php foreach ($roles as $role) : ?>
                                        <option value="<?= $role->rol_id ?>"><?= $role->rol_label ?></option>
                                    <?php endforeach ?>
                                </select>
                                <label class="form-label" for="fk_role_id"><?= alias("user", "rol_label") ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-errors-container alert alert-danger fade hide d-none" role="alert">
                        <div class="d-flex align-items-center">
                            <div>
                                <p class="mb-0">Une erreur est survenue.</p>
                                <ul class="list-form-errors mb-0"></ul>
                            </div>
                            <button type="button" class="btn btn-close ms-auto" aria-label="Fermer" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <span>Annuler</span>
                    </button>
                    <button type="submit" class="btn btn-accent">
                        <span>Enregistrer</span>
                        <i class="fa-solid fa-save ms-2" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
