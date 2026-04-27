<?php

namespace Bruder\Application;

class Application
{
  /**
   * Holds default dialogues for UI responsiveness.
   *
   * @var object
   */
  protected static $default_dialogues;

  /**
   * Applications default text responses with neat keys.
   *
   * @return array
   */
  public static function get_default_dialogues()
  {
    return [
      "INVALID_REQUEST" =>
      "<strong>Invalid request!</strong> Try again and if the error persists, open a support ticket on our discord server.",

      /**
       * Interactions.
       */
      "UPDATED" => "<strong>Updated!</strong>",
      "CREATED" => "<strong>Created!</strong>",
      "DELETED" => "<strong>Removed!</strong>",
    ];
  }
}
