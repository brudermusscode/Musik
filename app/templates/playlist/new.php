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
    <form data-action="playlist:create" fl fldircol gap=mid>
      <div fl fldircol gap>
        <input tabindex="1" crazy autofocus type=text name=name placeholder="Name?" />
        <input tabindex="2" crazy type=text name=subtext placeholder="Und ein Subtext?" />
      </div>

      <div fl jucend alic>
        <mbutton tabindex="3" material size=mid has-icon=right background=primary color=primary-text submit-closest>
          Ab geht's
          <mi>arrow_forward</mi>
        </mbutton>
      </div>
    </form>
  </popup-container__content>
  <p text bold widest fl jucend style="font-size:3.8em;margin-top:-18px;padding-right:24px;">PLAYLIST</p>
</popup-container>


<?php

die($Request->success(data: ob_get_clean()));
