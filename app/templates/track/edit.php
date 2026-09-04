<?php

use Bruder\Model\Track;

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var ?Track
 */
$Track = Track::findOrReturn($id, "No Track");

ob_start(); ?>

<popup-close>
  <mi>web_traffic</mi>
</popup-close>

<form request="track:update" close-overlays update-current-track responder=simple
  enctype="multipart/form-data">
  <popup-container>
    <popup-container__content smolplus posrel style=z-index:2; elevated>
      <select-media trigger-file-input fl alic jucc>
        <video src autoplay loop muted></video>
        <div fl alic jucc gap=smol+>
          <mbutton size=wide material icon-only>
            <mi>add</mi>
          </mbutton>
        </div>
        <input type=file accept="video/*" name=video hidden />
      </select-media>

      <input type=hidden name=id value="<?= $Track->id ?>" />
    </popup-container__content>

    <mbutton flone tabindex="3" material size=wide has-icon=right window
      submit-closest color=white>
      <mi>charger</mi>
    </mbutton>
  </popup-container>
</form>


<?php

die($Request->success(data: ob_get_clean()));
