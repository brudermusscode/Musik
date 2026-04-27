import * as Frontend from "../framework/frontend";
import * as Page from "../framework/page";
import * as Request from "../framework/requests";
import * as Global from "../pages/global";

let __input_timeout;
let __is_mousedown_on_track = false;
let __dragging = {
  enabled: false,
  object: null,
  placeholder: null,
  column: null,
  starting_position: null,
  can_drop: false,
  droppable_areas: null,
  current_droppable_area: null,
};

$(function () {
  /**
   * Drag a track somewhere else in the playlist.
   */
  $(document).on("mousedown", "song move-track", function (e) {
    if (e.button === 2) return;

    let track = this.closest("song");

    __dragging.object = track;
    __dragging.starting_position = this.getBoundingClientRect();

    __is_mousedown_on_track = true;

    document.body.setAttribute("disable-user-selection", true);
  });

  let __last_hovered_song = null;

  $(document).on("mousemove", function (e) {
    if (!__dragging.object || !__is_mousedown_on_track) return;

    let hovered_song = e.target.closest("song");
    let track_size = {
      w: __dragging.object.clientWidth,
      h: __dragging.object.clientHeight,
    };
    let mouse_pos = {
      x: e.pageX - document.documentElement.scrollLeft,
      y: e.pageY - document.documentElement.scrollTop,
    };

    let new_x = mouse_pos.x - 32; //  - track_size.w / 2
    let new_y = mouse_pos.y - track_size.h / 2;

    __dragging.object.style.left = `${new_x}px`;
    __dragging.object.style.top = `${new_y}px`;

    /**
     * Only run this if we are dragging a track. Everything after
     * is for starting the dragging operation.
     */
    if (__dragging.enabled) {
      if (hovered_song && __last_hovered_song !== hovered_song) {
        hovered_song.insertAdjacentElement(
          "beforebegin",
          __dragging.placeholder,
        );
      }

      return;
    }

    /**
     * Play drag sound.
     */
    new Audio("/assets/sounds/drag.ogg").play();

    __dragging.enabled = true;

    /**
     * Create placeholder.
     */
    let placeholder = document.createElement("song");

    placeholder.setAttribute("moving-temporary", true);
    placeholder.style.width = track_size.w + "px";
    placeholder.style.height = track_size.h + "px";
    __dragging.placeholder = placeholder;

    __dragging.object.insertAdjacentElement("afterend", placeholder);
    __dragging.object.setAttribute("moving", true);
  });

  $(document).on("mouseup", function (e) {
    if (!__dragging.enabled) return;

    document.body.removeAttribute("disable-user-selection");

    let target_song = e.target.closest("song");

    setTimeout(() => {
      __dragging.object.removeAttribute("moving");
      __dragging.object.removeAttribute("style");

      /**
       * Just insert the song wherever the placeholder is right now.
       */
      __dragging.placeholder.insertAdjacentElement(
        "beforebegin",
        __dragging.object,
      );
      __dragging.placeholder.remove();

      /**
       * Reset all keys of the dragging object.
       */
      __dragging.enabled = false;
      __dragging.object = null;
      __dragging.placeholder = null;
      __dragging.column = null;
      __dragging.starting_position = null;
      __dragging.can_drop = false;
      __dragging.droppable_areas = null;
      __dragging.current_droppable_area = null;

      let form = document.find('[data-form="playlist:track:reorder"]');
      let formdata = new FormData(form);

      /**
       * Play drop sound.
       */
      new Audio("/assets/sounds/drop.ogg").play();

      /**
       * Trigger playlist update to persist new order.
       */
      $.ajax({
        url: "/playlist/update",
        method: "POST",
        data: formdata,
      });
    }, 10);
  });

  $(document).on(
    "click",
    '[data-action="playlist:track:play-next"]',
    function (e) {
      Global.bulk_close_contextmenu();

      let track_id = this.closest("[track]").getAttribute("track");

      __player.priority_queue.push(parseInt(track_id));
    },
  );

  /**
   * Create a new playlist.
   *
   * @action CREATE
   * @controller PlaylistsController
   */
  $(document).on("submit", '[data-action="playlist:create"]', function (e) {
    e.preventDefault();

    let formdata = new FormData(this);
    let button = this.find("[submit-closest]");

    Frontend.load();

    $.ajax({
      url: Request.url(this),
      data: formdata,
      method: "POST",
      success: function (data) {
        Frontend.unload();

        if (data.status) {
          let Playlist = data.data;

          Page.get(`/playlist/${Playlist.id}`);
          Global.update_library();
        }

        Frontend.ajax_response(data.status ? "success" : "error");
      },
    });
  });

  /**
   * Delete a PlaylistTrack.
   *
   * @action DELETE
   * @controller PlaylistTracksController
   */
  $(document).on(
    "click",
    "[data-action='playlist:track:delete']",
    function (e) {
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
    },
  );
});
