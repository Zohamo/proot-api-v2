/**
 * Initialisation de Data Tables.
 *
 * @see /public/vendor/data-tables
 *
 * @see https://datatables.net/
 * @see https://datatables.net/extensions/
 *
 * Certaines extensions sont déjà installées:
 * Buttons @see https://datatables.net/extensions/buttons/
 * SearchPanes @see https://datatables.net/extensions/searchpanes/
 * DateTime @see https://datatables.net/extensions/datetime/
 */
jQuery(function () {
  /**
   * Options des boutons d'exportation.
   * @type {object}
   */
  const exportOptions = { columns: [] };
  $(".data-table thead th").each(function (index) {
    if (!$(this).hasClass("d-print-none")) {
      exportOptions.columns.push(index);
    }
  });

  const dataTable = $(".data-table").DataTable({
    // https://datatables.net/reference/option/dom
    dom:
      "<'datatables-header d-print-none d-md-flex justify-content-between align-items-center' B f>" +
      "<tr>" +
      "<'d-print-none d-md-flex justify-content-between align-items-center' l i p>",
    buttons: [
      { extend: "copy", exportOptions },
      { extend: "excel", exportOptions },
      { extend: "pdf", exportOptions },
      { extend: "print", exportOptions },
      {
        extend: "searchPanes",
        config: {
          cascadePanes: true,
          viewTotal: true,
          collapse: false,
        },
      },
    ],
    language: { url: appUrl + "/public/vendor/data-tables/fr-FR.json" },
    lengthMenu: [
      [10, 25, 50, -1],
      [10, 25, 50, "Tout"],
    ],
    order: [],
    orderCellsTop: true,
    pageLength: 25,
    drawCallback: function () {
      // Supprime le bouton 'tri' des colonnes non triables
      this.find(".not-sortable").removeClass("sorting").off();
      // Modifie le bouton de fermeture des filtres
      $(".dtb-popover-close")
        .html("")
        .addClass("btn-close")
        .css("border", "none")
        .css("background-color", "transparent");
    },
  });

  // Filtre par date (nécessite le module 'data-tables-datetime')
  $("#data-table-minDate, #data-table-maxDate").on("change", dataTable.draw);
});
