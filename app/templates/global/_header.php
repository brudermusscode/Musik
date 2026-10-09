<?php

use Bruder\Application\Cookie;

?>

<time-label background=light color=dark text smol bold pinline10 pblock6 rounded=smolplus elevated=wide></time-label>

<header fl fldircol jucsb alic gap>
  <div fl fldircol alic gap=smol>
    <a circled href="/">
      <mbutton material size=mid icon-only has-tooltip=right
        page=home <?= CURRENT_PAGE === "home" || !CURRENT_PAGE || CURRENT_PAGE === "/" ? "active" : "" ?>>
        <mi>animated_images</mi>
        <div ttooltip>Alles</div>
      </mbutton>
    </a>

    <a circled href="/albums">
      <mbutton material size=mid icon-only has-tooltip=right
        page=albums <?= CURRENT_PAGE === "albums" ? "active" : "" ?>>
        <mi>album</mi>
        <div ttooltip>Alben</div>
      </mbutton>
    </a>

    <mbutton data-action="bruder:open" material size=mid icon-only has-tooltip=right style=position:static;>
      <mi>search</mi>
      <div ttooltip>Alles durchsuchen</div>
    </mbutton>

    <dot-divider mt12 mb12></dot-divider>

    <mbutton material size=mid mid icon-only bold has-tooltip=right
      request-get="playlist:new">
      <mi>splitscreen_add</mi>
      <div ttooltip>Neue Playlist</div>
    </mbutton>

    <mbutton material size=mid mid icon-only bold has-tooltip=right
      request-get="album:new">
      <mi>sticker_add</mi>
      <div ttooltip>Neuer Release</div>
    </mbutton>
  </div>

  <div fl fldircol gap=smol>
    <theme-switcher <?= Cookie::get("__theme") === "light" ? "" : "active" ?>>
      <mi></mi>
    </theme-switcher>
  </div>
</header>