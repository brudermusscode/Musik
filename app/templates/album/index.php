<?php

use Illuminate\Support\Collection;
use Bruder\Model\Album;

/**
 * @var Collection<Album>
 */
$Albums = Album::with("tracks")
  ->orderBy("id", "DESC")
  ->get();

if (!$Albums) :
  include UNAVAILABLE;
else :

  /**
   * + Playlist banner
   */
  include TEMPLATE . "/global/_current-playlist.php"; ?>

  <div fl gap=smol flex-wrap>
    <?php

    /**
     * + Albums
     */
    foreach ($Albums as $Album) :
      include TEMPLATE . "/album/_album.php";
    endforeach; ?>
  </div>

<?php endif; ?>