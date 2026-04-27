<?php

use Bruder\Controller\AlbumTracksController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new AlbumTracksController($_POST))->delete();

exit($Controller);
