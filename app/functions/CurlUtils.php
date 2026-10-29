<?php

namespace App\Functions;

/**
 * Fonctions utilitaires pour les opérations cURL.
 */
class CurlUtils
{
    /**
     * Lance une opération cURL.
     *
     * @param array{URL: string, CUSTOMREQUEST: string} $params Clés :
     *        string 'url'            URL de l'action à effectuer.
     *        string 'customRequest'  Type d'action à réaliser (lecture de dossier, récupération de fichier etc).
     *                                @see https://curl.haxx.se/libcurl/c/CURLOPT_CUSTOMREQUEST.html
     *        bool   'noProgress'     Toggle on/off progress meter (true = off).
     *        string 'proxy'          URL du proxy.
     *        string 'proxyAuth'      Type d'authentification HTTP.
     *        string 'userAgent'      Nom de la connexion.
     *        array  'header'         Headers de la requètes http.
     *        int    'redir'          Nombre maximum de redirection acceptées.
     *        string 'certif'         Certificat utilisé lors de la connexion au serveur.
     *        string 'certKey'        Clé du certificat utilisé lors de la connexion au serveur.
     *        int    'verifyPeer'     Détermine si le cURL doit vérifier le certificat de la cible (1 = oui, 0 = non).
     *        int    'verifyHost'     Détermine ce qui doit être vérifié. (2 = Le serveur est-il bien celui demandé ? 1 = unused 0 = pas de vérification).
     *        int    'followLoc'      Détermine si le cURL accepte les redirections demandées par le serveur distant (1 = oui, 0 = non).
     *        int    'returnTransfer' Pour que curl_exec() renvoie une chaîne de caractères (1 = oui, 0 = non).
     * @return string|bool
     */
    public static function exec(array $params)
    {
        if (empty($params['url'])) {
            throw new \Exception("Vous devez définir une URL pour une requête cURL.");
        }

        $p = array_merge([
            "customRequest" => "GET",
            "noProgress" => env('CURL_NOPROGRESS'),
            "proxy" => env('PROXY_URL'),
            "userAgent" => env('CURL_USERAGENT'),
            "header" => [],
            "redir" => intval(env('CURL_MAXREDIRS')),
            "timeout" => intval(env('CURL_TIMEOUT')),
            "connectTimeout" => intval(env('CURL_CONNECTTIMEOUT')),
            "verifyPeer" => 0,
            "verifyHost" => 0,
            "followLoc" => 1,
            "returnTransfer" => 1
        ], $params);

        $curl = curl_init();
        try {
            curl_setopt_array($curl, [
                CURLOPT_URL => $p['url'],
                CURLOPT_CUSTOMREQUEST => $p['customRequest'],
                CURLOPT_NOPROGRESS => $p['noProgress'],
                CURLOPT_PROXY => $p['proxy'],
                CURLOPT_PROXYAUTH => CURLAUTH_BASIC,
                CURLOPT_USERAGENT => $p['userAgent'],
                CURLOPT_HTTPHEADER => $p['header'],
                CURLOPT_MAXREDIRS => $p['redir'],
                CURLOPT_TIMEOUT => $p['redir'],
                CURLOPT_CONNECTTIMEOUT => $p['redir'],
                CURLOPT_SSL_VERIFYPEER => $p['verifyPeer'],
                CURLOPT_SSL_VERIFYHOST => $p['verifyHost'],
                CURLOPT_RETURNTRANSFER => $p['returnTransfer'],
                CURLOPT_FOLLOWLOCATION => $p['followLoc'],
                CURLOPT_SSLVERSION => 7
            ]);
            if (!empty($p['certif'])) {
                curl_setopt($curl, CURLOPT_SSLCERT, $p['certif']);
            }
            if (!empty($p['certKey'])) {
                curl_setopt($curl, CURLOPT_SSLKEY, $p['certKey']);
            }

            $response = curl_exec($curl);

            if (curl_errno($curl)) {
                exit(curl_error($curl));
            }
            $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($http_code == intval(200)) {
                return $response;
            } else {
                return "Ressource introuvable : $http_code";
            }
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            unset($curl);
        }
    }
}
