<album data-id="<?= $Album->id ?>" has-menu>
  <?php

  /**
   * + Context menu
   */
  include __DIR__ . "/_menu.php" ?>

  <a href="/album/<?= $Album->id ?>">
    <div top-menu>
      <?php

      $track_count = $Album->tracks->count(); ?>
      <p text semibold tracks tar>
        <?= $track_count . "<br><span text smoler ttup regular>track" . ($track_count > 1 ? "s" : "") . "</span>" ?>
      </p>
    </div>

    <picture cover <?= $Album->art ? "has-art" : "" ?>>
      <?php if ($Album->art) : ?>
        <img src="/data/user/1/art/<?= $Album->art ?>" />
      <?php else : ?>
        <mi>emoji_symbols</mi>
      <?php endif; ?>
    </picture>

    <div metadata>
      <p name tac><?= $Album->name ?></p>
    </div>
  </a>
</album>