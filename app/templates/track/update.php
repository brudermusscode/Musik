<?php

use Bruder\Controller\TracksController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

/**
 * Append the file from video upload.
 */
if (isset($_FILES["video"]))
  $_POST["video"] = $_FILES["video"];

$Controller = (new TracksController($_POST))->update();

exit($Controller);
