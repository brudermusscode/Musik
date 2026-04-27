import * as Frontend from "../framework/frontend";
import * as Request from "../framework/requests";
import * as Page from "../framework/page";

let __timeout;

$(function () {
  $(document).on("input", '[data-action="track:add-to-explore"]', function (e) {
    let input = this;
    let value = input.value;
    let append = document.find(`[data-react="${this.dataset.action}"]`);
    let track_id = append.dataset.trackId;

    clearTimeout(__timeout);

    if ($.trim(value).length < 1) return;

    __timeout = setTimeout(() => {
      $.ajax({
        url: Request.url(this) + `?q=${value}&track_id=${track_id}`,
        success: function (data) {
          if (data.status) append.innerHTML = data.data;
          else Frontend.ajax_response("error");

          Frontend.reload_images();
        },
      });
    }, 100);
  });

  let __input_timeout;

  /**
   * Fetches Tracks based on the data action.
   *
   * @action GET
   */
  $(document).on(
    "input",
    "[data-action='playlist:track:explore'], [data-action='album:track:explore'], [data-action='track:explore']",
    function (e) {
      clearTimeout(__input_timeout);

      let action = this.dataset.action;
      let tracks_container = this.parentElement.find(
        "[data-react='" + action + "']",
      );

      __input_timeout = setTimeout(() => {
        let input = $.trim(this.value);
        let id = this.dataset.id;

        let data_string =
          Request.url(this) + `?` + (id ? `id=${id}&` : "") + `q=${input}`;

        $.ajax({
          url: data_string,
          success: function (data) {
            tracks_container.innerHTML = data.data;
          },
        });
      }, 20);
    },
  );
});
