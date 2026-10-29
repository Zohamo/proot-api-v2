<?php

namespace Core;

/**
 * Règles de validation.
 *
 * Ces règles peuvent être attribuées aux propriétés d'un modèle dans 'app/models/properties'.
 * Pour les appliquer, le modèle doit posséder le trait `ModelPropertiesValidatorTrait`,
 * et en exécuter la méthode `validate` qui renvoie un tableau d'erreurs.
 * Si ce tableau est vide cela signifie que les données respectent les règles définies.
 *
 * Chaque règle est une méthode statique qui prend en paramètre:
 * - $value (`mixed`) : La valeur de l'attribut
 * - $ruleValue (`mixed`, optionnel) : La valeur de la règle
 * et qui renvoie:
 * - soit `true` si la valeur fournie est valide
 * - soit un message d'erreur
 */
class ValidatorRules
{
    /**
     * Vérifie le type de la valeur.
     *
     * @param  mixed $value Valeur à vérifier.
     * @param  string $ruleValue Type de la valeur.
     * @return bool|string
     */
    public static function type($value, $ruleValue)
    {
        $valid = false;
        switch ($ruleValue) {
            case 'int':
            case 'integer':
                $valid = is_integer($value);
                break;
            case 'real':
            case 'float':
            case 'double':
                $valid = is_float($value);
                break;
            case 'string':
            case 'char':
            case 'varchar':
            case 'email':
            case 'tel':
            case 'url':
            case 'date':
            case 'datetime':
            case 'time':
                $valid = is_string($value);
                break;
            case 'bool':
            case 'boolean':
                $valid = is_bool($value);
                break;
            case 'object':
                $valid = is_object($value);
                break;
            case 'array':
                $valid = is_array($value);
                break;
            case 'json':
                json_decode($value);
                $valid = json_last_error() === JSON_ERROR_NONE;
                break;
            default:
                throw new \DomainException("Type d'attribut inconnu: $ruleValue");
        }
        return $valid ?: "doit être de type: $ruleValue";
    }

    /**
     * Vérifie si la valeur est requise.
     *
     * @param  mixed $value Valeur à vérifier.
     * @return bool|string
     */
    public static function required($value)
    {
        return !($value === null || (is_array($value) && empty($value)) || (is_string($value) && trim($value) === ''))
            ?: "est requis.";
    }

    /***************************************************************************
     * VALEUR
     ***************************************************************************/

    /**
     * Vérifie si la valeur est supérieure à une limite.
     *
     * @param  mixed $value Valeur à vérifier.
     * @param  mixed $ruleValue Limite.
     * @return bool|string
     */
    public static function min($value, $ruleValue)
    {
        return $value >= $ruleValue
            ?: "doit être supérieur ou égal à $ruleValue.";
    }

    /**
     * Vérifie si la valeur est inférieure à une limite.
     *
     * @param  mixed $value Valeur à vérifier.
     * @param  mixed $ruleValue Limite.
     * @return bool|string
     */
    public static function max($value, $ruleValue)
    {
        return $value <= $ruleValue
            ?: "doit être inférieur ou égal à $ruleValue.";
    }

    /**
     * Vérifie si la valeur est l'une des valeurs d'un tableau.
     *
     * @param  mixed $value Valeur à vérifier.
     * @param  array $array Tableau des valeurs autorisées.
     * @return bool|string
     */
    public static function in($value, $array)
    {
        return $array === null || in_array($value, $array)
            ?: "doit correspondre à une des valeurs suivantes: " . implode(", ", $array);
    }

    /***************************************************************************
     * TAILLE
     ***************************************************************************/

    /**
     * Vérifie si la valeur a une longueur minimale.
     *
     * @param  mixed $value Valeur à vérifier.
     * @param  mixed $ruleValue Longueur minimale.
     * @return bool|string
     */
    public static function minLength($value, $ruleValue)
    {
        return strlen((string) $value) >= $ruleValue
            ?: "doit avoir au minimum $ruleValue caractères.";
    }

    /**
     * Vérifie si la valeur a une longueur exacte.
     *
     * @param  mixed $value Valeur à vérifier.
     * @param  mixed $ruleValue Longueur exacte.
     * @return bool|string
     */
    public static function length($value, $ruleValue)
    {
        return strlen((string) $value) == $ruleValue
            ?: "doit avoir exactement $ruleValue caractères.";
    }

    /**
     * Vérifie si la valeur a une longueur maximale.
     *
     * @param  mixed $value Valeur à vérifier.
     * @param  mixed $ruleValue Longueur maximale.
     * @return bool|string
     */
    public static function maxLength($value, $ruleValue)
    {
        return strlen((string) $value) <= $ruleValue
            ?: "doit avoir au maximum $ruleValue caractères.";
    }

    /***************************************************************************
     * REGEX
     ***************************************************************************/

    /**
     * Vérifie si la valeur est une adresse courriel valide.
     *
     * @param  mixed $value Valeur à vérifier.
     * @return bool|string
     */
    public static function email($value)
    {
        return (bool) filter_var((string) $value, FILTER_VALIDATE_EMAIL)
            ?: "n'est pas une adresse courriel valide.";
    }

    /**
     * Vérifie si la valeur est une adresse URL valide.
     *
     * @param  mixed $value Valeur à vérifier.
     * @return bool|string
     */
    public static function url($value)
    {
        return (bool) filter_var((string) $value, FILTER_VALIDATE_URL)
            ?: "n'est pas une adresse URL valide.";
    }

    /**
     * Vérifie si la valeur est un numéro de téléphone valide.
     *
     * @param  mixed $value Valeur à vérifier.
     * @return bool|string
     */
    public static function phone($value)
    {
        return self::regex((string) $value, "^(\+ ?([0-9]+))?(\ ?(\(([0-9]+)\))?([0-9]+)?)+$")
            ?: "n'est pas un numéro de téléphone valide.";
    }

    /**
     * Vérifie si la valeur respecte une expression régulière.
     *
     * @param  mixed $value Valeur à vérifier.
     * @param  string $regex Expression régulière.
     * @return bool|string
     */
    public static function regex($value, $regex)
    {
        if (!is_string($regex)) {
            throw new \InvalidArgumentException("L'expression régulière doit être une chaîne de caractères");
        }
        return (bool) filter_var((string) $value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => $regex]])
            ?: "ne respecte pas l'expression régulière: $regex";
    }
}
