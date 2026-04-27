<?php

use Bruder\Controller\AlbumsController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new AlbumsController($_POST))->delete();

exit($Controller);
