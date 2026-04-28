<?php

// TODO: Upload custom art for playlists.
// TODO: Add mood tag for playlists (cry/happy/…).
// TODO: Change queue when reordering songs in playlist.

use Illuminate\Support\Collection;
use Bruder\Model\Playlist;
use Bruder\Time\Time;

/**
 * @var ?Playlist
 */
$Playlist = Playlist::with("tracks")
  ->find($GLOBALS["route_param_id"]);

if (!$Playlist) :
  include UNAVAILABLE;
else :

  /**
   * @var string
   */
  $add_new_tracks_button = <<<HTML
    request-get="playlist/track/new" data-id="$Playlist->id"
  HTML;

  /**
   * @var Collection<PlaylistTrack>
   */
  $PlaylistTracks = $Playlist->playlist_tracks
    ->sortBy(fn($PlaylistTrack) => $PlaylistTrack->playlist_index);

  /**
   * + Playlist banner
   */
  include TEMPLATE . "/global/_current-playlist.php"; ?>

  <page playlist data-type=playlist data-id=<?= $Playlist->id ?>>

    <?php

    /**
     * Create an array with all tracks as a queue.
     */
    $queue_ids = "";

    if ($PlaylistTracks->count()) :
      foreach ($PlaylistTracks as $PlaylistTrack)
        $queue_ids .= "," . $PlaylistTrack->track->id;

      /**
       * Remove the first , from the string.
       */
      $queue_ids = ltrim($queue_ids, $queue_ids[0]);
    endif; ?>
    <input type=hidden name=playlist-queue-ids value="<?= $queue_ids ?>" />

    <playlist-action-bar fl aliend jucsb gap pinline4>
      <div fl alic gap=smol>

        <picture playlist-cover disabled>
          <mi>art_track</mi>
        </picture>

        <div tag background=secondary color=secondary-text>
          <p text midler bold><?= $Playlist->tracks->count(); ?></p>
          <p text smoler regular ttup>Tracks</p>
        </div>
        <div tag background=slight-light>
          <p text midler bold><?= $Playlist->tracks->sum("listens") ?></p>
          <p text smoler regular ttup>Mal Gehört</p>
        </div>
      </div>
      <div fl alic gap=smol>
        <mbutton dno material icon-only background=red color=light-red>
          <mi>emoji_symbols</mi>
        </mbutton>
        <mbutton <?= $add_new_tracks_button ?> has-tooltip=bottom material icon-only background=slight-light hoverable>
          <mi>list_alt_add</mi>
          <div ttooltip>Musik rein hier</div>
        </mbutton>
        <form request-do="playlist:delete" redirect="/" update-library responder=simple>
          <input type=hidden name=id value="<?= $Playlist->id ?>" />
          <mbutton submit-closest has-tooltip=bottom material icon-only background=red color=light no-hover-shadow>
            <mi>delete_forever</mi>
            <div ttooltip>Lösch dich</div>
          </mbutton>
        </form>
        <p pr18 pl12 pblock12 background=slighter-light rounded=wide text smol fl alic gap=smol>
          <mi>directory_sync</mi>
          <?= Time::ago($Playlist->updated_at ?? $Playlist->created_at); ?>
        </p>
      </div>
    </playlist-action-bar>

    <?php

    /**
     * + No Tracks in this Playlist
     */
    if (!$Playlist->tracks->count()) : ?>
      <div fl fldircol jucc alic gap pblock62 background=slighterer-light rounded=wide>
        <div fl fldircol gap=smolest tac>
          <p text midler semibold>Bruder, nichts drin.</p>
          <p text>Klick auf den Button und such was schönes aus</p>
        </div>
        <mbutton <?= $add_new_tracks_button ?>
          material size=wide background=primary color=primary-text has-icon=left>
          <mi>music_note_add</mi>
          Add tracks
        </mbutton>
      </div>
    <?php endif; ?>

    <playlist disable-user-selection>
      <form data-form="playlist:track:reorder">
        <input type=hidden name=id value="<?= $Playlist->id ?>" />

        <div fl fldircol gap=smoler>
          <?php

          $song_playlist_index = 0;

          /**
           * + Show Tracks
           */
          foreach ($PlaylistTracks as $index => $PlaylistTrack) :

            /**
             * @var Track
             */
            $Track = $PlaylistTrack->track;

            include TEMPLATE . "/track/_track.php";
          endforeach; ?>

          <?php

          /**
           * Show this placeholder song to allow dragging a song in the
           * playlist to the really end of it.
           */
          if ($Playlist->tracks->count()) :    ?>
            <song style="height:60px;"></song>
          <?php endif; ?>
        </div>
      </form>
    </playlist>

  </page>

<?php endif; ?>