<?php

/**
 * General routes like the home and error pages.
 */

use Bruder\Application\Router;

/**
 * @var Router $Router
 */

$Router->get("/not-found", "error/404", title: "Bruder, was geht jetzt?");
$Router->get("/", "home/index", title: APP_NAME);

/**
 * Elements.
 */
$Router->get("/get/library", "components/library", return: "JSON");
$Router->get("/get/current-track", "components/current-track", return: "JSON");
