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
  ->find(Cookie::get("__player_Track"));

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

<current-track <?= $has_video ? "has-video" : "" ?> fl fldircol gap>

  <?php if ($CurrentTrack) : ?>

    <div fl fldircol gap=smol>
      <cover <?= $has_video ? "animation=fade-in-slow" : "animation=zoom-in" ?>>
        <div hint fl jucsb alic pinline10 pblock8 posabs style="top:0;left:0;z-index:2;width:100%;">
          <p pinline12 pblock8 text smoler semibold ttup background=hover-dark rounded=smolplus>Läuft gerade</p>

          <action-row fl alic gap=smoler>
            <?php if (!$has_video) : ?>
              <mbutton request-get="track:edit" data-id="<?= $CurrentTrack->id ?>" material size=std icon-only background=hover-dark has-tooltip=bottom>
                <mi>arrow_upload_progress</mi>
                <div ttooltip>Video</div>
              </mbutton>
            <?php else : ?>
              <form request="track:update" update-current-track responder=simple>
                <input type=hidden name=id value=<?= $CurrentTrack->id ?> />
                <input type=hidden name=video value="delete" />
                <mbutton submit-closest material size=std icon-only background=hover-dark has-tooltip=bottom>
                  <mi>reset_image</mi>
                  <div ttooltip>Video löschen</div>
                </mbutton>
              </form>
            <?php endif ?>
          </action-row>
        </div>

        <?php if ($CurrentTrack->video) : ?>
          <video src="<?= $CurrentTrack->video_link() ?>" autoplay loop></video>
        <?php else : ?>
          <picture>
            <?php if ($CurrentTrack?->art_link()) : ?>
              <img src="<?= $CurrentTrack->art_link() ?>" />
            <?php else : ?>
              <mi color=<?= Track::COLOR ?>>genres</mi>
            <?php endif ?>
          </picture>
        <?php endif ?>
      </cover>

      <div track-metadata fl fldircol gap=smolest alistart
        <?= $has_video ? "animation=fade-in-slow" : "animation=fade-in" ?>>

        <?php

        $title = $CurrentTrack->title;
        $title_size = match (true) {
          strlen($title) >= 22 => "stdplus",
          strlen($title) >= 12 => "midler",
          default => "wide",
        };

        ?>

        <p text <?= $title_size ?> bold trimt><?= $CurrentTrack->title ?></p>
        <a href="/artist/<?= $CurrentTrack->artistt->id ?>" fl alic gap=smoler hoverable background=slighter-light rounded=smol pl6 pr10 pblock4 maxw100>
          <mi text std color=<?= Artist::COLOR ?>><?= Artist::ICON ?></mi>
          <p text smol ttup regular style="text-overflow: ellipsis;overflow: hidden;white-space: nowrap;">
            <?= $CurrentTrack->artistt->name ?>
          </p>
        </a>
      </div>
    </div>

    <div pinline24 fl fldircol gap=midler>
      <?php if ($Relation && $track_relates) : ?>
        <div fl fldircol gap=smol>
          <p pinline4 text smoler semibold ttup>Läuft in</p>

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
            <div pr18 pl52 pblock14 hoverable rounded=std background=slight-dark ovhid posrel>
              <mi color=secondary style="position:absolute;bottom:-12px;left:-12px;font-size:52px;"><?= $icon ?></mi>
              <p text semibold trimt><?= $Relation->name ?></p>
              <div fl alic gap=smoler>
                <p text smoler semibold ttup><?= ucfirst($type) ?> &middot;</p>
                <p text smoler regular ttup><?= $Relation->tracks->count() ?> Tracks</p>
              </div>
            </div>
          </a>
        </div>
      <?php elseif ($Relation && !$track_relates) : ?>
        <p text semibold flone>Track gehört nicht zur gesendeten Relation.</p>
      <?php endif ?>

      <div artist fl fldircol gap=smol>
        <p pinline4 text smoler semibold ttup>Über den Künstler</p>
        <?php

        /**
         * @var Artist
         */
        $CurrentArtist = $CurrentTrack->artistt;

        if ($CurrentArtist && $CurrentArtist->art) : ?>

          <a href="/artist/<?= $CurrentArtist->id ?>">
            <cover-art animation=zoom-in>
              <picture>
                <img src="<?= $CurrentArtist->art_link() ?>" />
              </picture>
              <div metadata fl fldircol>
                <p text midler bold trimt><?= $CurrentArtist->name ?></p>
              </div>
            </cover-art>
          </a>
        <?php elseif ($CurrentArtist) : ?>
          <div request-get="artist:edit" data-id=<?= $CurrentArtist->id ?> pr18 pl52 pblock14 hoverable rounded=std background=slight-dark ovhid posrel>
            <mi color=<?= Artist::COLOR ?>
              style="position:absolute;bottom:-12px;left:-12px;font-size:52px;"><?= Artist::ICON ?></mi>
            <div fl alic flone gap=smol jucsb>
              <p text smol semibold trimt>Künstler bearbeiten</p>
              <mi>arrow_forward</mi>
            </div>
          </div>
        <?php endif ?>
      </div>
    </div>

  <?php

    /**
     * + No Track
     */
  else: ?>
    <div background=slight-dark p42 rounded fl fldircol alistart gap=smol+>
      <mi mid mb6 style=height:2.4em;width:2.4em; circled background=slighterer-light fl alic jucc>music_off</mi>
      <p text bold ttup>Nichts gespielt</p>
      <p text>Einfach was abspielen Bruder, dann wird hier alles zu dem Track angezeigt.</p>
    </div>

    <div open-bruder mt6 pr18 pl52 pblock14 hoverable rounded=std
      background=slight-dark ovhid posrel>
      <mi color=<?= Track::COLOR ?>
        style="position:absolute;bottom:-12px;left:-12px;font-size:52px;"><?= Track::ICON ?></mi>
      <div fl alic flone gap=smol jucsb>
        <p text smol semibold trimt>Tracks durchsuchen</p>
        <mi>arrow_forward</mi>
      </div>
    </div>
  <?php endif; ?>

</current-track>

<?php exit($Request->success(data: ob_get_clean())); ?>