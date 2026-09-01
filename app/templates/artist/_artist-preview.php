<?php

use Bruder\Model\Artist;

/**
 * @var ?Artist $Artist
 * @var ?Artist $SimArtist
 */

$UnoriginalArtist = $SimArtist ?? $Artist;

?>

<a href="/artist/<?= $UnoriginalArtist->id ?>">
  <div fl alic jucsb gap=smol hoverable=slight rounded=midplus p8>
    <div dno>
      <?php

      $track_count = $UnoriginalArtist->tracks->count(); ?>
      <p text semibold tracks tar>
        <?= $track_count . "<br><span text smoler ttup regular>track" . ($track_count > 1 ? "s" : "") . "</span>" ?>
      </p>
    </div>

    <div fl alic gap=smol+>
      <picture midler rounded=mid <?= $UnoriginalArtist->art ? "has-art" : "" ?> ovhid background=secondary posrel>
        <?php if ($UnoriginalArtist->art) : ?>
          <img src="<?= $UnoriginalArtist->art_link() ?>" loaded=true />
        <?php else : ?>
          <mi color=light style=font-size:42px;position:absolute;bottom:-10px;left:-6px;>
            artist</mi>
        <?php endif; ?>
      </picture>

      <div fl fldircol gap=smoler>
        <p text stdplus semibold><?= $UnoriginalArtist->name ?></p>
        <p text smoler regular ttup>Artist &nbsp;&middot;&nbsp; <?= $track_count ?> tracks</p>
      </div>
    </div>
  </div>
</a>