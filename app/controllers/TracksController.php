<?php

namespace Bruder\Controller;

use Bruder\Controller\Controller;
use Bruder\Model\Track;

class TracksController extends Controller
{


  /**
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
    $Track = Track::findOrReturn($this->params->id, "No Track");

    return $Track->edit($this->params);
  }

  /**
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
    $Track = Track::findOrReturn($this->params->id, "Kein Track");

    # Remove bookmark.
    $Track->bookmark()->delete();
    $Track->album_tracks()->delete();
    $Track->playlist_tracks()->delete();

    # We just set the song to soft deleted as we don't want to delete the whole track
    # together with the media file.
    $Track->update([
      "deleted_at" => CURRENT_TIMESTAMP,
    ]);

    return success();
  }
}
