<?php

namespace App\Controllers\Tools;

use Core\AbstractController;
use Core\History;

/**
 * Ce contrôleur contient des outils destinés UNIQUEMENT à l'administrateur technique
 * pour faciliter le débogage de l'application.
 */
class ToolsController extends AbstractController
{
    /**
     * Supprime les données de la session en cours.
     *
     * @return void
     * @route("/tools/detruire-session", methods={"GET"})
     */
    public function detruireSession()
    {
        session_destroy();
        (new History)->goBack();
    }

    /**
     * Permet à un administrateur technique de changer de rôle à la volée.
     *
     * @param  mixed[] $req Tableau associatif contenant les données passées en HTTP.
     * @return void
     */
    public function switchRole(array $req): void
    {
        if (empty($req['fk_role_id'])) {
            \App\Functions\HttpUtils::jsonResponse(["msg" => "Id. du rôle manquant."], 400);
        }

        $utiId = \Core\Auth::$user->usr_id;
        if (!(new \App\Models\UserModel(['usr_id' => $utiId]))
            ->update(['fk_role_id' => $req['fk_role_id']])) {
            \App\Functions\HttpUtils::jsonResponse(["msg" => "L'utilisateur n'a pas pu être modifié en BDD."], 500);
        }

        // On récupère l'utilisateur depuis la BDD et on le sauvegarde
        $_SESSION['user'] = (new \App\Models\UserModel)->with('role')->first($utiId);
        \Core\Auth::$user = $_SESSION['user'];

        \App\Functions\HttpUtils::jsonResponse([]);
    }
}
