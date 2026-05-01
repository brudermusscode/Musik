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
    <mbutton <?= Cookie::get("__player_shuffle") ? "active" : "" ?> material size=mid icon-only player-shuffle has-tooltip=bottom>
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