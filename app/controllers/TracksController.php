<?php

namespace Bruder\Controller;

use Bruder\Controller\Controller;
use Bruder\Model\Track;

class TracksController extends Controller
{


  /**
   * UPDATE
   *
   * @return string
   */
  public function update()
  {

    $this->validate_params(
      strict: ["id"],
      optional: ["title", "artist", "file_name", "video", "genre", "year", "listens"],
    );

    /**
     * @var ?Track
     */
    $Object = Track::findOrReturn($this->params->id, "No Track");

    return $Object->edit($this->params);
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
     * @var ?Track
     */
    $Object = Track::findOrReturn($this->params->id, "Kein Track");


    /**
     * Move the actual file to tracks/deleted/.
     */
    $path = _root() . "/public/data/user/1/tracks";
    $full_path = "$path/$Object->file_name";


    $Object->db_transaction();

    try {
      if (file_exists($full_path))
        rename($full_path, "$path/deleted/$Object->file_name");

      $Object->album_track()->delete();
      $Object->playlist_tracks()->delete();
      $Object->delete();
      $Object->db_commit();
      return success();
    } catch (\Exception $e) {
      $Object->db_rollback();
      return error($e->getMessage());
    }

    return success("Aaaaaaaaaaaaaah shadow submitted brooooo");
  }
}
