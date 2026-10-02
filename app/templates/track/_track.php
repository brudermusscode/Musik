<?php

// TODO: Add created_on to track in all songs list.
// TODO: Add times_listened to tracks.
// TODO: Add metadata information overview.
// TODO: Update track metadata (also in actual file).

use Illuminate\Support\Collection;
use Bruder\Model\Track;
use Bruder\Model\Album;
use Bruder\Model\Playlist;
use Bruder\Time\Time;

/**
 * @var Track $Track
 * @var ?Playlist $Playlist
 * @var int $index
 * @var int $song_playlist_index
 * @var bool $track_playable
 */

/**
 * @var ?Collection<Album>
 */
$Albums = $Track->albums;

/**
 * @var ?Album
 */
$Album = $Albums?->first();

$track_playable ??= true;
$show_count ??= false;
$song_playlist_index ??= 0;
$show_menu ??= true;
$show_playing ??= true;
$show_listens ??= false;
$length_minutes = $Track->length_seconds / 60;
$in_playlist = $Playlist->id ?? null;
$in_album ??= CURRENT_PAGE === "album";
$in_artist ??= CURRENT_PAGE === "artist";
$no_left_action = !$in_playlist && !$show_count && !$in_album && !$in_artist;
$show_cover ??= true;

/**
 * Includes the variable $show_active to make the track appear
 * different when it's being played right now.
 */
include __DIR__ . "/_show_active.php";

?>

<song
  <?= $show_active ?>
  <?= $show_menu ? "has-menu" : "" ?>
  <?= $in_album ? "in-album" : "" ?>
  <?= !$track_playable ? "display-only" : "" ?>
  track="<?= $Track->id ?>">

  <?php

  /**
   * When reordering songs in a playlist, this comes in handy as
   * we can set a new index for each song based on the place in
   * the array of playlist_song_index[].
   */
  if ($in_playlist) : ?>
    <!---
  Input to track the order inside the playlist. JS will call the script to save a new order automatically when dragging a song to a new place. --->
    <input type=hidden name="playlist_song_index[]" value=<?= $Track->id ?> />
  <?php endif;

  /**
   * + Context Menu
   */
  if ($show_menu)
    include __DIR__ . "/_menu.php"; ?>

  <div window
    <?= $no_left_action ? "style=\"padding:4px;padding-right:14px;pointer-events:none;border-radius: 14px 12px 12px 14px;\" background=slighter-light" : "" ?>
    <?= $track_playable ? 'play-track="' . $Track->id . '"' : "" ?>
    content fl alic jucsb gap=smol+ flone>
    <div fl alic <?= $no_left_action ? "gap=smol" : "gap" ?> flone flex-truncate>
      <?php if ($show_count) : ?>
        <p text smol track-count style=width:40px;rotate:-90deg;margin-left:-10px;margin-right:-14px; text smoler ttup bold tac><?= $count++; ?></p>
      <?php endif; ?>

      <?php if ($in_playlist): ?>
        <move-track>
          <mi>drag_indicator</mi>
        </move-track>
      <?php endif; ?>

      <?php if (!$in_album && $show_cover) :

        $track_art = $Track->art_link();

      ?>
        <album-art <?= $no_left_action ? "style=margin-right:8px;" : "" ?>>
          <picture size=smol>
            <?php if ($track_art) : ?>
              <img src="<?= $track_art ?>" />
            <?php else : ?>
              <mi>genres</mi>
            <?php endif; ?>
          </picture>
        </album-art>
      <?php endif; ?>

      <div fl alic gap=smol+ flone flex-truncate>
        <div fl alic jucsb gap flex-truncate>
          <div flex-truncate>
            <div fl alic gap=smol>
              <p text smoler ttup regular>
                <?= $Track->artistt->name ?></p>
            </div>
            <p title text semibold trimt style="line-height:1.2;">
              <?= $Track->title ?></p>
          </div>
          <div right-info fl fldircol aliend jucsb gap=smol>
            <div fl alic gap=smoler>
              <?php if ($Track->lyrics_w_timestamps) : ?>
                <div window pinline8 pblock4 rounded=smol>
                  <mi color=tertiary smoler>lyrics</mi>
                </div>
              <?php endif; ?>
              <div window pinline8 pblock4 rounded=smol>
                <p text smoler semibold><?= $Track->length_formatted(); ?> mins</p>
              </div>
              <div window pl6 pr8 pblock4 rounded=smol fl alic gap=smoler>
                <mi smoler color=primary>earbud_right</mi>
                <p text smoler semibold><?= $Track->listens ?: 0  ?></p>
              </div>
            </div>
            <p text regular smoler slight>
              vor <?= Time::ago($Track->created_at); ?></p>
          </div>
        </div>
      </div>
    </div>
    <mi status-icon mid></mi>
  </div>
</song>

<?php

$song_playlist_index++;

?>