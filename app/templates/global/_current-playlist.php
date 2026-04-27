<?php

use Bruder\Model\Playlist;
use Illuminate\Support\Collection;

/**
 * @var ?Playlist $Playlist
 */

/**
 *  @var Playlist
 */
$Playlist ??= new Playlist(["id" => 0]);

/**
 * @var Collection<Playlist>
 */
$Playlists = Playlist::orderBy("id", "ASC")->get();

/**
 * @var ?int
 */
$current_playlist_index = $Playlists->search(fn($p) => $p->id === $Playlist->id);

/**
 * @var ?Playlist
 */
$PreviousPlaylist = $Playlists->get($current_playlist_index - 1);

/**
 * @var ?Playlist
 */
$NextPlaylist = $Playlists->get($current_playlist_index === false ? 0 : $current_playlist_index + 1);

/**
 * Get the last palylist in case the user is at the home
 * directory. Going back a playlist should start the iteration
 * from the very end again, so it will never stop.
 */
$LastPlaylist = $Playlists->last();

/**
 * @var string
 */

if ($PreviousPlaylist || $NextPlaylist)
  $previous_playlist_href =
    in_array(CURRENT_PAGE, ["", "home"])
    ? "/playlist/$LastPlaylist->id"
    : (
      !$PreviousPlaylist
      ? "/"
      : "/playlist/$PreviousPlaylist->id"
    );

?>

<header dno>
  <div fl fldircol gap=smol alic jucc>
    <div style="width:200px;height:8px;" background=slighter-light rounded></div>
    <div style="width:200px;height:8px;" background=slighter-light rounded></div>
  </div>

  <div fl fldircol gap=smol alic jucc>
    <div style="width:200px;height:8px;" background=slighter-light rounded></div>
    <div style="width:200px;height:8px;" background=slighter-light rounded></div>
  </div>
</header>

<bruder <?= in_array(CURRENT_PAGE, ["album", "artist"]) ? "scroll-manipulated" : "" ?>>
  <current-playlist fl alic jucc gap>
    <a href="<?= $previous_playlist_href ?? "/" ?>">
      <mbutton previous-playlist material icon-only background=slight-light hoverable>
        <mi>keyboard_arrow_left</mi>
      </mbutton>
    </a>

    <p playlist-current text black ttup trimt>
      <?php if (CURRENT_PAGE === "playlist") : ?>
        <?= $Playlist->id ? $Playlist->name : "Keine Ahnung" ?>
      <?php elseif (in_array(CURRENT_PAGE, ["albums", "album"])) : ?>
        <?= isset($Album) ? $Album->name : "Alle Alben" ?>
      <?php elseif (in_array(CURRENT_PAGE, ["artists", "artist"])) : ?>
        <?= isset($Artist) ? $Artist->name : "Alle Künstler" ?>
      <?php elseif (CURRENT_PAGE === "") : ?>
        Alle Songs
      <?php endif; ?>
    </p>

    <a href="<?= !$NextPlaylist ? "/" : "/playlist/$NextPlaylist->id" ?>">
      <mbutton next-playlist material icon-only background=slight-light hoverable>
        <mi>keyboard_arrow_right</mi>
      </mbutton>
    </a>
  </current-playlist>

  <?php

  /**
   * @var string
   */
  $track_explore_action = "track:explore";

  ?>

  <search-tools>
    <input autofocus floating mb24 data-action="<?= $track_explore_action ?>" placeholder="Titel, Artist, oder sonst was..." />

    <div fl gap=smol+ alistart jucstretch>
      <div left-content fl fldircol gap=smol>
        <p text smol bold ttup slight pinline4>Machen</p>

        <mbutton material size=wide icon-only background=secondary color=tertiary-text
          close-bruder request-get="playlist:new">
          <mi>library_add</mi>
        </mbutton>

        <mbutton material size=wide icon-only background=secondary color=tertiary-text
          close-bruder request-get="album:new">
          <mi>control_point_duplicate</mi>
        </mbutton>
      </div>

      <div right-content data-react="<?= $track_explore_action ?>" tracks play-only flone>
        <div posrel pinline24 fl fldircol gap=smol>
          <p text smol bold ttup slight pinline4>jobs</p>
          <job-button background=slight-light hoverable fl alic jucc gap=smol+ rounded pinline24 pblock36
            execute-job="sync-albums">
            <mi midler>album</mi>
            <div loader class="loader-3"><span></span></div>
            Sync albums & cover art
          </job-button>
        </div>
      </div>
    </div>
  </search-tools>
</bruder>