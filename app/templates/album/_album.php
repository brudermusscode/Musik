<album data-id="<?= $Album->id ?>" has-menu>
  <menu>
    <inr>
      <div height>
        <section>
          <option request-get="album:edit" data-id="<?= $Album->id ?>">
            <mi>edit</mi>
            Editieren
          </option>

          <form request="album:delete" responder=simple reload>
            <input type=hidden name=id value=<?= $Album->id ?> />
            <option data-id="<?= $Album->id ?>" submit-closest>
              <mi>delete_forever</mi>
              Weg damit
            </option>
          </form>
        </section>
      </div>
    </inr>
  </menu>

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