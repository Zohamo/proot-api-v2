<?php

/**
 * Exemple d'utilisation du plugin DateTime de DataTables.
 *
 * Ce plugin permet de filtrer les éléments dont la date est comprise
 * dans une plage de temps définie.
 */

/**
 * 1. Dans le contrôleur : ajouter les modules "data-tables" et
 * "data-tables-datetime" à la liste de modules à implémenter dans la vue.
 */

use Core\View;

class ExampleController
{
    /**
     * Affiche une liste de la ressource.
     */
    public function index()
    {
        View::render(
            // Vue à utiliser dans le dossier `views`
            'example/index',
            // Variables à passer à la vue
            ["items" => $this->model->all()], // NOSONAR : il s'agit d'un exemple
            // Liste des modules/scripts à implémenter
            ["modules" => ["data-tables", "data-tables-datetime"]]
        );
    }
}

/**
 * 2. Dans la vue : ajouter à la table l'attribut "data-datetime" qui définit
 * le numéro de la colonne où se trouve la date à filtrer (à partir de 0).
 * ex: "date_modification" se trouve à l'index 2 => data-datetime="2"
 */

?>

<table class="data-table" data-datetime="2" aria-label="Liste des éléments">
    <thead>
        <tr>
            <?php foreach (["titre", "categorie", "date_modification"] as $col) : ?>
                <th scope="col"><?= alias("example", $col) ?></th>
            <?php endforeach ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($items as $item) : ?>
            <tr>
                <td><?= $item->titre ?></td>
                <td><?= $item->categorie ?></td>
                <td data-sort="<?= $item->date_modification ?>"> <!-- Ici le 'data-sort' permet à DataTables de trier cette colonne sans tenir compte de la mise en forme de la date -->
                    <?= formatDatetime($item->date_modification) ?><!-- Et le formatDatetime() permet d'afficher la date sous un autre format -->
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>

<?php
/**
 * 3. Dans la vue : ajouter la zone de filtre par plage de temps
 * avec le composant "app/template/components/data-table-datetime.php".
 */
?>

<div class="data-table-container mb-3">
    <div class="table-responsive bg-light p-3 shadow">
        <div class="mb-2">
            <?php include path("template") . "/components/data-table-datetime.php" ?>
        </div>
        <table class="data-table" data-datetime="2" aria-label="Liste des éléments">
            [...]
        </table>
    </div>
</div>
