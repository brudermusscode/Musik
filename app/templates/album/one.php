<?php

use Illuminate\Support\Collection;
use Bruder\Model\Album;
use Bruder\Model\Artist;
use Bruder\Model\Track;
use Bruder\Time\Time;

/**
 * @var int
 */
$id = filter_var($GLOBALS["route_param_id"]);

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
        <div fl alic jucsb gap=smol w100>
          <div fl alic gap=smoler>
            <p text smol semibold regular ttup background=<?= $Album::COLOR ?> color=<?= $Album::COLOR; ?>-text pr14 pl10 pblock6 rounded=std fl alic gap=smol>
              <mi midler>album</mi>
              Release
            </p>
          </div>

          <div fl alic gap=smol jucend>
            <form <?= $Album->bookmark
                    ? 'request="bookmark:delete" interchange-action="bookmark:create"'
                    : 'request="bookmark:create" interchange-action="bookmark:delete"'
                  ?> update-library reload toggle-button-active responder=simple>
              <input type=hidden name=id value=<?= $Album->id ?> />
              <input type=hidden name=type value=album />
              <mbutton <?= $Album->bookmark ? "active" : "" ?> submit-closest material icon-only has-tooltip=bottom>
                <mi><?= $Album->bookmark ? "remove" : "add" ?></mi>
                <div ttooltip text semibold>In o. aus Bib</div>
              </mbutton>
            </form>

            <mbutton request-get="album:edit" data-id="<?= $Album->id ?>" material icon-only has-tooltip=bottom>
              <mi>edit</mi>
              <div ttooltip text semibold>Bearbeiten</div>
            </mbutton>

            <mbutton request-get="album:track:new" data-id="<?= $Album->id ?>" material icon-only has-tooltip=bottom>
              <mi>list_alt_add</mi>
              <div ttooltip text semibold>Mehr Musik bitte</div>
            </mbutton>

            <dot-divider></dot-divider>

            <form request="album:delete" redirect="/" update-library responder=simple>
              <input type=hidden name=id value="<?= $Album->id ?>" />
              <mbutton submit-closest material icon-only has-tooltip=bottom>
                <mi>delete_forever</mi>
                <div ttooltip text semibold>Löschen</div>
              </mbutton>
            </form>
          </div>
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

          <p album-name text <?= $title_size ?> bold lh1 word-break>
            <?= $Album->name ?></p>
        </div>
      </div>
    </top-banner>

    <playlist-action-bar fl aliend jucsb gap pinline12>
      <div fl alic gap=smol>
        <?php

        /**
         * @var ?Collection<Artist>
         */
        $Artists = $Album->artists();

        $has_many_artists = $Artists->count() - 1;

        if ($Artists->count()) : ?>
          <a href="<?= "/artist/" . $Artists->first()->id ?>">
            <p text ttup smol regular fl alic gap=smol pl10 pr16 pblock6 rounded=std background=slighter-light hoverable>
              <mi midler color=<?= Artist::COLOR ?>>artist</mi>
              <?= $Artists->first()->name . ($has_many_artists ? " & $has_many_artists more" : "") ?>
            </p>
          </a>
        <?php endif ?>
      </div>

      <div fl alic gap=smol>
        <?php if ($Album->tracks->count()) : ?>
          <p background=slighter-light pinline14 pblock10 rounded=std text smol semibold><?= $Album->tracks->count() ?? 0 ?> track<?= $Album->tracks->count() > 1 ? "s" : "" ?></p>

          <p text smol bold>&middot;</p>

          <?php

          $sum = $Album->tracks->sum("length_seconds") / 60;
          $sum_explode = explode(".", $sum);
          $sum_minutes = $sum_explode[0];
          $sum_seconds = $sum_explode[1] % 60;

          ?>
          <div fl alic gap=smoler has-tooltip=left curwhat>
            <mi>motion_play</mi>
            <p text smol><?= $sum_minutes . "min, " . $sum_seconds . "sec" ?></p>
            <div ttooltip text semibold>Gesamte Spiellänge</div>
          </div>

          <p text smol bold>&middot;</p>
        <?php endif; ?>

        <div fl alic gap=smoler has-tooltip=left curwhat>
          <mi>directory_sync</mi>
          <p text smol>
            <?= Time::ago($Album->updated_at ?? $Album->created_at); ?></p>
          <div ttooltip text semibold>Letztes Update</div>
        </div>
      </div>
    </playlist-action-bar>

    <?php

    /**
     * + No Tracks
     */
    if (!$Album->tracks->count()) : ?>
      <div background=slight-dark fl fldircol jucc alic gap pblock62 rounded=wide>
        <div fl fldircol gap=smolest tac>
          <p text semibold>Bruder, nichts drin.</p>
          <p text>Such was schönes aus</p>
        </div>
        <mbutton request-get="album:track:new" data-id="<?= $Album->id ?>"
          material size=mid icon-only ovhid>
          <mi style="font-size:42px;position:absolute;bottom:-6px;left:-4px;">list_alt_add</mi>
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