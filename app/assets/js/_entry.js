// * Remove this import if you don't feel like using the SPA style
// * page loading capabilities of Bruder!
import * as Bruder from "./framework";
// * ……………………………………………

/**
 * Makes Bruder available in the DOM.
 * window.Bruder = Bruder;
 */

import * as Element from "./elements";
import * as Page from "./pages";

/**
 * SCSS will be processed through the node develeopment serveer,
 * so we need to import the entry file here.
 */
import "../scss/application.scss";
