<?php

namespace App\Controllers;

use App\Controllers;
use App\Models\ArtistModel;
use Core\AbstractControllerApi;

class ArtistsControllerApi extends AbstractControllerApi
{
    /**
     * Modèle associé au contrôleur.
     * @var ArtistModel
     */
    protected $model;

    /**
     * Noms des actions permettant d'identifier les formulaires pour la récupération des jetons CSRF.
     * @var string[]
     */
    protected $actions;

    /**
     * Crée une instance du contrôleur et définit ses propriétés.
     */
    public function __construct()
    {
        $this->model = new ArtistModel();
        $this->actions = array_fill_keys(
            ['create', 'update'],
            (new ArtistsController)->actions['updateOrCreate']
        );
    }
}
