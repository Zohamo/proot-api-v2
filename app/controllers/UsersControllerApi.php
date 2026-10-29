<?php

namespace App\Controllers;

use App\Functions\HttpUtils;
use App\Models\UserModel;
use Core\AbstractControllerApi;
use Core\Alert;
use Core\Entities\PropertyValidationErrors;
use Core\Entities\ValidationRuleError;

class UsersControllerApi extends AbstractControllerApi
{
    /**
     * Modèle associé au contrôleur.
     * @var UserModel
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
        $this->model = new UserModel();
        $this->actions = array_fill_keys(
            ['create', 'update'],
            (new UsersController)->actions['updateOrCreate']
        );
    }

    /**
     * Enregistre une nouvelle ressource.
     *
     * @param  mixed[] $req Tableau associatif contenant les données passées en HTTP.
     * @return void
     * @route("/api/users", methods={"POST"})
     */
    public function create(array $req)
    {
        // Vérification du jeton CSRF
        if (!$this->validateCsrfToken(__FUNCTION__, $req)) {
            HttpUtils::jsonResponse([], 401);
        }

        // Assainissement des données et vérification de leur conformité
        $errors = array_merge(
            $this->model->sanitize($req)->validate(),
            $this->checkAdminTechAttribution($req['fk_role_id'])
        );

        // Renvoi des erreurs
        if (!empty($errors)) {
            $this->resetCsrfToken();
            HttpUtils::jsonResponse($errors);
        }

        // Suppression du jeton CSRF de la session
        $this->unsetCsrfToken();

        // Insertion en BDD
        if ($this->model->toBool()->insert()) {
            Alert::add("<strong>{$this->model->usr_lastname} {$this->model->usr_firstname}</strong> a bien été ajouté.", "success");
        } else {
            Alert::add("Un problème est survenu lors de l'ajout de <strong>{$this->model->usr_firstname} {$this->model->usr_lastname}</strong>.", "danger");
        }

        HttpUtils::jsonResponse([]);
    }

    /**
     * Modifie la ressource spécifiée.
     *
     * @param  integer $id
     * @param  mixed[] $req Tableau associatif contenant les données passées en HTTP.
     * @return void
     * @route("/api/users/{id}", methods={"PUT"})
     */
    public function update($id, array $req)
    {
        // Vérification du jeton CSRF
        if (!$this->validateCsrfToken(__FUNCTION__, $req)) {
            HttpUtils::jsonResponse([], 401);
        }

        $this->getAndCheckResource($id);

        // Assainissement des données et vérification de leur conformité
        $data = array_merge($req, ["usr_id" => $id]);
        $errors = array_merge(
            $this->model->sanitize($data)->validate($id),
            $this->checkAdminTechAttribution($this->model->fk_role_id)
        );

        // Renvoi des erreurs
        if (!empty($errors)) {
            $this->resetCsrfToken();
            HttpUtils::jsonResponse($errors);
        }

        // Suppression du jeton CSRF de la session
        $this->unsetCsrfToken();

        // Mise à jour en BDD
        if ($this->model->toBool()->update()) {
            Alert::add("<strong>{$this->model->usr_firstname} {$this->model->usr_lastname}</strong> a bien été modifié.", "success");
        } else {
            Alert::add("Un problème est survenu lors de la modification de <strong>{$this->model->usr_firstname} {$this->model->usr_lastname}</strong>.", "danger");
        }

        HttpUtils::jsonResponse([]);
    }

    /**
     * Vérifie que l'utilisateur ait le droit d'attribuer le rôle selectionné.
     *
     * @param  integer $roleId Id. du rôle soumis par le formulaire.
     * @return PropertyValidationErrors[]
     */
    private function checkAdminTechAttribution($roleId)
    {
        return $roleId != \AppConstants::ID_ROLE_DEV || \Core\Auth::$user->isDev()
            ? []
            : [new PropertyValidationErrors("Id. rôle", $this->model->fk_role_id, "\\UtilisateurModel", [
                new ValidationRuleError("in", "n'est pas une valeur correcte.")
            ])];
    }

    /**
     * Vérifie que la ressource demandée existe en BDD et que l'utilisateur authentifié ait le droit de la modifier.
     *
     * @param  integer $id
     * @return \App\Entities\User
     * @throws \Exception
     */
    protected function getAndCheckResource($id)
    {
        // Vérification que la ressource existe en BDD
        $user = $this->model->first($id);
        if (!$user) {
            error(404);
        }

        // Vérification du droit de l'utilisateur à attribuer le rôle d'Administrateur technique
        if ($user->isDev() && !\Core\Auth::$user->isDev()) {
            Alert::add("Vous n'avez pas les droits suffisants pour modifier cet utilisateur.", "danger");
            error(403);
        }

        return $user;
    }

    /**
     * Vérifie la validité des données puis modifie toutes les ressources spécifiées.
     *
     * @param  mixed[] $req Tableau associatif contenant les données passées en HTTP.
     * @route("/api/users", methods={"PUT"})
     */
    public function bulkUpdate(array $req)
    {
        HttpUtils::jsonResponse(null, 404);
    }

    /**
     * Supprime la ressource spécifiée.
     *
     * @param  integer $id
     * @route("/api/users/{id}", methods={"DELETE"})
     */
    public function delete($id)
    {
        HttpUtils::jsonResponse(null, 404);
    }

    /**
     * Supprime toutes les ressources.
     *
     * @route("/api/users", methods={"DELETE"})
     */
    public function deleteAll()
    {
        HttpUtils::jsonResponse(null, 404);
    }
}
