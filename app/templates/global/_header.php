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

    <dot-divider mt12 mb12></dot-divider>

    <a circled href="/albums">
      <mbutton material size=mid icon-only has-tooltip=right
        page=albums <?= CURRENT_PAGE === "albums" ? "active" : "" ?>>
        <mi>album</mi>
        <div ttooltip>Alben</div>
      </mbutton>
    </a>

    <bruder <?= in_array(CURRENT_PAGE, ["album", "artist"]) ? "scroll-manipulated" : "" ?>>
      <mi>search</mi>

      <?php

      /**
       * @var string
       */
      $track_explore_action = "track:explore";

      ?>

      <search-tools>
        <input autofocus floating mb24 data-action="<?= $track_explore_action ?>" placeholder="Titel, Playlisten »   « Künstler, Alben" />

        <div fl gap=smol+ alistart jucstretch>
          <div right-content data-react="<?= $track_explore_action ?>" tracks play-only flone>
            <p text smol bold ttup tac slight>Tipp was ein, um zu suchen</p>
          </div>
        </div>
      </search-tools>
    </bruder>
  </div>

  <div fl fldircol gap=smol>
    <theme-switcher <?= Cookie::get("__theme") === "light" ? "" : "active" ?>>
      <mi></mi>
    </theme-switcher>
  </div>
</header>