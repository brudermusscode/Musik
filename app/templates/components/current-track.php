<?php

/**
 * Gets the current Track by the id which is set in the
 * `__player_Track` cookie.
 */

use Bruder\Application\Cookie;
use Bruder\Model\Track;
use Bruder\Http\Request;
use Bruder\Model\Album;
use Bruder\Model\Playlist;
use Bruder\Model\Artist;

include _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

/**
 * @var ?string
 */
$type = filter_input(INPUT_GET, "type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var ?Album|Playlist
 */
$Relation = match ($type) {
  "album" => Album::with("tracks")->find($id),
  "playlist" => Playlist::with("tracks")->find($id),
  default => null,
};

/**
 * @var ?Track
 */
$CurrentTrack = Track::with("artistt")
  ->whereNull("deleted_at")
  ->find(Cookie::get("__player_Track"));

$art = $Relation && ($Relation instanceof Album)
  ? $Relation->art_link()
  : $CurrentTrack?->art_link();


/**
 * @var bool
 */
$track_relates = $Relation?->tracks->contains($CurrentTrack);

/**
 * @var bool
 */
$has_video = $CurrentTrack?->video;

/**
 * Begin output buffer.
 */
ob_start(); ?>

<current-track <?= $has_video ? "has-video" : "" ?>>

  <?php if ($CurrentTrack) :

    $title = $CurrentTrack?->title;
    $title_size = match (true) {
      strlen($title) >= 22 => "stdplus",
      strlen($title) >= 10 => "midler",
      default => "wide",
    };

  ?>

    <div fl fldircol gap=smol+>

      <!--- TRACK COVER/VIDEO --->
      <div fl fldircol gap=smoler posrel>

        <?php if ($has_video) : ?>
          <cover animation=fade-in-slow>
            <div hint fl jucsb alistart posabs w100>
              <p window pinline12 pblock8 text smoler semibold ttup>Läuft gerade</p>

              <action-row fl alic gap=smoler>
                <form request="track:update" update-current-track responder=simple>
                  <input type=hidden name=id value=<?= $CurrentTrack->id ?> />
                  <input type=hidden name=video value="delete" />
                  <mbutton window submit-closest material size=std icon-only has-tooltip=left>
                    <mi>reset_image</mi>
                    <div ttooltip>Video löschen</div>
                  </mbutton>
                </form>
              </action-row>
            </div>

            <video src="<?= $CurrentTrack->video_link() ?>" autoplay loop></video>
          </cover>
        <?php else : ?>
          <cover animation=zoom-in title-size=<?= $title_size ?>>
            <div hint fl jucsb alistart posabs w100>
              <p window pinline12 pblock8 text smoler semibold ttup>Läuft gerade</p>
              <action-row fl alic gap=smoler>
                <mbutton window
                  request-get="track:edit"
                  data-id="<?= $CurrentTrack->id ?>"
                  material size=std icon-only has-tooltip=left>
                  <mi>arrow_upload_progress</mi>
                  <div ttooltip>Video</div>
                </mbutton>
              </action-row>
            </div>

            <picture>
              <?php if ($art) : ?>
                <img src="<?= $art ?>" />
              <?php else : ?>
                <mi color=<?= Track::COLOR ?>>genres</mi>
              <?php endif ?>
            </picture>
          </cover>
        <?php endif ?>

        <div track-metadata fl fldircol gap=smolest alistart>
          <p title text <?= $title_size ?> bold trimt><?= $CurrentTrack->title ?></p>
          <a href="/artist/<?= $CurrentTrack->artistt->id ?>" fl alic gap=smoler hoverable background=slighter-light rounded=smol pl6 pr10 pblock4 maxw100>
            <mi text std color=<?= Artist::COLOR ?>><?= Artist::ICON ?></mi>
            <p text smol ttup regular style="text-overflow: ellipsis;overflow: hidden;white-space: nowrap;">
              <?= $CurrentTrack->artistt->name ?>
            </p>
          </a>
        </div>
      </div>

      <div fl fldircol gap=smol+>

        <!--- TRACK RELATION --->
        <?php if ($Relation && $track_relates) : ?>
          <div window-light rounded=midler p4 fl fldircol gap=smol>
            <p pinline18 pt14 pb6 text smoler semibold ttup>Läuft in</p>

            <?php

            /**
             * @var string
             */
            $icon = match ($type) {
              "album" => "album",
              "playlist" => "stacks",
              default => "genres",
            };

            ?>

            <a href="/<?= $type ?>/<?= $id ?>">
              <div pr18 pl52 pblock14 hoverable rounded=std background=slighter-dark ovhid posrel>
                <mi color=secondary style="position:absolute;bottom:-12px;left:-12px;font-size:52px;">
                  <?= $icon ?></mi>
                <p text semibold trimt><?= $Relation->name ?></p>
                <div fl alic gap=smoler>
                  <p text smoler semibold ttup><?= ucfirst($type) ?> &middot;</p>
                  <p text smoler regular ttup><?= $Relation->tracks->count() ?> Tracks</p>
                </div>
              </div>
            </a>
          </div>
        <?php endif ?>

        <!--- TRACK LYRICS --->
        <?php

        $lyrics_content = $CurrentTrack->lyrics_w_timestamps;

        if ($lyrics_content) :
          $lyrics_lines_raw = explode("&#13;&#10;&#13;&#10;", $lyrics_content ?: "");
          $lyrics_lines = [];

          foreach ($lyrics_lines_raw as $raw_line) {
            $line = explode("&#13;&#10;", $raw_line);
            $lyrics_lines[$line[0]] = $line[1];
          }

        ?>

          <lyrics-placeholder></lyrics-placeholder>
          <lyrics>
            <div title fl alic jucsb>
              <p window-light pinline14 pblock8 text smol ttup semibold>Lyrics</p>
              <div fl alic gap=smol>
                <mbutton data-action="lyrics:fullscreen" material smol icon-only>
                  <mi>resize</mi>
                </mbutton>
                <mbutton request-get="track:edit-lyrics"
                  data-id="<?= $CurrentTrack->id ?>" material smol icon-only>
                  <mi>edit</mi>
                </mbutton>
                <mbutton shadow-submit
                  request="track:update"
                  data-id="<?= $CurrentTrack->id ?>"
                  data-lyrics_w_timestamps=""
                  update-current-track
                  material smol icon-only>
                  <mi>delete</mi>
                </mbutton>
              </div>
            </div>
            <lyrics-content>
              <?php foreach ($lyrics_lines as $timestamp => $line) :

                $ts_explode = explode(":", $timestamp);
                // $is_hours = count($ts_explode) === 3; // later
                $seconds = 0;

                foreach ($ts_explode as $key => $part) {
                  if ($key === 0) $seconds += (float) $part * 60;
                  else $seconds += (float) $part;
                }

              ?>
                <line timestamp="<?= $seconds ?>">
                  <?= $line ?>
                </line>
              <?php endforeach ?>
            </lyrics-content>
          </lyrics>
        <?php else: ?>
          <div
            request-get="track:edit-lyrics"
            data-id=<?= $CurrentTrack->id ?>
            window-light pr18 pl62 pblock18 hoverable ovhid posrel rounded=mid>
            <mi color=<?= Track::COLOR ?>
              style="position:absolute;bottom:-18px;left:-6px;font-size:52px;">
              lyrics</mi>
            <div fl alic flone gap=smol jucsb>
              <p text smol semibold trimt>Lyrics hinzufügen</p>
              <mi>arrow_forward</mi>
            </div>
          </div>
        <?php endif ?>

        <!--- SPOTLIGHT --->
        <div artist fl fldircol gap=smol>
          <?php

          /**
           * @var Artist
           */
          $CurrentArtist = $CurrentTrack->artistt;

          if ($CurrentArtist && $CurrentArtist->art) :

            $artist_name = $CurrentArtist->name;
            $artist_name_size = match (true) {
              strlen($artist_name) >= 24 => "stdplus style=line-height:1.4;",
              strlen($artist_name) >= 16 => "mid style=line-height:1.2;",
              default => "wide style=line-height:1;",
            };

          ?>

            <a href="/artist/<?= $CurrentArtist->id ?>" posrel>
              <div hint fl jucsb alistart posabs w100>
                <p pinline12 pblock8 window text smoler ttup semibold>Spotlight</p>
                <action-row>
                  <mbutton
                    request-get="artist:edit"
                    data-id="<?= $CurrentArtist->id ?>"
                    material window size=std icon-only has-tooltip=left>
                    <mi>styler</mi>
                    <div ttooltip>Künstler bearbeiten</div>
                  </mbutton>
                </action-row>
              </div>

              <cover-art animation=zoom-in>
                <picture>
                  <img src="<?= $CurrentArtist->art_link() ?>" />
                </picture>
                <div metadata fl fldircol maxw100>
                  <p text <?= $artist_name_size ?> bold trimt>
                    <?= $CurrentArtist->name ?></p>
                </div>
              </cover-art>
            </a>
          <?php elseif ($CurrentArtist) : ?>
            <div window-light request-get="artist:edit" data-id=<?= $CurrentArtist->id ?> pr18 pl62 pblock18 hoverable rounded=mid ovhid posrel>
              <mi color=<?= Artist::COLOR ?>
                style="position:absolute;bottom:-12px;left:-6px;font-size:52px;"><?= Artist::ICON ?></mi>
              <div fl alic flone gap=smol jucsb>
                <p text smol semibold trimt>Künstler bearbeiten</p>
                <mi>arrow_forward</mi>
              </div>
            </div>
          <?php endif ?>
        </div>
      </div>
    </div>

  <?php

    /**
     * + No Track
     */
  else: ?>
    <div p2>
      <div background=slight-dark p42 rounded fl fldircol alistart gap=smol+>
        <mi mid mb6 style=height:2.4em;width:2.4em; circled background=slighterer-light fl alic jucc>music_off</mi>
        <p text bold ttup>Nichts gespielt</p>
        <p text>Einfach was abspielen Bruder, dann wird hier alles zu dem Track angezeigt.</p>
      </div>

      <div open-bruder mt6 pr18 pl52 pblock14 hoverable style=border-radius:18px;
        background=slight-dark ovhid posrel>
        <mi color=<?= Track::COLOR ?>
          style="position:absolute;bottom:-16px;left:-8px;font-size:52px;"><?= Track::ICON ?></mi>
        <div fl alic flone gap=smol jucsb>
          <p text smol semibold trimt>Tracks durchsuchen</p>
          <mi>arrow_forward</mi>
        </div>
      </div>
    </div>
  <?php endif; ?>
</current-track>

<?php exit($Request->success(data: ob_get_clean())); ?>