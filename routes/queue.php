<?php

/**
 * General routes like the home and error pages.
 */

use Bruder\Application\Router;

/**
 * @var Router $Router
 */

$Router->post("/queue/create", "queue/create", return: "JSON");
