<?php

/** @var mixed[] $table */
/** @var string $varName */ ?>

<?php if (empty($table)) : ?>
    <p class="fst-italic">Aucune donnée.</p>
<?php else : ?>
    <div class="table-responsive">
        <table class="table table-sm" aria-label="liste des variables <?= $varName ?>" style="max-width: 100%">
            <thead>
                <th scope="col">Clé</th>
                <th scope="col">Valeur</th>
            </thead>
            <tbody>
                <?php foreach ($table as $k => $v) : ?>
                    <tr>
                        <th scope="row"><code><?= $k ?></code></th>
                        <td><?php \Symfony\Component\VarDumper\VarDumper::dump($v) ?></td>
                    </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </div>
<?php endif ?>
