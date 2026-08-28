<?php

use Bruder\Model\Track;
use Bruder\Model\Album;

/**
 * @var Track $Track
 */

/**
 * @var ?Album
 */
$Album = $Track->albums()->first();

?>

<song in-library has-menu track="<?= $Track->id ?>" play-track=<?= $Track->id; ?>>

  <?php include __DIR__ . "/_menu.php"; ?>

  <div content fl alic jucsb flone gap=smol+>
    <div fl alic gap=smol+ flone flex-truncate>
      <album-art <?= $Album ? "has-album" : "" ?>>
        <picture>
          <?php if ($Album && $Album->art) : ?>
            <img alt="<?= $Album->name ?> Cover Art" src="/data/user/1/art/<?= $Album->art ?>" />
          <?php else : ?>
            <mi>genres</mi>
          <?php endif; ?>
        </picture>
      </album-art>

      <div fl alic gap=smol+ flone flex-truncate>
        <div fl fldircol style="margin-top:-6px;" flex-truncate>
          <p title std text semibold trimt>
            <?= $Track->title ?>
          </p>
          <div fl alic gap=smoler>
            <p text smoler ttup regular fl alic gap=smoler>
              <mi stdplus color=secondary>artist</mi> <?= $Track->artist ?> &middot;
            </p>
            <p text smoler ttup><?= $Track->length_formatted(); ?> mins</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</song>