<?php

use Bruder\Controller\AlbumsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

/**
 * Append the file from art upload.
 */
if (isset($_FILES["art"]))
  $_POST["art"] = $_FILES["art"];

$Controller = (new AlbumsController($_POST))->create();

exit($Controller);
