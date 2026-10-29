<div class="toast-container position-fixed bottom-0 start-50 translate-middle p-3 d-print-none">
    <?php if (!empty($alerts)) : ?>
        <?php foreach ($alerts as $alert) : ?>
            <div class="toast align-items-center text-bg-<?= $alert['type'] ?> border-0 <?= $alert['autohide'] ? 'autohide' : 'show' ?>" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body"><?= $alert['message'] ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif ?>
</div>
