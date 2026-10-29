<?php

namespace App\Functions;

/**
 * Fonctions utilitaires pour les chaînes de caractères.
 */
class StringUtils
{
    /**
     * Renvoie l'index de la première chaîne de caractères contenant une chaîne de
     * caractères ($needle) dans un tableau ($haystack).
     *
     * @param  string[] $haystack
     * @param  string $needle
     * @return integer|false Index du premier élément trouvé ou `false`.
     */
    public static function arrayContains(array $haystack, string $needle)
    {
        return array_search(
            true,
            array_map(function ($k) use ($needle) {
                return strpos($k, $needle) !== false;
            }, $haystack)
        );
    }

    /**
     * Transforme une chaîne de caractères en camelCase.
     *
     * @param  string $str
     * @param  string $delimiter Caractère utilisé pour séparer les mots de la source.
     * @return string
     */
    public static function camel(string $str, string $delimiter = "_"): string
    {
        return lcfirst(
            str_replace(
                $delimiter,
                '',
                ucwords($str, $delimiter)
            )
        );
    }

    /**
     * Tansforme une chaîne de caractères en camelCase en snake_case.
     *
     * @param  string $str
     * @return string
     */
    public static function camelToSnake(string $str): string
    {
        // `(?<!^)[A-Z]` cherche toutes les lettres majuscules qui ne sont pas au début de la chaîne `(?<!^)`
        // Chaque majuscule trouvée est remplacée par _ suivi de la lettre `(_$0)`
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
    }

    /**
     * Ajoute des espaces à une chaîne de caractères en camelCase ou PascalCase et la met en minuscules.
     *
     * @example: "fooBar" devient "foo bar"
     * @param  string $str
     * @return string
     */
    public static function camelToSpaced($str)
    {
        preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $str, $matches);
        $ret = $matches[0];
        foreach ($ret as &$match) {
            $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        }
        return implode(' ', $ret);
    }

    /**
     * Met le premier caractère d'une chaîne en majuscule (même s'il comporte un accent).
     *
     * @param  string $s
     * @return string
     */
    public static function capitalize($s)
    {
        return mb_strtoupper(mb_substr($s, 0, 1)) . mb_substr($s, 1);
    }

    /**
     * Nettoie une chaîne de caractères passée dans un champ de recherche.
     *
     * @param  string $s
     * @return string
     */
    public static function cleanSearchQuery($s)
    {
        return htmlspecialchars(strtolower(self::reduceCharacters(trim($s))));
    }

    /**
     * Réduit la redondance d'espaces et de sauts de ligne dans une chaîne de caractères.
     *
     * @param  string $txt
     * @return string
     */
    public static function cleanText($txt)
    {
        // supprime les espaces en trop
        $txt = self::reduceCharacters(trim($txt));
        // supprime les lignes avec des espaces
        while (strpos($txt, "\n \n") !== false) {
            $txt = str_replace("\n \n", "\n", $txt);
        }
        // supprime les sauts de ligne trop importants
        while (strpos($txt, "\n\n\n") !== false) {
            $txt = str_replace("\n\n\n", "\n\n", $txt);
        }
        // supprime les sauts de ligne en début de texte
        while (strpos($txt, "\n") === 0) {
            $txt = substr($txt, 2);
        }
        return $txt;
    }

    /**
     * Coupe une chaîne d'un certain nombre de caractères et ajoute '...'.
     *
     * @param  string $str
     * @param  integer $length
     * @param  string $end Chaîne à ajouter à la fin de la chaîne coupée.
     * @return string
     */
    public static function cutString($str, $length = 50, $end = "&hellip;")
    {
        return strlen($str) <= $length ? $str : trim(substr($str, 0, $length)) . $end;
    }

    /**
     * Incrémente le numéro de copie dans un libellé.
     *
     * @example: "Nouveau dossier" => "Nouveau dossier (2)"
     * @example: "Nouveau dossier (2)" => "Nouveau dossier (3)"
     * @param  string $str
     * @return string
     */
    public static function incrementLabelVersionNumber($str)
    {
        $pattern = '#\(\d*\)$#';
        preg_match($pattern, $str, $match);
        if (empty($match)) {
            return "$str (2)";
        }
        $index = trim($match[0], "()") + 1;
        $replacement = "($index)";
        return preg_replace($pattern, $replacement, $str, 1);
    }

    /**
     * Transforme une chaîne de caractères en kebab-case.
     *
     * @param  string $str
     * @param  string $delimiter Caractère utilisé pour séparer les mots de la source.
     * @return string
     */
    public static function kebab($str, $delimiter = " ")
    {
        return str_replace($delimiter, "-", self::cleanSearchQuery($str));
    }

    /**
     * Transforme une chaîne de caractères en PascalCase.
     *
     * @param  string $str
     * @param  string $delimiter Caractère utilisé pour séparer les mots de la source.
     * @return string
     */
    public static function pascal($str, $delimiter = " ")
    {
        return implode("", array_map(function ($word) {
            return self::capitalize($word);
        }, explode($delimiter, self::cleanSearchQuery($str))));
    }

    /**
     * Prettifie une requête SQL.
     *
     * @param  string $query
     * @return string
     */
    public static function prettifySqlQuery($query)
    {
        // remplace les retours par des espaces
        $query = preg_replace("/[\r\n]/", " ", $query);
        // supprime les espaces en trop
        $query = self::reduceCharacters(trim($query));
        return str_replace(" ,", ",", $query);
    }

    /**
     * Réduit le nombre de plusieurs chaînes de caractères similaires
     * consécutives à un seul élément dans une chaîne de caractères.
     *
     * Supprime les espaces en trop par défaut.
     *
     * @param  string $str
     * @return string
     */
    public static function reduceCharacters($str, $char = " ")
    {
        while (strpos($str, "$char$char") !== false) {
            $str = str_replace("$char$char", $char, $str);
        }
        return $str;
    }

    /**
     * Remplace les caractères spéciaux d'une chaîne (accents, cédilles,..) par des caractères simples.
     *
     * @param string $str
     * @return string
     */
    public static function replaceSpecialChars($str)
    {
        $utf8 = include("assets/special-characters.php");
        return str_replace(array_keys($utf8), array_values($utf8), $str);
    }

    /**
     * Renvoie une URL raccourcie et tronquée de "http(s)://".
     *
     * @param  string $url
     * @return string
     */
    public static function shortenUrl($url)
    {
        if (substr($url, 0, 4) !== "http") {
            return $url;
        }
        $url = preg_replace("#^[^:/.]*[:/]+#i", "",  $url);
        $arr = explode("/", $url);
        if (!$arr[count($arr) - 1]) {
            array_pop($arr);
        }
        if (strlen($url) <= 100) {
            return implode("/", $arr);
        }
        $url = $arr[0];
        if (count($arr) > 2) {
            $url .= "/&hellip;/";
        }
        if (strlen($url) + strlen($arr[count($arr) - 1]) > 100) {
            $url .= substr($url, 0, 100 - strlen($arr[0])) . "&hellip;";
        } else {
            $url .= $arr[count($arr) - 1] ?: $arr[count($arr) - 2];
        }
        return $url;
    }

    /**
     * Transforme une chaîne de caractères en snake_case.
     *
     * @param  string $str
     * @param  string $delimiter Caractère utilisé pour séparer les mots de la source.
     * @return string
     */
    public static function snake($str, $delimiter = " ")
    {
        return str_replace($delimiter, "_", self::cleanSearchQuery($str));
    }

    /**
     * Change le formatage d'une chaîne de caractères.
     * Options :
     * - kebab-case ('k', 'kebab')
     * - PascalCase ('p', 'pascal')
     * - camelCase ('c', 'camel')
     *
     * @param  string $str
     * @param  string $case
     * @param  string $delimiter Caractère utilisé pour séparer les mots de la source.
     * @return string
     */
    public static function switchCase($str, $case = null, $delimiter = " ")
    {
        switch ($case) {
            case 'k':
            case 'kebab':
                $str = self::kebab($str, $delimiter);
                break;
            case 'p':
            case 'pascal':
                $str = self::pascal($str, $delimiter);
                break;
            case 'c':
            case 'camel':
                $str = self::camel($str, $delimiter);
                break;
            case '':
            default:
        }
        return $str;
    }
}
