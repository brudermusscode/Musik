<?php

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

ob_start(); ?>

<popup-close>
  <mi>hide</mi>
</popup-close>

<form data-action="playlist:create">
  <popup-container>
    <popup-container__content p42 posrel style=z-index:2; elevated fl fldircol gap>
      <div fl alic gap=smol color=tertiary>
        <mi>stacks</mi>
        <p text smol bold ttup>Neue Playlist</p>
      </div>

      <div fl fldircol gap mb24>
        <input tabindex="1" crazy autofocus type=text name=name placeholder="Name?" />
        <input tabindex="2" crazy type=text name=subtext placeholder="Und ein Subtext?" />
      </div>
    </popup-container__content>

    <mbutton flone tabindex="3" material size=wide has-icon=right window
      submit-closest color=tertiary>
      <mi>done</mi>
    </mbutton>
  </popup-container>
</form>


<?php

die($Request->success(data: ob_get_clean()));
