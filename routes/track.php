<?php

/**
 * General routes like the home and error pages.
 */

use Bruder\Application\Router;

/**
 * @var Router $Router
 */

$Router->get(
  "/track/one/:id",
  "track/one",
  constraints: ["id" => "\d+",],
  return: "JSON",
);

$Router->post(
  "/track/sync",
  "track/sync",
  return: "JSON",
);

$Router->get("/track/edit", return: "JSON");
$Router->get("/track/explore", return: "JSON");
$Router->get("/track/add-to", return: "JSON");
$Router->get("/track/add-to-explore", return: "JSON");

$Router->post("/track/update", return: "JSON");
