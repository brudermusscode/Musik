<?php

use Bruder\Model\Track;
use Bruder\Model\Album;

/**
 * @var Track $Track
 */

/**
 * @var ?Album
 */
$Album = $Track->album;

?>

<song simple track="<?= $Track->id ?>">
  <div content fl alic jucsb flone gap=smol+>
    <div fl alic gap=smol+ flone>
      <album-art>
        <picture>
          <?php if ($Album && $Album->art) : ?>
            <img alt="<?= $Album->name ?> Cover Art" src="/data/user/1/art/<?= $Album->art ?>" />
          <?php else : ?>
            <mi>genres</mi>
          <?php endif; ?>
        </picture>
      </album-art>

      <div fl alic gap=smol+ flone>
        <div fl fldircol style="margin-top:-6px;">
          <p title <?= strlen($Track->title) > 40 ? "std" : "stdplus" ?> text semibold trimt>
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