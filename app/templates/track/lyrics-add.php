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

<form request="track:update" close-overlays update-current-track responder=simple>
  <popup-container>
    <popup-container__content stdplus posrel style=z-index:2; elevated p42 fl fldircol gap>
      <div fl fldircol gap=smol>
        <p text smol ttup bold color=primary>Lyrics hinzufügen</p>
        <?php

        /**
         * @var bool
         */
        $track_playable = false;
        $show_menu = false;

        include TEMPLATE . "/track/_simple_track.php" ?>
      </div>

      <textarea name=lyrics_w_timestamps autofocus style=min-height:400px; placeholder="01:18.87
It should look…

01:58.87
exactly like this…

02:12.12
line by line!"><?= $Track->lyrics_w_timestamps ?></textarea>

      <input type=hidden name=id value="<?= $Track->id ?>" />
    </popup-container__content>

    <mbutton flone tabindex="3" material size=wide has-icon=right window
      submit-closest color=primary>
      <mi>resume</mi>
    </mbutton>
  </popup-container>
</form>

<?php

die($Request->success(data: ob_get_clean()));
