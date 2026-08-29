<?php

namespace Bruder\Controller;

use Bruder\Controller\Controller;
use Bruder\Model\Artist;
use Bruder\Model\Genre;
use Bruder\Model\Mood;

class ArtistsController extends Controller
{

  /**
   * @return string
   */
  public function create()
  {

    $this->validate_params(
      strict: ["name"],
      optional: [],
    );

    return error("No time for creating Artists");
  }

  /**
   * @return string
   */
  public function update()
  {

    $this->validate_params(
      strict: ["id",],
      optional: ["name", "art", "genres", "moods",],
    );

    /**
     * @var ?Artist
     */
    $Artist = Artist::findOrReturn(
      $this->params->id,
      "<strong>Bruder, Artist gibts nicht!</strong>"
    );

    # ? Name
    if (isset($this->params->name) && trim($this->params->name) !== $Artist->name) {
      // Nothing hihi
    }

    # ? Portrait
    if (!empty($this->params->art["tmp_name"])) {

      /**
       * @var object
       */
      $upload = $Artist->upload_art($this->params->art);

      if (!$upload->status)
        return error($upload->message);

      $Artist->art = $upload->data->file_name;
    }

    # ? Genres
    if (!empty($this->params->genres) && is_array($this->params->genres)) {
      $genres = $this->params->genres;

      foreach ($genres as $genre_id => $active) {
        # Check if any invalid genre is given and unset them.
        if (!Genre::find($genre_id))
          unset($genres[$genre_id]);

        # Any other that is active and not yet attached can be attached to the Artist.
        else if ($active && !$Artist->genres->contains("id", $genre_id))
          $Artist->genres()->attach($genre_id);

        # Any that is attached but disabled, can be deleted.
        else if (!$active && $Artist->genres->contains("id", $genre_id))
          $Artist->genres()->detach($genre_id);
      }
    }

    # ? Moods
    # TODO: Can be outsourced to an own function that checks bool values in an array.
    if (!empty($this->params->moods) && is_array($this->params->moods)) {
      $moods = $this->params->moods;

      foreach ($moods as $mood_id => $active) {
        # Check if any invalid mood is given and unset them.
        if (!Mood::find($mood_id))
          unset($moods[$mood_id]);

        # Any other that is active and not yet attached can be attached to the Artist.
        else if ($active && !$Artist->moods->contains("id", $mood_id))
          $Artist->moods()->attach($mood_id);

        # Any that is attached but disabled, can be deleted.
        else if (!$active && $Artist->moods->contains("id", $mood_id))
          $Artist->moods()->detach($mood_id);
      }
    }

    $Artist->db_transaction();

    try {
      $Artist->save();
      $Artist->db_commit();

      return success();
    } catch (\Exception $e) {
      $Artist->db_rollback();
      return error($e->getMessage());
    }

    return success();
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

    return error("No time for deleting Artists");
  }
}
