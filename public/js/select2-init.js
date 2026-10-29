/**
 * Initialisation de Select2.
 *
 * Pour l'implémentation de select2 dans une modale, en plus de la classe "select2"
 * dans le <select>, il faut définir l'id de la modale parente avec l'attribut "data-dropdown-parent"
 * pour éviter les problèmes de z-index des éléments du DOM.
 * @example: `data-dropdown-parent="#modalFormEvent"`
 *
 * @see /public/vendor/select2*
 *
 * @see https://select2.org/
 * @see https://apalfrey.github.io/select2-bootstrap-5-theme/
 */
jQuery(function () {
  $("select.select2").select2({
    language: "fr",
    theme: "bootstrap-5",
  });
});
