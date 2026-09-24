<?php

use Illuminate\Support\Collection;
use Bruder\Model\Album;
use Bruder\Model\Artist;
use Bruder\Model\Track;
use Bruder\Time\Time;

/**
 * @var int
 */
$id = filter_var($GLOBALS["route_param_id"]);

/**
 * @var ?Artist
 */
$Artist = Artist::with("albums.tracks")
  ->orderBy("id", "DESC")
  ->find((int) $id);

if (!$Artist) :
  include UNAVAILABLE;
else :

  /**
   * @var Collection<Album>
   */
  $Releases = $Artist->all_albums()->sortByDesc("release_year");

  /**
   * + Playlist banner
   */
  include TEMPLATE . "/global/_current-playlist.php";

?>

  <div fl fldircol gap=mid>
    <?php foreach ($Releases as $Release) :

      $track_count = $Release->tracks->count();

    ?>

      <page album data-type=album data-id=<?= $Release->id ?> fl fldircol gap=smol+ style=padding-top:0;>
        <div window-light p2 rounded=mid fl aliend gap>
          <picture ovhid wider rounded=mid>
            <img src="/data/user/1/art/<?= $Release->art ?>" />
          </picture>

          <div fl fldircol gap=smoler pb24>
            <p text midplus bold><?= $Release->name ?></p>
            <div fl alic gap=smol>
              <?php if ($track_count === 1) : ?>
                <p text smol semibold regular ttup background=<?= Album::COLOR ?>
                  color=<?= Album::COLOR ?>-text pr10 pl8 pblock4 rounded=std fl
                  alic gap=smol>
                  <mi midler>album</mi>
                  Single
                </p>
              <?php else : ?>
                <p text smol semibold regular ttup background=<?= Album::COLOR ?>
                  color=<?= Album::COLOR ?>-text pr10 pl8 pblock4 rounded=std fl
                  alic gap=smol>
                  <mi midler>album</mi>
                  Album
                </p>
              <?php endif ?>
              <p text regular smolplus>&nbsp;<?= $Release->release_year ?></p>
              <?php if ($track_count > 1) : ?>
                &middot;
                <p text smolplus regular color=tertiary ttup>
                  <?= $Release->tracks->count() ?> tracks</p>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div fl fldircol>
          <?php

          $in_album = true;
          $show_listens = true;
          $show_count = true;
          $show_cover = false;
          $count = 1;

          foreach ($Release->tracks as $Track) :
            include TEMPLATE . "/track/_track.php";
          endforeach; ?>
        </div>
      </page>
    <?php endforeach ?>
  </div>
<?php endif ?>