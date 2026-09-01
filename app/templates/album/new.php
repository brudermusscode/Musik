<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

ob_start(); ?>

<popup-close>
  <mi>hide</mi>
</popup-close>

<form request="album:create" responder=simple redirect="/album/:id"
  enctype="multipart/form-data">
  <popup-container>
    <popup-container__content p42 posrel style=z-index:2; elevated fl fldircol gap>

      <div fl alic gap=smol color=secondary>
        <mi>album</mi>
        <p text smol bold ttup>Neuer Release</p>
      </div>

      <div fl fldircol gap mb24>
        <div mt32 mb32>
          <div select-album-cover fl jucc>
            <picture widest rounded ovhid>
              <img src />
              <mi cover color=secondary>album</mi>
              <mbutton trigger-file-input blur=smol material background=hover-dark size=mid icon-only>
                <input type=file accept="image/*" name=art hidden />
                <mi>edit</mi>
              </mbutton>
            </picture>
          </div>
        </div>

        <input crazy autofocus type=text name=name placeholder="Titel" />
        <input crazy type=text name=release_year placeholder="Jahr der Veröffentlichung" />
      </div>
    </popup-container__content>

    <mbutton flone tabindex="3" material size=wide has-icon=right window
      submit-closest color=secondary>
      <mi>waving_hand</mi>
    </mbutton>
  </popup-container>
</form>


<?php

die($Request->success(data: ob_get_clean()));
