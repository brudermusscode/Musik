<?php

use Bruder\Model\Album;

/**
 * @var ?Album $Album
 */

$in_playlist ??= false;
$in_album ??= false;

?>

<menu>
  <inr>
    <div>
      <menu-option data-action="playlist:track:play-next">
        <mi>next_plan</mi>
        Als nächstes spielen
      </menu-option>

      <?php if ($in_playlist || $in_album) : ?>
        <menu-option data-action="<?= $in_playlist ? "playlist:track:delete" : "album:track:delete" ?>"
          data-track-id="<?= $Track->id ?>"
          data-id="<?= $Playlist?->id ?? $Album->id ?>">
          <mi>playlist_remove</mi>
          Rausschmeißen
        </menu-option>
      <?php endif ?>
    </div>

    <div>
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
          <menu-option>
            <mi>album</mi>
            Album
          </menu-option>
        </a>
      <?php endif ?>

      <a href="/artist/<?= $Track->artistt->id ?>">
        <menu-option>
          <mi>artist</mi>
          Künstler
        </menu-option>
      </a>
    </div>

    <div>
      <?php if (!$Track->bookmark) : ?>
        <menu-option has-sub shadow-submit update-library
          request="bookmark:create"
          data-id="<?= $Track->id ?>"
          data-type="track">
          <mi>add_row_above</mi>
          In Bib rein
        </menu-option>
      <?php else : ?>
        <menu-option has-sub shadow-submit update-library
          request="bookmark:delete"
          data-id="<?= $Track->id ?>"
          data-type="track">
          <mi>remove</mi>
          Aus Bib raus
        </menu-option>
      <?php endif; ?>

      <menu-option request-get="track:add-to" data-id="<?= $Track->id ?>" has-sub>
        <mi>box_add</mi>
        Hinzufügen zu…
      </menu-option>

      <menu-option request="track:delete" data-id="<?= $Track->id ?>"
        shadow-submit update-current-track update-library reload color=light-red has-sub>
        <mi>emoji_symbols</mi>
        Tschüss
      </menu-option>
    </div>
  </inr>
</menu>