<?php

use Bruder\Controller\TracksController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new TracksController($_POST))->delete();

exit($Controller);
