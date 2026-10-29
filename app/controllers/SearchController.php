<?php

namespace App\Controllers;

use App\Functions\ClassUtils;
use Core\AbstractController;
use Core\Alert;
use Core\View;

/**
 * Ce contrôleur permet d'effectuer des recherches dans la base de données.
 *
 * Paramétrage :
 * - définir la propriété `$categories` ('utilisateur' n'est là qu'à titre d'exemple !!!)
 * - pour chaque modèle de `$categories`, définir les propriétés où effectuer la recherche (`'search' => true`)
 *
 * Pour personnaliser les tableaux de résultats (supposons un nom de ressource `foo`) :
 * - créer dans `appiewsecherche\components` un fichier nommé `results-foo.php` à l'exemple de `results-utilisateur.php`
 */
class SearchController extends AbstractController
{
    /**
     * Recherche saisie.
     * @var string
     */
    private $query;

    /**
     * Nombre minimum de caractères de la saisie de recherche.
     * @var integer
     */
    private $queryMinLength = 3;

    /**
     * Nom de la ressource (modèle) où faire la recherche.
     * Cette catégorie doit faire partie de `$categories` pour être valide.
     * @var string
     */
    private $category;

    /**
     * Noms de toutes les catégories de ressources (modèles) où faire la recherche.
     * Si aucune catégorie n'est définie en POST, la catégorie sera considérée comme étant "tout" et
     * la recherche sera effectuée dans toutes ces catégories.
     * Pour être valide, la catégorie définie en POST (en kebab-case) doit faire partie de cette liste.
     * @var string[] Noms des ressources (en kebab-case).
     */
    private $categories = ['tout', 'user']; // `user` n'est là qu'à titre d'exemple !!!

    /**
     * Affiche la page de recherche.
     *
     * @route("/search", methods={"GET", "POST"})
     */
    public function index($request = [])
    {
        $results = $this->setAndValidateQuery($request)
            ? $this->getResults($this->query, $this->category)
            : [];
        View::render(
            // Vue à utiliser dans le dossier /views
            'search/search-index',
            // Paramètres à afficher dans la vue
            [
                // Titre de la page
                "title" => "Recherche",
                // Liste des résultats à afficher par catégorie
                "results" => $results,
                // Nombre de résultats
                "nbResults" => !empty($results)
                    ? count(array_merge(...array_values($results)))
                    : 0,
                // Recherche effectuée
                "query" => $this->query,
                // Liste des catégories pour le <select> du formulaire de recherche
                "categories" => $this->categories,
                // Catégorie dans laquelle la recherche a été effectuée
                "category" => $this->category
            ]
        );
    }

    /**
     * Assainit et vérifie la conformité des données passées en POST.
     * Crée une alerte si des erreurs sont relevées.
     *
     * @param  mixed[] $params
     * @return boolean
     */
    private function setAndValidateQuery(array $params)
    {
        /** @var \Core\Entities\PropertyValidationErrors[] */
        $propertiesValidationErrors = [];

        if (empty($params['query'])) {
            return false;
        }

        // Nettoyage du champ "recherche"
        $this->query = \App\Functions\StringUtils::cleanSearchQuery($params['query']);
        if (strlen($this->query) < $this->queryMinLength) {
            $propertiesValidationErrors['query'] =
                new \Core\Entities\PropertyValidationErrors('query', $this->query, "Search", [
                    'minLength' => new \Core\Entities\ValidationRuleError(
                        $this->queryMinLength,
                        "doit avoir au minimum {$this->queryMinLength} caractères."
                    )
                ]);
        }

        // Définition de la catégorie
        $this->category = empty($params['category'])
            ? $this->categories[0]
            : \App\Functions\StringUtils::cleanSearchQuery($params['category']);

        // Vérification de la catégorie
        if (!in_array($this->category, $this->categories)) {
            $propertiesValidationErrors['category'] =
                new \Core\Entities\PropertyValidationErrors('category', $this->category, "Search", [
                    'in' => new \Core\Entities\ValidationRuleError(
                        $this->category,
                        "doit correspondre à une des valeurs suivantes: " . implode(", ", $this->categories)
                    )
                ]);
        }

        if (empty($propertiesValidationErrors)) {
            return true;
        }

        $this->buildAlerts($propertiesValidationErrors);
        return false;
    }

    /**
     * Construit les messages d'erreur à afficher.
     *
     * @param  \Core\Entities\PropertyValidationErrors[] $propertiesValidationErrors
     * @return void
     */
    private function buildAlerts($propertiesValidationErrors)
    {
        $html = "";
        foreach ($propertiesValidationErrors as $propertyValidationErrors) {
            foreach ($propertyValidationErrors->errors as $validationRuleError) {
                $html .= "<p class='mb-0'>Le sujet recherché {$validationRuleError->message}</p>";
            }
        }
        Alert::add($html, "danger");
    }

    /**
     * Renvoie les résultats de la recherche.
     *
     * @param  string $query Recherche saisie.
     * @param  string $category Catégorie où effectuer la recherche (nom du modèle).
     * @return array
     */
    private function getResults($query, $category = null)
    {
        $results = [];
        if ($category && $category !== "tout") {
            $model = ClassUtils::getModel($category);
            if ($model) {
                $results[$category] = $model->search($query);
            }
        } else {
            foreach ($this->categories as $cat) {
                if ($cat !== "tout") {
                    $results = array_merge($results, $this->getResults($query, $cat));
                }
            }
        }
        return $results;
    }
}
