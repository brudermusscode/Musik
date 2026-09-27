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

?>

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
      default => "wide style=line-height:1.2;",
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