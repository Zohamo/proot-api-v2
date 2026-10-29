<form class="form-horizontal form-main-search mb-0" method="POST" action="<?= url() ?>/search">
    <div id="modalMainSearch" tabindex="-1" class="modal fade" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen-sm-down modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Que recherchez-vous&nbsp;?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer" />
                </div>
                <div class="modal-body">
                    <div class="nav-search my-3 my-md-0 mx-lg-3">
                        <label for="recherche" class="sr-only form-label"><?= alias("recherche", "recherche") ?></label>
                        <div class="input-group">
                            <input type="search" class="form-control" minlength="3" id="query" name="query" placeholder="<?= alias("recherche", "placeholder") ?>" aria-label="Recherche" />
                            <button type="submit" class="btn btn-outline-accent" aria-label="Rechercher">
                                <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
