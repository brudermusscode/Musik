<?php

namespace Bruder\Controller;

use Bruder\Controller\Controller;
use Bruder\Model\Album;
use Bruder\Model\AlbumTrack;
use Bruder\Model\Track;

class AlbumTracksController extends Controller
{

  /**
   * POST
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["id", "track_id"],
      optional: [],
    );

    /**
     * @var ?Album
     */
    Album::findOrReturn($this->params->id, "No Album");

    /**
     * @var ?Track
     */
    Track::findOrReturn(
      $this->params->track_id,
      "No Track"
    );

    /**
     * Track is in Album already?
     */
    $AlbumTrack = AlbumTrack::where("album_id", $this->params->id)
      ->where("track_id", $this->params->track_id)
      ->first();

    /**
     * Delete it.
     */
    if ($AlbumTrack)
      return $this->delete(skip_validation: true);

    return (new AlbumTrack)->new($this->params);
  }

  /**
   * DELETE
   *
   * @return string
   */
  public function delete(bool $skip_validation = false)
  {

    if (!$skip_validation)
      $this->validate_params(
        strict: ["id", "track_id"],
        optional: [],
      );

    /**
     * @var ?AlbumTrack
     */
    $AlbumTrack = AlbumTrack::where("album_id", $this->params->id)
      ->where("track_id", $this->params->track_id)
      ->first();

    // ! None found
    if (!$AlbumTrack)
      return $this->error("No AlbumTrack");

    return $AlbumTrack->remove($this->params);
  }
}
