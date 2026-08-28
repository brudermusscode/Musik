<?php

require_once _root() . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Track;
use Bruder\Model\Playlist;
use Bruder\Model\Album;
use Illuminate\Support\Collection;

/**
 * @var Request $Request
 */

/**
 * @var mixed
 */
$input = filter_input(INPUT_GET, "q", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * @var int
 */
$track_id = filter_input(INPUT_GET, "track_id", FILTER_VALIDATE_INT);

/**
 * Input is empty?
 */
if (strlen($input) < 1)
  die($Request->error("<strong>Nichts eingegeben Bruder.</strong> Warum machst du so?"));

/**
 * @var Track|ERROR
 */
$Track = Track::findOrReturn($track_id);

/**
 * extract playlist: or album:
 */
$Object = match (true) {
  str_starts_with($input, "playlist:") => Playlist::class,
  str_starts_with($input, "album:") => Album::class,
  default => null,
};

/**
 * Cut the colon param.
 */
if ($Object) {
  $input = str_replace("playlist:", "", $input);
  $input = str_replace("album:", "", $input);
}

/**
 * @var Collection<Playlist|Album|Playlist|Album>
 */
$Collection = match (true) {
  ($Object === Playlist::class) => Playlist::where("name", "LIKE", "%$input%")->get(),
  ($Object === Album::class) => Album::where("name", "LIKE", "%$input%")->get(),
  default => Playlist::where("name", "LIKE", "%$input%")->get()
    ->concat(Album::where("name", "LIKE", "%$input%")->get()),
};

/**
 * Start the output buffer.
 */
ob_start();

/**
 * + Object previews
 */
foreach ($Collection as $Object) :
  include TEMPLATE . "/track/_add_to_preview.php";
endforeach;

die($Request->success(data: ob_get_clean()));
