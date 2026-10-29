<?php

namespace App\Models;

use Core\AbstractModel;
use Core\Entities\Relationship;
use Core\ModelQueriesTrait;

class LogModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "t_logs";

    /**
     * Nom de la clé primaire de la table ou des clés composites.
     *
     * @var string|string[] ex: `id` ou [`fk_id`, `fk_role_id`]
     */
    protected string|array $pk = "log_id";

    /**
     * Tableau associatif d'attributs relatifs aux propriétés du modèle.
     *
     * @var array[]
     */
    protected array $attributes = [
        'log_id' => [
            'type' => 'int',
            'fillable' => false,
        ],
        'log_date' => [
            'type' => 'datetime',
        ],
        'fk_user_email' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
        'log_code' => [
            'type' => 'int',
            'min' => 0,
            'max' => 65535,
        ],
        'log_message' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
        'log_file' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
        'log_line' => [
            'type' => 'int',
            'min' => 0,
            'max' => 65535,
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
            'user' => new Relationship('UserModel', 'has-one', 'fk_user_email', 'usr_email'),
        ];
        return $this;
    }

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return \App\Entities\Log[]|mixed[]
     */
    public function all()
    {
        return $this->fields('log_id', 'log_date', 'fk_user_email', 'log_code', 'log_message', 'log_file', 'log_line')
            ->order("log_date DESC")->select();
    }

    /**
     * Création d'un fichier de log et ajout d'une entrée dans t_log en BDD.
     *
     * @param  \Exception $exception
     * @param  boolean $writeFile S'il faut créer un fichier de log.
     * @return boolean Si l'entrée a bien été ajoutée.
     */
    public function writeAndInsert(\Exception $exception, $writeFile = true)
    {
        $date = date('Y-m-d H:i:s');
        $email = isset(\Core\Auth::$user) ? \Core\Auth::$user->usr_email : null;
        // Création d'un fichier de log
        if ($writeFile) {
            $message = "::: $date :::";
            $message .= "\nUtilisateur : $email";
            $message .= "\nCode : " . $exception->getCode() ?: null;
            $message .= "\nException : " . get_class($exception);
            $message .= "\nMessage : " . $exception->getMessage();
            $message .= "\nTrace de pile :\n" . $exception->getTraceAsString();
            $message .= "\nDétecté dans '{$exception->getFile()}' ligne {$exception->getLine()}\n\n";
            error_log($message, 3, path('logs') . "/" . date('Y-m-d') . ".txt");
        }
        // Ajout d'une entrée dans t_log en BDD
        return $this->toBool()->insert([
            "log_date" => $date,
            "fk_user_email" => $email,
            "log_code" => $exception->getCode() ?: null,
            "log_message" => $exception->getMessage(),
            "log_file" => $exception->getFile(),
            "log_line" => $exception->getLine()
        ]);
    }
}
