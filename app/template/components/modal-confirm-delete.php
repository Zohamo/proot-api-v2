<?php

/** @var string $modalId */
/** @var string $message */
/** @var string $url */ ?>

<div id="<?= $modalId ?>" class="modal-confirm-delete modal fade" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation de suppression</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer" />
            </div>
            <div class="modal-body">
                <p class="mb-0"><?= $message ?></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" data-test="cancel">
                    Annuler <i class="fa-solid fa-times ms-2" aria-hidden="true"></i>
                </button>
                <a href="<?= $url ?>" class="btn btn-confirm btn-danger" data-test="confirm">
                    Supprimer <i class="fa-solid fa-trash ms-2" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</div>
