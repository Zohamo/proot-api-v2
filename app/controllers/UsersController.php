<?php

namespace App\Controllers;

use App\Entities\User;
use App\Models\UserModel;
use Core\AbstractController;
use Core\Alert;
use Core\Auth;
use Core\View;

class UsersController extends AbstractController
{
    /**
     * URL de la page d'accueil de la ressource.
     * @var string
     */
    protected $homeUrl = "users";

    /**
     * Titre de la page d'accueil de la ressource.
     * @var string
     */
    public $homeTitle = "Utilisateurs";

    /**
     * Noms des actions permettant d'identifier les formulaires pour la génération de jetons CSRF.
     * @var string[]
     */
    public $actions;

    /**
     * Liens à mettre en valeur dans la barre de navigation.
     * @var string[]
     */
    protected $activeLinks = ['users'];

    /**
     * Modèle associé au contrôleur.
     * @var UserModel
     */
    protected $model;

    /**
     * Crée une instance du contrôleur et définit ses propriétés.
     */
    public function __construct()
    {
        parent::__construct();
        $this->model = new UserModel();
        $this->actions = ['updateOrCreate' => "updateOrCreateUser"];
    }

    /**
     * Affiche une liste des ressources.
     *
     * @route("/users", methods={"GET"})
     */
    public function index()
    {
        View::render(
            // Vue à utiliser dans le dossier `views`
            'users/users-index',
            // Paramètres à afficher dans la vue
            [
                // Titre de la page
                'title' => $this->homeTitle,
                // Liste des ressources à afficher
                'users' => $this->model->all(),
                // Données pour le formulaire
                'actionName' => $this->actions['updateOrCreate'],
                'roles' => (new \App\Models\UserRoleModel)->allAllowed(Auth::$user->isDev()),
            ],
            // Liste des modules/scripts à implémenter
            ['modules' => ['data-tables', 'form-modal']]
        );
    }

    /**
     * Affiche la page de détails de la ressource.
     *
     * @param  integer $id
     * @route("/users/{id}", methods={"GET"})
     */
    public function detail($id)
    {
        $user = $this->model->get($id);
        if (!$user) {
            error(404);
        }
        View::render(
            // Vue à utiliser dans le dossier `views`
            'users/users-detail',
            // Paramètres à afficher dans la vue
            [
                // Titre de la page
                'title' => $user->fullName() ?? $user->usr_username,
                // Fil d'Ariane
                'breadcrumbs' => [[
                    'title' => $this->homeTitle,
                    'url' => $this->homeUrl
                ]],
                // Liste des ressources à afficher
                'user' => $user,
                // Données pour le formulaire
                'actionName' => $this->actions['updateOrCreate'],
                'roles' => (new \App\Models\UserRoleModel)->allAllowed(Auth::$user->isDev())
            ],
            // Liste des modules/scripts à implémenter
            ['modules' => ['form-modal']]
        );
    }

    /**
     * Désactive la ressource puis redirige vers la page d'accueil.
     *
     * @param  integer $id
     * @route("/users/{id}/deactivate", methods={"GET"})
     */
    public function deactivate($id)
    {
        $user = $this->getAndCheckResource($id);

        if (!$this->model->toBool()->update(['usr_id' => $id, 'usr_deleted_at' => date(\AppConstants::FORMAT_DATETIME)])) {
            Alert::add("Un problème est survenu lors de la désactivation de <strong>{$user->fullName()}</strong>.", "danger");
        } else {
            Alert::add("<strong>{$user->fullName()}</strong> a bien été désactivé.", "success");
        }

        redirect($this->homeUrl);
    }

    /**
     * Réactive la ressource puis redirige vers la page d'accueil.
     *
     * @param  integer $id
     * @route("/users/{id}/reactivate", methods={"GET"})
     */
    public function reactivate($id)
    {
        $user = $this->getAndCheckResource($id);

        if (!$this->model->toBool()->update(['usr_id' => $id, 'usr_deleted_at' => null])) {
            Alert::add("Un problème est survenu lors de la réactivation de <strong>{$user->fullName()}</strong>.", "danger");
        } else {
            Alert::add("<strong>{$user->fullName()}</strong> a bien été réactivé.", "success");
        }

        redirect($this->homeUrl);
    }

    /**
     * Supprime la ressource puis redirige vers la page d'accueil.
     *
     * @param  integer $id
     * @return void
     * @route("/users/{id}/delete", methods={"GET"})
     */
    public function delete($id)
    {
        $user = $this->getAndCheckResource($id);
        if (!$this->model->remove($id)) {
            Alert::add("Un problème est survenu lors de la suppression de <strong>{$user->fullName()}</strong>.", "danger");
        } else {
            Alert::add("<strong>{$user->fullName()}</strong> a bien été supprimé.", "success");
        }

        redirect($this->homeUrl);
    }

    /**
     * Récupère la ressource depuis la BDD et vérifie que l'utilisateur authentifié ait le droit de la modifier/supprimer.
     *
     * @param  integer $id
     * @return User
     * @throws \Exception
     */
    protected function getAndCheckResource($id)
    {
        // Vérification que la ressource existe en BDD
        $user = $this->model->first($id);
        if (!$user) {
            error(404);
        }

        // Vérification du droit de l'utilisateur authentifié à modifier/supprimer un utilisateur avec le rôle d'Administrateur technique
        if (!Auth::$user->isAdmin() || ($user->isDev() && !Auth::$user->isDev())) {
            Alert::add("Vous n'avez pas les droits suffisants pour modifier ou supprimer <strong>{$user->fullName()}</strong>.", "danger");
            error(403);
        }

        return $user;
    }
}
