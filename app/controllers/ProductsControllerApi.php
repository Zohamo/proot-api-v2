<?php

namespace App\Controllers;

use App\Controllers;
use App\Models\ProductModel;
use Core\AbstractControllerApi;

class ProductsControllerApi extends AbstractControllerApi
{
    /**
     * Modèle associé au contrôleur.
     * @var ProductModel
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
        $this->model = new ProductModel();
        $this->actions = array_fill_keys(
            ['create', 'update'],
            (new ProductsController)->actions['updateOrCreate']
        );
    }
}
