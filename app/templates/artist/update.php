<?php

use Bruder\Controller\ArtistsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

/**
 * Append the file from art upload.
 */
if (isset($_FILES["art"]))
  $_POST["art"] = $_FILES["art"];

$Controller = (new ArtistsController($_POST))->update();

exit($Controller);
