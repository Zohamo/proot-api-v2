<?php

/** @var float $time */
/** @var mixed $result */ ?>

<div class="d-flex align-items-center">
    <button class="btn btn-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDebugQueryResultat" aria-expanded="false" aria-controls="collapseDebugQueryResultat">
        <i class="fa-solid fa-arrow-left me-2" aria-hidden="true"></i> Résultat
    </button>
    <em class="small ms-2">Temps d'exécution&nbsp;: <?= formatTime($time) ?></em>
</div>
<div class="collapse" id="collapseDebugQueryResultat">
    <?php dump($result) ?>
</div>
</div>
</div>
</div><!-- /.card-body -->
</div><!-- /.card -->
