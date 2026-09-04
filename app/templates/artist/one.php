<?php

// TODO: Add artists index page.
// TODO: Add artist profiles.
// TODO: Add artists to bookmarks.

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
$Artist = Artist::with(["tracks", "bookmark"])
  ->orderBy("id", "DESC")
  ->find((int) $id);

if (!$Artist) :
  include UNAVAILABLE;
else :

  /**
   * @var Collection<Track>
   */
  $Tracks = $Artist->tracks->sortByDesc("listens")->take(6);

  /**
   * + Playlist banner
   */
  include TEMPLATE . "/global/_current-playlist.php"; ?>

  <page artist data-type=artist data-id=<?= $Artist->id ?>>

    <!--- PROFILE BANNER --->
    <top-banner scroll-manipulated fl jucstart alistretch>
      <picture art elevated=smol posrel>
        <?php if ($Artist->art) : ?>
          <img src="/data/user/1/portraits/<?= $Artist->art ?>">
        <?php else : ?>
          <mi color=<?= $Artist::COLOR ?>><?= $Artist::ICON ?></mi>
        <?php endif ?>
      </picture>

      <div metadata fl fldircol alistart jucsb gap=smol+>
        <div fl alic jucsb gap=smol w100>
          <div fl alic gap=smoler>
            <p text smol semibold regular ttup background=<?= $Artist::COLOR ?> color=secondary-text pr14 pl10 pblock6 rounded=std fl alic gap=smol>
              <mi midler>artist</mi>
              Artist
            </p>
          </div>

          <div fl alic gap=smol jucend>
            <form <?= $Artist->bookmark
                    ? 'request="bookmark:delete" interchange-action="bookmark:create"'
                    : 'request="bookmark:create" interchange-action="bookmark:delete"'
                  ?> update-library reload toggle-button-active responder=simple>
              <input type=hidden name=id value=<?= $Artist->id ?> />
              <input type=hidden name=type value=artist />
              <mbutton material <?= $Artist->bookmark ? "active" : "" ?> submit-closest icon-only has-tooltip=bottom>
                <mi><?= $Artist->bookmark ? "remove" : "add" ?></mi>
                <div ttooltip text semibold>
                  <?= $Artist->bookmark ? "aus bib" : "in bib" ?></div>
              </mbutton>
            </form>

            <mbutton material request-get="artist:edit" data-id="<?= $Artist->id ?>" icon-only has-tooltip=bottom>
              <mi>edit</mi>
              <div ttooltip text semibold>bearrrbeiten</div>
            </mbutton>

            <dot-divider></dot-divider>

            <p window-light pr18 pl12 pblock10 rounded=wide text smol fl alic gap=smol>
              <mi>directory_sync</mi>
              <?= Time::ago($Artist->updated_at ?? $Artist->created_at); ?>
            </p>
          </div>
        </div>

        <div fl fldircol alistart gap=smol+>
          <?php

          /**
           * Evaluate a good size for the length of the album title to
           * show it in.
           */
          $album_strlen = strlen($Artist->name);
          $title_size = match (true) {
            $album_strlen >= 24 => "midplus",
            $album_strlen >= 16 => "wide",
            $album_strlen >= 14 => "wider",
            $album_strlen >= 10 => "widest",
            default => "widester"
          };

          ?>
          <p album-name text <?= $title_size ?> bold lh1 word-break>
            <?= $Artist->name ?></p>
        </div>
      </div>
    </top-banner>

    <!--- MOSTE LISTENED SONGS --->
    <div fl fldircol gap=smol+>
      <p text bold std pinline16 ttup>Meist gehört</p>
      <div fl fldircol gap=smoler>
        <?php

        $show_listens = true;
        $show_count = true;
        $count = 1;

        foreach ($Tracks as $Track) :
          include TEMPLATE . "/track/_track.php";
        endforeach; ?>
      </div>
    </div>

    <!--- DISCOGRAPHY --->
    <?php

    /**
     * @var Collection<Album>
     */
    $Albums = $Artist->all_albums()->sortByDesc("release_year");

    if ($Albums->count()) : ?>
      <div fl fldircol gap=smol+>
        <p text bold std ttup pinline16>Von <?= $Artist->name ?></p>
        <div fl gap=smol flex-wrap>
          <?php foreach ($Albums as $Album) :

            /**
             * @var Album $Album
             */

            include TEMPLATE . "/album/_album.php";
          endforeach ?>
        </div>
      </div>
    <?php endif; ?>

    <!--- SIMILIAR ARTISTS --->
    <?php

    /**
     * @var Collection<Artist>
     */
    $SimiliarArtists = $Artist->similiar_artists();

    if ($SimiliarArtists->count()) : ?>

      <div fl fldircol gap=smol+>
        <p text bold std ttup pinline16>Ähnliche Künstler</p>
        <div fl gap=smol flex-wrap>
          <?php foreach ($SimiliarArtists as $SimArtist) :

            /**
             * @var Artist $SimArtist
             */

            include TEMPLATE . "/artist/_artist.php";
          endforeach ?>
        </div>
      </div>
    <?php endif; ?>

  </page>

<?php endif; ?>