<?php

/**
 * @var string $__log_path
 */

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
 * Check if error file exists and create one.
 */
if (!file_exists($__log_path)) {
  $lff = fopen($__log_path, "w");
  chmod($__log_path, 0666);
  fclose($lff);
}

unset($__log_path, $lff);
