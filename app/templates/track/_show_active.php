<?php

use Bruder\Application\Cookie;
use Bruder\Model\Track;

/**
 * @var Track $Track
 * @var ?bool $track_playable
 */

/**
 * @var bool
 */
$track_playable ??= true;

/**
 * @var string
 */
$show_active =
  Cookie::get("__player_active") && Cookie::get("__player_Track") == $Track->id
  ? "active"
  : (Cookie::get("__player_Track") == $Track->id ? "paused" : "");
