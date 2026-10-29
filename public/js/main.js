jQuery(function () {
  /**
   * Raccourcis clavier
   */
  $(document).on("keydown", function (e) {
    // ctrl+k : ouverture de la modale de recherche
    if (e.ctrlKey && e.key.toLowerCase() === "k") {
      e.preventDefault();
      e.stopPropagation();
      $("#modalMainSearch").modal("show");
    }
  });

  /**
   * Bootstrap : Popover
   */
  $('[data-bs-hover="popover"]').popover({
    trigger: "hover",
    placement: "bottom",
    container: "body",
  });

  /**
   * Bootstrap : Tabs -> Persistance de l'onglet affiché dans l'URL.
   *
   * Permet la persistance du dernier onglet ouvert en ajoutant la classe "nav-tabs-persistent"
   * à celle de "nav-tabs".
   * L'id du bouton doit terminer par "-tab", ex: "files-tab"
   */
  $('.nav-tabs.nav-tabs-persistent [data-bs-toggle="tab"]').on(
    "shown.bs.tab",
    (e) => {
      const tab = $(e.target).attr("id").replace("-tab", "");
      const url = globalThis.location.origin + globalThis.location.pathname;
      globalThis.history.replaceState({ tab }, undefined, url + "?tab=" + tab);
    }
  );
  // Récupération des paramètres GET de l'URL
  if (globalThis.location.search) {
    const urlParams = new URLSearchParams(globalThis.location.search);
    if (urlParams.get("tab")) {
      $(`#${urlParams.get("tab")}-tab`).tab("show");
    }
  }

  /**
   * Bootstrap : Toast
   */
  $(".toast.autohide").toast("show");

  /**
   * Bootstrap : Tooltip
   */
  $('[data-bs-hover="tooltip"]').tooltip({ delay: { show: 250, hide: 0 } });

  /**
   * Bouton : retour en haut de page
   */
  $(globalThis).on("scroll", function () {
    if ($(this).scrollTop() > 500) {
      $("#back-to-top").fadeIn();
    } else {
      $("#back-to-top").fadeOut();
    }
  });
  $("#back-to-top").on("click", function () {
    $("body, html").animate({ scrollTop: 0 }, 250);
  });

  /**
   * Modale : recherche
   * Autofocus sur le champ de saisie à l'ouverture de la modale.
   */
  $("#modalMainSearch").on("shown.bs.modal", function () {
    $("#query").trigger("focus");
  });

  /**
   * Modifie le lien de la modale de confirmation de suppression.
   *
   * Au clic on récupère la valeur de l'attribut `data-id` du bouton
   * et on le met à la place de `{?}` dans l'attribut `href` du bouton
   * de confirmation de la modale.
   */
  $(".modal-confirm-delete").each(function () {
    const deleteItem = $(this).find(".btn-confirm").attr("href");
    $(`[data-bs-toggle='modal'][data-bs-target='#${this.id}']`).on(
      "click",
      function () {
        $(`${$(this).data("bs-target")} .btn-confirm`).attr(
          "href",
          deleteItem.replace("{?}", $(this).data("id"))
        );
      }
    );
  });
});

if (typeof buildAlert !== "function") {
  /**
   * Crée un message d'alerte.
   *
   * @param {string} message Message à afficher
   * @param {string} type Type d'alerte Bootstrap (danger, success, warning, ...)
   *
   * @return {object} Objet JQuery
   */
  function buildAlert(message, type = "danger") {
    return $(`<div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
      ${message}
      <button type="button" class="btn-close" data-test="close" data-bs-dismiss="alert" aria-label="Fermer" />
    </div>`);
  }
}

if (typeof buildToast !== "function") {
  /**
   * Crée un message toast.
   *
   * Nécessite d'initialiser la fonction 'autohide' si à `true` : $(".toast.autohide").toast("show");
   *
   * @param {string} message Message à afficher
   * @param {string} type Type d'alerte Bootstrap (danger, success, warning, ...)
   * @param {boolean} autohide Si le message doit disparaître automatiquement
   *
   * @return {object} Objet JQuery
   */
  function buildToast(message, type = "danger", autohide = true) {
    return $(`<div class="toast align-items-center text-bg-${type} border-0 ${
      autohide ? "autohide" : "show"
    }" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
        <div class="toast-body">${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
</div>`);
  }
}

if (typeof getAndDisplayToasts !== "function") {
  /**
   * Appelle l'API de l'application pour récupérer les alertes stockées
   * puis les affiche sous la forme de Toasts.
   */
  function getAndDisplayToasts() {
    $.get(appUrl + "/api/alertes", function (alerts) {
      if (alerts?.length) {
        $(".toast-container").empty();
        for (const alert of alerts) {
          $(".toast-container").append(
            buildToast(alert.message, alert.type, alert.autohide)
          );
        }
        $(".toast.autohide").toast("show");
      }
    });
  }
}
