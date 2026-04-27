<?php

/**
 * @var ?Album $Album
 */

?>

<menu>
  <inr>
    <div height>
      <section>
        <option data-action="playlist:track:play-next">
          <mi>next_plan</mi>
          Als nächstes spielen
        </option>

        <option request-get="track:add-to" data-id="<?= $Track->id ?>" has-sub>
          <mi>sticker_add</mi>
          Hinzufügen
        </option>

        <?php if ($in_playlist || $in_album) : ?>
          <option data-action="<?= $in_playlist ? "playlist:track:delete" : "album:track:delete" ?>"
            data-track-id="<?= $Track->id ?>"
            data-id="<?= $Playlist?->id ?? $Album->id ?>">
            <mi>playlist_remove</mi>
            Rausschmeißen
          </option>
        <?php endif ?>
      </section>

      <section>
        <?php

        /**
         * @var bool
         */
        $show_album = true;

        /**
         * Evaluate, rather to show the album link in menu or not.
         */
        if (CURRENT_PAGE === "album" && $Album->id === (int) $_GET["id"])
          $show_album = false;
        else if (CURRENT_PAGE !== "album" && isset($Album) && $Album)
          $show_album = true;
        else if (!isset($Album) || !$Album)
          $show_album = false;

        if ($show_album): ?>
          <a href="/album/<?= $Album->id ?>">
            <option>
              <mi>album</mi>
              Album
            </option>
          </a>
        <?php endif ?>

        <a href="/artist/<?= $Track->artistt->id ?>">
          <option>
            <mi>artist</mi>
            Künstler
          </option>
        </a>
      </section>
    </div>
  </inr>
</menu>