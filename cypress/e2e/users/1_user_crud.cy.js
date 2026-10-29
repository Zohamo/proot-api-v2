import { delay, layout, url, user } from "../../constants";
import { fillForm } from "../../functions";
import { specs } from "../../specs";

describe("Ajout, modification et suppression d'un utilisateur", () => {
  const model = user;
  const urlM = url.admin.users;
  // Index du libellé dans les propriétés du modèle
  const labelPropIdx = model.props.map((p) => p.name).indexOf("usr_lastname");
  const testLabel = model.props[labelPropIdx].value;
  // Sélecteurs
  const selForm = model.sel.form;
  const selModalForm = model.sel.modalForm;
  const selCompteLDAP = selForm + " input#usr_email";
  const formErrorsContainer = selModalForm + " .form-errors-container";

  beforeEach(() => {
    cy.visit(urlM.index).wait(delay.loadPage);
  });

  it("Structure", () => {
    specs.firstCheck("users");
  });

  it("Nettoyage des tests précédents", () => {
    specs.cleanTests(model, testLabel, urlM);
  });

  it("Gestion des erreurs dans le formulaire", () => {
    cy.get(layout.main + " " + model.sel.btnAdd)
      .click()
      .wait(delay.fade);
    // Alimentation du formulaire
    fillForm(model.sel.form, model.props);
    // Insertion d'erreurs
    for (const invalidVal of model.props.find((p) => p.name === "usr_email")
      .invalidValues) {
      cy.get(selCompteLDAP).clear().type(invalidVal);
      cy.get(selForm).submit().wait(delay.loadPage);
      cy.get(formErrorsContainer)
        .should("be.visible")
        .and("contain", "Une erreur est survenue");
      cy.get(formErrorsContainer + " .btn-close")
        .click()
        .wait(delay.fade);
    }
  });

  it("Ajout d'un nouvel utilisateur", () => {
    specs.addItem(model, labelPropIdx);
  });

  it("Modification d'un utilisateur", () => {
    specs.editItem(model, labelPropIdx);
  });

  it("Désactivation d'un utilisateur", () => {
    specs.deactivateItem(model, testLabel, urlM);
  });

  it("Réactivation d'un utilisateur", () => {
    specs.reactivateItem(model, testLabel, urlM);
  });

  it("Désactivation d'un utilisateur", () => {
    specs.deactivateItem(model, testLabel, urlM);
  });

  it("Suppression d'un utilisateur", () => {
    specs.deleteItem(model, testLabel, urlM);
  });
});
