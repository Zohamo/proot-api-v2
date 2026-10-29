import { delay, layout, url } from "../constants";
import { specs } from "../specs";

describe("Navigation", () => {
  /**
   * Visite et vérifie des pages.
   *
   * @param {{name: string, linkName: string, showBreadcrumbs?: boolean,
   *  url: string}} pages
   */
  const visitAndCheckPages = (pages) => {
    for (const page of pages) {
      cy.log(page.name);
      cy.visit(page.url).wait(delay.loadPage);
      specs.firstCheck(page.linkName, page.showBreadcrumbs);
    }
  };

  it("Page d'accueil", () => {
    cy.visit(Cypress.config("baseUrl"));
    specs.firstCheck("accueil", false);
  });

  it("Pages d'administration", () => {
    cy.visit(Cypress.config("baseUrl"));
    cy.get(`${layout.header} [data-test=user-role]`).then(($userRole) => {
      if ($userRole.text().indexOf("Administrateur") > -1) {
        visitAndCheckPages([
          {
            name: "Gestion des utilisateurs",
            linkName: "utilisateurs",
            url: url.admin.utilisateurs.index,
          },
          {
            name: "Détail utilisateur",
            linkName: "utilisateurs",
            url: url.admin.utilisateurs.detail.replace("{id}", 4),
          },
        ]);
      }
    });
  });
});
