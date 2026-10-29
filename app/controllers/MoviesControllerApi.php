<?php

namespace App\Controllers;

use App\Controllers;
use App\Models\MovieModel;
use Core\AbstractControllerApi;

class MoviesControllerApi extends AbstractControllerApi
{
    /**
     * Modèle associé au contrôleur.
     * @var MovieModel
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
        $this->model = new MovieModel();
        $this->actions = array_fill_keys(
            ['create', 'update'],
            (new MoviesController)->actions['updateOrCreate']
        );
    }
}
