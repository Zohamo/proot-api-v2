import { delay, layout, url, user } from "../../constants";
import { specs } from "../../specs";

describe("Liste des utilisateurs", () => {
  const model = user;
  const urlM = url.admin.users;

  beforeEach(() => {
    cy.visit(urlM.index);
  });

  it("Structure", () => {
    specs.firstCheck("users");
  });

  it("Vérification du tableau et des boutons action", () => {
    specs.checkTable(model, urlM);
  });

  it("Vérification de la modale avec la liste des rôles", () => {
    cy.get(user.sel.modalListRoles).should("not.be.visible");
    cy.get(`${layout.main} ${user.sel.btnListRoles}`)
      .should("be.visible")
      .should("have.attr", "type", "button")
      .click();
    cy.wait(delay.fade)
      .get(user.sel.modalListRoles)
      .should("be.visible")
      .find("table tbody tr")
      .should("have.length", user.nbRoles);
    cy.get(
      user.sel.modalListRoles + " .modal-footer [data-test=close]",
    ).click();
    cy.wait(delay.fade).get(user.sel.modalListRoles).should("not.be.visible");
  });
});
