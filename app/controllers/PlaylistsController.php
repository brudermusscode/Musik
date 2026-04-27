<?php

namespace Bruder\Controller;

use Bruder\Controller\Controller;
use Bruder\Model\Playlist;

class PlaylistsController extends Controller
{

  /**
   * POST
   *
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["name"],
      optional: ["subtext"],
    );

    /**
     * @var ?Playlist
     */
    $Playlist = Playlist::where("name", $this->params->name)
      ->first();

    /**
     * Playlist exists?
     */
    if ($Playlist)
      return $this->error("<strong>Bruder, den Namen gibts schon!</strong>");

    return (new Playlist)->new($this->params);
  }

  /**
   * UPDATE
   *
   * @return string
   */
  public function update()
  {

    $this->validate_params(
      strict: ["id",],
      optional: ["playlist_song_index",],
    );

    /**
     * @var ?Playlist
     */
    $Playlist = Playlist::find($this->params->id);

    return $Playlist->edit($this->params);
  }

  /**
   * DELETE
   *
   * @return string
   */
  public function delete()
  {

    $this->validate_params(
      strict: ["id"],
      optional: [],
    );

    /**
     * @var ?Playlist
     */
    $Playlist = Playlist::find($this->params->id);

    /**
     * Playlist doesn't exist?
     */
    if (!$Playlist)
      return $this->error("<strong>Gibts nicht!</strong>");

    return $Playlist->remove();
  }
}
