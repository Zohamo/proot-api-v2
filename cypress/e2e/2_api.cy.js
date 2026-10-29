import { url, user } from "../constants";

describe("API", () => {
  it("API Utilisateur", () => {
    cy.request(url.admin.users.api.index).then((response) => {
      expect(response.body).to.have.length.least(user.minNbRows);
    });
  });
});
