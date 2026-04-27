<!-- Stylesheets -->
<link href="<?= STYLE . "/fonts.css" ?>" rel="stylesheet">
<link href="<?= STYLE . "/animate.css" ?>" rel="stylesheet">
<link href="<?= STYLE . "/normalize.css" ?>" rel="stylesheet">

<!-- Javascript -->
<script src="<?= SCRIPT . "/jquery371.js" ?>"></script>
<script src="<?= SCRIPT . "/utility.js" ?>"></script>
<script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>

<script>
  let __page = {
    current: "<?= filter_input(INPUT_GET, "page", FILTER_SANITIZE_SPECIAL_CHARS); ?>",
    marked: "home",
    is_loading: false,

    /**
     * Stores the previous window.scrollY value so we can scroll
     * back to where we left of when going back in history.
     * history.state is kind of scuffed, since when pressing the
     * back button, it IMMEDIATELY sets the old history state and
     * I have not yet found a way to stop that.
     */
    scrollY: 0,
  };

  /**
   * Things related to the player in the frontend. Can be accessed
   * in JS globally.
   */
  let __player = {
    active: false,
    volume: null,
    Track: {
      relation: {
        id: null,
        type: null,
      },
      audio: null,
      id: null,
      playlist: null,
      queue_index: null,
    },

    /**
     * This queue will be populated by songs of the currently
     * playing playlist.
     */
    queue: [],

    /**
     * Will be played before any other tracks from the regular
     * queue. Songs will be removed from this queue during
     * initialization already.
     */
    priority_queue: [],
  };

  let __current_overlay = null;
  let __current_second_overlay;
  let __current_audio_element;
  let __current_hover_card;

  let __material_button_ripple_effect_remove_interval = 100;
  let __material_button_ripple_effect_done = false;

  let __body = document.body;
  let __main = document.find("main");
  let __env = "<?= current_env() ?>";
  let __init = false;
</script>

<?php

/**
 * This file includes the main.bundle.js which can be used around
 * the website as a script file. I bet it's loading faster if it
 * is directly included in the DOM instead of imported through a file.
 *
 * * In dev
 * it's refering to a local script file which
 * will be automatically generated and updated by the node
 * development server.
 *
 * * In production & staging
 * It will include the whol production bundle
 * directly to the DOM.
 */

if (DEV) :
  echo '<script type="text/javascript" src="' . _env("NODE_BUNDLE_OUTPUT_PUBLIC_PATH") . 'main.bundle.js"></script>';
else :
  echo "<script defer>";
  include ASSET . "/js/main.production.bundle.js";
  echo "</script>";
endif;
