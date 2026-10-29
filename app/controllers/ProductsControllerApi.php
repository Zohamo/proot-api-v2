<?php

namespace App\Controllers;

use App\Functions\HttpUtils;
use App\Models\ProductModel;

class ProductsControllerApi
{
    /**
     * Modèle associé au contrôleur.
     * @var ProductModel
     */
    protected $model;

    /**
     * Crée une instance du contrôleur et définit ses propriétés.
     */
    public function __construct()
    {
        $this->model = new ProductModel();
    }

    /**
     * Renvoie une liste de toutes les ressources.
     *
     * @route("/products", methods={"GET"})
     */
    public function getAll()
    {
        HttpUtils::jsonResponse($this->model->all());
    }
}
