<?php

namespace App\Functions;

/**
 * Fonctions utilitaires pour les calculs.
 */
class CalcUtils
{
    /**
     * Calcule le pourcentage d'une valeur.
     *
     * @param  int|float $value Valeur à calculer.
     * @param  int|float $total Total pour le calcul.
     * @param  int       $precision Nombre de chiffres après la virgule.
     * @return float|false
     */
    public static function rate($value, $total, $precision = 2)
    {
        if (!$total) {
            return false;
        }
        return round($value * 100 / $total, $precision);
    }
}
