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
    <form data-form="library:reorder" update-library>
      <input type=hidden name="reorder" value="1" />

      <library view=list>

        <div fl alic jucsb style=margin-bottom:-2px;>
          <div fl alic gap=smol>
            <mbutton data-action="library:reorder" material smol icon-only>
              <mi>move_selection_down</mi>
            </mbutton>
            <mbutton submit-closest reorder-library material smol icon-only>
              <mi>done_all</mi>
            </mbutton>
          </div>

          <library-view fl alic jucend gap=smoler>
            <p active view=list hoverable pinline8 pblock6 rounded=std>
              <mi midler>view_day</mi>
            </p>
            <p disabled view=grid hoverable pinline8 pblock6 rounded=std>
              <mi midler>grid_view</mi>
            </p>
          </library-view>
        </div>

        <bookmarks window-light p8 rounded=mid>
          <get-content from="/get/library">
            <?php include TEMPLATE . "/global/_loader.php"; ?>
          </get-content>
        </bookmarks>
      </library>
    </form>
  </section>
</sidebar>

<sidebar right>
  <section current-track>
    <!--- Will be filled by js --->
  </section>
</sidebar>