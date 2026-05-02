<?php

use Bruder\Model\Track;

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

/**
 * @var Track
 */
$Track = Track::findOrReturn((int) $GLOBALS["route_param_id"]);

// ! Track not found
if (!file_exists(Track::base_path() . "/" . $Track->file_name))
  exit(error("Kein Track"));

/**
 * Append artwork.
 */
$Track->art = $Track->art_link();

/**
 * # Alright!
 */
die($Request->success("Here is your track m8 :*)", data: [
  "Track" => $Track,
  "track_public_url" => "/data/user/$Track->user_id/tracks/$Track->file_name",
]));
