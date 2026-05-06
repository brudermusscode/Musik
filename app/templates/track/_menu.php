<?php

/**
 * @var ?Album $Album
 */

?>

<menu>
  <inr>
    <div height>
      <section>
        <moption data-action="playlist:track:play-next">
          <mi>next_plan</mi>
          Als nächstes spielen
        </moption>

        <?php if ($in_playlist || $in_album) : ?>
          <moption data-action="<?= $in_playlist ? "playlist:track:delete" : "album:track:delete" ?>"
            data-track-id="<?= $Track->id ?>"
            data-id="<?= $Playlist?->id ?? $Album->id ?>">
            <mi>playlist_remove</mi>
            Rausschmeißen
          </moption>
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
            <moption>
              <mi>album</mi>
              Album
            </moption>
          </a>
        <?php endif ?>

        <a href="/artist/<?= $Track->artistt->id ?>">
          <moption>
            <mi>artist</mi>
            Künstler
          </moption>
        </a>
      </section>

      <section>
        <moption request-get="track:add-to" data-id="<?= $Track->id ?>" has-sub>
          <mi>sticker_add</mi>
          Hinzufügen
        </moption>

        <moption request="track:delete" data-id="<?= $Track->id ?>"
          shadow-submit update-current-track reload color=light-red has-sub>
          <mi>emoji_symbols</mi>
          Tschüss
        </moption>
      </section>
    </div>
  </inr>
</menu>