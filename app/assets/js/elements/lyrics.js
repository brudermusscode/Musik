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
    (!lyrics_fullscreen ? 72 : lyrics.clientHeight / 2);

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

$(function () {
  $(document).on("click", '[data-action="lyrics:fullscreen"]', function (e) {
    let lyrics = document.find("lyrics");
    let placeholder = document.find("lyrics-placeholder");

    if (!lyrics) return;

    if (lyrics.hasAttribute("active")) {
      lyrics.deactivate();
      placeholder.deactivate();
    } else {
      lyrics.activate();
      placeholder.activate();
    }

    setTimeout(() => {
      scroll_to_current_line();
    }, 300);
  });
});
