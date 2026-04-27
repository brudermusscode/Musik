<?php

namespace Bruder\Model;

use Bruder\Bruder;

class PlaylistTrack extends Bruder
{

  /**
   * @var array
   */
  protected $fillable = [
    "playlist_id",
    "track_id",
    "playlist_index",
    "deleted_at",
    "updated_at",
  ];

  /**
   * Create a new instance and save it to the database with given parameters.
   *
   * @param object $params
   * @return string
   */
  public function new(object $params)
  {

    /**
     * @var int
     */
    $index = 0;

    /**
     * Check if Index exists.
     */
    while (self::where([
      "playlist_id" => $params->id,
      "playlist_index" => $index
    ])->first())
      $index++;

    /**
     * Create it!
     */
    $Object = self::create([
      "playlist_id" => $params->id,
      "track_id" => $params->track_id,
      "playlist_index" => $index,
      "updated_at" => null,
    ]);

    /**
     * Update updated_at.
     */
    $Object->playlist->touch();

    return $this->success(data: $Object);
  }

  /**
   * Delete this instance.
   *
   * @return string
   */
  public function remove()
  {

    /**
     * Update updated_at.
     */
    $this->playlist->touch();

    /**
     * Delete it!
     */
    $this->delete();

    /**
     * @var int
     */
    $index = 0;

    /**
     * Update all playlist track indezies so that new tracks will
     * appended to the very end of a playlist and not inbetween.
     */
    foreach ($this->playlist->playlist_tracks->sortBy("playlist_index") as $Track) :
      $Track->update([
        "playlist_index" => $index,
      ]);

      $index++;
    endforeach;

    return $this->success();
  }

  /**
   * @return Playlist
   */
  public function playlist()
  {
    return $this->belongsTo(Playlist::class);
  }

  /**
   * @return Track
   */
  public function track()
  {
    return $this->belongsTo(Track::class);
  }
}
