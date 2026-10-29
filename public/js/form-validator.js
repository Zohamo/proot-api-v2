/**
 * Validateur de formulaire.
 * Fonctions de validation AJAX, d'affichage d'erreurs et redirection.
 *
 * # Description
 *
 * Si les données passées dans le formulaire sont correctes, on effectue une redirection.
 *
 * # Mise en place
 *
 *     1. Ajouter le module form-validator à la méthode du contrôleur appelant le formulaire :
 *        ["modules" => ["form-validator"]]
 *
 *     2. Ajouter la classe "form-validatable" à la balise <form> du formulaire,
 *        et éventuellement data-redirect="url_de_redirection" s'il faut rediriger la page
 *        suite à la soumission du formulaire.
 *
 *     3. La méthode du contrôleur doit renvoyer la réponse du validateur :
 *        `$errors = $this->model->sanitize($request)->validate();`
 *        puis `HttpUtils::jsonResponse($errors);`
 *
 * # Fonctionnement
 *
 *     1. On crée le conteneur de la liste des erreurs s'il n'existe pas
 *     2. À la soumission du formulaire, on fait un appel AJAX: `validateForm()`
 *     3. S'il y a des erreurs, on les affiche: `displayErrors()`,
 *        sinon on recharge la page, ou on redirige vers url_de_redirection si
 *        l'attribut data-redirect a été renseigné dans <form>.
 */

jQuery(function () {
  const $formsValidatable = $(".form-validatable");
  if (!$formsValidatable.length) {
    return;
  }

  $formsValidatable.on("submit", function (e) {
    e.preventDefault();
    validateForm($(this));
  });

  // On crée le conteneur d'erreurs s'il n'a pas été défini
  $formsValidatable.each(function () {
    if (!$(this).find(".form-errors-container").length) {
      $(this).prepend(listErrorsHTML);
    }
  });

  // On cache le conteneur des erreurs au clic sur la croix
  $(".form-errors-container .btn-close").on("click", function () {
    $(this).parent().parent().addClass("d-none").toggleClass("hide show");
  });
});

if (typeof validateForm !== "function") {
  /**
   * Appelle l'API qui vérifie la validité des données d'un formulaire puis
   * affiche les erreurs,
   * ou redirige vers l'URL définie par la propriété 'redirect' reçue,
   * ou recharge la page.
   *
   * @param {object} $form Formulaire JQuery.
   * @return {void}
   */
  function validateForm($form) {
    // Désactivation du bouton 'submit'
    $form.find("[type=submit]").addClass("disabled").attr("disabled", true);
    // On cache les erreurs précédentes
    hideErrors($form);

    // Appel AJAX
    $.ajax({
      url: $form.attr("action"),
      method: $form.attr("method") || "POST",
      data: $form.serialize(),
      success: function (errors) {
        // Récupération des erreurs
        if (
          typeof errors === "string" ||
          errors instanceof String ||
          (typeof errors === "object" &&
            !Array.isArray(errors) &&
            errors !== null &&
            !errors.redirect)
        ) {
          // Affichage des erreurs
          displayErrors($form, errors);
          // Réactivation du bouton 'submit'
          $form
            .find("[type=submit]")
            .removeClass("disabled")
            .prop("disabled", false);
        }
        // Aucune erreur : redirection ou rechargement de la page
        // On reçoit l'adresse de redirection
        else if (errors?.redirect) {
          globalThis.location.replace(errors.redirect);
        }
        // On récupère l'adresse depuis l'attribut data-redirect dans <form>
        else if ($form.data("redirect")) {
          globalThis.location.replace($form.data("redirect"));
        }
        // On recharge la page
        else {
          globalThis.location.reload();
        }
      },
      error: function () {
        globalThis.location.reload();
      },
    });
  }
}

if (typeof displayErrors !== "function") {
  /**
   * Affiche les erreurs de validation du formulaire.
   *
   * @param {object} $form Formulaire JQuery.
   * @param {array[]} errors Liste des erreurs.
   */
  function displayErrors($form, errors) {
    // On affiche les nouveaux messages d'erreur
    for (const field in errors) {
      for (const error in errors[field]["errors"]) {
        // Si un message d'erreur personnalisé existe on l'affiche (ex: id="matricule-unique")
        if ($form.find(`.list-form-errors #${field}-${error}`).length) {
          $form
            .find(`.list-form-errors #${field}-${error}`)
            .removeClass("d-none");
        }
        // Sinon on crée un message temporaire avec le message de l'erreur
        else {
          $form
            .find(".list-form-errors")
            .append(
              `<li class="temp-error"><strong>${errors[field]["alias"]}</strong> ${errors[field]["errors"][error]["message"]}</li>`,
            );
        }
      }
    }
    $form
      .find(".form-errors-container")
      .addClass("show")
      .removeClass("d-none hide");

    $("html, body").animate({
      scrollTop: $form.find(".list-form-errors").offset().top,
    });
  }
}

if (typeof hideErrors !== "function") {
  /**
   * Cache ou supprime tous les éventuels messages d'erreur.
   *
   * @param {object} $form Formulaire JQuery.
   */
  function hideErrors($form) {
    $form
      .find(".form-errors-container")
      .addClass("d-none hide")
      .removeClass("show");
    $form.find(".list-form-errors .temp-error").remove();
    $form.find(".list-form-errors li").addClass("d-none");
  }
}

/**
 * Conteneur HTML de la liste des erreurs.
 * @type {string}
 */
const listErrorsHTML = `<div class="form-errors-container alert alert-danger fade hide d-none" role="alert">
        <div class="d-flex align-items-center">
            <div>
                <p class="mb-0">Une erreur est survenue.</p>
                <ul class="list-form-errors mb-0"></ul>
            </div>
            <button type="button" class="btn btn-close ms-auto" aria-label="Fermer" />
        </div>
    </div>`;
