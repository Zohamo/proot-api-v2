/**
 * Formulaire dans une modale.
 *
 * Fonctions permettant l'auto-complétion du formulaire via des requêtes API.
 *
 * # Description
 *
 * On modifie les paramètres et les champs du formulaire selon qu'il s'agisse d'un ajout ou d'une
 * modification de ressource.
 *
 * # Mise en place
 *
 *     1. Ajouter le module form-modal à la méthode du contrôleur appelant le formulaire :
 *        ["modules" => ["form-modal"]]
 *        note: Le module form-validator est déjà inclus dans form-modal.
 *
 *     2. Dans la vue : définir dans la balise <form> les classes "form-modal form-validatable"
 *        et l'attribut 'data-url-api' avec pour valeur l'URL menant à l'API permettant le CRUD
 *        de la ressource.
 *        ex: <form class="form-modal form-validatable" data-url-api="<?= url() ?>/api/utilisateurs">
 * 
 *     3. Pour une modification : le bouton d'ouverture de la modale doit comporter
 *        l'attribut 'data-id' avec pour valeur l'id de l'élément à modifier.
 *        ex: <button data-bs-toggle="modal" data-bs-target="#modalFormUtilisateur" data-id="<?= $user->id() ?>">
                                         
 *     4. Pour changer le texte du titre de la modale selon qu'il s'agisse d'un ajout
 *        ou d'une modification : ajouter l'attribut 'data-text-edit' avec pour valeur
 *        le texte pour une modification dans une balise <span> contenant le texte
 *        pour un ajout.
 *        ex: <span data-text-edit="Modifier un">Nouvel</span> utilisateur
 *
 * # Fonctionnement
 *
 *     Le formulaire est vide par défaut pour l'ajout d'une nouvelle ressource.
 *
 *     1. On récupère le titre de la modale si 'data-text-edit' est défini.
 *     2. À l'ouverture de la modale, on définit les paramètres du formulaire (pour l'ajout ou la
 *        modification) et si 'data-id' est défini, on récupère les données de la ressource à modifier
 *        depuis l'API.
 *     3. À la fermeture de la modale on réinitialise le formulaire avec ses valeurs par défaut.
 */

jQuery(function () {
  $(".form-modal .modal").each(function () {
    const $form = $(this).parent("form");

    // Sauvegarde le texte d'ajout d'un élément
    if ($form.find("[data-text-edit]")) {
      $form
        .find("[data-text-edit]")
        .attr("data-text-create", $form.find("[data-text-edit]").text());
    }

    // Initialise le formulaire à l'ouverture de la modale
    $(`[data-bs-toggle='modal'][data-bs-target='#${$(this).attr("id")}']`).on(
      "click",
      function () {
        if ($(this).data("id")) {
          initFormUpdate(
            $form,
            $form.data("url-api") + "/" + $(this).data("id"),
            $form.data("key")
          );
        } else {
          initFormCreate($form, $form.data("url-api"));
        }
      }
    );

    // Vide le formulaire à la fermeture de la modale
    this.addEventListener("hidden.bs.modal", function () {
      $form.attr("action", "");
      $form.find("select").val("").trigger("change");
      $form.find("textarea").text("");
      $form.trigger("reset");
      $form.find(".spinner").show();
    });
  });
});
