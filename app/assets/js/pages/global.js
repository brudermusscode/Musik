import * as Frontend from "../framework/frontend";
import * as Cookie from "../framework/cookie";
import * as Prototype from "../abstract/prototypes";

/**
 * Set some defaults for future ajax requests.
 */
$.ajaxSetup({
  contentType: false,
  processData: false,
  method: "GET",
  error: function (error_data) {
    Frontend.ajax_error(error_data);
  },
});

/**
 * Searches for all contextmenu replacements in the DOM and sets
 * their state to display as closed.
 */
export const bulk_close_contextmenu = () => {
  document.find_all("[has-menu] menu")?.forEach((menu) => {
    menu.removeAttribute("style");
    menu.find("inr").removeAttribute("style");
    menu.deactivate();
  });
};

export const open_bruder = () => {
  document.find("bruder").activate();
  // document.find("player").style.bottom = "42px";

  setTimeout(() => {
    document.find("bruder input")?.focus();
  }, 100);
};

export const close_bruder = () => {
  let bruder = document.find("bruder");
  let player = document.find("player");

  if (!bruder || !bruder.hasAttribute("active")) return;

  bruder.deactivate();
  player.removeAttribute("style");
};

/**
 * Ajax call to sync track album job to curl newly added albums to
 * currently only music brainz and possible album covers. For the
 * real spotify vibe.
 */
const execute_job = async (name) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `/job/${name}`,
      method: "POST",
      success: function (data) {
        console.log(data);

        resolve(1);
      },
    });
  });
};

/**
 * Fetches the updated library and exchanges it with the old HTML Object.
 */
export const update_library = () => {
  let library = document.find("sidebar library bookmarks");

  $.ajax({
    url: "/get/library",
    success: function (data) {
      if (data.status) {
        library.innerHTML = data.data;
        Frontend.reload_images();
      } else Frontend.ajax_response("error");
    },
  });
};

let __update_track_timeout;

/**
 *
 * @param {string} type Either playlist or album
 * @param {int} id The id of the Playlist or Album
 * @returns {Promise}
 */
export const update_current_track = async (
  relation_id = null,
  relation_type = null,
  init = false,
  use_cookies = false,
) => {
  return new Promise((resolve) => {
    clearTimeout(__update_track_timeout);

    let sidebar = document.find("sidebar [current-track]");
    relation_id = use_cookies
      ? Cookie.get("__player_Track_relation_id")
      : relation_id;
    relation_type = use_cookies
      ? Cookie.get("__player_Track_relation_type")
      : relation_type;

    /**
     * Build the url. When init = true, the page has been reloaded
     * or any other module, which is not a new playing track has
     * triggered the update, so I want to fetch info from the cookies.
     */
    let url =
      `/get/current-track` +
      (relation_id && relation_type
        ? `?id=${relation_id}&type=${relation_type}`
        : "");

    if (!sidebar.loading()) sidebar.append_loader(true);

    sidebar.load();

    __update_track_timeout = setTimeout(() => {
      $.ajax({
        url: url,
        success: function (data) {
          if (data.status) {
            sidebar.innerHTML = data.data;
            sidebar.unload();

            Frontend.reload_images();

            if (__player.fullscreen)
              document.find("current-track[has-video] video")?.pause();

            /**
             * Set __player attributes for the Relation.
             */
            // __player.Track.relation.id = relation_id;
            // __player.Track.relation.type = relation_type;

            /**
             * On site load up, resolve here already. No need for
             * setting any new cookies or removing them.
             */
            // if (init) return resolve(1);

            /**
             * Set cookies for persistence or delete them, if a
             * track was played outside of a list.
             */
            // if (relation_id && relation_type) {
            //   Cookie.set("__player_Track_relation_id", relation_id, 365);
            //   Cookie.set("__player_Track_relation_type", relation_type, 365);
            // } else {
            //   Cookie.remove("__player_Track_relation_id");
            //   Cookie.remove("__player_Track_relation_type");
            // }

            return resolve(1);
          }

          Frontend.ajax_response("error");
          return resolve(0);
        },
      });
    }, 0);
  });
};

// document.addEventListener("DOMContentLoaded", async (e) => {});

$(function () {
  $(document).on("click", "theme-switcher", function (e) {
    if (this.hasAttribute("active")) {
      this.deactivate();
      document.body.setAttribute("theme", "light");
      Cookie.set("__theme", "light", 365);
    } else {
      this.activate();
      document.body.setAttribute("theme", "dark");
      Cookie.set("__theme", "dark", 365);
    }
  });

  $(document).on("click", "library-view [view]", function (e) {
    let lib = this.closest("library");
    let view = this.getAttribute("view");

    lib.setAttribute("view", view);

    this.closest("library-view")
      .find_all("[view]")
      .forEach((elem) => {
        elem.deactivate();
      });

    this.activate();

    Cookie.set("__lib_view", view);
  });

  $(document).on("click", "menu option", function (e) {
    return bulk_close_contextmenu();
  });

  $(document).on("keyup", function (e) {
    let key = e.key.toLowerCase();

    if (key === "escape") {
      close_bruder();
    }

    /**
     * Focusing a submit button with tag-name <mbutton> and
     * pressing enter will submit the closest form, basically just
     * clicking the button.
     */
    if (
      e.key.toLowerCase() === "enter" &&
      e.target?.tagName.toLowerCase() === "mbutton"
    )
      e.target.click();
  });

  $(document).on("scroll", function (e) {
    let $scroll_container = document.body.querySelectorAll(
      "[scroll-manipulated]",
    );

    if (!$scroll_container[0]) return;

    if (
      document.documentElement.scrollTop >= 40 ||
      document.body.scrollTop >= 40
    ) {
      $scroll_container.forEach((s) => {
        s.setAttribute("scrolled", true);
      });
    } else {
      $scroll_container.forEach((s) => {
        s.setAttribute("scrolled", false);
      });
    }
  });

  $(document).on("click", "[trigger-file-input]", function (e) {
    this.find("input[type='file']")?.click();
  });

  $(document).on(
    "change",
    "[trigger-file-input] input[type='file']",
    function (e) {
      let [file] = this.files;
      let change =
        this.closest("picture")?.find("img") ||
        this.closest("select-media")?.find("video");

      if (!change) return;

      this.closest("picture")?.setAttribute("filled", true);
      this.closest("select-media")?.setAttribute("filled", true);

      change.src = URL.createObjectURL(file);
    },
  );

  /**
   * Dynamically execute jobs by their name which is the path name
   * that is defined in the PHP routes.
   */
  $(document).on("click", "[execute-job]", async function (e) {
    let job_name = this.getAttribute("execute-job");

    this.activate();

    await execute_job(job_name);

    this.deactivate();

    Frontend.ajax_response("success");
  });

  $(document).on("click", "bruder", function (e) {
    if (
      !e.target.closest("[previous-playlist]") &&
      !e.target.closest("[next-playlist]") &&
      !this.hasAttribute("active")
    )
      open_bruder();
  });

  $(document).on("click", "[open-bruder]", function (e) {
    open_bruder();
  });

  $(window).on("scroll", function (e) {
    bulk_close_contextmenu();
    close_bruder();
  });

  $(document).on("click", function (e) {
    if (!e.target.closest("[has-menu] menu")) bulk_close_contextmenu();
    if (
      (!e.target.closest("bruder") || e.target.closest("[close-bruder]")) &&
      !e.target.closest("player") &&
      !e.target.closest("[open-bruder]")
    )
      close_bruder();
  });

  $(document).on("contextmenu", function (e) {
    if (e.target.closest("[has-menu]")) {
      e.preventDefault();

      let menu = e.target.closest("[has-menu]").find("menu");

      if (menu.hasAttribute("active")) {
        console.log("Menu is open already.");
        return;
      }

      bulk_close_contextmenu();

      document.find_all("[has-menu] menu")?.forEach((menu) => {
        menu.removeAttribute("style");
        menu.find("inr").removeAttribute("style");
        menu.deactivate();
      });

      let mouse_position = { x: e.clientX, y: e.clientY };
      let menu_size = { w: menu.clientWidth, h: menu.clientHeight };
      let menu_content_h = menu.find("[height]").clientHeight + 24;
      let style_top = mouse_position.y;

      menu.activate();
      menu.style.position = "fixed";
      menu.style.left = mouse_position.x - menu_size.w / 2 + "px";
      menu.style.top = style_top + 2 + "px";
      menu.find("inr").style.height = menu_content_h + "px";

      setTimeout(() => {
        menu.style.left = mouse_position.x - menu_size.w / 2 + 6 + "px";
        menu.style.top = style_top + 8 + "px";
      }, 10);
    }
  });

  $(document).on("click", "popup-close", function (e) {
    if (__current_overlay.overlay) {
      __current_overlay.delete();
    }
  });

  /**
   * Creates a button of type submit inside the closest form and
   * triggers a click event on it.
   */
  // TODO: Add this to bruder framework.
  $(document).on("click", "[submit-closest]", function (e) {
    let form = this.closest("form");

    if (!form) return;

    let submit_button = form.querySelector("button[type='submit']");

    if (!submit_button) {
      submit_button = document.createElement("button");
      submit_button.setAttribute("type", "submit");
    }

    form.appendChild(submit_button);
    form.querySelector("button[type='submit']").click();

    if (this.hasAttribute("confirm-submit-button"))
      this.removeAttribute("submit-closest");
  });
});
