<?php

/**
 * This partial can be included for searching through tracks for
 * either albums or playlists and add them. Depening on if the
 * track is a relation to the corresponding object already, it
 * will be shown and act differently on interacion.
 */

use Bruder\Model\Track;
use Bruder\Model\Playlist;

/**
 * @var Track $Track
 * @var ?Playlist $Playlist
 * @var ?int $id
 */

// # Playlist

/**
 * @var Playlist
 */
$Playlist ??= null;

/**
 * @var bool
 */
$track_in = $Playlist?->tracks->contains($Track);

/**
 * Includes variable $show_active which determines the view of the
 * current track.
 */
include TEMPLATE . "/track/_show_active.php";

/**
 * @var string
 */
$type = "playlist";

?>

<div track=<?= $Track->id ?> fl alic jucstretch gap=smol+ <?= $show_active ?>>
  <div option fl alic jucsb gap=smol+ flone
    data-action="<?= $type . ":track:create" ?>"
    data-type=<?= $type ?>
    data-id=<?= $Playlist->id ?>
    data-track-id=<?= $Track->id ?>
    <?= $track_in ? "active" : "" ?>>
    <div fl alic gap=smol+>
      <mi status-icon></mi>
      <p text smolplus semibold><?= $Track->title ?></p>
    </div>

    <div background=slight-light rounded=smol pinline6 pblock4>
      <p text smoler regular ttup><?= $Track->artist ?></p>
    </div>
  </div>

  <mbutton play-track="<?= $Track->id ?>" material icon-only>
    <mi></mi>
  </mbutton>
</div>