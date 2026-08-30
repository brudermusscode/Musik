<?php

// TODO: Reorder library bookmarks in a persistent way
// TODO: Right sidebar with song info.
// TODO: Right sidebar with queue.
// TODO: Shrink sidebars (left+right)

use Bruder\Application\Cookie;
use Bruder\Model\Track;
use Bruder\Model\Album;

?>

<sidebar left>
  <section>
    <library view=<?= Cookie::get("__lib_view") ?>>

      <div fl alic jucsb p4 dno>
        <div fl alic gap=smoler>
          <mbutton has-tooltip="right" material rounded=smol icon-only
            background="slighter-light" request-get="playlist:new" no-hover-shadow>
            <mi>add</mi>
            <div ttooltip="">Playlist erstellen</div>
          </mbutton>
        </div>

        <library-view fl alic jucend gap=smoler>
          <p <?= Cookie::get("__lib_view") === "list" ? "active" : "" ?> view=list hoverable pinline8 pblock6 rounded=smol>
            <mi midler>view_day</mi>
          </p>
          <p <?= Cookie::get("__lib_view") === "grid" ? "active" : "" ?> view=grid hoverable pinline8 pblock6 rounded=smol>
            <mi midler>grid_view</mi>
          </p>
        </library-view>
      </div>

      <bookmarks p4>
        <get-content from="/get/library">
          <?php include TEMPLATE . "/global/_loader.php"; ?>
        </get-content>
      </bookmarks>
    </library>
  </section>

  <mbutton material size=mid rounded=std mid window has-icon=left
    minline8 bold
    request-get="playlist:new">
    <mi>add</mi>
  </mbutton>
</sidebar>

<sidebar right>
  <section current-track>
    <get-content from="/get/current-track">
      <?php include TEMPLATE . "/global/_loader.php"; ?>
    </get-content>
  </section>
</sidebar>