<?php

namespace App\Functions;

/**
 * Fonctions utilitaires sur les requêtes HTTP.
 */
class HttpUtils
{
    /**
     * Crée les headers forçant le téléchargement d'un fichier.
     *
     * @param  string   $fileName Nom du fichier.
     * @param  string[] $headers  Autres headers à ajouter.
     * @return void
     */
    public static function dowloadSendHeaders(string $fileName, $headers = [])
    {
        $now = gmdate("D, d M Y H:i:s");

        // Désactive le cache
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");
        header('Pragma: public');

        // Force le téléchargement
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // Disposition / Encodage de la réponse
        header("Content-Disposition: attachment; filename={$fileName}");
        header("Content-Transfer-Encoding: binary");
        header('Content-Encoding: UTF-8');

        // Autres
        foreach ($headers as $value) {
            header($value);
        }
    }

    /**
     * Renvoie les données passées avec la méthode HTTP 'PUT'.
     *
     * @return string[]
     */
    public static function getPutData()
    {
        $putfp = fopen('php://input', 'r');
        $putData = '';
        while ($data = fread($putfp, 1024)) {
            $putData .= $data;
        }
        fclose($putfp);
        parse_str($putData, $result);
        return $result;
    }

    /**
     * Renvoie une réponse HTTP au format JSON.
     *
     * @param  mixed   $data  Données ou message.
     * @param  integer $code  Code de réponse HTTP.
     * @return void
     */
    public static function jsonResponse($data = null, $code = 200)
    {
        header_remove();
        http_response_code($code);
        header("Cache-Control: no-transform, no-store, no-cache, public, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type');
        if ($code !== 200) {
            $data = [
                'status' => $code,
                'message' => $data
            ];
        }
        echo json_encode($data);
        exit;
    }

    /**
     * Crée les headers forçant l'ouverture d'un fichier.
     *
     * @param  string  $fileName     Nom du fichier.
     * @param  string  $pathToFile   Chemin vers le fichier.
     * @param  string  $contentType  Type de contenu (ex: "application/pdf").
     * @return void
     */
    public static function openFile($fileName, $pathToFile, $contentType)
    {
        header("Content-type: $contentType");
        header("Content-Disposition: inline; filename=$fileName");
        @readfile($pathToFile);
    }
}
