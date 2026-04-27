<?php

require_once _root() . "/config/get_requirements.php";

use Bruder\Http\Request;
use Bruder\Model\Track;
use Bruder\Model\Playlist;

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
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

/**
 * @var ?Playlist
 */
$Playlist = Playlist::with("tracks")->find($id);

if (!$Playlist)
  die($Request->error("<strong>K1 Album</strong>"));

/**
 * Input is empty?
 */
if (strlen($input) < 1)
  die($Request->error("<strong>Nichts eingegeben Bruder.</strong> Warum machst du so?"));

/**
 * @var Track
 */
$Tracks = Track::where("title", "LIKE", "%$input%")
  ->orWhere("artist", "LIKE", "%$input%")
  ->orWhere("genre", "LIKE", "%$input%")
  ->orWhere("file_name", "LIKE", "%$input%")
  ->orderBy("created_at", "DESC")
  ->get();

/**
 * geBin da output bufferino.
 */
ob_start();

foreach ($Tracks as $Track) :
  include TEMPLATE . "/playlist/track/_track_preview.php";
endforeach;

die($Request->success("Deine Tracks Bruder ;)", data: ob_get_clean()));
