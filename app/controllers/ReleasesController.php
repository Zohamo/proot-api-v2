<?php

namespace App\Controllers;

use App\Models\ReleaseModel;
use Core\AbstractController;
use Core\View;

class ReleasesController extends AbstractController
{
    /**
     * URL de la page d'accueil de la ressource.
     * @var string
     */
    protected $homeUrl = "releases";

    /**
     * Titre de la page d'accueil de la ressource.
     * @var string
     */
    public $homeTitle = "Releases";

    /**
     * Noms des actions permettant d'identifier les formulaires pour la génération de jetons CSRF.
     * @var string[]
     */
    public $actions;

    /**
     * Liens à mettre en valeur dans la barre de navigation.
     * @var string[]
     */
    protected $activeLinks = ['release'];

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
        parent::__construct();
        $this->model = new ReleaseModel();
        $this->actions = ['updateOrCreate' => "updateOrCreateRelease"];
    }

    /**
     * Affiche une liste des ressources.
     *
     * @route("/releases", methods={"GET"})
     */
    public function index()
    {
        View::render(
            // Vue à utiliser dans le dossier `views`
            'releases/releases-index',
            // Paramètres à afficher dans la vue
            [
                // Titre de la page
                'title' => $this->homeTitle,
                // Liste des ressources à afficher
                'releases' => $this->model->all(),
            ],
            // Liste des modules/scripts à implémenter
            ['modules' => ['data-tables', 'form-modal']]
        );
    }

    /**
     * Affiche la page de détails de la ressource.
     *
     * @param  integer $id
     * @route("/releases/{id}", methods={"GET"})
     */
    public function detail($id)
    {
        View::render(
            // Vue à utiliser dans le dossier `views`
            'releases/releases-detail',
            // Paramètres à afficher dans la vue
            [
                // Titre de la page
                'title' => "Release #$id",
                // Fil d'Ariane
                'breadcrumbs' => [[
                    'title' => $this->homeTitle,
                    'url' => $this->homeUrl
                ]],
                // Liste des ressources à afficher
                'release' => $this->model->first($id),
            ],
            // Liste des modules/scripts à implémenter
            ['modules' => ['form-modal']]
        );
    }
}
