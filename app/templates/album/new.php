<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

/**
 * Begin output buffer.
 */
ob_start();

?>

<popup-close>
  <mi>hide</mi>
</popup-close>


<popup-container>
  <p text bold style="font-size:3.8em;margin-bottom:-18px;padding-left:24px;">CREATE</p>
  <popup-container__content p42 posrel style=z-index:2; elevated>
    <form request="album:create" responder=simple redirect="/album/:id" fl fldircol gap=mid
      enctype="multipart/form-data">
      <div fl fldircol gap>
        <div mt32 mb32>
          <div select-album-cover fl jucc>
            <picture widest rounded ovhid>
              <img src />
              <mi cover>child_hat</mi>
              <mbutton trigger-file-input blur=smol material background=hover-dark size=mid icon-only>
                <input type=file accept="image/*" name=art hidden />
                <mi>edit</mi>
              </mbutton>
            </picture>
          </div>
        </div>

        <input crazy autofocus type=text name=name placeholder="Album-Titel" />
        <input crazy type=text name=release_year placeholder="Jahr des Releases" />
      </div>

      <div fl jucend alic>
        <mbutton material size=mid icon-only background=secondary color=secondary-text submit-closest>
          <mi>waving_hand</mi>
        </mbutton>
      </div>
    </form>
  </popup-container__content>
  <p text bold widest fl jucend style="font-size:3.8em;margin-top:-18px;padding-right:24px;">ALBUM</p>
</popup-container>


<?php

die($Request->success(data: ob_get_clean()));
