import * as Frontend from "./frontend";

export default class Overlay {
  constructor(append = null, close = true) {
    let overlay;
    let loader;

    /**
     * Adds a class of the string to the overlay.
     */
    let attribute = !append ? "page" : "container";

    /**
     * Where to append the overlay. If no append is set, it will
     * append it to the body by default.
     */
    let append_to = append ? append : document.body;

    /**
     * Remove scrolling bars from the body by appending the
     * attribute toggled with a value of true.
     */
    if (!append) document.body.setAttribute("toggled", true);

    /**
     * If to close, add a close element to the overlay children.
     */
    let closer = close ? `<div o-closer></div>` : "";

    /**
     * Prepare the overlay and append it to the supposed appender.
     */
    overlay = document.createElement("overlay");
    overlay.setAttribute(attribute, "");
    overlay.insertAdjacentHTML(
      "beforeend",
      `
      <style>
        .path-loader[visible="true"] {
          visibility:visible;
          opacity:1;
          z-index:9999999999;
        }
      </style>
      <loader class="path-loader" style=height:4.8em;width:4.8em;>
        <svg class="circular" viewBox="25 25 50 50">
          <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="4 " stroke-miterlimit="10" />
        </svg>
      </loader>
      ${closer}
    `,
    );

    /**
     * Add the overlay as a child element to the append_to element.
     */
    append_to.appendChild(overlay);

    /**
     * Save the loader into a variable.
     */
    loader = append_to.find("loader");

    /**
     * Make the overlay visible after 10ms, so it fades in. Same
     * for the loader.
     */
    setTimeout(() => {
      overlay.setAttribute("visible", true);
      loader.setAttribute("visible", true);
    }, 10);

    this.overlay = overlay;
    this.loader = loader;
    this.is_second_overlay = __current_overlay ? true : false;

    if (!this.is_second_overlay) __current_overlay = this;
    else __current_second_overlay = this;
  }

  /**
   * Hides the loader.
   */
  hide_loader() {
    setTimeout(() => {
      this.loader.setAttribute("visible", "false");
    }, 20);
  }

  /**
   * Appends the given data (HTML) with a delay to the overlay
   * without deleting the closer and automatically reloads the
   * frontends images.
   */
  append(data) {
    this.hide_loader();
    this.overlay.insertAdjacentHTML("afterbegin", data);
    Frontend.reload_images();
  }

  /**
   * Remove the overlay.
   */
  delete() {
    this.overlay.setAttribute("visible", false);

    setTimeout(() => {
      this.overlay.remove();
      if (document.find_all("overlay").length < 1)
        document.body.setAttribute("toggled", false);
    }, 100);

    if (this.is_second_overlay) __current_second_overlay = null;
    else __current_overlay = null;
  }
}

/**
 * Toggles the visibility of a given overlay
 */
export const toggle = (overlay) => {
  overlay.setAttribute("visible", false);

  setTimeout(() => {
    overlay.remove();

    if (document.body.querySelectorAll("overlay[visible=true]").length < 1)
      document.body.setAttribute("toggled", false);
  }, 400);
};
