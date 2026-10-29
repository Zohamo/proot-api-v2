<?php if (empty($varVal)) : ?>
    <p class="fst-italic">Aucune requête SQL.</p>
<?php else : ?>

    <div class="table-responsive">
        <table class="table table-sm table-dark" aria-label="liste des requêtes SQL" style="max-width: 100%">
            <thead>
                <tr>
                    <?php foreach (["Contexte", "SQL", "Données", "Entité à instancier", "Temps d'exécution"] as $col) : ?>
                        <th scope="col"><?= $col ?></th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <?php foreach ($varVal as $q) : ?>
                <tr>
                    <td>
                        <?php $q['backtrace'] = array_splice($q['backtrace'], 1, -1) ?>
                        <ul>
                            <?php foreach ($q['backtrace'] as $t) : ?>
                                <li>
                                    <small class="fst-italic"><?= str_replace(env('APP_ROOT'), '', $t['file']) ?>::<?= $t['line'] ?></small><br />
                                    <?= $t['class'] ?><?= $t['type'] ?><?= $t['function'] ?>()
                                </li>
                            <?php endforeach ?>
                        </ul>
                    <td><?= \App\Functions\SqlFormatter::format($q['sql']) ?></td>
                    <td>
                        <?php if (!empty($q['values'])) : ?>
                            <code>[<?php foreach ($q['values'] as $k => $v) : ?>'<?= $k ?>' => "<?= $v ?>", <?php endforeach ?>]</code>
                        <?php endif ?>
                    <td>
                        <?php if (!empty($q['className'])) : ?>
                            <code><?= $q['className'] ?></code>
                        <?php endif ?>
                    </td>
                    <td class="text-end">
                        <?= isset($q['time']) ? formatTime($q['time']) : "" ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    </div>
<?php endif ?>
