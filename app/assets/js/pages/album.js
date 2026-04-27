import * as Frontend from "../framework/frontend";
import * as Page from "../framework/page";
import * as Request from "../framework/requests";

$(function () {
  /**
   * Delete an AlbumTrack.
   *
   * @action DELETE
   * @controller AlbumTracksController
   */
  $(document).on("click", "[data-action='album:track:delete']", function (e) {
    let button = this;
    let formdata = new FormData();

    formdata.append("id", this.dataset.id);
    formdata.append("track_id", this.dataset.trackId);

    button.deactivate();

    $.ajax({
      url: Request.url(this),
      data: formdata,
      method: "POST",
      success: function (data) {
        if (data.status) Page.reload();

        Frontend.ajax_response(data.status ? "success" : "error");
      },
    });
  });

  $(document).on(
    "click",
    '[data-action="album:track:create"], [data-action="playlist:track:create"]',
    function (e) {
      let preview = this;
      let formdata = new FormData();
      let id = this.dataset.id;
      let track_id = this.dataset.trackId;

      formdata.append("id", id);
      formdata.append("track_id", track_id);

      $.ajax({
        url: Request.url(this),
        data: formdata,
        method: "POST",
        success: function (data) {
          if (data.status) {
            preview.active() ? preview.deactivate() : preview.activate();
            Page.reload(true);
          }
        },
      });
    },
  );
});
