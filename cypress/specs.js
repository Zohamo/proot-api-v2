import { dataTables, delay, layout, sel, txt } from "./constants";
import { fillForm } from "./functions";

export const specs = {
  /**
   * Vérifie la structure de la page (header, main, footer) et son accessibilité.
   *
   * @param {string} [linkName] Lien à mettre en valeur dans la navbar (data-test="link-*").
   * @param {boolean} [showBreadcrumbs] Si le fil d'Ariane doit être affiché.
   */
  firstCheck: (linkName = "", showBreadcrumbs = true) => {
    // Structure principale
    for (const prop in layout) {
      if (prop !== "breadcrumbs") {
        cy.get(layout[prop]).should("be.visible");
      }
    }
    // Fil d'Ariane
    cy.get(`${layout.header} > ${layout.breadcrumbs}`).should(
      showBreadcrumbs ? "be.visible" : "not.exist",
    );
    cy.get("body").then(($body) => {
      // Attribut 'scope' aux <th> des tableaux
      if ($body.find("table th").length) {
        $body.find("table th").each(function () {
          expect(this).to.have.attr("scope").and.to.be.oneOf(["col", "row"]);
        });
      }
      // Présence des modales
      if ($body.find("[data-bs-toggle=modal]").length) {
        const modalsChecked = [];
        $body.find("[data-bs-toggle=modal]").each(function () {
          expect(this).to.have.attr("data-bs-target");
          if (!modalsChecked.includes(this.dataset.bsTarget)) {
            cy.get(this.dataset.bsTarget)
              .should("exist")
              .should("have.class", "modal");
            modalsChecked.push(this.dataset.bsTarget);
          }
        });
      }
    });
    // Lien de la navbar mis en valeur (actif)
    if (linkName) {
      cy.get(`${layout.navbar} [data-test=link-${linkName}]`).should(
        "have.class",
        "active",
      );
    }
  },

  /**
   * Supprime les éléments ajoutés lors des précédents tests.
   *
   * @param {{sel: {table: string, btnDelete: string}}} m Modèle de l'élément à nettoyer (cf. constants.js).
   * @param m.sel.btnDelete Sélecteur du bouton de suppression d'un élément.
   * @param m.sel.table Sélecteur de la table.
   * @param {string} label Libellé du test à supprimer.
   * @param {string} urlDelete URL de suppression du modèle.
   * @param {{index: string, delete: string}} urlM URL du modèle.
   */
  cleanTests: (m, label, urlM) => {
    if (Cypress.$(m.sel.table).hasClass(dataTables.class)) {
      cy.get(dataTables.sel.search).clear({ force: true }).type(label);
    }
    cy.get(m.sel.table + " tr").then(($rows) => {
      let $cyTestRows = $rows.filter(
        (i, el) =>
          Cypress.$(el).text().toLowerCase().indexOf(label.toLowerCase()) > -1,
      );
      if (!$cyTestRows.length) {
        cy.log("nettoyage : Aucun élément de test");
      } else {
        $cyTestRows.each(function () {
          const id = Cypress.$(this).find(m.sel.btnEdit).data("id");
          cy.visit(urlM.delete.replace("{id}", id));
          cy.log("nettoyage : Suppression de l'élément de test #" + id);
          cy.location("href").should("equal", urlM.index);
        });
      }
    });
  },

  /**
   * Vérifie un tableau, son nombre d'entrées, un bouton édition et l'ouverture de sa modale.
   *
   * @param {{sel: {btnEdit?: string, modalForm?: string, table: string},
   *    minCount?: number, minNbRows?: number}} m Modèle de l'élément à vérifier (cf. constants.js).
   * @param m.sel.btnEdit Sélecteur du bouton d'édition d'un élément.
   * @param m.sel.modalForm Sélecteur de la modale d'édition d'un élément.
   * @param m.sel.table Sélecteur de la table.
   * @param m.minCount Nombre de lignes que le tableau doit contenir.
   * @param m.minNbRows Nombre minimum de lignes que le tableau doit contenir.
   * @param {{delete: string, deactivateRegex?: RegExp, reactivateRegex?: RegExp,
   *    api: {index: string}}} urlM URL du modèle.
   */
  checkTable: (m, urlM) => {
    if (!m.sel?.table) {
      cy.log("Ce modèle ne contient pas de sélecteur du tableau.");
      return;
    }
    // Visibilité tu tableau
    cy.get(m.sel.table)
      .should("be.visible")
      .then(($table) => {
        // Nombre de lignes du tableau
        const nbRows = $table.find("tbody > tr").length;
        if (Number.isInteger(m.nbRows)) {
          expect(nbRows).to.eq(m.nbRows);
        }
        if (m.minCount) {
          expect(nbRows).to.be.at.least(m.minNbRows);
        }
        if (!nbRows) {
          cy.log("Aucune entrée dans le tableau.");
          return;
        }
        // Boutons d'édition
        if (m.sel.btnEdit && m.sel.modalForm) {
          const $btnEdit = $table.find(m.sel.btnEdit);
          if (!$btnEdit.length) {
            cy.log("Aucun bouton 'Modifier' dans le tableau");
          } else {
            expect($btnEdit).to.have.attr("data-bs-toggle", "modal");
            expect($btnEdit).to.have.attr("data-bs-target", m.sel.modalForm);
            expect($btnEdit).to.have.attr("data-id").not.to.be.empty;
            specs.checkModalForm(m, urlM);
          }
        }
        // Boutons de désactivation
        if (m.sel.btnDeactivate) {
          const $btnDeactivate = $table.find(m.sel.btnDeactivate);
          if (!$btnDeactivate.length) {
            cy.log("Aucun bouton 'Désactiver' dans le tableau");
          } else {
            expect($btnDeactivate)
              .to.have.attr("href")
              .and.match(urlM.deactivateRegex);
          }
        }
        // Boutons de réactivation
        if (m.sel.btnReactivate) {
          const $btnReactivate = $table.find(m.sel.btnReactivate);
          if (!$btnReactivate.length) {
            cy.log("Aucun bouton 'Réactiver' dans le tableau");
          } else {
            expect($btnReactivate)
              .to.have.attr("href")
              .and.match(urlM.reactivateRegex);
          }
        }
        // Boutons de suppression
        if (m.sel.btnDelete && m.sel.modalConfirmDelete) {
          const $btnDelete = $table.find(m.sel.btnDelete);
          if (!$btnDelete.length) {
            cy.log("Aucun bouton 'Supprimer' dans le tableau");
          } else {
            expect($btnDelete).to.have.attr("data-bs-toggle", "modal");
            expect($btnDelete).to.have.attr(
              "data-bs-target",
              m.sel.modalConfirmDelete,
            );
            expect($btnDelete).to.have.attr("data-id").not.to.be.empty;
            specs.checkModalConfirmDelete(m, urlM);
          }
        }
      });
  },

  /**
   * Vérifie la modale d'ajout/modification d'un élément.
   *
   * @param {{sel: {btnAdd: string, btnEdit: string, form: string, modalForm: string,
   *    table: string}, props: {name: string, tag: string, required?: boolean}[]}} m Modèle de l'élément à supprimer (cf. constants.js).
   * @param m.sel.btnAdd Sélecteur du bouton d'ajout d'un élément.
   * @param m.sel.btnEdit Sélecteur du bouton d'ajout d'un élément.
   * @param m.sel.form Sélecteur du formulaire d'ajout/modification d'un élément.
   * @param m.sel.modalForm Sélecteur de la modale de confirmation de suppression d'un élément.
   * @param m.sel.table Sélecteur de la table.
   * @param m.props Propriétés du modèle.
   * @param {{delete: string, api: {index: string}}} urlM URL du modèle.
   */
  checkModalForm: (m, urlM) => {
    cy.get(m.sel.modalForm).should("not.be.visible");
    // Vérification de la modale d'ajout
    if (Cypress.$(layout.main + " " + m.sel.btnAdd).length) {
      // Ouverture de la modale
      cy.get(layout.main + " " + m.sel.btnAdd)
        .should("be.visible")
        .should("have.attr", "type", "button")
        .click();
      cy.get(m.sel.modalForm).should("be.visible");
      // Vérification du formulaire
      cy.get(m.sel.form).should("have.attr", "method").should("equal", "POST");
      cy.get(m.sel.form)
        .should("have.attr", "action")
        .should("equal", urlM.api.index);
      // Vérification des champs du formulaire
      specs.checkFormFields(m);
      // Si le modèle a des propriétés requises,
      if (m.props.filter((p) => p.required).length) {
        cy.get(m.sel.form + " button[type=submit]")
          .click()
          .wait(delay.fade);
        // rien ne doit se passer à la soumission du formulaire
        cy.get(m.sel.modalForm).should("be.visible");
      }
      // Fermeture de la modale
      cy.get(m.sel.modalForm + " " + sel.modal.btnClose)
        .click()
        .wait(delay.fade);
      cy.get(m.sel.modalForm).should("not.be.visible");
    }
    // Vérification de la modale de modification
    cy.get(m.sel.table)
      .find(m.sel.btnEdit)
      .its("length")
      .then((elCount) => {
        const selectedIdx = Cypress._.random(elCount - 1);
        // Vérification du bouton
        cy.get(m.sel.btnEdit)
          .eq(selectedIdx)
          .should("have.attr", "data-id")
          .then((id) => {
            // Ouverture de la modale
            cy.get(m.sel.btnEdit)
              .eq(selectedIdx)
              .click({ force: true })
              .wait(delay.fade);
            cy.get(m.sel.modalForm).should("be.visible");
            // Vérification du formulaire
            cy.get(m.sel.form)
              .should("have.attr", "method")
              .should("equal", "PUT");
            cy.get(m.sel.form)
              .should("have.attr", "action")
              .should("equal", urlM.api.index + "/" + id);
            // Vérification des champs du formulaire
            specs.checkFormFields(m, true);
            // Fermeture de la modale
            cy.get(m.sel.modalForm + " " + sel.modal.btnClose)
              .click()
              .wait(delay.fade);
            cy.get(m.sel.modalForm).should("not.be.visible");
          });
      });
  },

  /**
   * Vérifie les champs d'un formulaire.
   *
   * @param {{sel: {form: string}, props: {name: string, tag: string,
   *    required?: boolean, value?: any}[]}} m Modèle du formulaire.
   * @param {boolean} [checkRequiredFilled] S'il faut vérifier que les champs requis soient pré-remplis.
   * @param {boolean} checkValues S'il faut vérifier les valeurs des champs.
   */
  checkFormFields: (m, checkRequiredFilled = false, checkValues = false) => {
    for (const p of m.props) {
      if (!p.tag) {
        continue;
      }
      cy.get(m.sel.form)
        .find(
          `${p.tag}[name=${p.name}]` +
            (p.required ? "[required]" : ":not([required])") +
            (p.tag.indexOf("[type=radio]") >= 0 ? `[value="${p.value}"]` : ""),
        )
        .then(($field) => {
          // Les champs requis doivent être pré-remplis
          if (checkRequiredFilled && p.required) {
            expect($field.val()).not.to.be.empty;
          }
          if (checkValues) {
            // FIXME Problèmes de vérification des caractères spéciaux,
            // surtout avec htmlspecialchars()
            expect($field.val().toLowerCase()).to.eq(
              p.value.toString().toLowerCase().replaceAll("\\'", "'"),
            );
          }
        });
    }
  },

  /**
   * Ouvre la modale du formulaire d'édition d'un élément d'un tableau
   * à partir d'une chaîne de caractère (label) que la ligne du tableau doit contenir
   * et vérifie les valeurs de ses champs.
   *
   * @param {{sel: {btnEdit: string, form: string, modalForm: string, table: string},
   *    props: {name: string, tag: string, required?: boolean, value: any}[]}} m Modèle de la table et du formulaire.
   * @param {string} label Libellé à retrouver dans le tableau.
   */
  openFormModalAndCheckFields: (m, label = "") => {
    if (!label) {
      cy.get(m.sel.btnEdit).click().wait(delay.fade);
    } else {
      // Récupération de l'élément
      if (Cypress.$(m.sel.table).hasClass(dataTables.class)) {
        cy.get(dataTables.sel.search).clear({ force: true }).type(label);
      }
      cy.get(m.sel.table)
        .contains("tr", label, { matchCase: false })
        .find(m.sel.btnEdit)
        .click()
        .wait(delay.fade);
    }
    // Vérification des valeurs du formulaire
    specs.checkFormFields(m, true, true);
    // Fermeture de la modale
    cy.get(m.sel.modalForm)
      .find(sel.modal.btnClose)
      .should("be.visible")
      .click()
      .wait(delay.fade);
    cy.get(m.sel.modalForm).should("not.be.visible");
    if (label) {
      // Stocke l'id de l'élément dans le modèle
      cy.get(m.sel.table)
        .contains("tr", label, { matchCase: false })
        .find(m.sel.btnEdit)
        .then(($btn) => {
          m.props.push({ name: "id", value: $btn.data("id") });
        });
    }
  },

  /**
   * Vérifie la modale de confirmation de suppression d'un élément.
   *
   * @param {{sel: {btnDelete: string, btnConfirmDelete: string, modalConfirmDelete: string,
   *    table: string}}} m Modèle de l'élément à supprimer (cf. constants.js).
   * @param m.sel.btnDelete Sélecteur du bouton de suppression d'un élément.
   * @param m.sel.btnConfirmDelete Sélecteur du bouton de confirmation de suppression d'un élément.
   * @param m.sel.modalConfirmDelete Sélecteur de la modale de confirmation de suppression d'un élément.
   * @param m.sel.table Sélecteur de la table.
   * @param {{delete: string}} urlM URL du modèle.
   */
  checkModalConfirmDelete: (m, urlM) => {
    cy.get(m.sel.modalConfirmDelete).should("not.be.visible");
    cy.get(m.sel.table)
      .find(m.sel.btnDelete)
      .its("length")
      .then((elCount) => {
        const selectedIdx = Cypress._.random(elCount - 1);
        cy.get(m.sel.btnDelete)
          .eq(selectedIdx)
          .should("have.attr", "data-id")
          .then((id) => {
            cy.get(m.sel.btnDelete)
              .eq(selectedIdx)
              .click({ force: true })
              .wait(delay.fade);
            cy.get(m.sel.modalConfirmDelete)
              .should("be.visible")
              .find(m.sel.btnConfirmDelete)
              .should("have.attr", "href")
              .should("equal", urlM.delete.replace("{id}", id));
            cy.get(m.sel.modalConfirmDelete + " [data-test=cancel]")
              .click()
              .wait(delay.fade);
            cy.get(m.sel.modalConfirmDelete).should("not.be.visible");
          });

        cy.get(m.sel.btnDelete)
          .eq(Cypress._.random(elCount - 1))
          .click({ force: true })
          .wait(delay.fade);
        cy.get(m.sel.modalConfirmDelete + " " + sel.modal.btnClose)
          .click()
          .wait(delay.fade);
        cy.get(m.sel.modalConfirmDelete).should("not.be.visible");
      });
  },

  /**
   * Ajoute un nouvel élément à un modèle.
   *
   * @param {{sel: {btnAdd?: string, modalForm?: string, table: string},
   *    txt?: {addOk?: string}}} m Modèle de l'élément à ajouter (cf. constants.js).
   * @param m.sel.btnAdd Sélecteur du bouton d'ajout d'un élément.
   * @param m.sel.modalForm Sélecteur de la modale d'édition d'un élément.
   * @param m.sel.table Sélecteur de la table.
   * @param m.txt.addOk Texte du toast de succès d'ajout.
   * @param {integer} labelPropIdx Index de la propriété du libellé du modèle.
   */
  addItem: (m, labelPropIdx) => {
    if (!m.props?.length) {
      cy.log("Ce modèle ne contient pas de propriétés.");
      return;
    }
    cy.get(layout.main + " " + m.sel.btnAdd)
      .click()
      .wait(delay.fade);
    // Alimentation et soumission du formulaire
    fillForm(m.sel.form, m.props);
    cy.get(m.sel.form).find("button[type=submit]").click().wait(delay.loadPage);
    // Vérification du toast de succès
    cy.get(sel.toast.success)
      .should("contain", m.txt?.addOk || txt.addOk)
      .find(sel.toast.btnClose)
      .click();
    cy.get(sel.toast.success + ".show").should("not.exist");
    // Vérification de l'élément ajouté et renvoi de l'id
    specs.openFormModalAndCheckFields(m, m.props[labelPropIdx].value);
  },

  /**
   * Modifie un élément d'un modèle.
   *
   * @param {{sel: {btnEdit?: string, modalForm?: string, table: string},
   *    txt?: {editOk?: string}}} m Modèle de l'élément à modifier (cf. constants.js).
   * @param m.sel.btnEdit Sélecteur du bouton de modification d'un élément.
   * @param m.sel.modalForm Sélecteur de la modale d'édition d'un élément.
   * @param m.sel.table Sélecteur de la table.
   * @param m.txt.editOk Texte du toast de succès de modification.
   * @param {integer} labelPropIdx Index de la propriété du libellé du modèle.
   * @param {boolean} findItemInTable S'il faut rechercher l'élément à modifier dans le tableau
   * afin de savoir s'il faut identifier le bouton d'ouverture de la modale de modification.
   */
  editItem: (m, labelPropIdx, findItemInTable = true) => {
    const label = m.props[labelPropIdx].value;
    if (findItemInTable) {
      // Récupération de l'élément de test ajouté
      if (Cypress.$(m.sel.table).hasClass(dataTables.class)) {
        cy.get(dataTables.sel.search).clear({ force: true }).type(label);
      }
      cy.get(m.sel.table)
        .contains("tr", label, { matchCase: false })
        .find(m.sel.btnEdit)
        .click()
        .wait(delay.fade);
    } else {
      cy.get(m.sel.btnEdit).click().wait(delay.fade);
    }
    // Alimentation du formulaire avec des valeurs aléatoires
    m.props = fillForm(m.sel.form, m.props);
    // Renommage du libellé du modèle (pour le retrouver facilement)
    m.props[labelPropIdx].value = label + " EDITED";
    cy.get(m.sel.modalForm)
      .find(`[name=${m.props[labelPropIdx].name}]`)
      .clear({ force: true })
      .type(m.props[labelPropIdx].value);
    // Soumission du formulaire modifié
    cy.get(m.sel.modalForm)
      .find("button[type=submit]")
      .click()
      .wait(delay.fade);
    // Vérification du toast de succès
    cy.get(sel.toast.success)
      .should("contain", m.txt?.editOk || txt.editOk)
      .find(sel.toast.btnClose)
      .click();
    cy.get(sel.toast.success + ".show").should("not.exist");
    // Vérification de l'élément modifié
    specs.openFormModalAndCheckFields(
      m,
      findItemInTable ? m.props[labelPropIdx].value : "",
    );
  },

  /**
   * Supprime un élément d'un modèle.
   *
   * @param {{sel: {btnDelete: string, btnConfirmDelete: string, modalConfirmDelete: string, table: string},
   *    txt?: {deleteOk?: string}}} m Modèle de l'élément à supprimer (cf. constants.js).
   * @param m.sel.btnDelete Sélecteur du bouton de suppression d'un élément.
   * @param m.sel.btnConfirmDelete Sélecteur du bouton de confirmation de suppression d'un élément.
   * @param m.sel.modalConfirmDelete Sélecteur de la modale de confirmation de suppression d'un élément.
   * @param m.sel.table Sélecteur de la table.
   * @param m.txt.deleteOk Texte du toast du succès de la suppression.
   * @param {string} label Libellé de l'élément de test.
   * @param {{deleteRegex: RegExp, index: string}} urlM URL du modèle.
   * @param {boolean} findItemInTable S'il faut rechercher l'élément à modifier dans le tableau
   * afin de savoir s'il faut identifier le bouton d'ouverture de la modale de modification.
   */
  deleteItem: (m, label, urlM, findItemInTable = true) => {
    if (findItemInTable) {
      // Récupération de l'élément de test
      if (Cypress.$(m.sel.table).hasClass(dataTables.class)) {
        cy.get(dataTables.sel.search).clear({ force: true }).type(label);
      }
      // Ouverture de la modale de confirmation
      cy.get(m.sel.table)
        .contains("tr", label, { matchCase: false })
        .find(m.sel.btnDelete)
        .click()
        .wait(delay.fade);
    } else {
      cy.get(m.sel.btnDelete).click().wait(delay.fade);
    }
    // Vérification de l'URL
    cy.get(m.sel.modalConfirmDelete)
      .should("be.visible")
      .find(m.sel.btnConfirmDelete)
      .should("have.attr", "href")
      .and("match", urlM.deleteRegex);
    //
    cy.get(m.sel.modalConfirmDelete)
      .find(m.sel.btnConfirmDelete)
      .click()
      .wait(delay.loadPage);
    cy.location("href").should("equal", urlM.index);
    // Vérification du toast de succès
    cy.get(sel.toast.success)
      .should("contain", m.txt?.deleteOk || txt.deleteOk)
      .find(sel.toast.btnClose)
      .click();
    cy.get(sel.toast.success + ".show").should("not.exist");
    if (findItemInTable) {
      cy.get(m.sel.table).should("not.include.text", label);
    }
  },

  /**
   * Désactive un élément d'un modèle.
   *
   * @param {{sel: {btnDeactivate: string, table: string},
   *    txt?: {deactivateOk?: string}}} m Modèle de l'élément à désactiver (cf. constants.js).
   * @param m.sel.btnDeactivate Sélecteur du bouton de désactivation d'un élément.
   * @param m.sel.table Sélecteur de la table.
   * @param m.txt.deactivateOk Texte du toast du succès de la désactivation.
   * @param {string} label Libellé de l'élément de test.
   * @param {{deactivateRegex: RegExp, index: string}} urlM URL du modèle.
   * @param {boolean} findItemInTable S'il faut rechercher l'élément à modifier dans le tableau
   * afin de savoir s'il faut identifier le bouton d'ouverture de la modale de modification.
   */
  deactivateItem: (m, label, urlM, findItemInTable = true) => {
    if (findItemInTable) {
      // Récupération de l'élément de test
      if (Cypress.$(m.sel.table).hasClass(dataTables.class)) {
        cy.get(dataTables.sel.search).clear({ force: true }).type(label);
      }
      cy.get(m.sel.table)
        .contains("tr", label, { matchCase: false })
        .find(m.sel.btnDeactivate)
        .click()
        .wait(delay.loadPage);
    } else {
      cy.get(m.sel.btnDeactivate).click().wait(delay.loadPage);
    }
    cy.location("href").should("equal", urlM.index);
    // Vérification du toast de succès
    cy.get(sel.toast.success)
      .should("contain", m.txt?.deactivateOk || txt.deactivateOk)
      .find(sel.toast.btnClose)
      .click()
      .wait(delay.fade);
    cy.get(sel.toast.success + ".show").should("not.exist");
    // Vérification que l'élément soit désactivé
    if (Cypress.$(m.sel.table).hasClass(dataTables.class)) {
      cy.get(dataTables.sel.search).clear({ force: true }).type(label);
    }
    if (findItemInTable) {
      cy.get(m.sel.table)
        .contains("tr", label, { matchCase: false })
        .should("have.class", dataTables.sel.deactivatedRowClasses);
      cy.get(dataTables.sel.search).clear({ force: true });
    }
  },

  /**
   * Réactive un élément d'un modèle.
   *
   * @param {{sel: {btnReactivate: string, table: string},
   *    txt?: {reactivateOk?: string}}} m Modèle de l'élément à réactiver (cf. constants.js).
   * @param m.sel.btnReactivate Sélecteur du bouton de réactivation d'un élément.
   * @param m.sel.table Sélecteur de la table.
   * @param m.txt.reactivateOk Texte du toast du succès de la réactivation.
   * @param {string} label Libellé de l'élément de test.
   * @param {{deactivateRegex: RegExp, index: string}} urlM URL du modèle.
   * @param {boolean} findItemInTable S'il faut rechercher l'élément à modifier dans le tableau
   * afin de savoir s'il faut identifier le bouton d'ouverture de la modale de modification.
   */
  reactivateItem: (m, label, urlM, findItemInTable = true) => {
    if (findItemInTable) {
      // Récupération de l'élément de test
      if (Cypress.$(m.sel.table).hasClass(dataTables.class)) {
        cy.get(dataTables.sel.search).clear({ force: true }).type(label);
      }
      cy.get(m.sel.table)
        .contains("tr", label, { matchCase: false })
        .find(m.sel.btnReactivate)
        .click()
        .wait(delay.loadPage);
    } else {
      cy.get(m.sel.btnReactivate).click().wait(delay.loadPage);
    }
    cy.location("href").should("equal", urlM.index);
    // Vérification du toast de succès
    cy.get(sel.toast.success)
      .should("contain", m.txt?.reactivateOk || txt.reactivateOk)
      .find(sel.toast.btnClose)
      .click()
      .wait(delay.fade);
    cy.get(sel.toast.success + ".show").should("not.exist");
    // Vérification que l'élément soit réactivé
    if (Cypress.$(m.sel.table).hasClass(dataTables.class)) {
      cy.get(dataTables.sel.search).clear({ force: true }).type(label);
    }
    if (findItemInTable) {
      cy.get(m.sel.table)
        .contains("tr", label, { matchCase: false })
        .should("not.have.class", dataTables.sel.deactivatedRowClasses);
      cy.get(dataTables.sel.search).clear({ force: true });
    }
  },
};
