import * as Frontend from "../framework/frontend";
import * as Player from "../elements/player";
import * as Global from "../pages/global";

const init_color = "#ff47ff";
const error_color = "#ff4769";
const success_color = "#23e934";
const job_color = "#2bbcff";

let __lyrics_current_line = null;
let __lyrics_current_line_index = null;
let __lyrics_next_line = null;

const scroll_to_current_line = () => {
  let lyrics = document.find("lyrics");
  let lyrics_fullscreen = lyrics?.hasAttribute("active");
  let line_offset =
    __lyrics_current_line.offsetTop -
    (!lyrics_fullscreen ? 72 : lyrics.clientHeight / 2 - 72);

  lyrics.find("lyrics-content")?.scrollTo({
    top: line_offset,
    behavior: "smooth",
  });
};

const init = () => {
  let lyrics = document.find("lyrics");
  let audio = __player.Track.audio;

  if (!lyrics || !audio) return null;

  let current_track_time = parseFloat(audio.currentTime);
  let lines = lyrics.find_all("line");

  // Deactivate all lines before initializing the current one, as this function will
  // be called when starting a new song or when choosing a new time of the current
  // song.
  lines.forEach((line) => {
    line.deactivate();
  });

  $(lines).each(function (index, line) {
    let time = parseFloat(line.getAttribute("timestamp"));

    if (time >= current_track_time) {
      __lyrics_current_line = lines[index - 1] ?? line;
      __lyrics_current_line_index = index;
      __lyrics_current_line.activate();

      scroll_to_current_line();

      __lyrics_next_line = __lyrics_current_line.nextElementSibling;

      console.log(
        `%c▒ Lyrics inited at line ${__lyrics_current_line_index}`,
        `color: ${init_color};`,
      );
      return false;
    }

    // If there is no line left, that comes later than the current track time, we just
    // add the last line to be the next one. This is especially important when click-
    // ing on the track overflow.
    __lyrics_next_line = lines[lines.length - 1];
  });
};

export const start = () => {
  if (init() === null) return;

  let audio = __player.Track.audio;

  audio?.addEventListener("timeupdate", () => {
    let current_track_time = parseFloat(audio.currentTime);
    let lyrics = document.find("lyrics");

    if (!lyrics) return false;

    let lyrics_fullscreen = lyrics.hasAttribute("active");
    let lines = lyrics.find_all("line");
    let next_line_time = __lyrics_next_line
      ? parseFloat(__lyrics_next_line.getAttribute("timestamp"))
      : null;

    if (__lyrics_next_line && current_track_time >= next_line_time) {
      lines.forEach((line) => {
        line.deactivate();
      });

      __lyrics_next_line.activate();
      __lyrics_current_line = __lyrics_next_line;

      scroll_to_current_line();

      __lyrics_next_line = lines[__lyrics_current_line_index + 1] ?? null;
      __lyrics_current_line_index += 1;
    }
  });
};

export const fullscreen = () => {
  let lyrics = document.find("lyrics");
  let placeholder = document.find("lyrics-placeholder");

  if (!lyrics) return;

  setTimeout(() => {
    lyrics.activate();
    placeholder.activate();
    __player.lyrics.fullscreen = true;

    setTimeout(() => {
      scroll_to_current_line();
    }, 300);
  }, 20);
};

export const close_fullscreen = () => {
  let lyrics = document.find("lyrics");
  let placeholder = document.find("lyrics-placeholder");

  if (!lyrics) return;

  lyrics.deactivate();
  placeholder.deactivate();
  __player.lyrics.fullscreen = false;

  setTimeout(() => {
    scroll_to_current_line();
  }, 300);
};

$(function () {
  $(document).on("click", '[data-action="lyrics:fullscreen"]', function (e) {
    let lyrics = document.find("lyrics");

    if (lyrics?.hasAttribute("active")) {
      close_fullscreen();
    } else {
      fullscreen();
    }
  });

  $(document).on("click", "edit-lyrics line:not([active])", function (e) {
    this.activate();
  });

  $(document).on("click", "lyrics[active] line", function (e) {
    let lyrics = this.closest("lyrics");
    let lines = lyrics.find_all("line");
    let timestamp = this.getAttribute("timestamp");
    let content = this.find("p").innerHTML;
    let formatted_timestamp = this.find("timestamp").innerHTML;

    let stop_editing_all_lines = (lines) => {
      if (!lyrics.hasAttribute("editing")) return;

      lines.forEach((line) => {
        line.removeAttribute("editing");
        line.removeAttribute("show-actions");
      });

      lyrics.removeAttribute("editing", true);
    };

    if (!this.hasAttribute("editing"))
      Player.set_time(parseFloat(timestamp), __control_pressed ? true : false);

    if (__control_pressed) {
      stop_editing_all_lines(lines);

      this.setAttribute("editing", true);
      lyrics.setAttribute("editing", true);
      setTimeout(() => {
        console.log("focusing input!");
        let input = this.find("input[name=lyrics_line_timestamp]");
        input.focus();
        input.setSelectionRange(input.value.length, input.value.length);
      }, 100);
    } else if (!__control_pressed && !this.hasAttribute("editing")) {
      stop_editing_all_lines(lines);
    }
  });

  /**
   * Show more actions when hovering a line in fullscreen when control key is pressed.
   */
  $(document).on("keydown", function (e) {
    let key = e.key.toLowerCase();

    if (key === "control") {
      let hovered_line = document.find("lyrics line:hover");
      if (hovered_line && __player.lyrics.fullscreen)
        hovered_line.setAttribute("show-actions", true);
    }
  });

  $(document).on("keyup", function (e) {
    if (!__player.lyrics.fullscreen) return;

    let key = e.key.toLowerCase();

    if (key === "control") {
      document.find_all("lyrics line").forEach((line) => {
        line.removeAttribute("show-actions");
      });
    }
  });

  $(document).on("mouseover", "lyrics[active] line", function (e) {
    if (!__control_pressed) return;

    this.setAttribute("show-actions", true);
  });

  $(document).on("mouseout", "lyrics[active] line", function (e) {
    if (this.hasAttribute("show-actions")) this.removeAttribute("show-actions");
  });

  /**
   * For updating a single line inside the lyrics fullscreen.
   */
  $(document).on(
    "submit",
    '[data-form="track:lyrics-update-inline"]',
    function (e) {
      e.preventDefault();

      let form = this;
      let button = this.find("mbutton[submit-closest]");
      let formdata = new FormData(this);
      let line = this.closest("line");
      let lyrics = this.closest("lyrics");
      let timestamp = this.find("timestamp");
      let content = this.find("p");

      button.disable();

      formdata.append("lyrics_w_timestamp_update_single_line", true);

      $.ajax({
        url: "/track/update",
        data: formdata,
        method: "POST",
        success: function (data) {
          console.log(data);

          button.enable();
          timestamp.innerHTML = data.data.line.timestamp;
          content.innerHTML = data.data.line.content;
          line.setAttribute("timestamp", data.data.line.timestamp_seconds);
          line.removeAttribute("show-actions");
          line.removeAttribute("editing");
          lyrics.removeAttribute("editing");

          Player.set_time(data.data.line.timestamp_seconds, false);
          Player.resume();
        },
      });
    },
  );

  /**
   * For a single line to be updated in the lyrics through the full edit window.
   */
  $(document).on("submit", "[data-form='track:lyrics-update']", function (e) {
    e.preventDefault();

    let button = this.find("[submit-closest]");
    let form = this;
    let formdata = new FormData(this);
    let line = form.find("line");
    let timestamp = line.find("timestamp");
    let content = line.find("content");

    button.disable();

    formdata.append("lyrics_w_timestamp_update_single_line", true);

    $.ajax({
      url: "/track/update",
      data: formdata,
      method: "POST",
      success: function (data) {
        button.enable();
        line.deactivate();
        timestamp.find("p").innerHTML = timestamp.find("input").value;
        content.find("p").innerHTML = content.find("input").value;

        // TODO: Update lyrics section only.
        Global.update_current_track();
        Frontend.ajax_response(data.status ? "success" : "error");
      },
    });
  });
});
