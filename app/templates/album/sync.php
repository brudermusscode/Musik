<?php

use Bruder\Http\Request;
use Bruder\Job\SyncAlbums;

include _root() . "/config/get_requirements.php";

/**
 * @var Request $Request
 */

exit((new SyncAlbums)->execute());
