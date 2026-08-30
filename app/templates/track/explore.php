<?php

require_once _root() . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Album;
use Bruder\Model\Artist;
use Bruder\Model\Playlist;
use Bruder\Model\Track;

/**
 * @var Request $Request
 */

/**
 * @var mixed
 */
$input = filter_input(INPUT_GET, "q", FILTER_SANITIZE_SPECIAL_CHARS);

/**
 * Input is empty?
 */
if (strlen($input) < 1)
  die($Request->error("<strong>Nichts eingegeben Bruder.</strong> Warum machst du so?"));

/**
 * @var Track
 */
$Tracks = Track::where(function ($q) use ($input) {
  $q->where("title", "LIKE", "%$input%")
    ->orWhere("artist", "LIKE", "%$input%")
    ->orWhere("genre", "LIKE", "%$input%")
    ->orWhere("file_name", "LIKE", "%$input%");
})
  ->whereNull("deleted_at")
  ->orderBy("created_at", "DESC")
  ->get();

$Albums = Album::where("name", "LIKE", "%$input%")
  ->whereNull("deleted_at")
  ->orderBy("created_at", "DESC")
  ->get();

$Playlists = Playlist::where("name", "LIKE", "%$input%")
  ->whereNull("deleted_at")
  ->orderBy("created_at", "DESC")
  ->get();

$Artists = Artist::where("name", "LIKE", "%$input%")
  ->whereNull("deleted_at")
  ->orderBy("created_at", "DESC")
  ->get();

# Bind together all search results ordering by the creation date.
$Results = collect()
  ->merge($Artists->sortByDesc("created_at"))
  ->merge($Tracks->sortByDesc("listens"))
  ->merge($Albums->sortByDesc("created_at"))
  ->merge($Playlists->sortByDesc("created_at"));

ob_start();

foreach ($Results as $R) :
  if ($R instanceof Track) :
    $Track = $R;
    include TEMPLATE . "/track/_track_preview.php";
  elseif ($R instanceof Album) :
    $Album = $R;
    include TEMPLATE . "/album/_album.php";
  elseif ($R instanceof Playlist) :
    $Playlist = $R;
    echo "Nothing";
  elseif ($R instanceof Artist) :
    $Artist = $R;
    include TEMPLATE . "/artist/_artist-preview.php";
  endif;
endforeach;

die($Request->success("Deine Tracks Bruder ;)", data: ob_get_clean()));
