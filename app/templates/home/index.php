<?php

// TODO: Add queue for when playing in all songs.
// TODO: Hide tracks completly.
// TODO: Different »sort by« options.

/**
 * Simple script to insert first tracks into the database.
 */

use Illuminate\Support\Collection;
use Bruder\Model\User;
use Bruder\Model\Track;

/**
 * @var User
 */
$User = User::with("tracks.artistt")
  ->find(1);

// + Playlist banner
include TEMPLATE . "/global/_current-playlist.php";

?>

<div fl fldircol gap=smoler>
  <?php

  $show_count = true;
  $count = 1;

  /**
   * @var Collection<Track>
   */
  $Tracks = $User->tracks()
    ->with("albums")
    ->orderBy("id", "DESC")
    ->get();

  foreach ($Tracks as $index => $Track) :
    include TEMPLATE . "/track/_track.php";
  endforeach;

  ?>
</div>