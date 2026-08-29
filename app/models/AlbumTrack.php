<?php

namespace Bruder\Model;

use Bruder\Bruder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlbumTrack extends Bruder
{

  /**
   * @var array
   */
  protected $fillable = [
    "album_id",
    "track_id",
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

    $Object = self::create([
      "album_id" => $params->id,
      "track_id" => $params->track_id,
    ]);

    $Object->album->touch();

    return $this->success("<strong>Track hinzugefügt.</strong>", $Object);
  }

  /**
   * Delete this instance.
   *
   * @return string
   */
  public function remove()
  {

    $this->album->touch();

    $this->delete();

    return $this->success("<strong>Track gelöscht.</strong>");
  }

  /**
   * @return BelongsTo<Album>
   */
  public function album()
  {
    return $this->belongsTo(Album::class);
  }

  /**
   * @return BelongsTo<Track>
   */
  public function track()
  {
    return $this->belongsTo(Track::class);
  }
}
