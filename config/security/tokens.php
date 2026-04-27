<?php

/**
 * Private tokens or passwords which handle authentication for
 * various üarts of the framework, like skipping maintenance mode
 * or csrf constants for validating requests.
 */

return [

  /**
   * Maintenance
   * The maintenance token to bypass maintenance mode.
   */
  // TODO: Make a good maintenance mode.
  "maintenance" => "",

  /**
   * Cross-Site-Request-Forgery
   * Security constant to only allow requests actually coming from
   * this website.
   */
  // TODO Implement CSRF as default.
  "csrf" => "",
];
