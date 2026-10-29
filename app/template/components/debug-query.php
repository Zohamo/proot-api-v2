<?php

/** @var string $query */
/** @var string $className */
/** @var mixed[] $values */ ?>

<div class="card text-white bg-dark m-4 d-print-none">
    <div class="card-body">
        <div class="d-flex">
            <i class="fa-solid fa-bug fa-4x mt-1" aria-hidden="true"></i>
            <div class="ms-4">
                <button class="btn btn-dark card-title" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDebugQueryRequete" aria-expanded="false" aria-controls="collapseDebugQueryRequete">
                    <i class="fa-solid fa-arrow-right me-2" aria-hidden="true"></i> Requête
                </button>
                <div class="collapse mt-1" id="collapseDebugQueryRequete">
                    <table class="table table-sm table-secondary" aria-label="Paramètres de la requête">
                        <tr>
                            <th scope="row">Requête SQL</th>
                            <td class="p-2" style="background: #000;"><?= \App\Functions\SqlFormatter::format($query) ?></td>
                        </tr>
                        <?php if ($className) : ?>
                            <tr>
                                <th scope="row">Entité à instancier</th>
                                <td><code><?= $className ?></code></td>
                            </tr>
                        <?php endif ?>
                        <?php if (!empty($values)) : ?>
                            <tr>
                                <th scope="row">Valeurs</th>
                                <td>
                                    <pre class="bg-light px-2"><?php dump($values) ?></pre>
                                </td>
                            </tr>
                        <?php endif ?>
                    </table>
                </div>
