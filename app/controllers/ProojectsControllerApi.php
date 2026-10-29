<?php

namespace App\Controllers;

use App\Functions\HttpUtils;
use App\Models\ProojectModel;

class ProojectsControllerApi
{
    /**
     * Modèle associé au contrôleur.
     * @var ProojectModel
     */
    protected $model;

    /**
     * Crée une instance du contrôleur et définit ses propriétés.
     */
    public function __construct()
    {
        $this->model = new ProojectModel();
    }

    /**
     * Renvoie une liste de toutes les ressources.
     *
     * @route("/proojects", methods={"GET"})
     */
    public function getAll()
    {
        HttpUtils::jsonResponse($this->model->all());
    }

    /**
     * Renvoie la ressource spécifiée.
     *
     * @param  string $slug
     * @route("/proojects/{slug}", methods={"GET"})
     */
    public function get(string $slug)
    {
        $res = $this->model->first($slug);
        if (empty($res)) {
            HttpUtils::jsonResponse(null, 404);
        } else {
            HttpUtils::jsonResponse($res);
        }
    }
}
