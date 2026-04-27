<?php

use Bruder\Model\Album;

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

/**
 * @var ?Playlist
 */
$Album = Album::with("tracks")->find($id);

/**
 * Begin output buffer.
 */
ob_start();

?>

<popup-close>
  <mi>celebration</mi>
</popup-close>

<popup-container>
  <popup-container__content mid pinline42 pb32 pt42 posrel style=z-index:2; elevated>

    <div fl gap=smol alic flex-wrap>
      <p text bold no-word-wrap>Add music to</p>
      <div pl6 pr12 pblock6 rounded=smol fl alic gap=smol background=slight-light>
        <mi>album</mi>
        <p text semibold trimt><?= $Album->name ?></p>
      </div>
    </div>

    <input autofocus floating placeholder="Titel, Artist, oder sonst was..."
      data-action="album:track:explore" data-id="<?= $Album->id ?>" />

    <div fl fldircol gap=smol+>
      <div tracks add-to fl fldircol gap=smoler
        data-react="album:track:explore"><?php foreach ($Album->tracks as $Track) : ?>
          <?php include TEMPLATE . "/album/track/_track_preview.php" ?>
        <?php endforeach; ?></div>
    </div>

    <p pinline12 text smol slight fl alic jucc gap=smol>
      <mi>info</mi>
      Song anklicken und easy hinzufügen
    </p>
  </popup-container__content>
</popup-container>


<?php

die($Request->success(data: ob_get_clean()));
