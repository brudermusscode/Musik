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

/**
 * Begin output buffer.
 */
ob_start();

?>

<popup-close>
  <mi>web_traffic</mi>
</popup-close>

<popup-container>
  <popup-container__content smolplus posrel style=z-index:2; elevated>
    <form request="track:update" close-overlays update-current-track responder=simple fl fldircol gap
      enctype="multipart/form-data">
      <select-media trigger-file-input fl alic jucc>
        <video src autoplay loop muted></video>
        <div fl alic jucc gap=smol+>
          <mbutton size=wide material icon-only background=tertiary color=tertiary-text>
            <mi>add</mi>
          </mbutton>
        </div>
        <input type=file accept="video/*" name=video hidden />
      </select-media>

      <input type=hidden name=id value="<?= $Track->id ?>" />

      <div fl jucend alic pinline42 pb28>
        <mbutton material size=mid icon-only hoverable outlined submit-closest
          has-tooltip=left no-trans-delay>
          <mi>charger</mi>
          <div ttooltip>Wow!</div>
        </mbutton>
      </div>
    </form>
  </popup-container__content>
</popup-container>


<?php

die($Request->success(data: ob_get_clean()));
