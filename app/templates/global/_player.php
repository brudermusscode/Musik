<?php

use Bruder\Application\Cookie;

?>

<show-player data-action="player:hide" <?= Cookie::get("__player_collapsed") == 1 ? "collapsed" : "" ?>>
  <mi mid>music_cast</mi>
</show-player>