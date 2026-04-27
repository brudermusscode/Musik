<?php

use Illuminate\Support\Collection;
use Bruder\Model\Album;
use Bruder\Model\Artist;
use Bruder\Time\Time;

/**
 * @var int
 */
$id = filter_var($GLOBALS["route_param_id"], FILTER_DEFAULT);

/**
 * @var ?Album
 */
$Album = Album::with("tracks.artistt")
  ->with("bookmark")
  ->orderBy("id", "DESC")
  ->find((int) $id);

if (!$Album) :
  include UNAVAILABLE;
else :

  /**
   * @var Collection<Track>
   */
  $Tracks = $Album->tracks->sortBy("album_tracks.id");

  /**
   * + Playlist banner
   */
  include TEMPLATE . "/global/_current-playlist.php"; ?>

  <?php

  /**
   * Create an array with all tracks as a queue.
   */
  $queue_ids = "";

  if ($Tracks->count()) :
    foreach ($Tracks as $Track)
      $queue_ids .= "," . $Track->id;

    /**
     * Remove the first , from the string.
     */
    $queue_ids = ltrim($queue_ids, $queue_ids[0]);
  endif; ?>
  <input type=hidden name=playlist-queue-ids value="<?= $queue_ids ?>" />

  <?php

  /**
   * This will be used to show additional informatin about this
   * album in the right sidebar.
   */ ?>
  <page album data-type=album data-id=<?= $Album->id ?>>

    <top-banner scroll-manipulated fl jucstart alistretch>
      <picture art elevated=smol posrel>
        <img src="/data/user/1/art/<?= $Album->art ?>">

        <div top-menu>
          <?php

          $track_count = $Album->tracks->count(); ?>
          <p text semibold tracks tar>
            <?= $track_count . "<br><span text smoler ttup regular>track" . ($track_count > 1 ? "s" : "") . "</span>" ?>
          </p>
        </div>
      </picture>

      <div metadata fl fldircol alistart jucsb gap=smol+>
        <div fl alistart jucend gap=smol flone w100>
          <form <?= $Album->bookmark
                  ? 'request="bookmark:delete" interchange-action="bookmark:create"'
                  : 'request="bookmark:create" interchange-action="bookmark:delete"'
                ?> update-library toggle-button-active responder=simple>
            <input type=hidden name=id value=<?= $Album->id ?> />
            <input type=hidden name=type value=album />
            <mbutton <?= $Album->bookmark ? "active" : "" ?> submit-closest material background=slight-light icon-only has-tooltip=bottom>
              <mi>bookmark_stacks</mi>
              <div ttooltip text semibold>In o. aus Bib</div>
            </mbutton>
          </form>

          <mbutton request-get="album:edit" data-id="<?= $Album->id ?>" material background=slight-light icon-only has-tooltip=bottom>
            <mi>edit</mi>
            <div ttooltip text semibold>Bearbeiten</div>
          </mbutton>

          <mbutton request-get="album:track:new" data-id="<?= $Album->id ?>" material icon-only background=slight-light hoverable has-tooltip=bottom>
            <mi>list_alt_add</mi>
            <div ttooltip text semibold>Mehr Musik bitte</div>
          </mbutton>

          <form request="album:delete" redirect="/" update-library responder=simple>
            <input type=hidden name=id value="<?= $Album->id ?>" />
            <mbutton submit-closest material icon-only background=primary color=light-red no-hover-shadow has-tooltip=bottom>
              <mi>delete_forever</mi>
              <div ttooltip text semibold>Löschen</div>
            </mbutton>
          </form>

          <p pr18 pl12 pblock12 ml8 background=hover-dark rounded=wide text smol fl alic gap=smol>
            <mi>directory_sync</mi>
            <?= Time::ago($Album->updated_at ?? $Album->created_at); ?>
          </p>
        </div>

        <div fl fldircol alistart gap=smol>
          <?php

          /**
           * Evaluate a good size for the length of the album title to
           * show it in.
           */
          $album_strlen = strlen($Album->name);
          $title_size = match (true) {
            $album_strlen >= 24 => "midplus",
            $album_strlen >= 16 => "wide",
            $album_strlen >= 14 => "wider",
            $album_strlen >= 10 => "widest",
            default => "widester"
          };

          ?>

          <p album-name text <?= $title_size ?> bold lh1 word-break><?= $Album->name ?></p>
          <div fl alic gap=smoler>
            <a href="/albums">
              <p text smol regular ttup background=quadro color=quadro-text pr12 pl8 pblock6 rounded=smol fl alic gap=smol>
                <mi midler>album</mi>
                Album
              </p>
            </a>

            <?php

            /**
             * @var ?Collection<Artist>
             */
            $Artists = $Album->artists();

            /**
             * @var bool
             */
            $has_many_artists = $Artists->count() - 1;

            if ($Artists->count()) : ?>
              <a href="<?= "/artist/" . $Artists->first()->id ?>">
                <p text ttup smol regular fl alic gap=smol pr12 pl8 pblock6 rounded=smol background=slight-light hoverable>
                  <mi midler>artist</mi>
                  <?= $Artists->first()->name . ($has_many_artists ? " & $has_many_artists more" : "") ?>
                </p>
              </a>
            <?php endif ?>
          </div>
        </div>
      </div>
    </top-banner>

    <playlist-action-bar fl aliend jucsb gap dno>
      <div fl alic gap=smol>

      </div>
      <div fl alic gap=smol>

      </div>
    </playlist-action-bar>

    <?php

    /**
     * + No Tracks
     */
    if (!$Album->tracks->count()) : ?>
      <div style="border:2px dotted #de54fc;" background=hover-dark m42 fl fldircol jucc alic gap pblock62 rounded=wide>
        <div fl fldircol gap=smolest tac>
          <p text midler semibold>Bruder, nichts drin.</p>
          <p text>Such was schönes aus</p>
        </div>
        <mbutton request-get="album:track:new" data-id="<?= $Album->id ?>"
          material size=mid background=primary color=primary-text icon-only ovhid>
          <mi style="font-size:52px;position:absolute;bottom:-12px;left:-12px;">list_alt_add</mi>
        </mbutton>
      </div>
    <?php endif; ?>

    <playlist disable-user-selection>
      <form data-form="playlist:track:reorder">
        <input type=hidden name=id value="<?= $Playlist->id ?>" />

        <div fl fldircol gap=smoler>
          <?php

          $song_playlist_index = 0;
          $show_count = true;
          $count = 1;

          /**
           * + All Tracks
           */
          foreach ($Tracks as $index => $Track) :
            include TEMPLATE . "/track/_track.php";
          endforeach; ?>
        </div>
      </form>
    </playlist>

  </page>

<?php endif; ?>