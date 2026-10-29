<?php

namespace App\Controllers;

use App\Functions\HttpUtils;
use App\Models\MovieModel;

class MoviesControllerApi
{
    /**
     * Modèle associé au contrôleur.
     * @var MovieModel
     */
    protected $model;

    /**
     * Crée une instance du contrôleur et définit ses propriétés.
     */
    public function __construct()
    {
        $this->model = new MovieModel();
    }

    /**
     * Renvoie une liste de toutes les ressources.
     *
     * @route("/movies", methods={"GET"})
     */
    public function getAll()
    {
        HttpUtils::jsonResponse($this->model->all());
    }

    /**
     * Renvoie la ressource spécifiée.
     *
     * @param  string $slug
     * @route("/movies/{slug}", methods={"GET"})
     */
    public function get($slug)
    {
        $res = $this->model->first($slug);
        if (empty($res)) {
            HttpUtils::jsonResponse(null, 404);
        } else {
            HttpUtils::jsonResponse($res);
        }
    }
}
