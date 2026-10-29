/**
 * Supprime les sauts de ligne et les espaces (trim) d'une chaîne de caractères.
 *
 * @param {string} str
 * @returns {string}
 */
export const cleanString = (str) =>
  str.replaceAll("\n", "").trim().replaceAll("  ", " ");

/**
 * Transforme une devise en nombre.
 *
 * @param {string} currency Depuis html() pas text() sinon le texte est interprété et &nbsp; devient un espace insécable.
 * @returns {number}
 */
export const currencyToNumber = (currency) =>
  +currency
    .replaceAll('&nbsp;', "")
    .replace("€", "")
    .replace(",", ".");

/**
 * Remplit un formulaire avec les valeurs par défaut de ses propriétés ou aléatoires.
 *
 * @param {string} selForm Sélecteur du formulaire.
 * @param {{name: string, type: 'number'|'string', max?: number,
 * offset?: number, decimals?: number, length?: number,
 * specialCharacters: boolean}[]} properties Propriétés du modèle.
 * @param {boolean} [setRandomValues] S'il faut générer des valeurs aléatoires.
 * @returns {object} Propriétés du modèle.
 */
export const fillForm = (selForm, properties, setRandomValues = false) => {
  for (const prop of properties) {
    if (!prop.tag || !prop.name) {
      continue;
    }
    if (setRandomValues || prop.value === undefined) {
      prop.value = getPropRandomValue(prop);
    }
    const cyField = cy.get(`${selForm} [name=${prop.name}]`);
    switch (prop.tag) {
      case "input[type=color]":
        cyField.invoke("val", prop.value).trigger("change");
        break;
      case "input[type=radio]":
        cyField.filter(`[value=${prop.value}]`).check();
        break;
      case "select":
        cyField.select(prop.value);
        break;
      default:
        cyField.clear({ force: true }).type(prop.value);
    }
  }
  return properties;
};

/**
 * Renvoie une valeur aléatoire d'une propriété.
 *
 * @param {object} prop
 * @returns {string|number}
 */
export const getPropRandomValue = (prop) => {
  switch (prop.type) {
    case "string":
      return randomString(prop.length || 25, prop.specialCharacters || false);
    case "number":
      return randomNumber(prop.max || 10, prop.offset || 0, prop.decimals || 0);
    default:
      return null;
  }
};

/**
 * Renvoie le nombre d'entrées dans un tableau.
 *
 * @param {string} selTable
 * @returns {number}
 */
export const getNbRowsInTable = (selTable) => {
  const nbRows = Cypress.$(selTable + " tbody > tr").length;
  cy.log(
    nbRows
      ? `Il y a ${nbRows} entrées dans le tableau ${selTable}.`
      : `Il y n'a aucune entrée dans le tableau ${selTable}.`
  );
  return nbRows;
};

/**
 * Formate un nombre en devise.
 *
 * @param {number} number
 * @param {string} [space]
 * @returns {string}
 */
export const formatCurrency = (number, space = " ") =>
  new Intl.NumberFormat("fr-FR", {
    style: "currency",
    currency: "EUR",
  })
    .format(number)
    .replace(/\s/g, space);

/**
 * Formate une date au format JJ/MM/YYYY.
 *
 * @param {string} [date] Date au format YYYY-MM-JJ.
 * @returns {string} Date au format JJ/MM/YYYY.
 */
export const formatDate = (date = "") =>
  (date || new Date().toJSON().slice(0, 10)).split("-").reverse().join("/");

/**
 * Génère un nombre aléatoire.
 *
 * @param {number} [max]
 * @param {number} [offset]
 * @param {number} [decimals]
 * @returns {number}
 */
export const randomNumber = (max = 10, offset = 0, decimals = 0) =>
  (Math.random() * (max - offset) + offset).toFixed(decimals); // NOSONAR contexte non sensible

/**
 * Génère une chaîne de caractères aléatoire.
 *
 * @param {number} [length]
 * @param {boolean} [specialCharacters] S'il faut intégrer des caractères spéciaux.
 * @returns {string}
 * @see http://stevehardie.com/2009/09/character-code-list-char-code/
 */
export const randomString = (length = 25, specialCharacters = false) => {
  let str = "";
  for (let ii = 0; ii < length; ii++) {
    let charCode;
    if (specialCharacters) {
      charCode = randomNumber(122 + 30, 32);
      if (charCode > 122) {
        charCode = 32;
      }
    } else {
      do {
        charCode = randomNumber(122, 65);
      } while ((charCode > 90 && charCode < 97) || [42].includes(charCode));
    }
    str += String.fromCharCode(charCode);
  }
  return cleanString(str);
};
