<?php

namespace Core;

/**
 * Contrôleur web de base.
 */
abstract class AbstractController
{
    /**
     * Modèle associé au contrôleur.
     * @var object
     */
    protected $model;

    /**
     * Liens à mettre en valeur dans la barre de navigation.
     * @var string[]
     */
    protected $activeLinks = [];

    /**
     * Définit les liens à mettre en valeur dans la vue.
     */
    public function __construct()
    {
        View::$activeLinks = $this->activeLinks;
    }
}
