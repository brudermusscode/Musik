<?php

use Bruder\Controller\PlaylistTracksController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new PlaylistTracksController($_POST))->create();

exit($Controller);
