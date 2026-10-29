<?php

namespace App\Functions;

/**
 * Fonctions utilitaires de manipulation des dates.
 */
class DateUtils
{
    /**
     * Renvoie une date au format anglais au format français.
     *
     * @param  string $date
     * @return string
     */
    public static function dateEnToFr($date)
    {
        if (!$date) {
            return "";
        }
        return date(\AppConstants::FORMAT_DATE_DISPLAY, strtotime($date));
    }

    /**
     * Renvoie une date au format français au format anglais.
     *
     * @param  string $date
     * @return string
     */
    public static function dateFrToEn($date)
    {
        if (!$date) {
            return "";
        }
        list($day, $month, $year) = explode("/", $date);
        return "$year-$month-$day";
    }

    /**
     * Convertit un format de date en un autre.
     *
     * @param  string $date   Date à convertir.
     * @param  string $before Format de date de la source.
     * @param  string $format  Format de date à renvoyer.
     * @return string
     */
    public static function convert($date, $before, $format)
    {
        return is_string($date) && $date
            ? \DateTime::createFromFormat($before, $date)->format($format)
            : $date;
    }

    /**
     * Convertit un format de date pour l'affichage en français.
     *
     * @see https://unicode-org.github.io/icu/userguide/format_parse/datetime/#date-field-symbol-table
     * @param  string $datetime Date/heure au format 'Y-m-d H:i:s'.
     * @param  string $format Format de date à renvoyer (défaut: "Mercredi 10 juillet 2024").
     * @return string
     */
    public static function convertWithIntl($datetime, $format = "EEEE e MMMM Y")
    {
        $fmt = new \IntlDateFormatter(
            'fr_FR',
            \IntlDateFormatter::FULL,
            \IntlDateFormatter::FULL,
            'Europe/Paris',
            \IntlDateFormatter::GREGORIAN,
            $format
        );
        return ucfirst($fmt->format(new \Datetime($datetime)));
    }

    /**
     * Convertit un format de date au format SQL.
     *
     * @param  string $date   Date à convertir.
     * @param  string $before Format de date de la source.
     * @return string
     */
    public static function toSQL($date, $before = '')
    {
        if (!$before) {
            $before = \AppConstants::FORMAT_DATE_DISPLAY;
        }
        return self::convert($date, $before, 'Y-m-d H:i:s');
    }

    /**
     * Convertit un format de date pour l'affichage.
     *
     * @param  string $date   Date à convertir.
     * @param  string $before Format de date de la source.
     * @return string
     */
    public static function toHTML($date, $before = 'Y-m-d H:i:s')
    {
        return self::convert($date, $before, \AppConstants::FORMAT_DATE_DISPLAY);
    }
}
