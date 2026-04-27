import * as Responder from "./responder";

/**
 * Sets the frontend to be loading.
 */
export const load = async () => {
  __page.is_loading = true;

  let overlay = document.find("page-loader");

  if (overlay) overlay.setAttribute("visible", true);
};

/**
 * Unsets the loading state of the frontend.
 */
export const unload = async () => {
  __page.is_loading = false;

  let overlay = document.find("page-loader");

  setTimeout(() => {
    if (overlay) overlay.setAttribute("visible", false);
  }, 10);
};

/**
 * Scrolls to the top!
 */
export const scroll_to_top = async () => {
  window.scrollTo({ top: 0, behavior: "smooth" });
};

/**
 * Scrolls to a certain place!
 * @param {int} number
 */
export const scroll_to = async (number) => {
  window.scrollTo({ top: number, behavior: "smooth" });
};

/**
 * Preloads a list of <img> elements by creating new Image
 * instances and marks them with a [loaded] tag so they will fade in.
 *
 * @param {HTMLImageElement[]} arr - Array of <img> elements to load
 * @returns {void}
 */
export const load_images = (arr) => {
  arr.forEach((img) => {
    if (img.hasAttribute("loaded")) return;

    let new_img = new Image();
    new_img.src = img.src;

    if (new_img.complete) {
      img.setAttribute("loaded", true);

      return;
    }

    img.onerror = () => {
      img.onerror = "";

      return;
    };

    img.onload = () => {
      img.setAttribute("loaded", true);

      return;
    };
  });
};

/**
 * Reloads all images and marks them again so new ones fade in.
 */
export const reload_images = () => load_images(document.find_all("img"));

/**
 * Creates a new responder with default values set.
 * Accepts either a message string or an object with `message` and `status`.
 *
 * @param {string|{message?: string, status?: boolean}} message - The message or config object
 * @param {string} status - Status string ("error", "success", etc.)
 * @param {HTMLElement} append_to - Element to append the responder to
 * @returns {void}
 */
export const create_responder = (
  message,
  status = "error",
  append_to = document.body,
) => {
  if (
    typeof message === "object" &&
    message !== null &&
    !Array.isArray(message)
  )
    new Responder.Responder().add(
      append_to,
      message?.message ?? "No message",
      message?.status ? "success" : "error",
    );
  else new Responder.Responder().add(append_to, message, status);
};

/**
 * Closes all open overlays.
 */
export const close_overlays = () => {
  let overlays = document.querySelectorAll("overlay");

  close_exception_overlay();

  if (!overlays) return;

  overlays.forEach((overlay) => {
    overlay.removeAttribute("visible");

    setTimeout(() => {
      overlay.remove();

      if (
        document.body.hasAttribute("toggled") &&
        document.body.getAttribute("toggled") == "true"
      )
        document.body.setAttribute("toggled", "false");
    }, 400);
  });

  __current_overlay = null;
  __current_second_overlay = null;
};

export const close_exception_overlay = () => {
  document.find("exception-container")?.remove();
};

/**
 * Extract an exception from the incoming reponse either as text
 * or HTML already.
 *
 * @param {string|HTMLElement} from
 * @returns {void}
 */
export const extract_exception = (from) => {
  if (
    from &&
    !(from instanceof Element) &&
    from.includes("exception-container")
  )
    document.body.insertAdjacentHTML("beforeend", from);
  else if (from && !(from instanceof Element)) return;

  let exception = document.find("exception-container");

  if (exception) {
    exception.remove();
    document.body.appendChild(exception);
  }
};

/**
 * Handles AJAX errors by cleaning up UI and displaying an error message.
 *
 * - Unloads any loaders
 * - Re-enables all submit buttons
 * - Extracts the exception message
 * - Shows a responder with the error status
 *
 * @param {{ responseText: string, statusText: string }} error
 * @returns {void}
 */
export const ajax_error = (error) => {
  unload();

  /**
   * Activate all submit buttons, so the user can try again.
   */
  let buttons = document.find_all("[submit-closest]");
  if (buttons)
    buttons.forEach((button) => {
      button.enable();
    });

  extract_exception(error.responseText);
  create_responder(error.statusText, "error");
};

/**
 * Animates the ajax response container based on the return of requests.
 *
 * @param {string} type
 */
export const ajax_response = (type = "success") => {
  let container = document.find("ajax-response");

  container.setAttribute(type, true);
  container.activate();

  container.addEventListener("animationend", function (e) {
    container.removeAttribute(type);
    container.deactivate();
  });
};

/**
 * Finds all <get-content></get-content> elements and loads the
 * content from the specified from attribute dynamically.
 */
export const get_content = () => {
  let get_contents = document.find_all("get-content");
  let from;

  // return;

  get_contents.forEach((getc) => {
    from = getc.getAttribute("from");

    if (from)
      $.ajax({
        url: from,
        success: function (data) {
          getc.insertAdjacentHTML("afterend", data?.data ?? data);
          getc.remove();

          reload_images();

          $.globalEval($(getc).find("script").text());
        },
      });
  });
};

$(function () {
  get_content();

  /**
   * Close all dangling overlays on click.
   */
  $(document).on("click", "[o-closer], [close-overlay]", function () {
    close_exception_overlay();
    close_overlays();
  });

  /**
   * * · Generalize keyboard shortcut event triggering ¬
   */
  document.addEventListener("keyup", (e) => {
    if (!e.key) return;

    if (e.key.toLowerCase() === "escape") {
      let has_overlays = document.find_all("overlay");

      if (has_overlays) close_overlays();

      return;
    }
  });
});
