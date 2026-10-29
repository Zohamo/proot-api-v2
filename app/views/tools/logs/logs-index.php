<?php

/** @var \App\Entities\Log[] $logs */
/** @var string[] $files */ ?>

<div class="container-fluid">
    <header class="page-header d-lg-flex justify-content-between align-items-center">
        <i class="page-icon fa-solid fa-triangle-exclamation" aria-hidden="true"></i>
        <h1 class="page-title"><?= $title ?></h1>
    </header>
    <div class="page-content">
        <ul class="nav nav-pills nav-tabs-persistent mb-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="db-tab" data-bs-toggle="tab" data-bs-target="#db-tab-pane" type="button" role="tab" aria-controls="db-tab-pane" aria-selected="true">
                    <i class="fa-solid fa-database me-1" aria-hidden="true"></i> Base de données
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="files-tab" data-bs-toggle="tab" data-bs-target="#files-tab-pane" type="button" role="tab" aria-controls="files-tab-pane" aria-selected="false">
                    <i class="fa-solid fa-file-waveform me-1" aria-hidden="true"></i> Fichiers
                </button>
            </li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane fade show active pt-3" id="db-tab-pane" role="tabpanel" aria-labelledby="db-tab" tabindex="0">
                <?php if (empty($logs)) : ?>
                    <p class="text-muted">Aucune erreur en base de données.</p>
                <?php else : ?>
                    <div class="table-responsive data-table-container">
                        <table class="data-table table table-sm table-hover align-middle" aria-label="Liste des <?= $title ?>">
                            <thead>
                                <tr>
                                    <th scope="col"><?= alias("log", "log_date") ?></th>
                                    <th scope="col"><?= alias("log", "log_email") ?></th>
                                    <th scope="col"><?= alias("log", "log_code") ?></th>
                                    <th scope="col">Erreur</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($logs as $log) : ?>
                                    <tr>
                                        <td><?= str_replace(" ", "&nbsp;", formatDatetime($log->log_date)) ?></td>
                                        <td>
                                            <?= strtoupper($log->user->usr_lastname) ?>&nbsp;<?= $log->user->usr_firstname ?><br />
                                            <span class="fst-italic"><?= $log->fk_user_email ?></span>
                                        </td>
                                        <td class="text-center"><?= $log->log_code ?></td>
                                        <td>
                                            <?= str_replace(env("APP_ROOT"), "", $log->log_file) ?>::ligne <?= formatNumber($log->log_line) ?>
                                            <small class="text-muted"><?= $log->log_message ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif ?>
            </div>
            <div class="tab-pane fade" id="files-tab-pane" role="tabpanel" aria-labelledby="files-tab" tabindex="1">
                <div class="d-flex">
                    <div class="p-3">
                        <ul class="nav nav-pills flex-column">
                            <?php foreach ($files as $ii => $filename) : ?>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="file-<?= $ii ?>-tab" data-bs-toggle="tab" data-bs-target="#file-<?= $ii ?>-tab-pane" type="button" data-loaded="false" data-index="<?= $ii ?>" data-filename="<?= $filename ?>">
                                        <?= $filename ?>
                                    </button>
                                    <a class="nav-link" href="#"></a>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <div class="tab-content">
                        <?php foreach ($files as $ii => $filename) : ?>
                            <div class="tab-pane fade p-3" id="file-<?= $ii ?>-tab-pane" role="tabpanel" aria-labelledby="file-<?= $ii ?>-tab" tabindex="<?= $ii ?>">
                                <?php spinner() ?>
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(function() {
        /**
         * Charge et affiche le contenu des fichiers
         */
        $("[data-loaded]").on("click", function() {
            const $btn = $(this);
            if ($btn.data("loaded")) {
                return;
            }
            $.get(appUrl + "/tools/logs/files/" + $btn.data("filename"), function(data) {
                $(`#file-${$btn.data("index")}-tab-pane`).html("<samp>" + data.replaceAll("\n", "<br />") + "</samp>");
                $btn.data("loaded", true);
            });
        });
    })
</script>
