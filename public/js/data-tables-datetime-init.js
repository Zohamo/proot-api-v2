/**
 * Initialisation de l'extension 'datetime' de Data Tables.
 *
 * Permet de filtrer une plage de temps.
 *
 * @usage La balise <table> doit comporter l'attribut 'data-datetime'
 * avec l'index de la colonne à filtrer (à partir de 0).
 *
 * @see app/template/components/data-table-datetime.php
 * @see https://datatables.net/
 * @see https://datatables.net/extensions/datetime/
 */

/**
 * Fonction de filtre qui recherchera dans la colonne définie par l'attribut 'data-datetime'
 * (dans la balise <table>) les données comprises entre les deux valeurs.
 */
$.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
  const dateIndex = $(".data-table").data("datetime");
  if (dateIndex === null || isNaN(dateIndex)) {
    // Aucun index n'est défini pour la colonne à filtrer
    // => ajouter l'attribut 'data-datetime' à <table>.
    return true;
  }
  const min = $("#data-table-minDate").val();
  const max = $("#data-table-maxDate").val();
  if (!min && !max) {
    return true;
  }
  if (!data[dateIndex]) {
    return false;
  }
  const dateValue = data[dateIndex].split("/").reverse().join("-");
  return (!min || min <= dateValue) && (!max || max >= dateValue);
});
