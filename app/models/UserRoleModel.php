<?php

namespace App\Models;

use App\Entities\UserRole;
use Core\AbstractModel;
use Core\ModelQueriesTrait;

class UserRoleModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "t_users_roles";

    /**
     * Nom de la clé primaire de la table ou des clés composites.
     *
     * @var string|string[] ex: `id` ou [`fk_id`, `fk_role_id`]
     */
    protected string|array $pk = "rol_id";

    /**
     * Tableau associatif d'attributs relatifs aux propriétés du modèle.
     *
     * @var array[]
     */
    protected array $attributes = [
        'rol_id' => [
            'type' => 'int',
            'fillable' => false,
        ],
        'rol_label' => [
            'type' => 'string',
            'maxLength' => 50,
            'search' => true,
        ],
        'rol_description' => [
            'type' => 'string',
            'maxLength' => 2048,
            'search' => true,
        ],
    ];

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return UserRole[]|mixed[]
     */
    public function all()
    {
        return $this->fields('rol_id', 'rol_label', 'rol_description')
            ->order("rol_id ASC")->select();
    }

    /**
     * Renvoie la liste des tous des rôles que l'utilisateur est autorisé à attribuer.
     *
     * @param  boolean $isDev Si l'utilisateur a le rôle de développeur.
     * @return UserRole[]|array[]|boolean
     */
    public function allAllowed($isDev = false)
    {
        return $isDev
            ? $this->all()
            : $this->order("rol_id ASC")->find('rol_id', '!=', \AppConstants::ID_ROLE_DEV);
    }
}
