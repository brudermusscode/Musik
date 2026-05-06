<?php

use Bruder\Application\Cookie;

?>

<header>
  <logo>
    <picture>
      <img src="/logo.svg" />
    </picture>
  </logo>

  <div fl alic gap=smol+>
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

    <theme-switcher <?= Cookie::get("__theme") === "light" ? "" : "active" ?>>
      <mi></mi>
    </theme-switcher>
  </div>
</header>