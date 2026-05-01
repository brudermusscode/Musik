<?php

use Bruder\Controller\QueueController;

require _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

$Controller = (new QueueController($_POST))->create();

exit($Controller);
