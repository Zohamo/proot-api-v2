import { delay, pageSearch, sel, url, modalMainSearch } from "../constants";
import { specs } from "../specs";

describe("Recherche", () => {
  const urlM = url.search;

  it("navbar : recherche de 'cu'", () => {
    cy.visit(url.index);
    cy.get(modalMainSearch.sel.btnOpenModal)
      .should("be.visible")
      .click()
      .wait(delay.fade);
    cy.get(modalMainSearch.sel.input).type("cu{enter}");
    cy.location("href").should("equal", urlM.index);
    cy.get(sel.toast.error).should(
      "contain",
      "Le sujet recherché doit avoir au minimum 3 caractères",
    );
    cy.get("main .search-resultats").should(
      "contain",
      "Aucun résultat n'a été trouvé",
    );
  });

  it(`recherche de '${pageSearch.testInput}'`, () => {
    cy.visit(url.index);
    cy.get(modalMainSearch.sel.btnOpenModal)
      .should("be.visible")
      .click()
      .wait(delay.fade);
    cy.get(modalMainSearch.sel.input).type(pageSearch.testInput);
    cy.get(modalMainSearch.sel.form)
      .find("button[type=submit]")
      .click()
      .wait(delay.loadPage);
    cy.location("href").should("equal", urlM.index);
    specs.firstCheck();
    cy.get(pageSearch.sel.results)
      .should("be.visible")
      .find("table tbody tr")
      .its("length")
      .should("least", 1);
  });

  it(`recherche l'introuvable : '${pageSearch.testInputUnknown}'`, () => {
    cy.visit(urlM.index);
    specs.firstCheck();
    cy.get(pageSearch.sel.results).should("not.exist");
    cy.get(pageSearch.sel.input).type(pageSearch.testInputUnknown + "{enter}");
    cy.get(pageSearch.sel.input).should(
      "have.value",
      pageSearch.testInputUnknown,
    );
    cy.get(pageSearch.sel.results).should(
      "contain",
      "Aucun résultat n'a été trouvé",
    );
  });
});
