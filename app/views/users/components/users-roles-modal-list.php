<?php

/** @var \App\Entities\UserRole[] $roles */ ?>

<div class="modal fade" id="modalListRoles" tabindex="-1" aria-labelledby="modalListRolesLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-md-down modal-lg modal-dialog-centered">
        <div class="modal-content position-relative">
            <div class="modal-header">
                <h5 class="modal-title text-aero" id="modalListRolesLabel">
                    <i class="fa-solid fa-user-tag" aria-hidden="true"></i> Liste des rôles utilisateur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer" data-test="close" />
            </div>
            <div class="modal-body">
                <table class="table align-middle" id="tableRoles" aria-label="Liste des rôles utilisateur">
                    <thead>
                        <tr>
                            <th scope="col"><?= alias('userRole', 'rol_label') ?></th>
                            <th scope="col"><?= alias('userRole', 'rol_description') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role) : ?>
                            <tr>
                                <td><?= $role->rol_label ?></td>
                                <td class="font-italic text-muted"><?= str_replace("\r\n", "<br />", $role->rol_description) ?></td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-test="close">
                    <span>Fermer</span>
                </button>
            </div>
        </div>
    </div>
</div>
