<?php

use Bruder\Model\Album;
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
    <input autofocus floating mb24 data-action="<?= $track_explore_action ?>" placeholder="Titel, Playlisten »   « Künstler, Alben" />

    <div fl gap=smol+ alistart jucstretch>
      <div right-content data-react="<?= $track_explore_action ?>" tracks play-only flone>
        <p text smol bold ttup tac slight>Tipp was ein, um zu suchen</p>
      </div>
    </div>
  </search-tools>
</bruder>