<?php

use Bruder\Application\Router;
use Bruder\Model\Artist;

/**
 * @var Router $Router
 */

$Router->get(
  "/artist/:id",
  "artist/one",
  constraints: ["id" => "\d+",],
  title: function ($params) {
    $Artist = Artist::find($params["id"]);

    if (!$Artist) return "Bruder wo?";

    return $Artist->name;
  },
);

$Router->get("/artists", "artist/index", title: "Alle Künstler");
$Router->get("/artist/edit", "artist/edit", return: "JSON");

$Router->post("/artist/update", "artist/update", return: "JSON");
