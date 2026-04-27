const echo = (msg) => {
  console.log(msg);
};

document.find = (selector) => {
  return document.querySelector(selector);
};

document.find_all = (selector) => {
  return document.querySelectorAll(selector);
};

/**
 * Find an element in HTML DOM by class, attribute, id, … .
 * @param {string} selector
 * @returns
 */
Element.prototype.find = function (selector) {
  return this.querySelector(selector);
};

Element.prototype.find_all = function (selector) {
  return this.querySelectorAll(selector);
};

Element.prototype.hide = function (selector) {
  return this.setAttribute("hidden", "");
};

Element.prototype.unhide = function (selector) {
  return this.removeAttribute("hidden");
};

Element.prototype.disable = function (selector) {
  return this.setAttribute("disabled", "");
};

Element.prototype.enable = function (selector) {
  return this.removeAttribute("disabled");
};

Element.prototype.activate = function (selector) {
  return this.setAttribute("active", true);
};

Element.prototype.deactivate = function (selector) {
  return this.removeAttribute("active");
};

Element.prototype.done = function (selector) {
  return this.setAttribute("done", true);
};

Element.prototype.undone = function (selector) {
  return this.removeAttribute("done");
};

Element.prototype.active = function (selector) {
  return this.hasAttribute("active");
};

Element.prototype.fade_out = function (selector) {
  return this.setAttribute("invisible", "");
};
