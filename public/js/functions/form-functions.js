if (typeof initFormCreate !== "function") {
  /**
   * Initialise un formulaire de modification d'une ressource.
   *
   * 1. Définit les attributs du formulaire,
   * 2. Appelle l'API pour récupèrer les données de la ressource,
   * 3. Alimente les champs du formulaire avec les données récupérées.
   *
   * @param {object} $form Formulaire au format objet JQuery.
   * @param {string} urlApi
   * @returns {void}
   */
  function initFormCreate($form, urlApi) {
    $form.find(".spinner").hide();
    $form.find("[data-text-create]").data("text-create");
    $form
      .find("[data-text-create]")
      .text($form.find("[data-text-create]").data("text-create"));
    $form.attr("method", "POST");
    $form.attr("action", urlApi);
  }
}

if (typeof initFormUpdate !== "function") {
  /**
   * Initialise un formulaire de modification d'une ressource.
   *
   * 1. Définit les attributs du formulaire,
   * 2. Appelle l'API pour récupèrer les données de la ressource,
   * 3. Alimente les champs du formulaire avec les données récupérées.
   *
   * @param {object} $form Formulaire au format objet JQuery.
   * @param {string} urlApi
   * @param {string} primaryKey
   * @returns {void}
   */
  function initFormUpdate($form, urlApi, primaryKey = null) {
    $form
      .find("[data-text-edit]")
      .text($form.find("[data-text-edit]").data("text-edit"));
    // Modification de la requête de soumission du formulaire
    $form.attr("method", "PUT");
    $form.attr("action", urlApi);
    // Appel à l'API pour récupérer les données de la ressource
    $.get(urlApi, function (data) {
      if (!data || typeof data !== "object" || Array.isArray(data)) {
        return;
      }
      if (primaryKey && primaryKey in data) {
        $form.find("[data-id]").data("id", data[primaryKey]);
      }
      for (const prop in data) {
        setFormField(
          $form.find(
            Array.isArray(data[prop])
              ? `[name^="${prop}[]"]`
              : `[name="${prop}"]`
          ),
          data[prop]
        );
        $form.find(".spinner").hide();
      }
    });
  }
}

if (typeof setFormField !== "function") {
  /**
   * Attribue une valeur à un champ de formulaire selon son type.
   *
   * @param {object} $field Champ de formulaire au format objet JQuery.
   * @param {any} value
   * @returns {void}
   */
  function setFormField($field, value) {
    switch ($field.prop("tagName")) {
      case "SELECT":
        $field.val(value).change();
        break;
      case "TEXTAREA":
        $field.text(value);
        break;
      case "INPUT":
        switch ($field.attr("type")) {
          case "checkbox":
            $field.prop("checked", !!value);
            break;
          case "date":
            $field.val(value.split(" ")[0]);
            break;
          case "radio":
            $field.filter(`[value="${value}"]`).prop("checked", true);
            break;
          default:
            $field.val(value);
        }
    }
  }
}
