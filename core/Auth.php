<?php

namespace Core;

use App\Entities\User;
use App\Models\UserModel;
use AppConstants;
use DateTime;

class Auth
{
    /**
     * Utilisateur authentifié.
     * @var User
     */
    public static $user;

    /**
     * Modèle Utilisateur.
     * @var UserModel
     */
    private $model;

    /**
     * Crée une instance de Auth et définit ses propriétés.
     *
     * @return void
     */
    public function __construct()
    {
        $this->model = new UserModel();
    }

    /**
     * Identifie l'utilisateur et le définit en propriété statique '$user'.
     *
     * @return User|false
     * @throws \ErrorException
     */
    public function login()
    {
        if (env('AUTH_REQUIRED')) {
            return false;
        } else {
            $email = env('AUTH_EMAIL');
        }
        // On récupère les données de l'utilisateur connecté
        self::$user = $this->getAndSetUtilisateur($email);
        return self::$user;
    }

    /**
     * Renvoie l'utilisateur stocké en session ou en BDD.
     *
     * @param  string $email
     * @return User|false
     */
    private function getAndSetUtilisateur($email)
    {
        if (!$this->validUserInSession()) {
            // On récupère l'utilisateur depuis la BDD
            $_SESSION['user'] = $this->model->with('role')
                ->where("usr_deleted_at IS NULL")
                ->first('usr_email', $email);
            if (!$_SESSION['user']) {
                return false;
            }
            // et on met à jour la date de dernière connexion
            $_SESSION['user']->usr_last_login_at = $this->updateLastLogin($email);
        }
        return $_SESSION['user'];
    }

    /**
     * Vérifie si on a le bon utilisateur en session et que la session n'a pas expiré.
     *
     * @param  string $email
     * @return boolean
     */
    private function validUserInSession(?string $email = null)
    {
        return !empty($_SESSION['user'])
            && gettype($_SESSION['user']) === "object"
            && get_class($_SESSION['user']) === "App\Entities\User"
            // && (time() - strtotime((string) $_SESSION['user']->usr_last_login_at->getTimestamp())) <= env('USER_SESSION_DURATION') * 60
            && ($email ? $_SESSION['user']->usr_email === $email : true);
    }

    /**
     * Met à jour la date de dernière connexion de l'utilisateur en BDD,
     * dans la propriété statique '$user' et dans la superglobale $_SESSION['utilisateur].
     *
     * @param  string $email
     * @return string Datetime mis à jour.
     * @throws \DomainException
     */
    private function updateLastLogin(string $email)
    {
        $now = date(AppConstants::FORMAT_DATETIME);
        if (
            $this->model
            ->where('usr_email = :usr_email')
            ->update(
                ['usr_last_login_at' => $now],
                ['usr_email' => $email]
            ) === false
        ) {
            throw new \DomainException("L'utilisateur n'a pas pu être mis à jour.");
        }
        return $now;
    }
}
