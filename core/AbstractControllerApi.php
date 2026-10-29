<?php

namespace Core;

use App\Functions\HttpUtils;

/**
 * Contrôleur API de base contenant les méthodes pour une API REST standard.
 *
 * @see \core\assets\routes-api-generic
 */
class AbstractControllerApi
{
    use FormValidatorTrait;

    /**
     * Renvoie une liste de toutes les ressources.
     *
     * @route("/api/foobars", methods={"GET"})
     */
    public function getAll()
    {
        HttpUtils::jsonResponse($this->model->all());
    }

    /**
     * Renvoie la ressource spécifiée.
     *
     * @param  string $id
     * @route("/api/foobars/{id}", methods={"GET"})
     */
    public function get($id)
    {
        $res = $this->model->toArray()
            ->where($this->model->pk() . " = ?")
            ->select([$id]);

        if (empty($res)) {
            HttpUtils::jsonResponse(null, 404);
        } else {
            HttpUtils::jsonResponse($res[0]);
        }
    }

    /**
     * Vérifie la validité des données puis enregistre la ressource.
     *
     * @param  mixed[] $req Tableau associatif contenant les données passées en HTTP.
     * @return void
     * @route("/api/foobars", methods={"POST"})
     */
    public function create(array $req)
    {
        if ($this->validateForm(__FUNCTION__, $req) && $this->model->toBool()->insert()) {
            Alert::add("Ajout effectué.", "success");
        } else {
            Alert::add("Un problème est survenu lors de l'enregistrement.", "danger");
        }

        HttpUtils::jsonResponse([]);
    }

    /**
     * Vérifie la validité des données puis modifie la ressource spécifiée.
     *
     * @param  integer $id
     * @param  mixed[] $req Tableau associatif contenant les données passées en HTTP.
     * @route("/api/foobars/{id}", methods={"PUT"})
     */
    public function update($id, array $req)
    {
        if ($this->validateForm(__FUNCTION__, $req, $id) && $this->model->toBool()->update()) {
            Alert::add("Modification effectuée.", "success");
        } else {
            Alert::add("Un problème est survenu lors de l'enregistrement.", "danger");
        }

        HttpUtils::jsonResponse([]);
    }

    /**
     * Vérifie la validité des données puis modifie toutes les ressources spécifiées.
     *
     * @param  mixed[] $req Tableau associatif contenant les données passées en HTTP.
     * @route("/api/foobars", methods={"PUT"})
     */
    public function bulkUpdate(array $req)
    {
        // Vérification du jeton CSRF
        $this->validateCsrfToken(__FUNCTION__, $req);

        // Vérification des données reçues
        if (empty($req['items']) || !is_array($req['items'])) {
            HttpUtils::jsonResponse("Les données doivent être incluses dans un tableau nommé 'items'", 400);
        }
        $errors = [];
        $sanitizedData = [];
        foreach ($req['items'] as $data) {
            if (empty($data[$this->model->pk()])) {
                HttpUtils::jsonResponse(null, 400);
            }
            // Assainissement des données du formulaire et vérification de leur conformité
            $errors = array_merge(
                $this->model->sanitize($data)
                    ->validate($data[$this->model->pk()]),
                $errors
            );

            $props = $this->model->properties();
            if (count($props) > 1) {
                // Il y a au moins un champ à modifier
                $sanitizedData[] = $props;
            }
            $this->model->setProperties([]);
        }

        // Renvoi des erreurs
        if (!empty($errors)) {
            HttpUtils::jsonResponse($errors);
        }

        // Mise à jour en BDD
        if ($this->model->toBool()->multiUpdate($sanitizedData)) {
            Alert::add("Modifications effectuées.", "success");
        } else {
            Alert::add("Un problème est survenu lors de l'enregistrement.", "danger");
        }

        HttpUtils::jsonResponse([]);
    }

    /**
     * Supprime la ressource spécifiée.
     *
     * @param  integer $id
     * @route("/api/foobars/{id}", methods={"DELETE"})
     */
    public function delete($id)
    {
        // Vérification que la ressource existe en BDD
        $res = $this->model->fields($this->model->pk())->toArray()->first($id);
        if (empty($res)) {
            HttpUtils::jsonResponse(null, 404);
        } elseif ($this->model->delete([$this->model->pk() => $id])) {
            HttpUtils::jsonResponse(null, 204);
        }
        HttpUtils::jsonResponse(null, 500);
    }

    /**
     * Supprime toutes les ressources.
     *
     * @route("/api/foobars", methods={"DELETE"})
     */
    public function deleteAll()
    {
        HttpUtils::jsonResponse(null, $this->model->delete() ? 204 : 500);
    }
}
