<?php

use Bruder\Application\Cookie;

?>

<time-label background=light color=dark text smol bold pinline10 pblock6 rounded=smolplus elevated=wide></time-label>

<header fl w100 alic jucsb gap=smol>
  <div fl alic gap=smol>
    <theme-switcher <?= Cookie::get("__theme") === "light" ? "" : "active" ?>>
      <mi></mi>
    </theme-switcher>

    <div divider background=slight-light rounded minline2
      style="height:6px;width:6px;"></div>

    <a circled href="/">
      <mbutton material size=mid icon-only window has-tooltip=bottom
        page=home <?= CURRENT_PAGE === "home" || !CURRENT_PAGE || CURRENT_PAGE === "/" ? "active" : "" ?>>
        <mi>newsstand</mi>
        <div ttooltip>Alles</div>
      </mbutton>
    </a>

    <a circled href="/albums">
      <mbutton material size=mid icon-only window has-tooltip=bottom
        page=albums <?= CURRENT_PAGE === "albums" ? "active" : "" ?>>
        <mi>album</mi>
        <div ttooltip>Alben</div>
      </mbutton>
    </a>
  </div>

  <div fl alic gap=smol>
    <mbutton player-repeat
      repeat=<?= Cookie::get("__player_repeat") ?? "\"\"" ?>
      <?= in_array(Cookie::get("__player_repeat"), ["all", "single"]) ? "active" : "" ?>
      material size=midler icon-only has-tooltip=bottom window>
      <mi></mi>
      <div ttooltip text regular></div>
    </mbutton>

    <mbutton player-shuffle <?= Cookie::get("__player_shuffle") ? "active" : "" ?> material size=midler icon-only has-tooltip=bottom window>
      <mi>shuffle</mi>
      <div ttooltip text regular>
        Shuffle?
      </div>
    </mbutton>

    <div divider background=slight-light rounded minline2
      style="height:6px;width:6px;"></div>

    <!--- Volume will be populated by js. --->
    <player <?= Cookie::get("__player_collapsed") == 1 ? "collapsed" : "" ?>>
      <player-content fl alic jucc>

        <fullscreen-player-close>
          <mi midplus>close</mi>
        </fullscreen-player-close>

        <player-overflow>
          <duration-track></duration-track>
        </player-overflow>

        <player-actions fl alic gap=smol>
          <mbutton play-previous previous material icon-only size=midler hoverable no-hover-shadow window>
            <mi>chevron_backward</mi>
          </mbutton>
          <play-button play>
            <mi></mi>
          </play-button>
          <mbutton size=midler play-next next material icon-only hoverable no-hover-shadow window>
            <mi>chevron_forward</mi>
          </mbutton>
        </player-actions>
      </player-content>
    </player>

    <div divider background=slight-light rounded minline2
      style="height:6px;width:6px;"></div>

    <volume-controls fl alic gap=smol volume="<?= CURRENT_VOLUME ?>">
      <div controls elevated>
        <mbutton volume-up material size=midler icon-only hoverable>
          <mi>add</mi>
        </mbutton>
        <mbutton volume-down material size=midler icon-only hoverable>
          <mi>remove</mi>
        </mbutton>
      </div>

      <div volume-display volume-mute window>
        <div muted></div>
        <mi style=font-size:1.4em;></mi>
      </div>
    </volume-controls>

  </div>
</header>