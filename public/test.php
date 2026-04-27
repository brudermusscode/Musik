<?php

include dirname($_SERVER["DOCUMENT_ROOT"]) . "/vendor/autoload.php";

use Bruder\Model\Album;

header('Content-Type: application/json');

$response = Album::curl_song("The Weeknd", "Blinding Lights");

echo $response;
