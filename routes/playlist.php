<?php

/**
 * General routes like the home and error pages.
 */

use Bruder\Application\Router;
use Bruder\Model\Playlist;

/**
 * @var Router $Router
 */

$Router->get(
  "/playlist/:id",
  "playlist/one",
  constraints: ["id" => "\d+",],
  title: function ($params) {
    $Playlist = Playlist::find($params["id"]);

    if (!$Playlist) return "Bruder wo?";

    return $Playlist->name;
  },
);

$Router->get("/playlist/new", "playlist/new", return: "JSON");
$Router->get("/playlist/track/new", "playlist/track/new", return: "JSON");
$Router->get("/playlist/track/explore", "playlist/track/explore", return: "JSON");

$Router->post("/playlist/create", "playlist/create", return: "JSON",);
$Router->post("/playlist/delete", "playlist/delete", return: "JSON",);
$Router->post("/playlist/update", "playlist/update", return: "JSON",);
$Router->post("/playlist/track/create", "playlist/track/create", return: "JSON",);
$Router->post("/playlist/track/delete", "playlist/track/delete", return: "JSON",);
