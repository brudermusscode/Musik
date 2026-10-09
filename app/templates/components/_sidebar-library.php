<form data-form="library:reorder" update-library>
  <input type=hidden name="reorder" value="1" />

  <library view=list>

    <!--- bib heading --->
    <div window-light z p8 fl alic jucsb
      style="border-radius:24px 24px 8px 8px;margin-bottom:-2px;">
      <p text smol ttup bold pinline12>BIBLIOTHEEK</p>

      <div fl alic gap=smol>
        <div fl alic gap=smol>
          <mbutton data-action="library:reorder" no-hover-shadow material normal smol icon-only has-tooltip=bottom>
            <mi>format_line_spacing</mi>
            <div ttooltip>Zeit für 1 neue Ordnung</div>
          </mbutton>
          <mbutton submit-closest reorder-library no-hover-shadow material normal smol icon-only has-tooltip=bottom>
            <mi>done_all</mi>
            <div ttooltip>Gut so</div>
          </mbutton>
        </div>

        <dot-divider></dot-divider>

        <library-view fl alic jucend gap=smoler>
          <mbutton active material normal no-hover-shadow smol icon-only view=list>
            <mi stdplus>view_day</mi>
          </mbutton>
          <mbutton disabled material normal no-hover-shadow smol icon-only view=grid>
            <mi stdplus>grid_view</mi>
          </mbutton>
        </library-view>
      </div>
    </div>

    <!--- actual bib with bookmarks --->
    <bookmarks window-light p12
      style="border-radius:8px 8px 24px 24px;margin-bottom:-2px;">
      <get-content from="/get/library">
        <?php include TEMPLATE . "/global/_loader.php"; ?>
      </get-content>
    </bookmarks>
  </library>
</form>