<?php

namespace App\Models;

use Core\AbstractModel;
use Core\Entities\Relationship;
use Core\ModelQueriesTrait;

class UserModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "t_users";

    /**
     * Nom de la clé primaire de la table ou des clés composites.
     *
     * @var string|string[] ex: `id` ou [`fk_id`, `fk_role_id`]
     */
    protected string|array $pk = "usr_id";

    /**
     * Tableau associatif d'attributs relatifs aux propriétés du modèle.
     *
     * @var array[]
     */
    protected array $attributes = [
        'usr_id' => [
            'type' => 'int',
            'fillable' => false,
        ],
        'usr_username' => [
            'type' => 'string',
            'search' => true,
        ],
        'usr_password' => [
            'type' => 'string',
        ],
        'usr_lastname' => [
            'type' => 'string',
            'maxLength' => 255,
            'search' => true,
        ],
        'usr_firstname' => [
            'type' => 'string',
            'maxLength' => 255,
            'search' => true,
        ],
        'usr_email' => [
            'type' => 'string',
            'email' => true,
            'search' => true,
        ],
        'fk_role_id' => [
            'type' => 'int',
            'default' => '1',
            'min' => 1,
            'max' => 127,
        ],
        'usr_last_login_at' => [
            'type' => 'datetime',
            'fillable' => false,
        ],
        'usr_is_super_admin' => [
            'type' => 'int',
            'fillable' => false,
        ],
        'usr_created_at' => [
            'type' => 'datetime',
            'default' => 'now()',
            'fillable' => false,
        ],
        'usr_deleted_at' => [
            'type' => 'datetime',
            'fillable' => false,
        ],
    ];

    /**
     * Définit les relations entre ce modèle et d'autres modèles.
     *
     * @return $this
     */
    protected function setRelationships()
    {
        $this->relationships = [
            'role' => new Relationship('UserRoleModel', 'has-one', 'fk_role_id', 'rol_id'),
        ];
        return $this;
    }

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return \App\Entities\User[]|mixed[]
     */
    public function all()
    {
        return $this->with('role')
            ->order('usr_deleted_at', 'usr_username', 'usr_firstname', 'usr_lastname')
            ->select();
    }

    /**
     * Renvoie la ressource correspondant à l'identifiant donné.
     *
     * @param  integer $id
     * @return \App\Entities\User|mixed[]|null
     */
    public function get($id)
    {
        $res = $this->with('role')
            ->where("usr_id = ?")
            ->limit(1)
            ->select([$id]);
        return $res[0] ?? null;
    }

    /**
     * Renvoie les résultats d'une recherche.
     *
     * @param  string $query Chaîne de caractères à trouver.
     * @return \App\Entities\User[]|array[]|boolean
     */
    public function search(string $query)
    {
        return $this->where(\App\Functions\SqlUtils::searchQueryBuilder($this->attributes('search'), $query))
            ->all();
    }
}
