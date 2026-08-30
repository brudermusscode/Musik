<?php

/**
 * This partial can be used to display Tracks in a rather tight
 * list with the option to play a Track on click. You can't add to
 * anything like a playlist.
 */

use Bruder\Model\Track;

/**
 * @var Track $Track
 */

/**
 * @var string
 */
$action = 'play-track="' . $Track->id . '"';

/**
 * Includes the variable $show_active to make the track appear
 * different when it's being played right now.
 */
include __DIR__ . "/_show_active.php";

?>

<div fl alic jucstretch gap=smol+ track="<?= $Track->id ?>"
  <?= $show_active; ?>>

  <div option fl alic gap=smol+ flone <?= $action ?>>
    <picture midler background=primary posrel rounded=std ovhid>
      <?php if ($Track->art_link()) : ?>
        <img rounded=std src="<?= $Track->art_link() ?>" loaded=true />
      <?php else : ?>
        <mi color=light style=font-size:42px;position:absolute;bottom:-10px;left:-6px;>genres</mi>
      <?php endif; ?>
    </picture>

    <div fl alic jucsb flone>
      <div>
        <p text semibold><?= $Track->title ?></p>
        <p text smoler regular ttup fl alic gap=smoler>
          <mi midler color=secondary>artist</mi>
          <?= $Track->artist ?>
        </p>
      </div>

      <div fl alic gap=smol>
        <div background=slight-light rounded=smol pinline6 pblock4>
          <p text smoler semibold style=width:3.8em; tac ttup><?= $Track->length_formatted() ?> mins</p>
        </div>
        <mi status-icon midler></mi>
      </div>
    </div>
  </div>
</div>