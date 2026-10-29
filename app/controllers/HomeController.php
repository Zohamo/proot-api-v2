<?php

namespace App\Controllers;

use Core\AbstractController;
use Core\View;

class HomeController extends AbstractController
{
    /**
     * Liens à mettre en valeur dans la barre de navigation.
     * @var string[]
     */
    protected $activeLinks = ["accueil"];

    /**
     * Affiche la page d'accueil.
     *
     * @route("/", methods={"GET"})
     */
    public function index()
    {
        View::render('home');
    }
}
