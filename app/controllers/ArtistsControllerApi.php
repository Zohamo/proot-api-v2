<?php

namespace App\Controllers;

use App\Functions\HttpUtils;
use App\Models\ArtistModel;

class ArtistsControllerApi
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
    }

    /**
     * Renvoie une liste de toutes les ressources.
     *
     * @route("/artists", methods={"GET"})
     */
    public function getAll()
    {
        HttpUtils::jsonResponse($this->model->all());
    }

    /**
     * Renvoie la ressource spécifiée.
     *
     * @param  string $id
     * @route("/artists/{id}", methods={"GET"})
     */
    public function get($id)
    {
        $res = $this->model->toArray()
            ->where($this->model->pk() . " = ?")
            ->select([$id]);

        if (empty($res)) {
            HttpUtils::jsonResponse(null, 404);
        } else {
            HttpUtils::jsonResponse($res[0]);
        }
    }
}
