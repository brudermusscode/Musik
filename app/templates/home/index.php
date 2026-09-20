<?php

// TODO: Add queue for when playing in all songs.
// TODO: Hide tracks completly.
// TODO: Different »sort by« options.

/**
 * Simple script to insert first tracks into the database.
 */

$sort = filter_var(global_param("route_param_sort"));

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

<div fl fldircol gap=smol+>
  <div fl alic gap=smol jucsb z>
    <div></div>

    <div fl alic gap=smol>
      <a href="/home/latest">
        <mbutton material icon-only has-tooltip=bottom
          <?= $sort === "latest" || !$sort ? "active" : "" ?>>
          <mi>hourglass_arrow_down</mi>
          <div ttooltip>
            Neuste
          </div>
        </mbutton>
      </a>
      <a href="/home/oldest">
        <mbutton material icon-only has-tooltip=bottom
          <?= $sort === "oldest" ? "active" : "" ?>>
          <mi>hourglass_arrow_up</mi>
          <div ttooltip>
            Älteste
          </div>
        </mbutton>
      </a>
      <a href="/home/alphabetical">
        <mbutton material icon-only has-tooltip=bottom
          <?= $sort === "alphabetical" ? "active" : "" ?>>
          <mi>sort_by_alpha</mi>
          <div ttooltip>
            Alphabetisch
          </div>
        </mbutton>
      </a>
      <a href="/home/listens">
        <mbutton material icon-only has-tooltip=bottom
          <?= $sort === "listens" ? "active" : "" ?>>
          <mi>earbud_right</mi>
          <div ttooltip>
            Liebste
          </div>
        </mbutton>
      </a>
    </div>
  </div>

  <div fl fldircol gap=smoler>
    <?php

    $show_count = true;
    $count = 1;

    $sort_map = match ($sort) {
      "alphabetical" => ["title", "ASC"],
      "listens" => ["listens", "DESC"],
      "oldest" => ["created_at", "ASC"],
      "latest" => ["created_at", "DESC"],
      default => ["created_at", "DESC"],
    };

    /**
     * @var Collection<Track>
     */
    $Tracks = $User->tracks()
      ->with("albums")
      ->orderBy($sort_map[0], $sort_map[1])
      ->get();

    foreach ($Tracks as $index => $Track) :
      include TEMPLATE . "/track/_track.php";
    endforeach;

    ?>
  </div>
</div>