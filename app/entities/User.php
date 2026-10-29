<?php

namespace App\Entities;

use AppConstants;
use Core\AbstractDTO;

class User extends AbstractDTO
{
    public int $usr_id;
    public ?string $usr_username;
    public ?string $usr_password;
    public ?string $usr_lastname;
    public ?string $usr_firstname;
    public string $usr_email;
    public int $fk_role_id;
    public ?string $usr_last_login_at = null;
    public ?int $usr_is_super_admin = 0;
    public ?string $usr_created_at = null;
    public ?string $usr_deleted_at = null;
    public UserRole $role;

    /**
     * Définit les relations de l'objet.
     *
     * @return void
     */
    public function __construct()
    {
        $this->role = new UserRole();
    }

    /**
     * Le nom en majuscules.
     *
     * @return string
     */
    public function lastname(): string
    {
        return strtoupper($this->usr_lastname);
    }

    /**
     * Le nom en majuscules suivi du prénom.
     *
     * @return string
     */
    public function fullName(): string
    {
        return $this->lastname() . " " . $this->usr_firstname;
    }

    /**
     * Si l'Utilisateur a le rôle d'Administrateur.
     *
     * @param  bool $strict À `false` le rôle de développeurest également accepté.
     * @return bool
     */
    public function isAdmin($strict = false): bool
    {
        return $strict
            ? $this->fk_role_id == AppConstants::ID_ROLE_ADMIN
            : in_array($this->fk_role_id, [AppConstants::ID_ROLE_ADMIN, AppConstants::ID_ROLE_DEV]);
    }

    /**
     * Si l'Utilisateur a le rôle de développeur.
     *
     * @return bool
     */
    public function isDev(): bool
    {
        return $this->fk_role_id == AppConstants::ID_ROLE_DEV;
    }

    /**
     * Si l'Utilisateur est en mode super administrateur.
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        return boolval($this->usr_is_super_admin);
    }
}
