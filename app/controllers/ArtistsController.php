<?php

namespace App\Controllers;

use App\Models\ArtistModel;
use Core\AbstractController;
use Core\View;

class ArtistsController extends AbstractController
{
    /**
     * URL de la page d'accueil de la ressource.
     * @var string
     */
    protected $homeUrl = "artists";

    /**
     * Titre de la page d'accueil de la ressource.
     * @var string
     */
    public $homeTitle = "Artists";

    /**
     * Noms des actions permettant d'identifier les formulaires pour la génération de jetons CSRF.
     * @var string[]
     */
    public $actions;

    /**
     * Liens à mettre en valeur dans la barre de navigation.
     * @var string[]
     */
    protected $activeLinks = ['artist'];

    /**
     * Modèle associé au contrôleur.
     * @var ArtistModel
     */
    protected $model;

    /**
     * Crée une instance du contrôleur et définit ses propriétés.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new ArtistModel();
        $this->actions = ['updateOrCreate' => "updateOrCreateArtist"];
    }

    /**
     * Affiche une liste des ressources.
     *
     * @route("/artists", methods={"GET"})
     */
    public function index()
    {
        View::render(
            // Vue à utiliser dans le dossier `views`
            'artists/artists-index',
            // Paramètres à afficher dans la vue
            [
                // Titre de la page
                'title' => $this->homeTitle,
                // Liste des ressources à afficher
                'artists' => $this->model->all(),
            ],
            // Liste des modules/scripts à implémenter
            ['modules' => ['data-tables', 'form-modal']]
        );
    }

    /**
     * Affiche la page de détails de la ressource.
     *
     * @param  integer $id
     * @route("/artists/{id}", methods={"GET"})
     */
    public function detail($id)
    {
        View::render(
            // Vue à utiliser dans le dossier `views`
            'artists/artists-detail',
            // Paramètres à afficher dans la vue
            [
                // Titre de la page
                'title' => "Artist #$id",
                // Fil d'Ariane
                'breadcrumbs' => [[
                    'title' => $this->homeTitle,
                    'url' => $this->homeUrl
                ]],
                // Liste des ressources à afficher
                'artist' => $this->model->first($id),
            ],
            // Liste des modules/scripts à implémenter
            ['modules' => ['form-modal']]
        );
    }
}
