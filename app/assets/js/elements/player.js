// TODO: Make queue persistent on page reload.
// TODO: Add audio volume normalization.

import * as Frontend from "../framework/frontend";
import * as Cookie from "../framework/cookie";
import * as Global from "../pages/global";

const __duration_track_interval = null;
const __duration_track_timeout = null;
const DEFAULT_VOLUME = 0.2;

const init_color = "#ff47ff";
const error_color = "#ff4769";
const success_color = "#23e934";
const job_color = "#2bbcff";

const current_track_video_path = "current-track cover video";

/**
 * Syncs new files from main directory set in PHP.
 */
export const sync_files = () => {
  $.ajax({
    url: "/track/sync",
    method: "POST",
    success: function (data) {
      console.log(`%c▒ ${data.message}`, `color: ${job_color};`);
    },
  });
};

/**
 * Pass a track from PHP to set it as the currently playing track
 * in the frontend's player.
 *
 * @param {JSON} Track
 * @param {string} public_source_url
 * @param {bool} init
 * @param {bool} priority
 */
export const play_track = async (
  Track,
  public_url,
  init = false,
  priority = false,
) => {
  return new Promise((resolve) => {
    let player_metadata = Player.find("player-metadata");

    pause();
    remove_current_audio();
    reset_duration_track();

    /**
     * Set metadata.
     */
    player_metadata.find("[title]").innerHTML = Track.title;
    player_metadata.find("[artist]").innerHTML =
      `<mi>artist</mi> ${Track.artist}`;

    let player_art = player_metadata.find("picture");

    if (Track.art) {
      player_art.setAttribute("has-art", true);
      player_art.find("img").setAttribute("src", Track.art);
    } else {
      player_art.removeAttribute("has-art");
      player_art.find("img").removeAttribute("src");
    }

    /**
     * Create a new Audio Object with the requested Track.
     */
    let audio = new Audio(public_url);
    audio.volume = __player.volume;

    /**
     * Set global __player variable values.
     */
    __player.Track.id = Track.id;
    __player.Track.audio = audio;

    /**
     * Set the toggled state of the frontend's player to playing
     * when it's ready. Readyness is checked by loaded metadata state.
     */
    audio.addEventListener("loadedmetadata", () => {
      activate_track_HTMLobjects(Track.id, init ? true : false);

      /**
       * Return here on initialization of the app.
       */
      if (init) return resolve(1);

      set_track(Track);
      set_track_relation();
      kick_duration_track(audio, true);

      audio.play();

      // if (!priority) __player.Track.queue_index = parseInt(Track.index);

      Player.activate();

      playing();

      /**
       * Add +1 listens.
       */
      let formdata = new FormData();
      formdata.append("id", Track.id);
      formdata.append("listens", 1);

      $.ajax({
        url: "/track/update",
        data: formdata,
        method: "POST",
      });

      document.title = `🎶 ${Track.artist} × ${Track.title}`;

      console.log(
        `%cPlaying Track ID::${Track.id}\n${Track.title}\n${Track.artist}\nDuration: ${(Track.length_seconds / 60).toFixed(2)}min\nVolume: ${__player.volume * 100}%\nQueue: ${priority ? "Priority" : "Regular"}`,
        `color: ${success_color};`,
      );

      return resolve(1);
    });
  });
};

/**
 * Populates all necessary dependencies with the track that is passed.
 * @param {object} Track
 */
export const set_track = (Track) => {
  Cookie.set("__player_Track", Track.id, 365);
};

/**
 * This searches the DOM for a page tag that has an attribute of
 * either [playlist] or [album] and uses the dataset attributes to
 * set the relation of a played song.
 */
export const set_track_relation = () => {
  let relation =
    document.find("page[playlist]") ?? document.find("page[album]");
  let relation_id = null;
  let relation_type = null;

  /**
   * If no relation was found, delete the cookies and set
   * everything related to null.
   */
  if (!relation) {
    __player.Track.relation = {
      id: null,
      type: null,
    };

    Cookie.remove("__player_Track_relation_id");
    Cookie.remove("__player_Track_relation_type");

    return;
  }

  relation_id = relation.dataset.id;
  relation_type = relation.dataset.type;

  __player.Track.relation = {
    id: relation_id,
    type: relation_type,
  };

  Cookie.set("__player_Track_relation_id", relation_id);
  Cookie.set("__player_Track_relation_type", relation_type);
};

/**
 * Finds any [track] and sets it to active state.
 */
export const activate_track_HTMLobjects = (track_id, paused = false) => {
  document.find_all("[track]").forEach((elem) => {
    elem.removeAttribute(paused ? "active" : "paused");

    if (elem.getAttribute("track") == track_id) {
      paused ? elem.setAttribute("paused", true) : elem.activate();
    } else {
      elem.removeAttribute("paused");
      elem.deactivate();
    }
  });
};

/**
 * Simple wrapper function to update the right sidebar with the
 * persistent set cookies without setting new ones or deleting
 * any. It's a simple refresh of the already existing information.
 */
export const update_current_track_w_cookies = async () =>
  Global.update_current_track(null, null, false, true);

/**
 * Searches for a song after the possible currently playing and
 * starts playing it.
 */
export const queue_play_next = async (skipped = false) => {
  if (__player.queue.length < 1 && __player.priority_queue.length < 1) return;

  // ? Priority Queue
  let pq = __player.priority_queue;

  if (pq.length > 0) return priority_queue_play_next(skipped);

  // ? Base Queue
  let q = __player.queue;
  let current_q_idx = __player.Track.queue_index;
  let next_track_id = q[current_q_idx + 1];

  // Return if no Track is left in any queue.
  if (!next_track_id) {
    console.log("[queue] Nothing else to play!");

    if (!skipped) pause();

    return;
  }

  let response = await get_Track(next_track_id);
  let Track = response.data.Track;
  let track_public_url = response.data.track_public_url;

  // Add new index (+1) to Track object.
  Track.index = current_q_idx + 1;

  // Set new index.
  __player.Track.queue_index = parseInt(Track.index);

  // Play the new Track.
  await play_track(Track, track_public_url, false, false);

  // Update right sidebar
  await update_current_track_w_cookies();
};

/**
 * Plays the next song from priority queue.
 *
 * @param {bool} skipped
 */
export const priority_queue_play_next = async () => {
  let pq = __player.priority_queue;

  let response = await get_Track(pq[0]);
  let Track = response.data.Track;
  let track_public_url = response.data.track_public_url;

  // Remove Track from prio queue.
  __player.priority_queue.splice(0, 1);

  await play_track(Track, track_public_url, false, true);

  // Update right sidebar
  await update_current_track_w_cookies();
};

/**
 * Searches for a song before the possible currently playing and
 * starts playing it.
 */
export const queue_play_previous = async () => {
  if (__player.queue.length < 1) return;

  let q = __player.queue;
  let current_q_idx = __player.Track.queue_index;
  let previous_track = q[current_q_idx - 1];

  if (!previous_track) {
    console.log("[queue] No previous tracks!");
    return;
  }

  let response = await get_Track(previous_track);
  let Track = response.data.Track;
  let track_public_url = response.data.track_public_url;

  // Add new index (+1) to Track object.
  Track.index = current_q_idx - 1;

  // Set new index.
  __player.Track.queue_index = parseInt(Track.index);

  // Play the new Track.
  await play_track(Track, track_public_url, false, false);

  // Update right sidebar
  await update_current_track_w_cookies();
};

/**
 * Set anything related to the player to playing.
 */
export const playing = () => {
  __player.active = true;
  Cookie.set("__player_active", 1, 2);
};

/**
 * Set anything related to the player to playing.
 */
export const not_playing = () => {
  __player.active = false;
  Cookie.set("__player_active", 0, 2);
};

/**
 * Start the player if there is an active <audio></audio> element
 * in the DOM.
 */
export const resume = () => {
  if (!__player.Track.audio || !__player.Track.id) return;

  // Play cover video in sidebar
  document.find(current_track_video_path)?.play();

  __player.Track.audio.play();

  Player.activate();

  kick_duration_track(__player.Track.audio);
  set_playing_HTMLobjects();
  playing();
};

/**
 * Searches for any button that could trigger a play or pause of a
 * track and sets it to playing again (active).
 */
export const set_playing_HTMLobjects = () => {
  let buttons = document.find_all("[play-track]");

  buttons?.forEach((button) => {
    if (button.getAttribute("play-track") == __player.Track.id) {
      button.activate();
      button.closest("[track]")?.activate();
      button.closest("[track]").removeAttribute("paused");
    }
  });
};

/**
 * Pauses/Stops the currently playing song and sets any play
 * button for the currently playing song to resume.
 */
export const pause = () => {
  __player.Track.audio?.pause();

  // Pause cover video in sidebar
  document.find(current_track_video_path)?.pause();

  Player.deactivate();

  clearInterval(__duration_track_interval);
  clearTimeout(__duration_track_timeout);

  /**
   * Deactivate ANY preview button when the song gets paused.
   */
  set_paused_HTMLobjects();

  not_playing();
};

/**
 * Searches for any HTMLObject that could trigger a play or pause
 * of a track and sets it to paused state.
 */
export const set_paused_HTMLobjects = () => {
  let buttons = document.find_all("[play-track]");

  buttons?.forEach((button) => {
    if (button.getAttribute("play-track") == __player.Track.id)
      button.closest("[track]").setAttribute("paused", true);

    button.deactivate();
    button.closest("[track]")?.deactivate();
  });
};

/**
 * Finds the only audio element that should be in the DOM and
 * deletes it.
 */
export const remove_current_audio = () => {
  __player.Track.id = null;
  __player.Track.audio = null;
};

/**
 * Loads a track by given :: ID.
 *
 * @param {int} id
 */
export const get_Track = async (id) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      url: `/track/one/${id}`,
      processData: false,
      contentType: false,
      success: function (data) {
        return resolve(data);
      },
      error: function (error) {
        Frontend.ajax_error(error);
        return reject(0);
      },
    });
  });
};

/**
 * Sets the duration track to 0 width and clears all
 * intervals/timeouts that calculate the current width based on
 * the time played of the currently playing song.
 */
export const reset_duration_track = () => {
  let Player = document.find("player");
  let player_track_duration = document.find("player duration-track");

  Player.deactivate();

  clearTimeout(__duration_track_timeout);
  clearInterval(__duration_track_interval);

  player_track_duration.style.width = "0%";
};

/**
 * Kickstarts the duration track of the currently playing song.
 * Respects the current width of a started song, that has played
 * already for some seconds or minutes.
 *
 * @param {HTMLElement} audio_element
 */
export const kick_duration_track = (audio_element, reset = false) => {
  let duration = audio_element.duration;
  let player_track_duration = Player.find("duration-track");

  let player_width = parseFloat(
    getComputedStyle(Player.find("player-content")).width,
  );
  let track_starting_width = parseFloat(
    getComputedStyle(player_track_duration).width,
  );
  let current_width = !reset ? (track_starting_width * 100) / player_width : 0;

  /**
   * Gets the exact start time in ms.
   */
  let interval_step_ms = 10;
  let total_duration_ms = 1000 * duration;
  let start = performance.now();
  let add_width;

  /**
   * Increase the width of the duration track by x% every 10 ms.
   * See calculation down below.
   */
  __duration_track_interval = setInterval(() => {
    const time_elapsed_ms = performance.now() - start;

    add_width = Math.min(100, (time_elapsed_ms / total_duration_ms) * 100);
    player_track_duration.style.width = `${current_width + add_width}%`;
  }, interval_step_ms);

  /**
   * After a timeout of the full duration in ms, clear the above
   * interval and set the duration track's width to 0 again!
   */
  let left_duration = audio_element.duration - audio_element.currentTime;
  let total_left_duration_ms = 1000 * left_duration;

  __duration_track_timeout = setTimeout(() => {
    reset_duration_track();
    queue_play_next();
  }, Math.round(total_left_duration_ms));
};

/**
 * Asynchronyously initializes the volume on player startup.
 */
export const init_volume = async () => {
  return new Promise((resolve) => {
    let Player = document.find("player");
    let current_volume_cookie = Cookie.get("__player_volume");

    console.log(
      `%c▒ Initialized Volume: ${current_volume_cookie * 100} %`,
      `color: ${init_color};`,
    );

    let parsed_volume = parseFloat(current_volume_cookie);

    /**
     * If anything went wrong and the current volume parsed is not
     * a number anymore or its bigger than 1, return the default volume.
     */
    if (isNaN(parsed_volume) || parsed_volume > 1.0)
      parsed_volume = DEFAULT_VOLUME;

    Player.setAttribute("volume", parsed_volume);
    __player.volume = parsed_volume;
    if (__player.Track.audio) __player.Track.audio.volume = parsed_volume;

    /**
     * Set a cookie for persistence.
     */
    Cookie.set("__player_volume", parsed_volume);

    return resolve(1);
  });
};

/**
 * Based on the Track.id set in `__player_Track` cookie, this
 * function fetches and sets all necessary dependencies on site
 * load up.
 */
export const init_current_track = async () => {
  return new Promise(async (resolve) => {
    let track_id = Cookie.get("__player_Track");

    /**
     * No cookie set with a recent track id?
     */
    if (!track_id || isNaN(track_id)) {
      console.log(`%c▒ No recent Track found.`, `color: ${init_color};`);
      return resolve(1);
    }

    let Track_response = await get_Track(track_id);

    /**
     * Any error loading current/last track?
     */
    if (!Track_response.status) {
      console.log(`%c▒ Error loading current track.`, `color: ${error_color};`);
      return resolve(1);
    }

    /**
     * Play the current track with an init flag.
     */
    await play_track(
      Track_response.data.Track,
      Track_response.data.track_public_url,
      true,
    );

    /**
     * Get song info in right sidebar.
     */
    let relation_id = Cookie.get("__player_Track_relation_id");
    let relation_type = Cookie.get("__player_Track_relation_type");

    await Global.update_current_track(relation_id, relation_type, true);

    /**
     * Pause the video in right sidebar. It should only play when
     * the song is playing.
     */
    document.find(current_track_video_path)?.pause();

    console.log(`%c▒ Recent Track found and loaded.`, `color: ${init_color};`);

    return resolve(1);
  });
};

/**
 * Mutes the volume but keeps running.
 */
export const mute = () => {
  let volume = 0.0;

  // Persistent cookie
  Cookie.set("__player_volume", volume, 365);

  // Global player
  __player.volume = volume;

  // <audio>-Element
  if (__player.Track.id && __player.Track.audio)
    __player.Track.audio.volume = volume;

  // Frontend Player
  Player.setAttribute("volume", volume);
};

/**
 * When unmuting, set the volume to the default.
 */
export const unmute = () => {
  let volume = DEFAULT_VOLUME;

  // Persistent cookie
  Cookie.set("__player_volume", volume, 365);

  // Global player
  __player.volume = volume;

  // <audio>-Element
  if (__player.Track.audio && __player.Track.audio)
    __player.Track.audio.volume = volume;

  // Frontend Player
  Player.setAttribute("volume", volume);
};

/**
 * Update the queue based on the currently playing
 */
export const create_queue = async (type, id, track_id) => {
  return new Promise((resolve) => {
    let formdata = new FormData();
    formdata.append("type", type);
    formdata.append("id", id);
    formdata.append("track_id", track_id);

    $.ajax({
      url: "/queue/create",
      data: formdata,
      method: "POST",
      success: function (data) {
        if (data.status) {
          __player.queue = data.data.Queue;
          __player.Track.queue_index = data.data.index;

          console.log(`%c▒ Queue created.`, `color: ${init_color};`);
          return resolve(1);
        }

        Frontend.ajax_response("error");
        console.log(`%c▒ Queue creation failed.`, `color: ${error_color};`);
        return resolve(0);
      },
    });
  });
};

document.addEventListener("DOMContentLoaded", async function () {
  /**
   * Set lib view.
   */
  if (!Cookie.get("__lib_view")) {
    document.find("library").setAttribute("view", "list");
    document.find("library-view [view=list]").activate();
    Cookie.set("__lib_view", "list");
  }

  Cookie.set("__player_active", 0, 2);

  await init_volume();
  await init_current_track();

  /**
   * On page initialization, create the queue from saved Track and
   * Relation saved in cookies. Skip, if either of one doesn't exist.
   */
  if (__player.Track.id && __player.Track.relation.id)
    await create_queue(
      __player.Track.relation.type,
      __player.Track.relation.id,
      __player.Track.id,
    );

  console.log(`%c▒ Everything loaded!`, `color: ${success_color};`);

  sync_files();
});

$(function () {
  /**
   * Clicking the play button!
   *
   * @action GET
   * @controller TracksController
   * @model Track
   */
  $(document).on("click", "[play-track]", async function (e) {
    let track_id = this.getAttribute("play-track");

    /**
     * If the same Track saved in __player is clicked, just resume
     * it and return.
     */
    if (track_id == __player.Track.id) {
      document.find("player [play]")?.click();
      return;
    }

    let response = await get_Track(track_id);

    if (!response.status) return Frontend.create_responder(response.error);

    await play_track(response.data.Track, response.data.track_public_url);

    /**
     * Update the current track with init = false which will
     * search the closest page element's dataset for an album or
     * playlist id. Any found? Will be set to cookies for persistence!
     */
    let relation_id = this.closest("page")?.dataset.id ?? null;
    let relation_type = this.closest("page")?.dataset.type ?? null;

    await create_queue(relation_type, relation_id, track_id);
    await Global.update_current_track(relation_id, relation_type);
  });

  let __cursor_move_timeout_fullscreen_player;
  let __cursor_move_timeout_fullscreen_player_ms = 2000;

  $(document).on("mousemove click", "player[fullscreen]", function (e) {
    clearTimeout(__cursor_move_timeout_fullscreen_player);

    if (!this.hasAttribute("cursor-moved"))
      this.setAttribute("cursor-moved", true);

    __cursor_move_timeout_fullscreen_player = setTimeout(() => {
      this.removeAttribute("cursor-moved");
    }, __cursor_move_timeout_fullscreen_player_ms);
  });

  /**
   * Resizes the player to the fullscreen version.
   */
  $(document).on(
    "click",
    "fullscreen-player, fullscreen-player-close",
    function (e) {
      clearTimeout(__cursor_move_timeout_fullscreen_player);

      if (!Player.hasAttribute("fullscreen")) {
        Player.setAttribute("fullscreen", true);
        Player.setAttribute("cursor-moved", true);

        /**
         * Stop video playback, if a song is currently playing.
         */
        if (__player.active)
          document.find("current-track[has-video] video")?.pause();

        __cursor_move_timeout_fullscreen_player = setTimeout(() => {
          this.removeAttribute("cursor-moved");
        }, __cursor_move_timeout_fullscreen_player_ms);
      } else {
        Player.removeAttribute("fullscreen");
        Player.removeAttribute("cursor-moved");

        /**
         * Start video playback, if a song is currently playing.
         */
        if (__player.active)
          document.find("current-track[has-video] video")?.play();
      }
    },
  );

  /**
   * Toggles the players visibility.
   */
  $(document).on("click", "[data-action='player:hide']", function (e) {
    let show_button = document.find("show-player");

    if (!Player.hasAttribute("collapsed")) {
      Player.setAttribute("collapsed", true);
      show_button.setAttribute("collapsed", true);
      Cookie.set("__player_collapsed", 1);
    } else {
      Player.removeAttribute("collapsed");
      show_button.removeAttribute("collapsed");
      Cookie.set("__player_collapsed", 0);
    }
  });

  /**
   * Toggle play/pause.
   */
  $(document).on("click", "player [play]", function (e) {
    let Player = document.find("player");

    if (Player.hasAttribute("active")) pause();
    else resume();
  });

  /**
   * Play next song in queue.
   */
  $(document).on("click", "[play-next]", async function (e) {
    await queue_play_next(true);
  });

  /**
   * Play next song in queue.
   */
  $(document).on("click", "[play-previous]", async function (e) {
    await queue_play_previous();
  });

  /**
   * Increase the volume of the currently playing track and set it globally.
   */
  $(document).on("click", "[volume-up]", function () {
    let new_volume = __player.volume + 0.1;

    if (new_volume >= 1.0) {
      __player.volume = 1;
    } else __player.volume += 0.1;

    if (__player.Track.audio) __player.Track.audio.volume = __player.volume;

    let parsed_volume = __player.volume.toFixed(1);

    Player.setAttribute("volume", parsed_volume);

    Cookie.set("__player_volume", parsed_volume, 365);

    console.log(`Volume: ${Math.round(__player.volume * 100)}%`);
  });

  /**
   * Decrease the volume of the currently playing track and set it globally.
   */
  $(document).on("click", "[volume-down]", function () {
    let new_volume = __player.volume - 0.1;

    if (new_volume < 0) {
      __player.volume = 0;
    } else __player.volume -= 0.1;

    if (__player.Track.audio) __player.Track.audio.volume = __player.volume;

    let parsed_volume = __player.volume.toFixed(1);

    Player.setAttribute("volume", parsed_volume);

    Cookie.set("__player_volume", parsed_volume, 365);

    console.log(`Volume: ${Math.round(__player.volume * 100)}%`);
  });

  /**
   * Mute volume.
   */
  $(document).on("click", "[volume-mute]", function (e) {
    if (__player.volume == 0.0) unmute();
    else mute();
  });
});
