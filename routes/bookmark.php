<?php

/**
 * General routes like the home and error pages.
 */

use Bruder\Application\Router;

/**
 * @var Router $Router
 */

$Router->post("/bookmark/create", "bookmark/create", return: "JSON");
$Router->post("/bookmark/delete", "bookmark/delete", return: "JSON");
