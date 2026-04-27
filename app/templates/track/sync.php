<?php

// TODO: Add new album or add to existing album automatically.

/**
 * Loading the app will trigger a "sync new music" job.
 * This file will go through /public/data/user/1/tracks and search
 * for tracks by file name, which are not yet in the database. It
 * skips already added files.
 *
 * ! Make sure to populate metadata of music files correctly!
 */


use Bruder\Model\Track;

require_once dirname($_SERVER["DOCUMENT_ROOT"]) . "/config/get_requirements.php";

/**
 * @var string
 */
$log_path = ROOT . "/storage/logs/track_sync.log";

/**
 * Log beginning of sync.
 */
file_put_contents(
  $log_path,
  PHP_EOL . "[" . date("H:i:s / d.m.Y") . "] Started file sync!" . PHP_EOL,
  FILE_APPEND | LOCK_EX
);

/**
 * @var string
 */
$DIR__to_scan = ROOT . "/public/data/user/1/tracks";

/**
 * @var array
 */
$files = scandir($DIR__to_scan);


/**
 * Build new array with full file path isntead of just the filename.
 */
foreach ($files as $key => $file_name) {

  /**
   * Remove common unix system file paths like . and .. which
   * do not represent files.
   */
  if (in_array($file_name, [".", ".."])) {
    unset($files[$key]);
    continue;
  }

  $files[$key] = "$DIR__to_scan/$file_name";
}

/**
 * Try adding the files.
 */
$SyncTracks = (new Track)->hhhhhhi89999999999999999999pcreate_from_file($files);

die($SyncTracks);
