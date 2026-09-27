<?php

/**
 * Gets the current Track by the id which is set in the
 * `__player_Track` cookie.
 */

use Bruder\Application\Cookie;
use Bruder\Model\Track;
use Bruder\Http\Request;
use Bruder\Model\Album;
use Bruder\Model\Playlist;
use Bruder\Model\Artist;

include _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
$type = filter_input(INPUT_GET, "type", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var ?Album|Playlist
 */
$Relation = match ($type) {
  "album" => Album::with("tracks")->find($id),
  "playlist" => Playlist::with("tracks")->find($id),
  default => null,
};

/**
 * @var ?Track
 */
$CurrentTrack = Track::with("artistt")
  ->whereNull("deleted_at")
  ->find(Cookie::get("__player_Track"));

$track_relates = $Relation?->tracks->contains($CurrentTrack);
$has_video = $CurrentTrack?->video;

ob_start(); ?>

<current-track <?= $has_video ? "has-video" : "" ?>>

  <?php if ($CurrentTrack) : ?>
    <div fl fldircol gap=smol+>

      <!--- TRACK COVER/VIDEO --->
      <?php include TEMPLATE . "/components/_sidebar-current-track.php"; ?>

      <!--- TRACK RELATION --->
      <?php include TEMPLATE . "/components/_sidebar-current-track-relation.php"; ?>

      <!--- TRACK LYRICS --->
      <?php include TEMPLATE . "/components/_sidebar-current-track-lyrics.php"; ?>

      <!--- SPOTLIGHT --->
      <?php include TEMPLATE . "/components/_sidebar-current-track-spotlight.php"; ?>
    </div>

  <?php

  // + No Track
  else: ?>
    <div p2>
      <div background=slight-dark p42 rounded fl fldircol alistart gap=smol+>
        <mi mid mb6 style=height:2.4em;width:2.4em; circled background=slighterer-light fl alic jucc>music_off</mi>
        <p text bold ttup>Nichts gespielt</p>
        <p text>Einfach was abspielen Bruder, dann wird hier alles zu dem Track angezeigt.</p>
      </div>

      <div open-bruder mt6 pr18 pl52 pblock14 hoverable style=border-radius:18px;
        background=slight-dark ovhid posrel>
        <mi color=<?= Track::COLOR ?>
          style="position:absolute;bottom:-16px;left:-8px;font-size:52px;"><?= Track::ICON ?></mi>
        <div fl alic flone gap=smol jucsb>
          <p text smol semibold trimt>Tracks durchsuchen</p>
          <mi>arrow_forward</mi>
        </div>
      </div>
    </div>
  <?php endif; ?>
</current-track>

<?php exit($Request->success(data: ob_get_clean())); ?>