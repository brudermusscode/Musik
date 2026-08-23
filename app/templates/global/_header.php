<?php

use Bruder\Application\Cookie;

?>

<header>
  <div></div>

  <div fl w100 alic jucsb gap=smol>
    <theme-switcher <?= Cookie::get("__theme") === "light" ? "" : "active" ?>>
      <mi></mi>
    </theme-switcher>

    <div fl alic gap=smol>
      <mbutton player-repeat
        repeat=<?= Cookie::get("__player_repeat") ?? "\"\"" ?>
        <?= in_array(Cookie::get("__player_repeat"), ["all", "single"]) ? "active" : "" ?>
        material size=mid icon-only has-tooltip=bottom window>
        <mi></mi>
        <div ttooltip text regular></div>
      </mbutton>

      <mbutton player-shuffle <?= Cookie::get("__player_shuffle") ? "active" : "" ?> material size=mid icon-only has-tooltip=bottom window>
        <mi>shuffle</mi>
        <div ttooltip text regular>
          Shuffle?
        </div>
      </mbutton>

      <div divider background=slighter-light rounded minline6
        style="height:42px;width:2px;"></div>

      <volume-controls fl alic gap=smol volume="<?= CURRENT_VOLUME ?>">
        <div controls elevated>
          <mbutton volume-up material size=mid icon-only hoverable>
            <mi>add</mi>
          </mbutton>
          <mbutton volume-down material size=mid icon-only hoverable>
            <mi>remove</mi>
          </mbutton>
        </div>

        <div volume-display volume-mute window>
          <div muted></div>
          <mi style=font-size:1.4em;></mi>
        </div>
      </volume-controls>
    </div>
  </div>
</header>