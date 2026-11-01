<?php

namespace App\Controllers;

use App\Functions\HttpUtils;
use App\Models\ReleaseModel;

class ReleasesControllerApi
{
    /**
     * Modèle associé au contrôleur.
     * @var ReleaseModel
     */
    protected $model;

    /**
     * Crée une instance du contrôleur et définit ses propriétés.
     */
    public function __construct()
    {
        $this->model = new ReleaseModel();
    }

    /**
     * Renvoie une liste de toutes les ressources.
     *
     * @route("/releases", methods={"GET"})
     */
    public function getAll()
    {
        HttpUtils::jsonResponse($this->model->all());
    }

    /**
     * Renvoie la ressource spécifiée.
     *
     * @param  string $ref Référence ou slug de la ressource à récupérer.
     * @route("/releases/{ref}", methods={"GET"})
     */
    public function get(string $ref)
    {
        $res = $ref === 'last' ? $this->model->last() : $this->model->first($ref);
        if (empty($res)) {
            HttpUtils::jsonResponse(null, 404);
        } else {
            HttpUtils::jsonResponse($res);
        }
    }
}
