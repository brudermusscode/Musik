<?php

use Bruder\Model\Artist;

/**
 * @var ?Artist $Artist
 * @var ?Artist $SimArtist
 */

$UnoriginalArtist = $SimArtist ?? $Artist;

?>
<album data-id="<?= $UnoriginalArtist->id ?>" has-menu>
  <?php

  /**
   * + Context menu
   */
  include __DIR__ . "/_menu.php" ?>

  <a href="/artist/<?= $UnoriginalArtist->id ?>">
    <div top-menu>
      <?php

      $track_count = $UnoriginalArtist->tracks->count(); ?>
      <p text semibold tracks tar>
        <?= $track_count . "<br><span text smoler ttup regular>track" . ($track_count > 1 ? "s" : "") . "</span>" ?>
      </p>
    </div>

    <picture cover <?= $UnoriginalArtist->art ? "has-art" : "" ?>>
      <?php if ($UnoriginalArtist->art) : ?>
        <img src="<?= $UnoriginalArtist->art_link() ?>" />
      <?php else : ?>
        <mi>emoji_symbols</mi>
      <?php endif; ?>
    </picture>

    <div metadata>
      <p name tac><?= $UnoriginalArtist->name ?></p>
    </div>
  </a>
</album>