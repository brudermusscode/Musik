<?php

// TODO: Add queue for when playing in all songs.
// TODO: Hide tracks completly.
// TODO: Different »sort by« options.

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
  <sort fl alic jucsb gap=smoler>
    <a href="/home/latest" flone>
      <mbutton material has-icon=left has-tooltip=bottom
        <?= $sort === "latest" || !$sort ? "active" : "" ?>>
        <mi>hourglass_arrow_down</mi>
        <span hide-tablet>Neuste</span>
        <div ttooltip>
          Neuste
        </div>
      </mbutton>
    </a>
    <a href="/home/oldest" flone>
      <mbutton material has-icon=left has-tooltip=bottom
        <?= $sort === "oldest" ? "active" : "" ?>>
        <mi>hourglass_arrow_up</mi>
        <span hide-tablet>Älteste</span>
        <div ttooltip>
          Älteste
        </div>
      </mbutton>
    </a>
    <a href="/home/title" flone>
      <mbutton material has-icon=left has-tooltip=bottom
        <?= $sort === "title" ? "active" : "" ?>>
        <mi>sort_by_alpha</mi>
        <span hide-tablet>Titel</span>
        <div ttooltip>
          Titel
        </div>
      </mbutton>
    </a>
    <a href="/home/artist" flone>
      <mbutton material has-icon=left has-tooltip=bottom
        <?= $sort === "artist" ? "active" : "" ?>>
        <mi>artist</mi>
        <span hide-tablet>Artist</span>
        <div ttooltip>
          Artist
        </div>
      </mbutton>
    </a>
    <a href="/home/listens" flone>
      <mbutton material has-icon=left has-tooltip=bottom
        <?= $sort === "listens" ? "active" : "" ?>>
        <mi>earbud_right</mi>
        <span hide-tablet>Liebste</span>
        <div ttooltip>
          Meist gehört
        </div>
      </mbutton>
    </a>
  </sort>

  <div fl fldircol gap=smoler>
    <?php

    $show_count = true;
    $count = 1;

    $sort_map = match ($sort) {
      "title" => ["title", "ASC"],
      "artist" => ["artist", "ASC"],
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