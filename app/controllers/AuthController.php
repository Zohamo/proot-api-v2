<?php

namespace App\Controllers;

use Core\AbstractController;
use Core\History;

class AuthController extends AbstractController
{
    /**
     * Supprime l'Utilisateur de la session.
     *
     * @return void
     * @route("/signout", methods={"GET"})
     */
    public function signout(): void
    {
        unset($_SESSION['user']);
        (new History())->goBack();
    }
}
