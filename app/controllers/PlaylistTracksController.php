<?php

namespace Bruder\Controller;

use Bruder\Controller\Controller;
use Bruder\Model\Playlist;
use Bruder\Model\Track;
use Bruder\Model\PlaylistTrack;

class PlaylistTracksController extends Controller
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
     * Find the Playlist.
     */
    Playlist::findOrReturn($this->params->id, "No Playlist");

    /**
     * Find the Track.
     */
    Track::findOrReturn($this->params->track_id, "No Track");

    /**
     * Track is in Playlist already?
     */
    $PlaylistTrack = PlaylistTrack::where("playlist_id", $this->params->id)
      ->where("track_id", $this->params->track_id)
      ->first();

    /**
     * Delete it.
     */
    if ($PlaylistTrack)
      return $this->delete(skip_validation: true);

    return (new PlaylistTrack)->new($this->params);
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
     * @var ?PlaylistTrack
     */
    $PlaylistTrack = PlaylistTrack::where("playlist_id", $this->params->id)
      ->where("track_id", $this->params->track_id)
      ->first();

    // ! None found
    if (!$PlaylistTrack)
      return $this->error("No PlaylistTrack");

    return $PlaylistTrack->remove($this->params);
  }
}
