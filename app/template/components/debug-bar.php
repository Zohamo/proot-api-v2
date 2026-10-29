<?php

/** @var \App\Entities\User $auth */

$varGlob = ["SQL" => \Core\QueryBuilder::$queries, "Get" => $_GET, "Post" => $_POST, "Files" => $_FILES, "Session" => $_SESSION, "Cookies" => $_COOKIE]; ?>

<div style="background-color: #d3d3d3;" class="order-2">

    <ul class="nav nav-pills" id="debugTabList" role="tablist">
        <li class="nav-item">
            <button type="button" id="closeDebugTabsContent" class="nav-link" aria-controls="debugTabContent" aria-selected="false" style="padding: 4px 0.5rem;">
                <i class="fa-solid fa-bug" aria-hidden="true"></i>
            </button>
        </li>
        <?php foreach (array_keys($varGlob) as $varName) : ?>
            <li class="nav-item" role="presentation">
                <button type="button" id="<?= $varName ?>-tab" class="nav-link text-uppercase py-1 px-2" data-bs-toggle="tab" data-bs-target="#debug-<?= $varName ?>" role="tab" aria-controls="<?= $varName ?>" aria-selected="false">
                    <?= $varName ?>
                </button>
            </li>
        <?php endforeach ?>
        <li class="nav-item ms-auto d-flex align-items-center">
            <i class="fa-solid fa-user-tag me-1" aria-hidden="true"></i>
            <select id="superSwitchRole" class="form-select form-select-sm" data-action="<?= url() ?>/tools/switch-role" data-bs-hover="tooltip" title="Changer de rôle" aria-label="Changer de rôle" style="background-color: #c9c9c9;">
                <?php foreach ((new \App\Models\UserRoleModel)->all() as $role) : ?>
                    <option value="<?= $role->rol_id ?>" <?= $role->rol_id == $auth->fk_role_id ? "selected" : "" ?>><?= $role->rol_label ?></option>
                <?php endforeach ?>
            </select>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="<?= \AppConstants::TELA_URL_DOC . \AppConstants::TELA_VERSION ?>" target="_blank" data-bs-hover="tooltip" title="Documentation de Tela" style="padding: 4px 0.5rem;">
                <i class="fa-solid fa-scroll me-1" aria-hidden="true"></i> Tela <small><?= \AppConstants::TELA_VERSION ?></small>
            </a>
        </li>
    </ul>

    <div class="container-fluid tab-content" id="debugTabContent">
        <?php foreach ($varGlob as $varName => $varVal) : ?>
            <div class="tab-pane fade pt-2 px-3" id="debug-<?= $varName ?>" role="tabpanel" aria-labelledby="debug-<?= $varName ?>-tab" tabindex="0">
                <?php if ($varName === "SQL") : ?>
                    <?php include "debug-bar-table-sql.php" ?>
                <?php else : ?>
                    <?php if ($varName === "Session") : ?>
                        <div class="d-flex justify-content-between align-items-center">
                            <h3><?= $varName ?></h3>
                            <a class="btn btn-sm btn-danger" href="<?= url() ?>/tools/detruire-session">Détruire la session</a>
                        </div>
                    <?php else : ?>
                        <h3><?= $varName ?></h3>
                    <?php endif ?>
                    <?php $table = $varVal; ?>
                    <?php include "debug-bar-table.php" ?>
                <?php endif ?>
            </div>
        <?php endforeach ?>
    </div>

    <script>
        jQuery(function() {
            /**
             * Ferme tous les onglets et enlève la mise en forme de l'onglet précedemment sélectionné.
             */
            $("#closeDebugTabsContent").on("click", function() {
                $("#debugTabList .nav-item, #debugTabList .nav-link, #debugTabContent .tab-pane").removeClass("active")
            })

            /**
             * Change le rôle de l'utilisateur authentifié à la volée en modifiant le <select>.
             */
            $("#superSwitchRole").on("change", function() {
                $.post($(this).data("action"), {
                    fk_role_id: $(this).val()
                }, function(data, textStatus, jqXHR) {
                    location.reload();
                })
            })
        })
    </script>

</div>
