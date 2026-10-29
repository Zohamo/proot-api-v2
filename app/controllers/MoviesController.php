<?php

namespace App\Controllers;

use App\Models\MovieModel;
use Core\AbstractController;
use Core\View;

class MoviesController extends AbstractController
{
    /**
     * URL de la page d'accueil de la ressource.
     * @var string
     */
    protected $homeUrl = "movies";

    /**
     * Titre de la page d'accueil de la ressource.
     * @var string
     */
    public $homeTitle = "Movies";

    /**
     * Noms des actions permettant d'identifier les formulaires pour la génération de jetons CSRF.
     * @var string[]
     */
    public $actions;

    /**
     * Liens à mettre en valeur dans la barre de navigation.
     * @var string[]
     */
    protected $activeLinks = ['movie'];

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
        parent::__construct();
        $this->model = new MovieModel();
        $this->actions = ['updateOrCreate' => "updateOrCreateMovie"];
    }

    /**
     * Affiche une liste des ressources.
     *
     * @route("/movies", methods={"GET"})
     */
    public function index()
    {
        View::render(
            // Vue à utiliser dans le dossier `views`
            'movies/movies-index',
            // Paramètres à afficher dans la vue
            [
                // Titre de la page
                'title' => $this->homeTitle,
                // Liste des ressources à afficher
                'movies' => $this->model->all(),
            ],
            // Liste des modules/scripts à implémenter
            ['modules' => ['data-tables', 'form-modal']]
        );
    }

    /**
     * Affiche la page de détails de la ressource.
     *
     * @param  integer $id
     * @route("/movies/{id}", methods={"GET"})
     */
    public function detail($id)
    {
        View::render(
            // Vue à utiliser dans le dossier `views`
            'movies/movies-detail',
            // Paramètres à afficher dans la vue
            [
                // Titre de la page
                'title' => "Movie #$id",
                // Fil d'Ariane
                'breadcrumbs' => [[
                    'title' => $this->homeTitle,
                    'url' => $this->homeUrl
                ]],
                // Liste des ressources à afficher
                'movie' => $this->model->first($id),
            ],
            // Liste des modules/scripts à implémenter
            ['modules' => ['form-modal']]
        );
    }
}
