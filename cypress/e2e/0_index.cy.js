import { delay, layout, modalMainSearch, url } from "../constants";
import { specs } from "../specs";

describe("Accueil", () => {
  const links = ["accueil"];
  const adminLinks = ["users"];
  const adminTechLinks = ["logs"];

  beforeEach(() => {
    cy.visit(url.index);
  });

  it("structure", () => {
    specs.firstCheck("accueil", false);
  });

  it("navbar > liens", () => {
    links.forEach((link) => {
      cy.get(
        `${layout.navbar} .nav-item .nav-link[data-test=link-${link}]`,
      ).should("be.visible");
    });
  });

  it("navbar > recherche", () => {
    cy.get(modalMainSearch.sel.modal).should("not.be.visible");
    cy.get(modalMainSearch.sel.btnOpenModal)
      .should("be.visible")
      .click()
      .wait(delay.fade);
    cy.get(modalMainSearch.sel.modal).should("be.visible");
  });

  it("navbar > profil", () => {
    cy.get(`${layout.header} [data-bs-target="#offcanvasDarkNavbar"]`)
      .click()
      .wait(delay.fade);
    cy.get("[data-test=nav-profil]")
      .should("be.visible")
      .then(($menu) => {
        ["name", "email", "role"].forEach((el) => {
          expect($menu.find(`[data-test=user-${el}]`)).to.be.visible;
        });
      });
  });

  it("navbar > administration", () => {
    cy.get(`${layout.header} [data-bs-target="#offcanvasDarkNavbar"]`)
      .click()
      .wait(delay.fade);
    cy.get(`${layout.header} [data-test=user-role]`).then(($userRole) => {
      if (
        $userRole.text().indexOf("Admin") === -1 &&
        $userRole.text().indexOf("Dev") === -1
      ) {
        cy.get(`${layout.navbar} [data-test=nav-administration]`).should(
          "not.exist",
        );
        cy.get(
          `${layout.navbar} [data-test=nav-administration-technique]`,
        ).should("not.exist");
      } else {
        cy.get(`${layout.navbar} [data-test=nav-administration]`).should(
          "be.visible",
        );
        adminLinks.forEach((link) => {
          cy.get(
            `${layout.navbar} [data-test=nav-administration] [data-test=link-${link}]`,
          ).should("be.visible");
        });
        if ($userRole.text().indexOf("Dev") > -1) {
          cy.get(
            `${layout.navbar} [data-test=nav-administration-technique]`,
          ).should("be.visible");
          adminTechLinks.forEach((link) => {
            cy.get(
              `${layout.navbar} [data-test=nav-administration-technique] [data-test=link-${link}]`,
            ).should("be.visible");
          });
        }
      }
    });
  });
});
