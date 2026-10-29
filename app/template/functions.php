<?php

/**
 *--------------------------------------------------------------------------
 * Fonctions utilitaires pour les vues
 *--------------------------------------------------------------------------
 */

/**
 * Renvoie "active" s'il s'agit de la page courante.
 *
 * Utilisé pour mettre en valeur la page courante dans la barre de navigation.
 *
 * Utilisation :
 * - définir `$activeLinks` dans le contrôleur.
 *      ex: `$activeLinks = ["administration", "utilisateur"]`
 * - (facultatif) ajouter une ou plusieurs clés en appelant View::render
 *      ex: View::render(
 *          ...,
 *          [
 *              [...]
 *              "activeLinks" => "formulaireUtilisateur", ...
 * - dans l'attribut 'classe' du lien de navigation ajouter <?= activeLink('identifiant-du-lien') ?>
 *      ex: <li class="nav-item <?= activeLink('formulaire-utilisateur'); ?>">
 *              <a class="nav-link" href="<?= url() ?>">
 *                  Formulaire utilisateur
 *              </a>
 *          </li>
 *
 * @param  string|string[] $links Identifiant(s) du(des) lien(s).
 * @param  string $classActive Classe CSS à renvoyer si le lien est actif.
 * @param  string $classInactive Classe CSS à renvoyer si le lien est inactif.
 * @return string
 */
function activeLink($links, $classActive = "active", $classInactive = "")
{
    if (!is_array($links)) {
        $links = [$links];
    }
    foreach ($links as $link) {
        if (in_array($link, \Core\View::$activeLinks)) {
            return $classActive;
        }
    }
    return $classInactive;
}

/**
 * Renvoie un champ 'hidden' avec un jeton CSRF à placer dans un formulaire.
 *
 * La méthode \App\Functions\HttpUtils::validateCsrfToken() permet de vérifier la validité du jeton à la réception du formulaire.
 *
 * @param  string $actionName Nom de l'action du formulaire.
 * @return string
 */
function csrfToken($actionName)
{
    $k = \AppConstants::CSRF_TOKEN_KEY . "_$actionName";
    $_SESSION[$k] = bin2hex(random_bytes(32));
    $_SESSION[$k . "_time"] = time();
    return '<input type="hidden" name="' . $k . '" value="' . $_SESSION[$k] . '" />';
}

/**
 * Renvoie un lien en HTML si la chaîne passée commence par "http".
 *
 * @param  string $link
 * @param  bool $openNewTab Ajoute l'attribut target="_blank".
 * @return string
 */
function displayLink($link, $openNewTab = true)
{
    if (substr($link, 0, 4) !== "http") {
        return $link;
    }
    $txt = \App\Functions\StringUtils::shortenUrl($link);
    return "<a href=\"$link\"" . ($openNewTab ? "target=\"_blank\"" : "") . ">$txt</a>";
}

/**
 * Renvoie le template de la modale de suppression.
 *
 * Si l'URL est variable selon l'ID, remplacer l'ID par "{?}" dans l'URL,
 * puis ajouter l'attribut "data-id" avec la valeur de l'ID dans la balise du bouton
 * d'ouverture de la modale.
 *
 * @example: Confirmation de suppression d'un utilisateur.
 * - `$modalId = "modalConfirmDeleteUser"`
 * - `$url = url() . "/admin/users/{?}/delete"`
 * - button : `<button data-bs-toggle="modal" data-bs-target="#modalConfirmDeleteUser" data-id="<?= $user->id ?>"...`
 *
 * @param  string $modalId ex: "modalConfirmDeleteUser"
 * @param  string $url URL de redirection en cas de confirmation.
 * @param  string $message
 * @return void
 * @see: app/template/components/modal-confirm-delete.php
 */
function modalConfirmDelete($modalId, $url, $message = "Êtes-vous sûr·e de vouloir supprimer cet élément&nbsp;?") // NOSONAR : Variables utilisées dans le fichier inclus
{
    include path("template") . "/components/modal-confirm-delete.php";
}

/**
 * Renvoie les scripts JavaScript et/ou CSS à afficher dans la vue et à l'endroit correspondant.
 *
 * @param  string $location   Endroit dans lequel seront insérés les scripts ('head', 'end').
 * @param  array  $addScripts Scripts supplémentaires à intégrer dans la page.
 * @return string
 * @throws InvalidArgumentException
 */
function scripts($location, $addScripts = [])
{
    $pathTemplate = path('template');
    // On récupère les scripts globaux à l'application
    $scripts = include "$pathTemplate/scripts.php";
    $scripts = !empty($scripts[$location]) ? $scripts[$location] : [];

    // S'il y a des modules supplémentaires, on les ajoute aux scripts globaux
    if (!empty($addScripts['modules'])) {
        foreach ($addScripts['modules'] as $moduleName) {
            $scriptsFile = "$pathTemplate/modules/{$moduleName}.php";
            if (!is_readable($scriptsFile)) {
                throw new \InvalidArgumentException("Le fichier '{$moduleName}.php' est introuvable.");
            }
            $moduleScripts = include $scriptsFile;
            if (!empty($moduleScripts[$location])) {
                $scripts = array_merge($scripts, $moduleScripts[$location]);
            }
        }
    }

    // S'il y a des scripts supplémentaires, on les ajoute également
    if (!empty($addScripts[$location])) {
        $scripts = array_merge($scripts, $addScripts[$location]);
    }

    // On génère l'affichage des scripts avec le template
    if (!empty($scripts)) {
        include "$pathTemplate/components/script-link.php";
    }
}

/**
 * Renvoie la valeur d'une constante de session.
 *
 * @param  string $key
 * @return mixed
 */
function session($key)
{
    return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
}

/**
 * Affiche un spinner de chargement.
 *
 * @param  boolean $fullsize Prend toute la taille de la <div> parente si utilisée est en position relative.
 * @return void
 * @see: app/template/components/spinner.php
 */
function spinner($fullsize = false) // NOSONAR : Variable utilisée dans le fichier inclus
{
    include path("template") . "/components/spinner.php";
}

/**
 * Renvoie le titre de la page HTML qui s'affiche dans l'onglet.
 *
 * @param  string $pageTitle
 * @return string
 */
function titleHead(string $pageTitle = "")
{
    $appTitle = env('APP_TITLE');
    if ($pageTitle) {
        return "$pageTitle | $appTitle";
    }
    $desc = config("description");
    return $desc ? "$appTitle &hybull; $desc" : $appTitle;
}
