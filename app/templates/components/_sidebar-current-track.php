<?php

use Bruder\Model\Track;
use Bruder\Model\Artist;
use Bruder\Model\Album;
use Bruder\Model\Playlist;

/**
 * @var Track $CurrentTrack
 * @var Album|Playlist|null $Relation
 * @var int $id
 * @var string $type
 * @var bool $has_video
 * @var bool $track_relates
 */

$art = $Relation && ($Relation instanceof Album)
  ? $Relation->art_link()
  : $CurrentTrack?->art_link();

$title = $CurrentTrack?->title;
$title_size = match (true) {
  strlen($title) >= 22 => "stdplus",
  strlen($title) >= 10 => "midler",
  default => "wide",
};

?>

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