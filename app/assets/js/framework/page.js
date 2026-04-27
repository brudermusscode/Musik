import * as Router from "./router";
import * as Frontend from "./frontend";
import * as Responder from "./responder";
import Overlay from "./overlay";
import * as Global from "../pages/global";

/**
 * Reloads the page by calling the get function and setting the
 * reload parameter to true.
 */
export const reload = (keep_overlays = false) => {
  get(
    window.location.pathname + window.location.search,
    false,
    null,
    false,
    true,
    keep_overlays,
  );
};

/**
 *
 * @param {string} url
 * @returns Router
 */
export const get_route = async (url) => {
  let query_params_split = url.split("?");
  let url_no_query_params = query_params_split[0];
  let url_parameter = url_no_query_params.split("/");

  url_parameter.shift();

  /**
   * Mark home if none.
   */
  if (url_parameter[0] === "") url_parameter[0] = "home";

  let route = url_parameter[0];

  return await Router.router(route);
};

function wait() {
  return new Promise((resolve) => {
    const check = () => {
      if (window.scrollY < 1) {
        return resolve(1);
      } else {
        setTimeout(check, 100);
      }
    };

    check();
  });
}

/**
 *
 * @param {string} href
 * @param {boolean} state
 * @param {?string} anchor
 * @param {boolean} scroll_top
 * @param {boolean} reload
 * @param {boolean} keep_overlays
 * @returns void
 */
export const get = async (
  href,
  state = false,
  anchor = null,
  scroll_top = true,
  reload = false,
  keep_overlays = false,
) => {
  /**
   * If the page is in loading state, return.
   */
  if (__page.is_loading && !reload) return;
  if (__page.current === "maintenance") return;

  let current_scrollY = window.scrollY;
  let url = href;
  let main_container = document.body.querySelector("main");
  let title;

  /**
   * Serialize the route the user is coming from.
   */
  let coming_from_path = window.location.pathname;
  let coming_from_path_split = coming_from_path.split("/");

  /**
   * Prepare a reload.
   */
  if (reload)
    url =
      url.concat(url.includes("?") ? "&reload=" : "?reload=") +
      random_string(8);

  /**
   * Get the current route.
   */
  let Route = await get_route(url);

  /**
   * Check if the current location is the same as the
   * requested url. Return nothing in this case. Defines a variable
   * is_same_location to use around this function as well as a
   * variable that just figures out if the current location
   * and the destinated one are from the same oruigin page
   */
  let current_location_route = window.location.pathname.split("/").shift()[0];
  let is_same_location =
    window.location.pathname.concat(window.location.search) == url;
  let is_same_history_route = url == current_location_route;

  /**
   * Return nothing if the current location is the same as the
   * requested one.
   */
  if (!state && is_same_location) return;

  /**
   * Create a new overlay only when the app is being initialized.
   * Otherwise there would be two overlays lingering.
   */
  if (document.find("[loading-app]")) new Overlay(null, false);

  /**
   * Begin the new page load.
   */
  Frontend.load();

  /**
   * Get CSRF token.
   */
  const __csrf_token = document
    .find("head meta[name=csrf_token]")
    ?.getAttribute("content");

  /**
   * Create the object for the history.
   */
  let history_params = {};
  history_params["href"] = url;
  history_params["scrollY"] = window.scrollY;

  $.ajax({
    url: url,
    method: "GET",
    contentType: false,
    processData: false,
    success: async function (data) {
      /**
       * Close overlays if.
       */
      if (!keep_overlays) Frontend.close_overlays();

      /**
       * Close the bruder!
       */
      Global.close_bruder();

      /**
       * Scroll to the top, wait for it and set the body's
       * scrolling to disabled.
       */
      if (!state && scroll_top) {
        window.scrollTo(0, 0);
        Frontend.scroll_to_top();
        await wait();
      }

      /**
       * Update the page global.
       */
      __page.current = Route.key;
      __page.marked = Route.mark ? Route.mark : Route.key;

      /**
       * Update main content.
       */
      main_container.innerHTML = data;

      /**
       * Extract the title.
       * @var string
       */
      title = main_container.find("title")?.innerHTML;

      /**
       * Pushes the coming state to the browser history and sets a proper
       * title to the document.
       */
      if (!state && !reload) {
        history.pushState(history_params, title, url);
        document.title =
          title !== undefined && title !== null && title
            ? title
            : "Unknown Page";
      }

      /**
       * Set header to scrolled.
       */
      if (window.scrollY >= 20)
        document.find_all("[scroll-manipulated]")?.forEach((elem) => {
          elem.setAttribute("scrolled", true);
        });

      /**
       * Check for an exception and move it to a direct child of
       * the body to be present in the very foreground.
       */
      Frontend.extract_exception(main_container);

      /**
       * If a redirect exists, redirect to the page inside the to attribute.
       */
      let redirector = main_container.find("redirect");
      if (redirector && redirector.hasAttribute("to"))
        return window.location.replace(redirector.getAttribute("to"));

      /**
       * Autofocus any input that has the attribute.
       */
      if (document.find("[autofocus]")) document.find("[autofocus]").focus();

      /**
       * Execute once function will only be fired when first
       * accessing a new page, which is determined by the body
       * carrying an attribute named after the route itself.
       */
      if (
        typeof Route.execute_once === "function" &&
        !document.body.hasAttribute(Route.key, Route)
      )
        Route.execute_once(url);

      /**
       * A function that should be fired everytime a new page even
       * inside a subpage is loaded.
       */
      if (typeof Route.execute_always === "function")
        Route.execute_always(url, Route);

      /**
       * Remove all route attributes from any section where it will be
       * added to.
       */
      Object.keys(Router.routes).forEach((index) => {
        document.body.removeAttribute(index);
      });

      /**
       * Set route attributes.
       */
      document.body.setAttribute(
        Route.key == ""
          ? "home"
          : Route.body_attribute !== undefined
            ? Route.body_attribute
            : Route.key,
        "",
      );

      /**
       * Free the clicking on other links by disabling page loading.
       */
      Frontend.unload();

      /**
       * Load dynamic content.
       */
      Frontend.get_content();

      /**
       * Eval all script tags inside the newly fetched content.
       */
      main_container.find_all("script").forEach((script) => {
        eval(script.innerHTML);
      });

      /**
       * Deactivate all main navigation buttons.
       */
      document.find_all("[page]").forEach((button) => {
        button.deactivate();
      });

      /**
       * Set any navigation button carrying the [page] attribute
       * with the name of the currently processed page to active.
       */
      if (Route.mark !== undefined && Route.mark)
        document
          .find_all(`[page="${Route.mark}"]`)
          ?.forEach((button) => button.activate());

      // Set the current page to be marked.
      // __page.marked = Route.mark ? Route.mark : route;

      // The previous Router, basically the router for the page
      // the user is coming from.
      let PreviousRoute = await Router.router(coming_from_path_split[1]);

      /**
       * Set the body to initialized.
       */
      document.body.setAttribute("initialized", true);

      /**
       * If a hashtag is at the end of url, search for the
       * corresponding container and scroll it into the view.
       */
      let url_has_hashtag = url.includes("#");
      let hashtag_id = url_has_hashtag ? url.split("#")[1] : null;

      if (hashtag_id)
        main_container
          .find(`#${hashtag_id}`)
          ?.scrollIntoView({ behavior: "smooth" });

      /**
       * Load in all images with a nice effect.
       */
      Frontend.reload_images();

      /**
       * Now at the end of execution, we give the router the
       * option to execute something.
       */
      if (typeof Route.execute_last === "function")
        Route.execute_last(url, Route);

      /**
       * Scroll to the previously set scrollY position, only if we
       * are going back or forth in history.
       */
      if (state) Frontend.scroll_to(__page.scrollY);

      /**
       * Set the new scrollY only if it is not a reload.
       */
      if (!reload) __page.scrollY = current_scrollY;

      return true;
    },
    error: function (error) {
      Frontend.ajax_error(error);
    },
  });
};

export const get_component = async (
  react,
  url,
  data = null,
  empty_container = false,
  where = "top",
) => {
  $.ajax({
    url: url,
    data: data,
    method: "GET",
    processData: false,
    contentType: "JSON",
    success: function (data) {
      if (!data.status)
        return new Responder.Responder().add(
          document.body,
          data.message,
          "error",
        );

      if (empty_container) {
        react.innerHTML = data;
      } else {
        if (where === "top") react.insertAdjacentHTML("afterbegin", data);
        else react.insertAdjacentHTML("beforeend", data);
      }

      Frontend.reload_images();
    },
    error: function (data) {
      new Responder.Responder().add(
        document.body,
        data.message,
        "error",
        "user",
      );
    },
  });
};

export const objectify_query_params = (query) => {
  if (!query) return;

  query = query.replace("?", "");

  let params = query.split("&");

  if (!params[0] && params.length > 0) params.shift();

  let obj = {};

  params.forEach((param) => {
    let ass = param.split("=");
    obj[ass[0]] = ass[1];
  });

  return obj;
};

export const disable_scroll = () => {
  let TopScroll = window.pageY || document.documentElement.scrollTop;
  let LeftScroll = window.pageX || document.documentElement.scrollLeft;

  window.onscroll = function () {
    window.scrollTo(LeftScroll, TopScroll);
  };
};

export const enable_scroll = () => {
  window.onscroll = function () {};
};

export const random_string = (length) => {
  const characters =
    "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  let randomString = "";

  for (let i = 0; i < length; i++) {
    const randomIndex = Math.floor(Math.random() * characters.length);
    randomString += characters.charAt(randomIndex);
  }

  return randomString;
};
