<?php

use Bruder\Controller\BookmarksController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new BookmarksController($_POST))->update();

exit($Controller);
