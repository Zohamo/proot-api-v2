<?php

namespace Core;

/**
 * Filtres d'assainissement.
 *
 * Ces filtres peuvent être attribués aux propriétés d'un modèle dans 'app/models/properties'.
 * Pour les appliquer, le modèle doit posséder le trait `ModelPropertiesSanitizerTrait`,
 * et en exécuter la méthode `sanitize`.
 *
 * @see core/ModelPropertiesSanitizerTrait.php
 */
class SanitizerFilters
{
    /**
     * Met en majuscule la première lettre d'une chaîne.
     *
     * @param  string $value
     * @return string
     */
    public static function capitalize($value)
    {
        return is_string($value) ? \App\Functions\StringUtils::capitalize($value) : $value;
    }

    /**
     * Convertit une valeur en un type donné.
     *
     * @param  string $value
     * @param  string $type
     * @return mixed
     * @throws \DomainException
     */
    public static function cast($value, $type = null)
    {
        switch ($type) {
            case 'int':
            case 'integer':
                $value = intval($value);
                break;
            case 'real':
            case 'float':
            case 'double':
                $value = floatval($value);
                break;
            case 'string':
            case 'char':
            case 'varchar':
            case 'email':
            case 'tel':
            case 'url':
                $value = strval($value);
                break;
            case 'date':
            case 'datetime':
                $value = $value ? strval($value) : null;
                break;
            case 'bool':
            case 'boolean':
                $value = is_string($value) ? in_array($value, ["true", "1", "on"]) : boolval($value);
                break;
            case 'object':
                $value = is_array($value) ? (object) $value : json_decode($value, false);
                break;
            case 'array':
                if (is_string($value)) {
                    $value = json_decode($value, true);
                }
                break;
            case 'json':
                break;
            default:
                throw new \DomainException("Type d'attribut inconnu: {$type}.");
        }
        return $value;
    }

    /**
     * Ne renvoie que les caractères numériques d'une chaîne.
     *
     * @param  string $value
     * @return string
     */
    public static function digit($value)
    {
        return preg_replace('/[^0-9.-]/si', '', str_replace([',', ' ', '+'], ['.', '', ''], $value));
    }

    /**
     * Convertit les caractères spéciaux en entités HTML.
     *
     * @param  string $value
     * @return string
     */
    public static function escape($value)
    {
        return is_string($value) ? htmlspecialchars($value) : $value;
    }

    /**
     * Change le format d'une date.
     *
     * @param  string $value
     * @param  array $options [$format_de_la_source, $format_de_la_cible]
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function formatDate($value, $options = [])
    {
        if (!$value) {
            return $value;
        }
        if (count($options) < 2) {
            throw new \InvalidArgumentException("Le formatage de date nécessite les formats de la source ET de la cible.");
        }

        return \App\Functions\DateUtils::convert($value, trim($options[0]), trim($options[1]));
    }

    /**
     * Met tous les caractères en minuscules.
     *
     * @param  string $value
     * @return string
     */
    public static function lowercase($value)
    {
        return is_string($value) ? mb_strtolower($value, 'UTF-8') : $value;
    }

    /**
     * Supprime les balises HTML et PHP d'une chaîne.
     *
     * @param  string  $value
     * @return string
     */
    public static function stripTags($value)
    {
        return is_string($value) ? strip_tags($value) : $value;
    }

    /**
     * Supprime les espaces en début et fin de chaîne.
     *
     * @param  string  $value
     * @param  string  $characters
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function trim($value, $characters = " \t\n\r\0\x0B")
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            return $value;
        }
        if (!is_string($characters)) {
            throw new \InvalidArgumentException("Le paramètre \$characters de 'trim' doit être une chaîne de caractères, reçu: $characters");
        }

        return trim($value, $characters);
    }

    /**
     * Met tous les caractères en majuscules.
     *
     * @param  string $value
     * @return string
     */
    public static function uppercase($value)
    {
        return is_string($value) ? mb_strtoupper($value) : $value;
    }
}
