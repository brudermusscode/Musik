<?php

use Bruder\Controller\PlaylistsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new PlaylistsController($_POST))->update();

exit($Controller);
