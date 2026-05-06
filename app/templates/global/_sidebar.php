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
  <!--- this will cause chaos I prmoimse 😆 --->
  <section style=height:106px;>
    <pages>
      <a page=home href="/" <?= CURRENT_PAGE === "" ? "active" : "" ?>>
        <moption fl alic jucsb>
          <div fl alic gap=smol+>
            <mi>all_inclusive</mi>
            <p title>Alle Songs</p>
          </div>
          <p text smol ttup><?= Track::count() ?></p>
        </moption>
      </a>

      <a page=albums href="/albums" <?= in_array(CURRENT_PAGE, ["albums", "album"]) ? "active" : "" ?>>
        <moption fl alic jucsb>
          <div fl alic gap=smol+>
            <mi>album</mi>
            <p title>Alben</p>
          </div>
          <p text smol ttup><?= Album::count() ?></p>
        </moption>
      </a>
    </pages>
  </section>

  <section>
    <library view=<?= Cookie::get("__lib_view") ?>>

      <div fl alic jucsb p4>
        <div pl10 text smoler bold ttup>BIB</div>
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
</sidebar>

<sidebar right>
  <section current-track>
    <get-content from="/get/current-track">
      <?php include TEMPLATE . "/global/_loader.php"; ?>
    </get-content>
  </section>
</sidebar>