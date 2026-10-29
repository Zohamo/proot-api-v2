# Proot API v2

Proot API v2 est basé sur le framework PHP **Tela**, développé par l'équipe TDI et influencé par [github.com/daveh/php-mvc](https://github.com/daveh/php-mvc) pour la structure, [github.com/tiagopaes/php-dao](https://github.com/tiagopaes/php-dao) pour le modèle DAO et [Laravel](https://laravel.com/) pour la syntaxe.

## Sommaire

- **[Configuration](#configuration)**
  - [Configuration requise pour le serveur](#configuration-serveur)
  - [Configuration de l'application](#configuration-application)
  - [Configuration de l'environnement](#configuration-environnement)
- **[Tests](#tests)**
  - [Installation de Cypress](#installation-cypress)
  - [Lancer les tests](#lancer-tests)
- **[Sass/Scss](#sass)**
  - [Installation du compileur SCSS](#installation-sass)
  - [Architecture Scss](#architecture-sass)
- **[Librairies](#librairies)**
- **[Erreurs](#erreurs)**

## <a id="configuration"></a>Configuration

### <a id="configuration-serveur"></a>Configuration requise pour le serveur

- Une version supérieure ou égale à **PHP 7.2.5** est nécessaire.

- Le **module de réécriture d'URL** doit être activé dans la configuration Apache : décommenter la ligne `LoadModule rewrite_module "{APACHEPATH}/modules/mod_rewrite.so"`.

- Apache doit permettre au fichier **.htaccess** d'outrepasser sa configuration : dans le fichier _/etc/httpd/conf/httpd.conf_, modifier le `AllowOverride` du répertoire concerné à `AllowOverride All`.

- **PDO** doit être installé :

```
yum install php-pdo
yum install php-pdo_mysql
service httpd restart
```

- **php-json** : `dnf install php-json.x86_64`

### <a id="configuration-application"></a>Configuration de l'application

Le fichier **composer.json** contient certaines informations relatives à l'application, notamment :

- la **version** de l'application
- les **librairies tierces** utilisées

### <a id="configuration-environnement"></a>Configuration de l'environnement

Le fichier **[.env](.env)** contient toutes les constantes de configuration globale du projet :

- **ENV_NAME** : Le nom de l'environnement courant
- **APP_TITLE** : Le nom de l'application
- **APP_URL** : L'URL absolue vers l'application
- **APP_ROOT** : Le chemin absolu du serveur vers l'application

## <a id="tests"></a>Tests

La librairie Cypress permet de jouer des scénarios de tests bout à bout (end-to-end).

### <a id="installation-cypress"></a>Installation de Cypress

`npm install cypress`.

### <a id="lancer-tests"></a>Lancer les tests

_Avant de lancer les tests il est préférable d'enlever le mode de débogage (`is_super_admin` dans la table 't_users') puis de détruire la session ([/tools/detruire-session])._

Pour ouvrir Cypress : `npx cypress open`.

Puis choisir les tests à lancer.

## <a id="sass"></a>Sass/Scss

L'utilisation du SCSS permet de personnaliser les couleurs de Bootstrap facilement mais aussi d'utiliser une syntaxe CSS plus riche.
Pour en bénéficier il nous faut compiler les fichiers SCSS en un ou plusieurs fichiers CSS.

### <a id="installation-sass"></a>Installation du compileur SCSS

La manière de compiler le SCSS la manière la plus simple en utilisant VSCode est la suivante:

1. Installer le plugin _Live Sass Compiler_ dans VSCode
2. Une fois placé sur le fichier SCSS à compiler, cliquer sur _Watch Sass_ en bas à droite de VSCode
3. Le plugin génèrera automatiquement les fichiers CSS et MAP associés

Pour d'autres IDE il faudra probablement installer et utiliser Sass via [npm sass](https://www.npmjs.com/package/sass).

### <a id="architecture-sass"></a>Architecture Scss

Le seul fichier à intégrer dans l'application est le fichier _public/css/main.css_. Ce fichier généré depuis _public/css/main.scss_ contient la compilation de tous les sous-fichiers importés, comme les éléments (séparés) de Bootstrap et divers fichiers de style de Tela.

## <a id="librairies"></a>Librairies

Les librairies externes utilisées :

| Librairie                                                                          | Langage | Version | Description                                                                                                                                                                                                                                                            |
| ---------------------------------------------------------------------------------- | :-----: | :-----: | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| [Bootstrap](https://getbootstrap.com/)                                             | CSS/JS  |  5.3.8  | Collection d'outils utiles à la création du design de sites et d'applications web. C'est un ensemble qui contient des codes HTML et CSS, des formulaires, boutons, outils de navigation et autres éléments interactifs, ainsi que des extensions JavaScript en option. |
| [Cypress](https://www.cypress.io/)                                                 |   JS    | 16.2.0  | Outil permettant de jouer des scénarios de tests bout à bout (end-to-end).                                                                                                                                                                                             |
| [DataTables](https://datatables.net/)                                              |   JS    |  3.1.4  | Plugin JQuery dédié aux tableaux, permettant la recherche, la pagination et le tri des données.                                                                                                                                                                        |
| [Font Awesome](https://fontawesome.com/)                                           |   CSS   |  7.3.1  | Police d'écriture et un outil d'icônes qui se base sur CSS, LESS et SASS.                                                                                                                                                                                              |
| [jQuery](https://jquery.com/)                                                      |   JS    |  4.0.0  | Bibliothèque JavaScript libre et multiplateforme créée pour faciliter l'écriture de scripts côté client dans le code HTML des pages web.                                                                                                                               |
| [Moment.js](https://momentjs.com/)                                                 |   JS    | 2.31.0  | Outil de manipulation de dates et du temps.                                                                                                                                                                                                                            |
| [Select2](https://select2.org/)                                                    |   JS    |  4.1.0  | Librairie Javascript / jQuery qui améliore l'usage des listes déroulantes natives des navigateurs en facilitant entre autres la recherche d'un terme dans la liste.                                                                                                    |
| [Select2 Bootstrap 5 theme](https://apalfrey.github.io/select2-bootstrap-5-theme/) |   CSS   |  1.3.0  | Donne l'apparence de Bootstrap 5 à Select2.                                                                                                                                                                                                                            |
| [symfony/var-dumper](https://symfony.com/doc/current/components/var_dumper.html)   |   PHP   |  8.1.9  | Fonctions de débogage (`dump` et `dd`) issues de Symfony, permettant d'afficher les informations structurées d'une variable.                                                                                                                                           |
| [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv)                            |   PHP   |  5.8.0  | Permet de définir et récupérer les variables d'environnement facilement.                                                                                                                                                                                               |

## <a id="erreurs"></a>Erreurs

Voici une liste d'erreurs récurrentes et la solution pour les résoudre.

### Erreur : `Parse error: syntax error, unexpected ':', expecting '{' in ...endor\symfony\polyfill-php80\bootstrap.php on line 23`

- Solution : Vérifier que la version de PHP soit >= 8.5 (fréquent en local).

### Erreur : Seulement une partie de la barre de navigation s'affiche et pas le reste de la page

Exemple: `Exception: '{get_class(InvalidArgumentException: L'id. du rôle de l'utilisateur est inconnu.`

Il s'agit possiblement d'un conflit avec l'utilisateur enregistré en Session entre différentes applications sur le même serveur.

- Solution : Forcer la déconnexion à l'application en tapant `signout` dans l'URL. ex: `/tela/signout`
