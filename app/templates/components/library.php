<?php

use Bruder\Model\Track;
use Illuminate\Support\Collection;
use Bruder\Http\Request;
use Bruder\Model\Bookmark;
use Bruder\Model\Playlist;
use Bruder\Model\Artist;
use Bruder\Model\Album;

include _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

/**
 * @var Collection<Bookmark>
 */
$Bookmarks = Bookmark::with(["album", "playlist", "artist"])
  ->orderBy("view_index", "DESC")
  ->get();

/**
 * Begin output buffer.
 */
ob_start();

/**
 * + No Bookmatks.
 */
if (!$Bookmarks->count()) : ?>

  <div request-get="playlist:new" pr18 pl52 pblock14 hoverable rounded=std
    background=slight-dark ovhid posrel>
    <mi color=<?= Playlist::COLOR ?>
      style="position:absolute;bottom:-12px;left:-12px;font-size:52px;"><?= Playlist::ICON ?></mi>
    <div fl alic flone gap=smol jucsb>
      <p text smol semibold trimt>Playlist erstellen</p>
      <mi>arrow_forward</mi>
    </div>
  </div>

<?php endif;

/**
 * + Show Bookmarks
 */
foreach ($Bookmarks ?? [] as $key => $Bookmark) :

  /**
   * @var Bookmark $Bookmark
   */

  /**
   * @var ?Album|Playlist|Artist|Track
   */
  $Ref = $Bookmark->reference();

  if (!$Ref) continue;

  /**
   * @var bool
   */
  $show_active = CURRENT_PAGE === $Bookmark->type
    && isset($GLOBALS["route_param_id"])
    && $GLOBALS["route_param_id"] == $Ref->id;

?>

  <?php

  /**
   * We allow tracks to be added as bookmarks for easyily looping one song. Since
   * tracks have own HTML structures, we include the seperate structure instead of
   * manipulating the one for any other bookmarks.
   */
  if ($Bookmark->type !== "track") : ?>
    <a
      href="<?= $Bookmark->url() ?>"
      page=<?= $Bookmark->type ?>
      data-id="<?= $Ref->id ?>"
      <?= $show_active ? "active" : "" ?>>

      <moption flex-truncate>
        <div playing rounded color=tertiary background=slight-dark alic jucc z>
          <mi stdplus>volume_up</mi>
        </div>

        <?php

        $single_to_no_art = false;

        /**
         * @var ?array|string
         */
        $artworks = $Bookmark->art_link();

        /**
         * Playlists will have more than one art when there is no
         * custom art set, so we check for the artworks to be an
         * array and iterate through them, showing at most 4 artworks.
         */
        if (is_array($artworks) && count($artworks)) : ?>
          <cover no-custom-art=<?= count($artworks) ?>>
            <div>
              <?php foreach ($artworks as $keyy => $art) :
                if ($keyy == 4) break; ?>
                <picture>
                  <img src="<?= $art ?>" />
                </picture>
              <?php endforeach ?>
            </div>
          </cover>
        <?php

          /**
           * Anything else with a single or without an artwork.
           */
        else : ?>
          <cover single>
            <div>
              <picture>
                <?php if ($artworks) : ?>
                  <img src="<?= $artworks ?>" />
                <?php else : ?>
                  <mi color=<?= $Ref::COLOR ?>><?= $Ref::ICON ?></mi>
                <?php endif; ?>
              </picture>
            </div>
          </cover>
        <?php endif ?>

        <div metadata fl fldircol flone flex-truncate lh1>
          <p title text trimt><?= $Ref->name ?></p>
          <div sub fl alic gap=smoler>
            <p text smoler ttup regular><?= ucfirst($Bookmark->type) ?></p>
            &middot;
            <p text smoler ttup regular><?= $Ref->tracks->count() ?> Tracks</p>
          </div>
        </div>
      </moption>
    </a>
  <?php else :

    $Track = $Ref;
    $track_playable = true;

  ?>

    <?php include TEMPLATE . "/track/_track-library.php" ?>

  <?php endif; ?>
<?php endforeach;

die($Request->success(data: ob_get_clean()));
