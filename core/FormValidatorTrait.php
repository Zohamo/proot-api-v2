<?php

namespace Core;

use App\Functions\HttpUtils;

/**
 * Ce trait est utilisé par AbstractControllerApi.
 * Il contient les méthodes permettant de vérifier la validité d'un formulaire.
 */
trait FormValidatorTrait
{
    /**
     * Modèle associé au contrôleur.
     * @var AbstractModel
     */
    protected $model;

    /**
     * Noms des actions permettant d'identifier les formulaires pour la récupération des jetons CSRF.
     * La clé du tableau correspond au nom de la méthode appelée. ex: `['create' => "createFoobar"]`
     * @var string[]
     */
    protected $actions;

    /**
     * Clé du jeton CSRF stocké en session
     * @var string
     */
    protected $csrfTokenKey;

    /**
     * Vérifie la validité du jeton CSRF et des données à insérer en BDD.
     *
     * @param  string  $actionName Nom de l'action du formulaire (méthode appelée),
     *                          la propriété `$actions` doit avoir cette clé définie.
     * @param  mixed[] $req Tableau associatif contenant les données passées en HTTP.
     * @param  integer $id Identifiant de la ressource à modifier dans le cas d'un update.
     * @return true|void Renvoie une réponse JSON en cas d'erreur.
     */
    protected function validateForm($actionName, $req, $id = null)
    {
        // Vérification du jeton CSRF et de la présence de la ressource dans le cas d'un update
        $code = $this->preValidation($actionName, $req, $id);
        if ($code !== 200) {
            HttpUtils::jsonResponse(null, $code);
        }

        // Ajout de l'id passé dans l'URL aux données du formulaire
        if ($id !== null) {
            $req[$this->model->pk()] = $id;
        }
        // Assainissement des données et vérification de leur conformité
        $errors = $this->model->sanitize($req)->validate($id);
        // Renvoi des erreurs
        if (!empty($errors)) {
            $this->resetCsrfToken();
            HttpUtils::jsonResponse($errors);
        }

        // Suppression du jeton CSRF de la session
        $this->unsetCsrfToken();

        return true;
    }

    /**
     * Vérification du jeton CSRF et de la présence de la ressource dans le cas d'un update
     *
     * @param  string  $actionName Nom de l'action du formulaire (méthode appelée).
     * @param  mixed[] $req Tableau associatif contenant les données passées en HTTP.
     * @param  integer $id Identifiant de la ressource à modifier dans le cas d'un update.
     * @return integer Code HTTP (200 = succès, 401 = accès non autorisé, 404 = ressource introuvable).
     */
    protected function preValidation($actionName, $req, $id = null)
    {
        // Vérification du jeton CSRF
        if (!$this->validateCsrfToken($actionName, $req)) {
            return 401;
        }

        // Vérification que la ressource existe en BDD dans le cas d'un update
        if ($id !== null && !$this->model->fields($this->model->pk())
            ->where($this->model->pk() . " = ?")
            ->limit(1)->toBool()->select([$id])) {
            return 404;
        }

        return 200;
    }

    /**
     * Vérifie que le nom de l'action soit défini et que le jeton CSRF soit conforme.
     *
     * @param  string  $actionName Nom de l'action du formulaire (méthode appelée).
     * @param  mixed[] $req Tableau associatif contenant les données passées en HTTP.
     * @return boolean
     * @throws \LogicException
     */
    protected function validateCsrfToken($actionName, $req)
    {
        if (empty($this->actions) || empty($this->actions[$actionName])) {
            throw new \LogicException("Le nom de l'action '$actionName' n'a pas été défini pour la récupération du jeton CSRF.");
        }

        // Définition de la clé du jeton CSRF
        $this->csrfTokenKey = \AppConstants::CSRF_TOKEN_KEY . "_" . $this->actions[$actionName];

        if (
            empty($req[$this->csrfTokenKey]) || empty($_SESSION[$this->csrfTokenKey])
            || empty($_SESSION[$this->csrfTokenKey . "_time"])
        ) {
            if (\Core\Auth::$user->isDev()) {
                throw new \DomainException("Jeton CSRF introuvable");
            }
            return false;
        }

        $valid = false;
        if (time() - $_SESSION[$this->csrfTokenKey . "_time"] > floatval(env('CSRF_TOKEN_DURATION')) * 60) {
            \Core\Alert::add("Votre session a expiré.", "danger");
        } elseif ($req[$this->csrfTokenKey] !== $_SESSION[$this->csrfTokenKey]) {
            if (\Core\Auth::$user->isDev()) {
                throw new \DomainException("Le jeton CSRF envoyé ne correspond pas à celui en session.");
            }
            \Core\Alert::add("Une erreur est survenue.", "danger");
        } else {
            $valid = true;
        }

        return $valid;
    }

    /**
     * Réinitialise l'heure du jeton CSRF de la session.
     *
     * @return void
     */
    protected function resetCsrfToken()
    {
        $_SESSION[$this->csrfTokenKey . "_time"] = time();
    }

    /**
     * Supprime le jeton CSRF de la session.
     *
     * @return void
     */
    protected function unsetCsrfToken()
    {
        $_SESSION[$this->csrfTokenKey] = null;
        $_SESSION[$this->csrfTokenKey . "_time"] = null;
    }
}
