<?php

namespace Bruder\Controller;

use Bruder\Controller\Controller;
use Bruder\Model\Album;
use Bruder\Model\Artist;
use Bruder\Model\Bookmark;
use Bruder\Model\Playlist;

class BookmarksController extends Controller
{

  /**
   * POST
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["id", "type"],
      optional: [],
    );

    /**
     * @var ?Album|Playlist|Artist
     */
    $Object = match ($this->params->type) {
      "album" => Album::findOrReturn($this->params->id),
      "playlist" => Playlist::findOrReturn($this->params->id),
      "artist" => Artist::findOrReturn($this->params->id),
      default => die($this->error()),
    };

    // ! Bookmark exists
    if ($Object->bookmark()->first())
      return $this->error();

    $this->params->Object = $Object;

    return (new Bookmark)->new($this->params);
  }

  /**
   * DELETE
   *
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["id", "type"],
      optional: [],
    );

    /**
     * @var ?Album|Playlist|Artist
     */
    $Bookmark = Bookmark::where("type", $this->params->type)
      ->where("reference_id", $this->params->id)
      ->first();


    // ! Bookmark doesn't exist
    if (!$Bookmark) return $this->error();

    $Bookmark->delete();

    return $this->success();
  }
}
