<?php

use Bruder\Model\Playlist;

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

/**
 * @var ?Playlist
 */
$Playlist = Playlist::find($id);

/**
 * Begin output buffer.
 */
ob_start();

?>

<popup-close>
  <mi>glyphs</mi>
</popup-close>

<popup-container>
  <popup-container__content mid pinline42 pb32 pt42 posrel style=z-index:2; elevated>

    <?php if ($Playlist) : ?>
      <div fl gap=smol alic>
        <p text bold>Add music to</p>
        <div pl6 pr12 pblock6 rounded=smol fl alic gap=smol background=slight-light>
          <mi>library_music</mi>
          <p text semibold><?= $Playlist->name ?></p>
        </div>
      </div>
    <?php endif; ?>

    <input autofocus floating placeholder="Titel, Artist, oder sonst was..."
      data-action="playlist:track:explore" data-id="<?= $Playlist->id ?>" />

    <div fl fldircol gap=smol+>
      <div tracks add-to fl fldircol gap=smoler
        data-react="playlist:track:explore"><?php foreach ($Playlist->tracks as $Track) : ?>
          <?php include TEMPLATE . "/playlist/track/_track_preview.php" ?>
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
