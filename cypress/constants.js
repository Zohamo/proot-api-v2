import { faker } from "@faker-js/faker";
/********************************************
 * Configuration
 ********************************************/
export const isAdmin = true;
/** Temps d'attente de chargement de l'affichage */
export const delay = {
  fade: 500,
  loadDataTables: 1000,
  loadPage: 500,
};
/** Sélecteurs génériques */
export const sel = {
  modal: { btnClose: ".modal-header .btn-close" },
  toast: {
    success: ".toast.text-bg-success",
    error: ".toast.text-bg-danger",
    btnClose: "button.btn-close",
  },
};
/** Sélecteurs de la mise en page */
export const layout = {
  header: "body > header.main-header",
  navbar: "nav.navbar",
  breadcrumbs: "[data-test=breadcrumbs]",
  btnOpenSearchModal: "[data-bs-target='#modalMainSearch']",
  main: "body > main",
  footer: "body > footer.main-footer",
};
/** Constantes relatives à DataTables */
export const dataTables = {
  class: "data-table",
  sel: {
    search: ".data-table-container input[type=search]",
    deactivatedRowClasses: "table-secondary text-muted small",
  },
};
/** Textes des messages de notifications génériques */
export const txt = {
  addOk: "Ajout effectué",
  editOk: "Modification effectuée",
  deactivateOk: "a bien été désactivé",
  reactivateOk: "a bien été réactivé",
  deleteOk: "La suppression a réussi",
};
/********************************************
 * Navigation
 ********************************************/
/** Liens de navigation dans la navbar */
export const nav = {
  links: ["home"],
  linksAdmin: ["users"],
};
/** Routes de l'application */
export const url = {
  index: Cypress.config("baseUrl"),
  search: { index: Cypress.config("baseUrl") + "/search" },
  admin: {
    users: {
      index: Cypress.config("baseUrl") + "/users",
      detail: Cypress.config("baseUrl") + "/users/{id}",
      deactivateRegex: /users\/([a-zA-Z0-9_-]*)\/deactivate$/,
      reactivateRegex: /users\/([a-zA-Z0-9_-]*)\/reactivate$/,
      delete: Cypress.config("baseUrl") + "/users/{id}/delete",
      deleteRegex: /users\/([a-zA-Z0-9_-]*)\/delete$/,
      api: {
        index: Cypress.config("baseUrl") + "/api/users",
      },
    },
  },
};
/********************************************
 * Recherche
 ********************************************/
/** Constantes relatives à la page de recherche */
export const modalMainSearch = {
  sel: {
    btnOpenModal: "[data-bs-target='#modalMainSearch']",
    modal: "#modalMainSearch",
    form: "form.form-main-search",
    input: "form.form-main-search input[type=search]",
  },
};
/** Constantes relatives à la page de recherche */
export const pageSearch = {
  sel: {
    form: layout.main + " form.form-page-search",
    input: layout.main + " form.form-page-search input[type=search]",
    results: layout.main + " .search-resultats",
  },
  testInput: "Mille",
  testInputUnknown: "impossible de trouver ça",
};
/********************************************
 * Modèles
 ********************************************/
/** Constantes relatives au modèle User */
export const user = {
  /** Sélecteurs permettant d'identifier les éléments du DOM */
  sel: {
    table: "table#tableUsers",
    btnAdd: "button[data-test=add]",
    btnListRoles: "button[data-bs-target='#modalListRoles']",
    btnEdit: "button[data-test=edit]",
    btnDeactivate: "a[data-test=deactivate]",
    btnReactivate: "a[data-test=reactivate]",
    btnDelete:
      "button[data-bs-toggle=modal][data-bs-target='#modalConfirmDeleteUser'][data-test=delete]",
    btnConfirmDelete: "a.btn-confirm",
    modalConfirmDelete: "#modalConfirmDeleteUser",
    form: "form#formUser.form-modal.form-validatable",
    detail: `[data-test="detail-user"]`,
    modalForm: "#modalFormUser",
    modalListRoles: "#modalListRoles",
  },
  /** Nombre minimum d'entrées du tableau */
  minNbRows: 1,
  /** Nombre de rôles que l'on doit trouver dans la liste */
  nbRoles: 3,
  /** Propriétés du modèle avec leurs attributs pour vérifier les champs du formulaire et générer des valeurs aléatoires */
  props: [
    {
      name: "usr_lastname",
      tag: "input[type=text]",
      type: "string",
      required: true,
      length: 50,
      value: "Cytest",
    },
    {
      name: "usr_firstname",
      tag: "input[type=text]",
      type: "string",
      required: true,
      length: 50,
      value: faker.person.firstName(),
    },
    {
      name: "usr_email",
      tag: "input[type=email]",
      required: true,
      length: 255,
      value: faker.internet.email(),
      invalidValues: ["invalid-email", "user@domain", "user@domain."],
    },
    {
      name: "fk_role_id",
      tag: "select",
      type: "number",
      required: true,
      value: faker.helpers.arrayElement(["1", "25", "28"]),
    },
  ],
  /** Textes des messages de notifications spécifiques à ce modèle */
  txt: {
    addOk: "a bien été ajouté",
    editOk: "a bien été modifié",
    deleteOk: "a bien été supprimé",
  },
};
