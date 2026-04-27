<?php

use Bruder\Model\Album;

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var ?Album
 */
$Album = Album::find($id);

/**
 * Album exists?
 */
if (!$Album)
  die($Request->error("<strong>Bruder, das Album gibt es nicht D:</strong>"));

/**
 * Begin output buffer.
 */
ob_start();

?>

<popup-close>
  <mi>sentiment_satisfied</mi>
</popup-close>

<popup-container>
  <popup-container__content p42 posrel style=z-index:2; elevated>
    <form request="album:update" reload responder=simple update-library update-current-track
      fl fldircol gap=mid
      enctype="multipart/form-data">
      <div fl fldircol gap=smol+>
        <div mt32 mb32>
          <div select-album-cover fl jucc>
            <picture widest rounded ovhid
              <?= $Album->art ? "filled" : "" ?>>
              <img <?= $Album->art ? "src=\"/data/user/1/art/$Album->art\"" : "src" ?> />
              <mi cover>child_hat</mi>
              <mbutton trigger-file-input blur=smol material background=hover-dark size=mid icon-only>
                <input type=file accept="image/*" name=art hidden />
                <mi>edit</mi>
              </mbutton>
            </picture>
          </div>
        </div>

        <input autofocus crazy type=text name=name placeholder="Wie heißt es?"
          value="<?= $Album->name ?>" />
        <input crazy type=text name=release_year placeholder="Jahr des Releases"
          value="<?= $Album->release_year ?>" />
      </div>

      <input type=hidden name=id value="<?= $Album->id ?>" />

      <div fl jucend alic>
        <mbutton material size=mid icon-only color=tertiary hoverable outlined submit-closest
          has-tooltip=left no-trans-delay>
          <mi>mode_standby</mi>
          <div ttooltip>Gut so!</div>
        </mbutton>
      </div>
    </form>
  </popup-container__content>
</popup-container>


<?php

die($Request->success(data: ob_get_clean()));
