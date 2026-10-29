<?php

namespace App\Entities;

use Core\AbstractDTO;

/**
 * Entité d'une procédure stockée pour faciliter sa création en base de données.
 */
class StoredProcedure extends AbstractDTO
{
    /**
     * Nom de la procédure avec le préfixe "ps_ctrl_".
     * @var string
     */
    public $nom;

    /**
     * Corps de la procédure.
     * @var string
     */
    public $corps;

    /**
     * Construit et renvoie la requête de création de la procédure stockée.
     *
     * @return string
     */
    public function query()
    {
        $username = env('DB_USERNAME');
        $host = env('DB_HOST');
        $name = $this->nom;
        $body = $this->corps;

        return "CREATE DEFINER = '$username'@'$host' PROCEDURE $name(v_id_controle int)\n$body";
    }

    /********************************************************
     * Setters
     ********************************************************/

    /**
     * Définit le nom de la procédure.
     * Ajoute le préfixe "ps_ctrl_" si nécessaire.
     *
     * @param string $nom
     * @return void
     */
    public function setNom($nom)
    {
        $prefixe = \AppConstants::PREFIX_PS;
        $this->nom = strpos($nom, $prefixe) === 0
            ? $nom
            : "$prefixe$nom";
    }

    /**
     * Définit le corps de la procédure.
     *
     * @param string $corps
     * @return void
     */
    public function setCorps($corps)
    {
        $this->corps = trim($corps);
    }
}
