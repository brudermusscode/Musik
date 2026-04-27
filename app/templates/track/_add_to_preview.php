<?php

use Bruder\Model\Playlist;
use Bruder\Model\Album;
use Bruder\Model\Track;

/**
 * @var Playlist|Album $Object
 * @var Track $Track
 */

if (!empty($Track)) :

  /**
   * @var string
   */
  $type = class_basename($Object);

  /**
   * @var bool
   */
  $track_in_it = $Object->tracks->contains($Track);

?>

  <preview
    data-id=<?= $Object->id ?>
    data-type=<?= strtolower($type) ?>
    data-action="<?= strtolower($type) . ":track:create" ?>"
    data-track-id="<?= $Track->id ?>"
    <?= $track_in_it ? "active" : "" ?>>

    <div check>
      <mi></mi>
    </div>

    <div style="position:absolute;top:12px;right:12px;z-index:2;" class=window rounded=smol
      pinline6 pblock2>
      <p text smoler ttup semibold><?= $type; ?></p>
    </div>

    <picture>
      <?php if ($Object->art) : ?>
        <img alt=" <?= $Object->name ?> Cover Art" src="/data/user/1/art/<?= $Object->art ?>" />
      <?php else : ?>
        <mi>genres</mi>
      <?php endif; ?>
    </picture>
    <div fl fldircol alic pinline6 pb8>
      <p text smol bold trimt tac><?= $Object->name ?></p>
    </div>
  </preview>

<?php endif ?>