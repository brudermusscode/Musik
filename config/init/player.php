<?php

use Bruder\Application\Cookie;
use Bruder\Model\Track;

define("DEFAULT_VOLUME", 0.2);
define("CURRENT_VOLUME", (float) Cookie::get("__player_volume") ?? DEFAULT_VOLUME);
define("CURRENT_TRACK", Track::find(Cookie::get("__player_current_Track") ?? 0));
