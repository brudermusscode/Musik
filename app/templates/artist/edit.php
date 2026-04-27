<?php

use Bruder\Model\Artist;

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT) ?? 0;

/**
 * @var ?Artist
 */
$Artist = Artist::findOrReturn($id, "Kein Künstler");

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
    <form request="artist:update" reload update-current-track update-library responder=simple
      fl fldircol gap=mid
      enctype="multipart/form-data">
      <div fl fldircol gap=smol+>
        <div mt32 mb32>
          <div select-album-cover fl jucc>
            <picture widest rounded ovhid
              <?= $Artist->art ? "filled" : "" ?>>
              <img <?= $Artist->art
                      ? 'src="' . $Artist->art_link() . '"'
                      : "src" ?> />
              <mi cover color=quadro>face_3</mi>
              <mbutton trigger-file-input blur=smol material background=hover-dark size=mid icon-only>
                <input type=file accept="image/*" name=art hidden />
                <mi>edit</mi>
              </mbutton>
            </picture>
          </div>
        </div>
      </div>

      <input type=hidden name=id value="<?= $Artist->id ?>" />

      <div fl jucend alic>
        <mbutton material size=mid icon-only color=tertiary hoverable outlined submit-closest
          has-tooltip=left no-trans-delay>
          <mi>html</mi>
          <div ttooltip>Pööörfekt!</div>
        </mbutton>
      </div>
    </form>
  </popup-container__content>
</popup-container>


<?php

die($Request->success(data: ob_get_clean()));
