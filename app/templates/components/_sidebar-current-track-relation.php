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

if ($Relation && $track_relates) : ?>

  <div window-light rounded=midler p4 fl fldircol gap=smol>
    <p pinline18 pt14 pb6 text smoler semibold ttup>Läuft in</p>

    <?php

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

<?php endif; ?>