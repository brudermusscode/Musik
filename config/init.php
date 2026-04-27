<?php

require_once dirname(__DIR__) . "/vendor/autoload.php";

use Bruder\Utils\Utils;

/**
 * Base error handling.
 */
require_once __DIR__ . "/init/error_handling.php";

/**
 * Initialize database connection.
 */
new Bruder\Database\Database;

/**
 * Include files from config/init/ directory
 */
foreach (glob(__DIR__ . "/init/*.php") as $filename) {

  /**
   * glob returns the files complete path, so we need to extract
   * the real filename from it. Split the path's string by slashes
   * and get the lasdt element. Remove the .php from the end for
   * ease of use.
   */
  $real_filename = str_replace(".php", "", array_last(explode("/", $filename)));

  /**
   * Continue on certain files that need to be loaded before or
   * after all other init files.
   */
  if (in_array($real_filename, ["error_handling", "router"]))
    continue;

  include_once $filename;
}

/**
 * Include definitions.
 */
require_once __DIR__ . "/define.php";
require_once __DIR__ . "/init/router.php";


/**
 * @var string
 */
$csrf_token = Utils::create_unique_token(24);

/**
 * Define this contant to tell further functions that the app has
 * been completly initialized.
 */
define("APP_INIT", true);
