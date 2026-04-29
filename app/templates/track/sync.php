<?php

// TODO: Add new album or add to existing album automatically.
// TODO: Go deeper than track root directory to sync new tracks.

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
$ignore_dirs = ["deleted", ".", ".."];
$files = [];

/**
 * @var array
 */

/**
 * @return ?array
 */
function scan_dir_and_add_files_to_array(string $path_to_dir, array $ignore_names, ?string $prefix = null)
{

  $files = scandir($path_to_dir);
  $array = [];

  foreach ($files as $key => $file) {

    /**
     * @var array $files
     * @var string $file
     * @var int $key
     */

    $full_path = "$path_to_dir/$file";

    /**
     * If any file from files is in the ignore_names array, unset
     * them from the files and continue.
     */
    if (in_array($file, $ignore_names)) {
      unset($files[$key]);
      continue;
    }

    /**
     * Run this function again, if the path is a directory.
     */
    if (is_dir($full_path)) {

      $files2 = scan_dir_and_add_files_to_array($full_path, $ignore_names, prefix: $file);

      foreach ($files2 ?? [] as $file2)
        $array[] = $file2;

      /**
       * Remove the lingering folder which is not a file.
       */
      unset($array[$key]);
      continue;
    }

    /**
     * Append the file to the returner array.
     */
    $array[] = $prefix ? "$prefix/$file" : $file;
  }

  return $array ?: null;
}

$files = scan_dir_and_add_files_to_array($DIR__to_scan, $ignore_dirs);

/**
 * Try adding the files.
 */
$SyncTracks = (new Track)->hhhhhhi89999999999999999999pcreate_from_file($files);

die($SyncTracks);
