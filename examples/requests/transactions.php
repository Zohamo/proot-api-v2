<?php

use App\Models\UserModel;
use App\Models\UserRoleModel;
use Core\AbstractController;
use Core\Alert;

/**
 * Une transaction est un ensemble d'une ou plusieurs requêtes SQL
 * regroupées au sein d'un bloc qui est exécuté sur un jeu de données.
 *
 * Avec PDO, le début d'une transaction est marqué par la méthode `beginTransaction()` et la fin,
 * en fonction de ce qu'on souhaite faire, par la méthode `commit()` (valider) ou `rollBack()` (annuler).
 *
 * Une requête SQL est la plus petite transaction possible pour laquelle les mots clef
 * de début et fin sont rajoutés de manière implicite par le serveur de base de données.
 *
 * @see https://www.php.net/manual/fr/pdo.transactions.php
 *
 * **Notes importantes**
 * - Les transactions ne fonctionnent que sur les tables de type **InnoDB** !
 * - Certaines instructions MySQL provoquent une validation (commit) implicite : `CREATE..`, `DROP..`, `ALTER..`,..
 * @see https://dev.mysql.com/doc/refman/8.0/en/implicit-commit.html
 */
class TransactionExample extends AbstractController
{
    /**
     * Modèle associé au contrôleur.
     *
     * @var UserModel
     */
    protected $model;

    /**
     * Initialisation des propriétés de l'objet.
     */
    public function __construct()
    {
        $this->model = new UserModel();
    }

    /**
     * Exemple d'une transaction où toutes les instructions peuvent être groupées dans la même condition.
     */
    public function transactionSimple()
    {
        // Début de la transaction
        $this->model->beginTransaction();

        try {
            // Ajout des instructions
            $this->model->insert(['prenom' => "Herbert"]);
            (new UserModel)->update(['id' => 1, 'nom' => "Cutest"]);
            (new UserRoleModel())->update(['rol_id' => 3, "label" => "Visiteur"]);
            (new UserModel)->where("usr_lastname = ?")->delete(["Inconnu"]);

            // Tout s'est bien passé, on valide la transaction
            $this->model->commit();
            Alert::add("L'enregistrement a bien été pris en compte.", "success");
        } catch (Exception $e) {
            // Un problème est survenu, on annule la transaction
            $this->handleRollBack($e);
        }
    }

    /**
     * Exemple de transaction où il est nécessaire de décomposer le script en plusieurs étapes.
     *
     * @param mixed[] $request Tableau associatif contenant les données passées en HTTP.
     */
    public function transactionPasApas(array $request)
    {
        // Début de la transaction
        $this->model->beginTransaction();

        // Ajout des instructions pas à pas

        // Première instruction
        $prenom = $request['prenom'];
        try {
            $this->model->insert(['prenom' => $prenom]);
        } catch (Exception $e) {
            $this->handleRollBack($e);
        }

        // Jusque là tout va bien, on continue à traiter les données
        if (!empty($request['supprimer_uti'])) {
            try {
                $utiIDaSupprimer = $request['supprimer_uti'];
                (new UserModel)->where("usr_id = ?")->delete([$utiIDaSupprimer]);
            } catch (Exception $e) {
                $this->handleRollBack($e);
            }
        }

        // Autre instruction
        if (boolval((new UserRoleModel())->first($request["rol_id"]))) {
            try {
                (new UserRoleModel())->update(["usr_label" => "Guest", 'rol_id' => $request["rol_id"]]);
            } catch (Exception $e) {
                $this->handleRollBack($e);
            }
        }

        // Tout s'est bien passé, on valide la transaction
        $this->model->commit();
        Alert::add("L'enregistrement a bien été pris en compte.", "success");
    }

    /**
     * Gestion du rollback générique en cas d'erreur.
     *
     * @throws DomainException
     */
    private function handleRollBack(Exception $e)
    {
        $this->model->rollBack();
        Alert::add("Un problème est survenu lors de l'enregistrement.", "danger");
        throw new \DomainException("Erreur ! {$e->getCode()} - {$e->getMessage()}");
    }
}
