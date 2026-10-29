import { delay, url, user } from "../../constants";
import { specs } from "../../specs";

describe("Ajout et vérification de la page de détail d'un utilisateur", () => {
  const model = user;
  const urlM = url.admin.users;
  let detailPageUrl;
  // Index du libellé dans les propriétés du modèle
  const labelPropIdx = model.props.map((p) => p.name).indexOf("usr_lastname");
  const testLabel = model.props[labelPropIdx].value;

  it("Nettoyage des tests précédents", () => {
    cy.visit(urlM.index).wait(delay.loadPage);
    specs.cleanTests(model, testLabel, urlM);
  });

  it("Ajout d'un nouvel utilisateur", () => {
    cy.visit(urlM.index).wait(delay.loadPage);
    specs.addItem(model, labelPropIdx);
  });

  it("Vérification des informations de l'utilisateur", () => {
    detailPageUrl = urlM.detail.replace(
      "{id}",
      model.props.find((p) => p.name === "id").value,
    );
    cy.visit(detailPageUrl).wait(delay.loadPage);
    for (const p of model.props.filter((p) => p.tag && p.tag !== "select")) {
      cy.get(model.sel.detail).contains(
        `[data-test="${p.name}"]`,
        p.value.toString().toLowerCase().replaceAll("\\'", "'"),
        { matchCase: false },
      );
    }
  });

  it("Modification de l'utilisateur", () => {
    cy.visit(detailPageUrl).wait(delay.loadPage);
    specs.editItem(model, labelPropIdx, false);
  });

  it("Désactivation d'un utilisateur", () => {
    cy.visit(detailPageUrl).wait(delay.loadPage);
    specs.deactivateItem(model, testLabel, urlM, false);
  });

  it("Réactivation d'un utilisateur", () => {
    cy.visit(detailPageUrl).wait(delay.loadPage);
    specs.reactivateItem(model, testLabel, urlM, false);
  });

  it("Désactivation d'un utilisateur", () => {
    cy.visit(detailPageUrl).wait(delay.loadPage);
    specs.deactivateItem(model, testLabel, urlM, false);
  });

  it("Suppression d'un utilisateur", () => {
    cy.visit(detailPageUrl).wait(delay.loadPage);
    specs.deleteItem(model, testLabel, urlM, false);
  });
});
