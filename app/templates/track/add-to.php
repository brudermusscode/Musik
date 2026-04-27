<?php

// TODO: Add new album/playlist directly inside add-to.

use Bruder\Http\Request;
use Illuminate\Support\Collection;
use Bruder\Model\Album;
use Bruder\Model\Track;
use Bruder\Model\Playlist;

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

/**
 * @var int
 */
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

/**
 * @var ?Playlist
 */
$Track = Track::findOrReturn($id);

/**
 * Begin output buffer.
 */
ob_start();

?>

<popup-close>
  <mi>celebration</mi>
</popup-close>

<popup-container>
  <popup-container__content mid pinline42 pb32 pt42 posrel style=z-index:2; elevated>

    <div fl fldircol gap=smol>
      <p text regular no-word-wrap color=secondary>Track hinzufügen</p>

      <?php

      /**
       * @var bool
       */
      $track_playable = false;
      $show_menu = false;

      include TEMPLATE . "/track/_simple_track.php" ?>

      <div dno fl alic gap=smol>
        <mi style=height:100%; pblock18 pinline12 rounded=std background=secondary color=secondary-text>music_note_add</mi>

        <div flone pl18 pr16 pblock16 rounded=std fl alic jucsb gap=smol background=slight-light>
          <div fl alic gap=smol>
            <p text semibold trimt><?= $Track->title ?></p>
          </div>
          <p artist text smol regular lh1 fl alic gap="smoler" background="slight-light" rounded="smol" pl6 pr8 pblock4 clickable="no-shadow" no-word-wrap>
            <mi>artist</mi> <?= $Track->artist ?>
          </p>
        </div>
      </div>
    </div>

    <?php

    /**
     * @var string
     */
    $add_to_search_action = "track:add-to-explore"; ?>

    <input floating autofocus data-action="<?= $add_to_search_action ?>"
      placeholder="playlist:name »  « album:name" />

    <div fl fldircol gap=smol+>
      <div tracks add-to
        data-react="<?= $add_to_search_action ?>"
        data-track-id="<?= $Track->id ?>"
        fl jucc alistart gap=smol flex-wrap>

        <div fl fldircol gap flone>
          <div fl fldircol gap=smol alistart flone>
            <p text semibold pinline4>Vielleicht eine Playlist?</p>
            <div fl jucc alistart gap=smol flex-wrap flone w100>
              <?php

              /**
               * @var ?Collection<Playlist>
               */
              $Playlists = Playlist::with("tracks")
                ->whereHas("tracks", function ($q) use ($Track) {
                  $q->where("tracks.id", $Track->id);
                })
                ->limit(4)
                ->get();

              /**
               * @var int
               */
              $count = $Playlists->count();

              /**
               * Fetch as many playlists to get a count of 4.
               */
              $UnionPlaylists = Playlist::when($Playlists->count(), function ($q) use ($Playlists) {
                $q->whereNotIn("id", $Playlists->select("id")->values());
              })
                ->inRandomOrder()
                ->limit(4 - $count)
                ->get();

              /**
               * Add up all playlists to one collection.
               */
              $Playlists = $Playlists->concat($UnionPlaylists);

              foreach ($Playlists as $Object) : ?>
                <?php include TEMPLATE . "/track/_add_to_preview.php" ?>
              <?php endforeach ?>
            </div>
          </div>

          <div fl fldircol gap=smol alistart flone>
            <p text semibold pinline4>Oder ein Album?</p>
            <div fl jucc alistart gap=smol flex-wrap flone w100>
              <?php


              /**
               * @var ?Collection<Album>
               */
              $Albums = Album::with("tracks")
                ->whereHas("tracks", function ($q) use ($Track) {
                  $q->where("tracks.id", $Track->id);
                })
                ->limit(4)
                ->get();

              /**
               * @var int
               */
              $count = $Albums->count();

              /**
               * Fetch as many playlists to get a count of 4.
               */
              $UnionAlbums = Album::inRandomOrder()
                ->limit(4 - $count)
                ->get();

              /**
               * Add up all playlists to one collection.
               */
              $Albums = $Albums->concat($UnionAlbums);

              foreach ($Albums as $Object) : ?>
                <?php include TEMPLATE . "/track/_add_to_preview.php" ?>
              <?php endforeach ?>
            </div>
          </div>
        </div>

      </div>
    </div>

    <p text smol slight fl alistart gap=smol+>
      <mi>info</mi>
      Suche Playlisten mit „playlist:name“ oder Alben mit „album:name“. Wenn du kein Prefix angibst, durchsucht das Programm beide Modelle. Liebe.
    </p>
  </popup-container__content>
</popup-container>


<?php

die($Request->success(data: ob_get_clean()));
