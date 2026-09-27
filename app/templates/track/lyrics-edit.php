<?php

use Bruder\Model\Track;

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var ?Track
 */
$Track = Track::findOrReturn($id, "No Track");

ob_start(); ?>

<popup-close>
  <mi>web_traffic</mi>
</popup-close>

<popup-container>
  <popup-container__content stdplus posrel style=z-index:2; elevated p42 fl fldircol gap>
    <div fl fldircol gap=smol>
      <p text smol ttup bold color=primary>Lyrics bearbeiten</p>
      <?php

      /**
       * @var bool
       */
      $track_playable = false;
      $show_menu = false;

      include TEMPLATE . "/track/_simple_track.php" ?>
    </div>

    <edit-lyrics fl fldircol gap=smoler>

      <?php

      $lyrics = $Track->lyrics_formatted();

      if ($lyrics) :
        foreach ($lyrics as $key => $line) : ?>

          <form data-form="track:lyrics-update">
            <line id="line-<?= $key; ?>" background=slight-light hoverable rounded=mid fl fldircol gap=smoler>
              <mbutton submit-closest material std icon-only>
                <mi>save</mi>
              </mbutton>

              <timestamp>
                <p text semibold stdplus color=tertiary><?= $line->timestamp; ?></p>
                <input crazy type=text name="lyrics_line_timestamp" value="<?= $line->timestamp; ?>" />
              </timestamp>

              <content>
                <p text stdplus bold><?= $line->content; ?></p>
                <input crazy type=text name="lyrics_line_content" value="<?= $line->content; ?>" />
              </content>

              <input type=hidden name="lyrics_line_key" value="<?= $key; ?>" />
              <input type=hidden name="id" value="<?= $Track->id; ?>" />
            </line>
          </form>

      <?php endforeach;
      endif; ?>
    </edit-lyrics>

    <p text smol slight fl alistart gap=smol+>
      <mi>info</mi>
      Klick einfach auf irgendeine Line, um sie zu bearbeiten.
    </p>
  </popup-container__content>
</popup-container>

<?php

die($Request->success(data: ob_get_clean()));
