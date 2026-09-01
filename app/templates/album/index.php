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

  <div fl alic jucsb mb12 dno>
    <div></div>
    <div fl alic gap=smol>
      <mbutton material size=midler window icon-only>
        <mi>add</mi>
      </mbutton>
    </div>
  </div>

  <div fl gap=smol flex-wrap>
    <album request-get="album:new" fl alic jucc window-light hoverable rounded>
      <div fl fldircol alic jucc gap=smol>
        <mi mid>add</mi>
        <p text smoler bold ttup dno>Neues Album</p>
      </div>
    </album>

    <?php

    /**
     * + Albums
     */
    foreach ($Albums as $Album) :
      include TEMPLATE . "/album/_album.php";
    endforeach; ?>
  </div>

<?php endif; ?>