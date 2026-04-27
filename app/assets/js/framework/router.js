/**
 * * If using Bruder's SPA style page loading capabilities, you
 * * want to add each route here as a key in the routes object.
 * * The key should match the exact name of the page in the
 * * browser's address bar.
 * * Example: /beatmaps/12/all would require the key "beatmaps".
 */

export const routes = {
  "not-found": {},

  home: {
    // * Any HTML Element holding the [page] attribute with a
    // * value of this object's key value will be marked [active].
    mark: "home",

    // * Should match the exact params in your router for this route.
    params: "/:id/:sub",

    // * Some functions should just be executed once when loading a
    // * page and not again, when entering a sub page of this route.
    // * For example, you click to /user/1 and it should load all
    // * scores of the user with the id 1. Then you click on
    // * /user/1/friends and it should not execute the function
    // * loading all scores again.
    execute_once: (url, Route) => {},

    // * Executes whenever a new page is loaded, even when it's a
    // * subpage in a main page.
    execute_always: (url, Route) => {},

    // * Executes always at the very end of a new page load.
    execute_last: (url, Route) => {},
  },

  albums: {
    mark: "albums",
  },

  album: {
    params: "/:id",
    mark: "albums",
    execute_last: (url, Route) => {
      let extracted_params = extract_params(url, Route);
      let buttons = document.find_all(
        `[page="album"][data-id="${extracted_params.id}"]`,
      );

      buttons?.forEach((button) => {
        button.setAttribute("active", true);
      });
    },
  },

  playlist: {
    params: "/:id",
    execute_last: (url, Route) => {
      let extracted_params = extract_params(url, Route);
      let buttons = document.find_all(
        `[page="playlist"][data-id="${extracted_params.id}"]`,
      );

      buttons?.forEach((button) => {
        button.setAttribute("active", true);
      });
    },
  },
};

export const extract_params = (url, Route) => {
  let url_split = url.split("/");
  let page = url_split[1];
  let Route_param_split = Route.params.split("/");

  // Remove first two elements as these are an empty string and
  // the page which is not a param in the sense of a model
  // instance id or something.
  url_split = url_split.slice(2);

  // Remove the first array key as it is an empty string.
  Route_param_split.shift();

  if (url_split.length !== Route_param_split.length)
    throw new Error("uri split length doesn't match Route param split length!");

  let final = { page: page };

  url_split.forEach((param, index) => {
    let key = Route_param_split[index].replace(":", "");
    final[key] = param;
  });

  return final;
};

export const router = async (route) => {
  let match;
  let matches_any = route in routes ? true : null;

  // Check for any matches or fallback to not_found.
  match = !matches_any ? routes["not-found"] : routes[route];

  // Append the key.
  match["key"] = route;

  return match;
};
