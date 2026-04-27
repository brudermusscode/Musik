<?php

/**
 * Set the default timezone.
 */
date_default_timezone_set('Europe/Berlin');

/**
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 * ,,,,,,,,,,,,,, Ensure files ,,,,,,,,,,,,,,,,,,,
 * ,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,,
 */

/**
 * ? /storage/logs/php_errors.php
 */

/**
 * Define the path to the log file.
 */
$log_file_path = _root() . "/storage/logs/php_errors.log";

/**
 * Check if error file exists and create one.
 */
if (!file_exists($__log_path)) {
  echo "Creating new error file.";
  $lff = fopen($__log_path, "w");
  chmod($__log_path, 0666);
  fclose($lff);
}
