<?php

/**
 * General routes like the home and error pages.
 */

use Bruder\Application\Router;
use Bruder\Model\Album;

/**
 * @var Router $Router
 */

$Router->get(
  "/album/:id",
  "album/one",
  constraints: ["id" => "\d+",],
  title: function ($params) {
    $Album = Album::find($params["id"]);

    if (!$Album) return "Bruder wo?";

    return $Album->name;
  },
);
$Router->get("/albums", "album/index", title: "Alle Alben");

$Router->get("/album/edit", return: "JSON");
$Router->get("/album/new", return: "JSON");
$Router->post("/album/create", return: "JSON");
$Router->post("/album/update", return: "JSON");
$Router->post("/album/delete", return: "JSON");

$Router->get("/album/track/explore", return: "JSON");
$Router->get("/album/track/new", return: "JSON");
$Router->post("/album/track/create", return: "JSON");
$Router->post("/album/track/delete", return: "JSON");

$Router->post("/job/sync-albums", "album/sync", return: "JSON");
